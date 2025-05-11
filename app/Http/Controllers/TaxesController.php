<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Tax;
use Illuminate\Support\Facades\Session;
use App\Services\DeletionService;
use Illuminate\Support\Facades\DB;

class TaxesController extends Controller
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
        $taxes = $this->workspace->taxes();
        $taxes = $taxes->count();
        return view('taxes.list', ['taxes' => $taxes]);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'required|unique:taxes,title',
            'type' => [
                'required',
                Rule::in(['amount', 'percentage']),
            ],
            'amount' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->type === 'amount';
                }),
                'nullable',
                function ($attribute, $value, $fail) {
                    $error = validate_currency_format($value, 'amount');
                    if ($error) {
                        $fail($error);
                    }
                }
            ],
            'percentage' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->type === 'percentage';
                }),
                'nullable',
                'numeric',
            ],
        ], [
            'percentage.numeric' => 'Percentage must be a numeric value.'
        ]);
        $validatedData['amount'] = str_replace(',', '', $request->input('amount'));
        // Set workspace_id
        $validatedData['workspace_id'] = $this->workspace->id;

        // Create Tax instance
        if ($tax = Tax::create($validatedData)) {
            return response()->json([
                'error' => false,
                'message' => 'Tax created successfully.',
                'id' => $tax->id,
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Tax couldn\'t be created.',
            ]);
        }
    }


    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $types = request('types');
        $taxes = $this->workspace->taxes();
        if ($search) {
            $taxes = $taxes->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('amount', 'like', '%' . $search . '%')
                    ->orWhere('percentage', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        if (!empty($types)) {
            $taxes = $taxes->whereIn('type', $types);
        }
        $canEdit = checkPermission('edit_taxes');
        $canDelete = checkPermission('delete_taxes');
        $total = $taxes->count();
        $taxes = $taxes->orderBy($sort, $order)
            ->paginate(request("limit"))
            ->through(function ($tax) use ($canEdit, $canDelete) {
                $actions = '';

                if ($canEdit) {
                    $actions .= '<a href="javascript:void(0);" class="edit-tax" data-id="' . $tax->id . '" title="' . get_label('update', 'Update') . '">' .
                        '<i class="bx bx-edit mx-1"></i>' .
                        '</a>';
                }

                if ($canDelete) {
                    $actions .= '<button title="' . get_label('delete', 'Delete') . '" type="button" class="btn delete" data-id="' . $tax->id . '" data-type="taxes">' .
                        '<i class="bx bx-trash text-danger mx-1"></i>' .
                        '</button>';
                }

                $actions = $actions ?: '-';

                return [
                    'id' => $tax->id,
                    'title' => $tax->title,
                    'type' => ucfirst($tax->type),
                    'percentage' => $tax->percentage,
                    'amount' => format_currency($tax->amount),
                    'created_at' => format_date($tax->created_at, true),
                    'updated_at' => format_date($tax->updated_at, 'H:i:s'),
                    'actions' => $actions,
                ];
            });

        return response()->json([
            "rows" => $taxes->items(),
            "total" => $total,
        ]);
    }



    public function get($id)
    {
        $tax = Tax::findOrFail($id);
        $tax->amount = format_currency($tax->amount, false, false);
        return response()->json(['tax' => $tax]);
    }

    public function update(Request $request)
    {
        // Validate the request data
        $formFields = $request->validate([
            'title' => 'required|unique:taxes,title,' . $request->id
        ]);

        $formFields['workspace_id'] = $this->workspace->id;

        $tax = Tax::findOrFail($request->id);

        if ($tax->update($formFields)) {
            return response()->json(['error' => false, 'message' => 'Tax updated successfully.', 'id' => $tax->id]);
        } else {
            return response()->json(['error' => true, 'message' => 'Tax couldn\'t updated.']);
        }
    }

    public function destroy($id)
    {
        $tax = Tax::findOrFail($id);
        DB::table('estimates_invoice_item')
            ->where('tax_id', $tax->id)
            ->update(['tax_id' => null]);
        $response = DeletionService::delete(Tax::class, $id, 'Tax');
        return $response;
    }
    public function destroy_multiple(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ids' => 'required|array', // Ensure 'ids' is present and an array
            'ids.*' => 'integer|exists:taxes,id' // Ensure each ID in 'ids' is an integer and exists in the table
        ]);

        $ids = $validatedData['ids'];
        $deletedIds = [];
        $deletedTitles = [];
        // Perform deletion using validated IDs
        foreach ($ids as $id) {
            $tax = Tax::findOrFail($id);
            DB::table('estimates_invoice_item')
                ->where('tax_id', $tax->id)
                ->update(['tax_id' => null]);
            $deletedIds[] = $id;
            $deletedTitles[] = $tax->title;
            DeletionService::delete(Tax::class, $id, 'Tax');
        }

        return response()->json(['error' => false, 'message' => 'Tax(es) deleted successfully.', 'id' => $deletedIds, 'titles' => $deletedTitles]);
    }
}
