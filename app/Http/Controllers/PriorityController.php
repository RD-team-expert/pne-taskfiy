<?php

namespace App\Http\Controllers;

use App\Models\Priority;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\DeletionService;
use Illuminate\Support\Facades\Session;

class PriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('priority.list');
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
        $slug = generateUniqueSlug($request->title, Priority::class);
        $formFields['slug'] = $slug;

        if ($priority = Priority::create($formFields)) {
            return response()->json(['error' => false, 'message' => 'Priority created successfully.', 'id' => $priority->id, 'priority' => $priority]);
        } else {
            return response()->json(['error' => true, 'message' => 'Priority couldn\'t created.']);
        }
    }

    public function list()
    {
        $search = request('search');
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $priority = Priority::orderBy($sort, $order);

        if ($search) {
            $priority = $priority->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }

        $total = $priority->count();

        // Check permissions
        $canEdit = checkPermission('edit_priorities');
        $canDelete = checkPermission('delete_priorities');

        $priority = $priority
            ->paginate(request("limit"))
            ->through(function ($priority) use ($canEdit, $canDelete) {
                $actions = '';

                if ($canEdit) {
                    $actions .= '<a href="javascript:void(0);" class="edit-priority" data-bs-toggle="modal" data-bs-target="#edit_priority_modal" data-id="' . $priority->id . '" title="' . get_label('update', 'Update') . '">' .
                        '<i class="bx bx-edit mx-1"></i>' .
                        '</a>';
                }

                if ($canDelete) {
                    $actions .= '<button title="' . get_label('delete', 'Delete') . '" type="button" class="btn delete" data-id="' . $priority->id . '" data-type="priority">' .
                        '<i class="bx bx-trash text-danger mx-1"></i>' .
                        '</button>';
                }
                $actions = $actions ?: '-';
                return [
                    'id' => $priority->id,
                    'title' => $priority->title,
                    'color' => '<span class="badge bg-' . $priority->color . '">' . $priority->title . '</span>',
                    'created_at' => format_date($priority->created_at, true),
                    'updated_at' => format_date($priority->updated_at, true),
                    'actions' => $actions,
                ];
            });

        return response()->json([
            "rows" => $priority->items(),
            "total" => $total,
        ]);
    }


    /**
     * List or search priorities.
     * 
     * This endpoint retrieves a list of priorities based on various filters. The user must be authenticated to perform this action. The request allows searching and sorting by different parameters.
     * 
     * @authenticated
     * 
     * @group Priority Management
     *
     * @urlParam id int optional The ID of the priority to retrieve. Example: 1
     * 
     * @queryParam search string optional The search term to filter priorities by title or id. Example: High
     * @queryParam sort string optional The field to sort by. Defaults to "id". Sortable fields include: id, title, color, created_at, and updated_at. Example: title
     * @queryParam order string optional The sort order, either "ASC" or "DESC". Defaults to "DESC". Example: ASC
     * @queryParam limit int optional The number of priorities per page for pagination. Example: 10
     * @queryParam offset int optional The offset for pagination, indicating the starting point of results. Example: 0
     * 
     * @response 200 {
     *   "error": false,
     *   "message": "Priorities retrieved successfully",
     *   "total": 1,
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "High",
     *       "color": "primary",
     *       "created_at": "20-07-2024 17:50:09",
     *       "updated_at": "21-07-2024 19:08:16"
     *     }
     *   ]
     * }
     * 
     * @response 200 {
     *   "error": true,
     *   "message": "Priority not found",
     *   "total": 0,
     *   "data": []
     * }
     * 
     * @response 200 {
     *   "error": true,
     *   "message": "Priorities not found",
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

        $priorityQuery = Priority::query();

        // Apply search filter
        if ($search) {
            $priorityQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }

        if ($id) {
            $priority = $priorityQuery->find($id);
            if (!$priority) {
                return formatApiResponse(
                    false,
                    'Priority not found',
                    [
                        'total' => 0,
                        'data' => []
                    ]
                );
            }
            return formatApiResponse(
                false,
                'Priority retrieved successfully',
                [
                    'total' => 1,
                    'data' => [
                        [
                            'id' => $priority->id,
                            'title' => $priority->title,
                            'color' => $priority->color,
                            'created_at' => format_date($priority->created_at, to_format: 'Y-m-d'),
                            'updated_at' => format_date($priority->updated_at, to_format: 'Y-m-d'),
                        ]
                    ]
                ]
            );
        } else {
            $total = $priorityQuery->count(); // Get total count before applying offset and limit

            $priorities = $priorityQuery->orderBy($sort, $order)
                ->skip($offset)
                ->take($limit)
                ->get();

            if ($priorities->isEmpty()) {
                return formatApiResponse(
                    false,
                    'Priorities not found',
                    [
                        'total' => 0,
                        'data' => []
                    ]
                );
            }

            $data = $priorities->map(function ($priority) {
                return [
                    'id' => $priority->id,
                    'title' => $priority->title,
                    'color' => $priority->color,
                    'created_at' => format_date($priority->created_at, to_format: 'Y-m-d'),
                    'updated_at' => format_date($priority->updated_at, to_format: 'Y-m-d'),
                ];
            });

            return formatApiResponse(
                false,
                'Priorities retrieved successfully',
                [
                    'total' => $total,
                    'data' => $data
                ]
            );
        }
    }

    public function get($id)
    {
        $priority = Priority::findOrFail($id);
        return response()->json(['priority' => $priority]);
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
        $slug = generateUniqueSlug($request->title, Priority::class, $request->id);
        $formFields['slug'] = $slug;
        $priority = Priority::findOrFail($request->id);

        if ($priority->update($formFields)) {
            return response()->json(['error' => false, 'message' => 'Priority updated successfully.', 'id' => $priority->id]);
        } else {
            return response()->json(['error' => true, 'message' => 'Priority couldn\'t updated.']);
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
        $priority = Priority::findOrFail($id);
        $priority->projects(false)->update(['priority_id' => null]);
        $priority->tasks(false)->update(['priority_id' => null]);
        $response = DeletionService::delete(Priority::class, $id, 'Priority');
        $data = $response->getData();
        if ($data->error) {
            return response()->json(['error' => true, 'message' => $data->message]);
        } else {
            return response()->json(['error' => false, 'message' => 'Priority deleted successfully.', 'id' => $id, 'title' => $priority->title]);
        }
    }

    public function destroy_multiple(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ids' => 'required|array', // Ensure 'ids' is present and an array
            'ids.*' => 'integer'
        ]);

        $ids = $validatedData['ids'];
        $deletedIds = [];
        $deletedTitles = [];
        $defaultPriorityIds = [];
        $nonDefaultIds = [];

        // Perform deletion using validated IDs
        foreach ($ids as $id) {
            if ($id == 0) {
                $defaultPriorityIds[] = $id;
            } else {
                // Update related records
                $priority = Priority::find($id);
                if($priority){
                    $priority->projects(false)->update(['priority_id' => null]);
                    $priority->tasks(false)->update(['priority_id' => null]);
    
                    // Record deletion
                    $deletedIds[] = $id;
                    $deletedTitles[] = $priority->title;
                    DeletionService::delete(Priority::class, $id, 'Priority');
                    $nonDefaultIds[] = $id;
                }
            }
        }

        // Respond based on whether default priorities were included in the request
        if (count($defaultPriorityIds) > 0) {
            if (count($ids) == 1) {
                return response()->json(['error' => true, 'message' => 'Default priority cannot be deleted.']);
            } else {
                return response()->json(['error' => false, 'message' => 'Priority/Priorities deleted successfully except default.', 'id' => $deletedIds, 'titles' => $deletedTitles]);
            }
        } else {
            return response()->json(['error' => false, 'message' => 'Priority/Priorities deleted successfully.', 'id' => $deletedIds, 'titles' => $deletedTitles]);
        }
    }
}
