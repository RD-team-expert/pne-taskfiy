<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Item;
use Illuminate\Support\Facades\Session;
use App\Services\DeletionService;

class ItemsController extends Controller
{
    protected $workspace;
    protected $user;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // fetch session and use it in entire class with constructor
            $this->workspace = Workspace::find(getWorkspaceId());
            $this->user = getAuthenticatedUser();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $items = $this->workspace->items();
        $items = $items->count();
        $units = $this->workspace->units;
        return view('items.list', ['items' => $items, 'units' => $units]);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $formFields = $request->validate([
            'title' => 'required|unique:items,title',
            'price' => [
                'required',
                function ($attribute, $value, $fail) {
                    $error = validate_currency_format($value, 'price');
                    if ($error) {
                        $fail($error);
                    }
                }
            ],
            'unit_id' => 'nullable',
            'description' => 'nullable',
        ], [
            'price.regex' => 'The price must be a valid number with or without decimals.'
        ]);
        $formFields['price'] = str_replace(',', '', $request->input('price'));
        $formFields['workspace_id'] = $this->workspace->id;

        if ($res = Item::create($formFields)) {
            return response()->json(['error' => false, 'message' => 'Item created successfully.', 'id' => $res->id, 'item' => $res]);
        } else {
            return response()->json(['error' => true, 'message' => 'Item couldn\'t created.']);
        }
    }

    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $unit_ids = request('unit_ids');
        $where = ['items.workspace_id' => $this->workspace->id];
        $items = Item::select(
            'items.*',
            'units.title as unit'
        )
            ->leftJoin('units', 'items.unit_id', '=', 'units.id');
        if ($search) {
            $items = $items->where(function ($query) use ($search) {
                $query->where('items.title', 'like', '%' . $search . '%')
                    ->orWhere('items.description', 'like', '%' . $search . '%')
                    ->orWhere('price', 'like', '%' . $search . '%')
                    ->orWhere('unit_id', 'like', '%' . $search . '%')
                    ->orWhere('items.id', 'like', '%' . $search . '%');
            });
        }
        if (!empty($unit_ids)) {
            $items = $items->whereIn('unit_id', $unit_ids);
        }
        $items->where($where);
        $canEdit = checkPermission('edit_items');
        $canDelete = checkPermission('delete_items');

        $total = $items->count();
        $items = $items->orderBy($sort, $order)
            ->paginate(request("limit"))
            ->through(function ($item) use ($canEdit, $canDelete) {
                $actions = '';

                if ($canEdit) {
                    $actions .= '<a href="javascript:void(0);" class="edit-item" data-id="' . $item->id . '" title="' . get_label('update', 'Update') . '">' .
                        '<i class="bx bx-edit mx-1"></i>' .
                        '</a>';
                }

                if ($canDelete) {
                    $actions .= '<button title="' . get_label('delete', 'Delete') . '" type="button" class="btn delete" data-id="' . $item->id . '" data-type="items">' .
                        '<i class="bx bx-trash text-danger mx-1"></i>' .
                        '</button>';
                }

                $actions = $actions ?: '-';

                return [
                    'id' => $item->id,
                    'unit_id' => $item->unit_id,
                    'unit' => $item->unit,
                    'title' => $item->title,
                    'price' => format_currency($item->price),
                    'description' => $item->description,
                    'created_at' => format_date($item->created_at, true),
                    'updated_at' => format_date($item->updated_at, 'H:i:s'),
                    'actions' => $actions,
                ];
            });

        return response()->json([
            "rows" => $items->items(),
            "total" => $total,
        ]);
    }



    public function get($id)
    {
        $item = Item::findOrFail($id);
        $item->price = format_currency($item->price, false, false);
        return response()->json(['item' => $item]);
    }

    public function update(Request $request)
    {
        // Validate the request data
        $formFields = $request->validate([
            'title' => 'required|unique:items,title,' . $request->id,
            'price' => [
                'required',
                function ($attribute, $value, $fail) {
                    $error = validate_currency_format($value, 'price');
                    if ($error) {
                        $fail($error);
                    }
                }
            ],
            'unit_id' => 'nullable',
            'description' => 'nullable',
        ]);
        $formFields['price'] = str_replace(',', '', $request->input('price'));
        $formFields['workspace_id'] = $this->workspace->id;

        $item = Item::findOrFail($request->id);

        if ($item->update($formFields)) {
            return response()->json(['error' => false, 'message' => 'Item updated successfully.', 'id' => $item->id]);
        } else {
            return response()->json(['error' => true, 'message' => 'Item couldn\'t updated.']);
        }
    }

    public function destroy($id)
    {
        $response = DeletionService::delete(Item::class, $id, 'Item');
        return $response;
    }
    public function destroy_multiple(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ids' => 'required|array', // Ensure 'ids' is present and an array
            'ids.*' => 'integer|exists:items,id' // Ensure each ID in 'ids' is an integer and exists in the table
        ]);

        $ids = $validatedData['ids'];
        $deletedIds = [];
        $deletedTitles = [];
        // Perform deletion using validated IDs
        foreach ($ids as $id) {
            $unit = Item::findOrFail($id);
            $deletedIds[] = $id;
            $deletedTitles[] = $unit->title;
            DeletionService::delete(Item::class, $id, 'Item');
        }

        return response()->json(['error' => false, 'message' => 'Item(s) deleted successfully.', 'id' => $deletedIds, 'titles' => $deletedTitles]);
    }
}
