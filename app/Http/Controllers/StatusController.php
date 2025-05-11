<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\DeletionService;
use Illuminate\Support\Facades\Session;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('status.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('status.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $formFields = $request->validate([
            'title' => ['required'],
            'color' => ['required']
        ]);
        $slug = generateUniqueSlug($request->title, Status::class);
        $formFields['slug'] = $slug;
        $roleIds = $request->input('role_ids');
        if ($status = Status::create($formFields)) {
            $status->roles()->attach($roleIds);
            return response()->json(['error' => false, 'message' => 'Status created successfully.', 'id' => $status->id, 'status' => $status]);
        } else {
            return response()->json(['error' => true, 'message' => 'Status couldn\'t created.']);
        }
    }

    public function list()
    {
        $search = request('search');
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $status = Status::orderBy($sort, $order);

        if ($search) {
            $status = $status->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }

        $total = $status->count();

        $canEdit = checkPermission('edit_statuses');
        $canDelete = checkPermission('delete_statuses');

        $status = $status
            ->paginate(request("limit"))
            ->through(function ($status) use ($canEdit, $canDelete) {
                $roles = $status->roles->pluck('name')->map(function ($roleName) {
                    return ucfirst($roleName);
                })->implode(', ');

                $actions = '';

                if ($canEdit) {
                    $actions .= '<a href="javascript:void(0);" class="edit-status" data-bs-toggle="modal" data-bs-target="#edit_status_modal" data-id="' . $status->id . '" title="' . get_label('update', 'Update') . '">' .
                        '<i class="bx bx-edit mx-1"></i>' .
                        '</a>';
                }

                if ($canDelete) {
                    $actions .= '<button title="' . get_label('delete', 'Delete') . '" type="button" class="btn delete" data-id="' . $status->id . '" data-type="status">' .
                        '<i class="bx bx-trash text-danger mx-1"></i>' .
                        '</button>';
                }
                $actions = $actions ?: '-';
                return [
                    'id' => $status->id,
                    'title' => $status->title . ($status->id == 0 ? ' <span class="badge bg-success">' . get_label('default', 'Default') . '</span>' : ''),
                    'roles_has_access' => $roles ?: ' - ',
                    'color' => '<span class="badge bg-' . $status->color . '">' . $status->title . '</span>',
                    'created_at' => format_date($status->created_at, true),
                    'updated_at' => format_date($status->updated_at, true),
                    'actions' => $actions ?? '-',
                ];
            });

        return response()->json([
            "rows" => $status->items(),
            "total" => $total,
        ]);
    }

    /**
     * List or search statuses.
     * 
     * This endpoint retrieves a list of statuses based on various filters. The user must be authenticated to perform this action. The request allows searching and sorting by different parameters.
     * 
     * @authenticated
     * 
     * @group Status Management
     *
     * @urlParam id int optional The ID of the status to retrieve. Example: 1
     * 
     * @queryParam search string optional The search term to filter statuses by title or id. Example: Active
     * @queryParam sort string optional The field to sort by. Defaults to "id". Sortable fields include: id, title, color, created_at, and updated_at. Example: title
     * @queryParam order string optional The sort order, either "ASC" or "DESC". Defaults to "DESC". Example: ASC
     * @queryParam limit int optional The number of statuses per page for pagination. Example: 10
     * @queryParam offset int optional The offset for pagination, indicating the starting point of results. Example: 0
     * 
     * @response 200 {
     *   "error": false,
     *   "message": "Statuses retrieved successfully",
     *   "total": 1,
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Active",
     *       "color": "primary",
     *       "created_at": "20-07-2024 17:50:09",
     *       "updated_at": "21-07-2024 19:08:16"
     *     }
     *   ]
     * }
     * 
     * @response 200 {
     *   "error": true,
     *   "message": "Status not found",
     *   "total": 0,
     *   "data": []
     * }
     * 
     * @response 200 {
     *   "error": true,
     *   "message": "Statuses not found",
     *   "total": 0,
     *   "data": []
     * }
     */
    public function apiList(Request $request, $id = '')
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);

        $statusQuery = Status::query();

        // Apply search filter
        if ($search) {
            $statusQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }

        if ($id) {
            $status = $statusQuery->find($id);
            if (!$status) {
                return formatApiResponse(
                    false,
                    'Status not found',
                    [
                        'total' => 0,
                        'data' => []
                    ]
                );
            }

            if (!canSetStatus($status)) {
                return formatApiResponse(
                    false,
                    'Access denied for the specified status',
                    [
                        'total' => 0,
                        'data' => []
                    ]
                );
            }

            return formatApiResponse(
                true,
                'Status retrieved successfully',
                [
                    'total' => 1,
                    'data' => [
                        [
                            'id' => $status->id,
                            'title' => $status->title,
                            'color' => $status->color,
                            'created_at' => format_date($status->created_at, to_format: 'Y-m-d'),
                            'updated_at' => format_date($status->updated_at, to_format: 'Y-m-d'),
                        ]
                    ]
                ]
            );
        } else {
            $statuses = $statusQuery->get()->filter(function ($status) {
                return canSetStatus($status);
            });

            $total = $statuses->count(); // Count only accessible statuses

            $statuses = $statuses->sortBy($sort, $order === 'DESC' ? SORT_DESC : SORT_ASC)
                ->slice($offset, $limit);

            if ($statuses->isEmpty()) {
                return formatApiResponse(
                    false,
                    'Statuses not found',
                    [
                        'total' => 0,
                        'data' => []
                    ]
                );
            }

            $data = $statuses->map(function ($status) {
                return [
                    'id' => $status->id,
                    'title' => $status->title,
                    'color' => $status->color,
                    'created_at' => format_date($status->created_at, to_format: 'Y-m-d'),
                    'updated_at' => format_date($status->updated_at, to_format: 'Y-m-d'),
                ];
            });

            return formatApiResponse(
                false,
                'Statuses retrieved successfully',
                [
                    'total' => $total,
                    'data' => $data->values() // Ensure proper reindexing
                ]
            );
        }
    }



    public function get($id)
    {
        $status = Status::findOrFail($id);
        $roles = $status->roles()->pluck('id')->toArray();
        return response()->json(['status' => $status, 'roles' => $roles]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $formFields = $request->validate([
            'id' => ['required'],
            'title' => ['required'],
            'color' => ['required']
        ]);
        $slug = generateUniqueSlug($request->title, Status::class, $request->id);
        $formFields['slug'] = $slug;
        $status = Status::findOrFail($request->id);

        if ($status->update($formFields)) {
            $roleIds = $request->input('role_ids');
            $status->roles()->sync($roleIds);
            return response()->json(['error' => false, 'message' => 'Status updated successfully.', 'id' => $status->id]);
        } else {
            return response()->json(['error' => true, 'message' => 'Status couldn\'t updated.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = Status::findOrFail($id);
        $status->projects(false)->update(['status_id' => 0]);
        $status->tasks(false)->update(['status_id' => 0]);
        $response = DeletionService::delete(Status::class, $id, 'Status');
        $data = $response->getData();
        if ($data->error) {
            return response()->json(['error' => true, 'message' => $data->message]);
        } else {
            return response()->json(['error' => false, 'message' => 'Status deleted successfully.', 'id' => $id, 'title' => $status->title]);
        }
    }

    public function destroy_multiple(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ids' => 'required|array', // Ensure 'ids' is present and an array
            'ids.*' => 'integer|exists:statuses,id' // Ensure each ID in 'ids' is an integer and exists in the table
        ]);

        $ids = $validatedData['ids'];
        $deletedIds = [];
        $deletedTitles = [];
        $defaultStatusIds = [];
        $nonDefaultIds = [];

        // Perform deletion using validated IDs
        foreach ($ids as $id) {
            $status = Status::findOrFail($id);
            if ($status) {
                if ($status->id == 0) {
                    $defaultStatusIds[] = $id;
                } else {
                    $status->projects(false)->update(['status_id' => 0]);
                    $status->tasks(false)->update(['status_id' => 0]);
                    $deletedIds[] = $id;
                    $deletedTitles[] = $status->title;
                    DeletionService::delete(Status::class, $id, 'Status');
                    $nonDefaultIds[] = $id;
                }
            }
        }

        if (count($defaultStatusIds) > 0) {
            if (count($ids) == 1) {
                return response()->json(['error' => true, 'message' => 'Default status cannot be deleted.']);
            } else {
                return response()->json(['error' => false, 'message' => 'Status(es) deleted successfully except default.', 'id' => $deletedIds, 'titles' => $deletedTitles]);
            }
        } else {
            return response()->json(['error' => false, 'message' => 'Status(es) deleted successfully.', 'id' => $deletedIds, 'titles' => $deletedTitles]);
        }
    }
}
