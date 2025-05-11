<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Deduction;
use Illuminate\Support\Facades\Session;
use App\Services\DeletionService;

class DeductionsController extends Controller
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
        $deductions = $this->workspace->deductions();
        $deductions = $deductions->count();
        return view('deductions.list', ['deductions' => $deductions]);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'required|unique:deductions,title',
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
        $validatedData['workspace_id'] = $this->workspace->id;
        if ($deduction = Deduction::create($validatedData)) {
            return response()->json(['error' => false, 'message' => 'Deduction created successfully.', 'id' => $deduction->id, 'deduction' => $deduction]);
        } else {
            return response()->json(['error' => true, 'message' => 'Deduction couldn\'t created.']);
        }
    }

    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $types = request('types');
        $deductions = $this->workspace->deductions();
        if ($search) {
            $deductions = $deductions->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('amount', 'like', '%' . $search . '%')
                    ->orWhere('percentage', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        if (!empty($types)) {
            $deductions = $deductions->whereIn('type', $types);
        }
        $canEdit = checkPermission('edit_deductions');
        $canDelete = checkPermission('delete_deductions');

        $total = $deductions->count();
        $deductions = $deductions->orderBy($sort, $order)
            ->paginate(request("limit"))
            ->through(function ($deduction) use ($canEdit, $canDelete) {
                $actions = '';

                if ($canEdit) {
                    $actions .= '<a href="javascript:void(0);" class="edit-deduction" data-id="' . $deduction->id . '" title="' . get_label('update', 'Update') . '" class="card-link">' .
                        '<i class="bx bx-edit mx-1"></i>' .
                        '</a>';
                }

                if ($canDelete) {
                    $actions .= '<button title="' . get_label('delete', 'Delete') . '" type="button" class="btn delete" data-id="' . $deduction->id . '" data-type="deductions">' .
                        '<i class="bx bx-trash text-danger mx-1"></i>' .
                        '</button>';
                }

                $actions = $actions ?: '-';

                return [
                    'id' => $deduction->id,
                    'title' => $deduction->title,
                    'type' => ucfirst($deduction->type),
                    'percentage' => $deduction->percentage,
                    'amount' => format_currency($deduction->amount),
                    'created_at' => format_date($deduction->created_at, true),
                    'updated_at' => format_date($deduction->updated_at, true),
                    'actions' => $actions,
                ];
            });

        return response()->json([
            "rows" => $deductions->items(),
            "total" => $total,
        ]);
    }



    public function get($id)
    {
        $deduction = Deduction::findOrFail($id);
        $deduction->amount = format_currency($deduction->amount, false, false);
        return response()->json(['deduction' => $deduction]);
    }

    public function update(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'required|unique:deductions,title,' . $request->id,
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

        // Ensure deduction exists
        $deduction = Deduction::findOrFail($request->id);

        // Update deduction
        if ($deduction->update($validatedData)) {
            return response()->json([
                'error' => false,
                'message' => 'Deduction updated successfully.',
                'id' => $deduction->id,
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Deduction couldn\'t be updated.',
            ]);
        }
    }


    public function destroy($id)
    {
        $deduction = Deduction::findOrFail($id);
        $deduction->payslips()->detach();
        $response = DeletionService::delete(Deduction::class, $id, 'Deduction');
        return $response;
    }
    public function destroy_multiple(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ids' => 'required|array', // Ensure 'ids' is present and an array
            'ids.*' => 'integer|exists:deductions,id' // Ensure each ID in 'ids' is an integer and exists in the table
        ]);

        $ids = $validatedData['ids'];
        $deletedIds = [];
        $deletedTitles = [];
        // Perform deletion using validated IDs
        foreach ($ids as $id) {
            $deduction = Deduction::findOrFail($id);
            $deletedIds[] = $id;
            $deletedTitles[] = $deduction->title;
            $deduction->payslips()->detach();
            DeletionService::delete(Deduction::class, $id, 'Deduction');
        }

        return response()->json(['error' => false, 'message' => 'Deduction(s) deleted successfully.', 'id' => $deletedIds, 'titles' => $deletedTitles]);
    }
}
