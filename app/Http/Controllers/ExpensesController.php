<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Workspace;
use App\Models\ExpenseType;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Services\DeletionService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ExpensesController extends Controller
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
        $expenses = $this->workspace->expenses();
        if (!isAdminOrHasAllDataAccess()) {
            $expenses = $expenses->where(function ($query) {
                $query->where('expenses.created_by', isClient() ? 'c_' . $this->user->id : 'u_' . $this->user->id)
                    ->orWhere('expenses.user_id', $this->user->id);
            });
        }
        $expenses = $expenses->count();
        return view('expenses.list', ['expenses' => $expenses]);
    }
    public function expense_types(Request $request)
    {
        $expense_types = ExpenseType::forWorkspace($this->workspace->id);
        $expense_types = $expense_types->count();
        return view('expenses.expense_types', ['expense_types' => $expense_types]);
    }
    public function store(Request $request)
    {
        // Validate the request data
        $formFields = $request->validate([
            'title' => 'required|unique:expenses,title', // Validate the title
            'expense_type_id' => 'required',
            'user_id' => 'nullable',
            'amount' => [
                'required',
                function ($attribute, $value, $fail) {
                    $error = validate_currency_format($value, 'amount');
                    if ($error) {
                        $fail($error);
                    }
                }
            ],
            'expense_date' => 'required',
            'note' => 'nullable'
        ], [
            'expense_type_id.required' => 'The expense type field is required.'            
        ]);
        $expense_date = $request->input('expense_date');
        $formFields['expense_date'] = format_date($expense_date, false, app('php_date_format'), 'Y-m-d');
        $formFields['amount'] = str_replace(',', '', $request->input('amount'));
        $formFields['workspace_id'] = $this->workspace->id;
        $formFields['created_by'] = isClient() ? 'c_' . $this->user->id : 'u_' . $this->user->id;

        if ($exp = Expense::create($formFields)) {
            return response()->json(['error' => false, 'message' => 'Expense created successfully.', 'id' => $exp->id]);
        } else {
            return response()->json(['error' => true, 'message' => 'Expense couldn\'t created.']);
        }
    }

    public function store_expense_type(Request $request)
    {
        // Validate the request data
        $formFields = $request->validate([
            'title' => 'required|unique:expense_types,title', // Validate the type
            'description' => 'nullable'
        ]);
        $formFields['workspace_id'] = $this->workspace->id;

        if ($et = ExpenseType::create($formFields)) {
            return response()->json(['error' => false, 'message' => 'Expense type created successfully.', 'id' => $et->id, 'title' => $et->type, 'type' => 'expense_type']);
        } else {
            return response()->json(['error' => true, 'message' => 'Expense type couldn\'t created.']);
        }
    }

    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $type_ids = request('type_ids', []);
        $user_ids = request('user_ids', []);
        $exp_date_from = (request('date_from')) ? request('date_from') : "";
        $exp_date_to = (request('date_to')) ? request('date_to') : "";
        $where = ['expenses.workspace_id' => $this->workspace->id];

        $expenses = Expense::select(
            'expenses.*',
            DB::raw('CONCAT(users.first_name, " ", users.last_name) AS user_name'),
            'expense_types.title as expense_type'
        )
            ->leftJoin('users', 'expenses.user_id', '=', 'users.id')
            ->leftJoin('expense_types', 'expenses.expense_type_id', '=', 'expense_types.id');


        if (!isAdminOrHasAllDataAccess()) {
            $expenses = $expenses->where(function ($query) {
                $query->where('expenses.created_by', isClient() ? 'c_' . $this->user->id : 'u_' . $this->user->id)
                    ->orWhere('expenses.user_id', $this->user->id);
            });
        }
        if (!empty($type_ids)) {
            $expenses = $expenses->whereIn('expenses.expense_type_id', $type_ids);
        }
        if (!empty($user_ids)) {
            $expenses = $expenses->whereIn('expenses.user_id', $user_ids);
        }
        if ($exp_date_from && $exp_date_to) {
            $expenses = $expenses->whereBetween('expenses.expense_date', [$exp_date_from, $exp_date_to]);
        }
        if ($search) {
            $expenses = $expenses->where(function ($query) use ($search) {
                $query->where('expenses.title', 'like', '%' . $search . '%')
                    ->orWhere('amount', 'like', '%' . $search . '%')
                    ->orWhere('expenses.note', 'like', '%' . $search . '%')
                    ->orWhere('expenses.id', 'like', '%' . $search . '%');
            });
        }

        $expenses->where($where);
        $total = $expenses->count();

        $canCreate = checkPermission('create_expenses');
        $canEdit = checkPermission('edit_expenses');
        $canDelete = checkPermission('delete_expenses');

        $expenses = $expenses->orderBy($sort, $order)
            ->paginate(request("limit"))
            ->through(function ($expense) use ($canEdit, $canDelete, $canCreate) {
                $actions = '';

                if ($canEdit) {
                    $actions .= '<a href="javascript:void(0);" class="edit-expense" data-bs-toggle="modal" data-id="' . $expense->id . '" title="' . get_label('update', 'Update') . '" class="card-link"><i class="bx bx-edit mx-1"></i></a>';
                }

                if ($canDelete) {
                    $actions .= '<button title="' . get_label('delete', 'Delete') . '" type="button" class="btn delete" data-id="' . $expense->id . '" data-type="expenses">' .
                        '<i class="bx bx-trash text-danger mx-1"></i>' .
                        '</button>';
                }

                if ($canCreate) {
                    $actions .= '<a href="javascript:void(0);" class="duplicate" data-id="' . $expense->id . '" data-title="' . $expense->title . '" data-type="expenses" title="' . get_label('duplicate', 'Duplicate') . '">' .
                        '<i class="bx bx-copy text-warning mx-2"></i>' .
                        '</a>';
                }

                $actions = $actions ?: '-';



                return [
                    'id' => $expense->id,
                    'user_id' => $expense->user_id ?? '-',
                    'user' => formatUserHtml($expense->user),
                    'title' => $expense->title,
                    'expense_type_id' => $expense->expense_type_id,
                    'expense_type' => $expense->expense_type,
                    'amount' => format_currency($expense->amount),
                    'expense_date' => format_date($expense->expense_date),
                    'note' => $expense->note,
                    'created_by' => strpos($expense->created_by, 'u_') === 0 ? formatUserHtml(User::find(substr($expense->created_by, 2))) : formatClientHtml(Client::find(substr($expense->created_by, 2))),
                    'created_at' => format_date($expense->created_at, true),
                    'updated_at' => format_date($expense->updated_at, true),
                    'actions' => $actions
                ];
            });


        return response()->json([
            "rows" => $expenses->items(),
            "total" => $total,
        ]);
    }

    public function expense_types_list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $expense_types = ExpenseType::forWorkspace($this->workspace->id);
        if ($search) {
            $expense_types = $expense_types->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        $total = $expense_types->count();
        $canEdit = checkPermission('edit_expense_types');
        $canDelete = checkPermission('delete_expense_types');
        $expense_types = $expense_types->orderBy($sort, $order)
        ->paginate(request("limit"))
        ->through(function ($expense_type) use ($canEdit, $canDelete) {
            $actions = '';

            if ($canEdit) {
                $actions .= '<a href="javascript:void(0);" class="edit-expense-type" data-id="' . $expense_type->id . '" title="' . get_label('update', 'Update') . '">' .
                    '<i class="bx bx-edit mx-1"></i>' .
                    '</a>';
            }

            if ($canDelete) {
                $actions .= '<button title="' . get_label('delete', 'Delete') . '" type="button" class="btn delete" data-id="' . $expense_type->id . '" data-type="expense-type">' .
                    '<i class="bx bx-trash text-danger mx-1"></i>' .
                    '</button>';
            }

            $actions = $actions ?: '-';

            return [
                'id' => $expense_type->id,
                'title' => $expense_type->title . ($expense_type->id == 0 ? ' <span class="badge bg-success">' . get_label('default', 'Default') . '</span>' : ''),
                'description' => $expense_type->description,
                'created_at' => format_date($expense_type->created_at, true),
                'updated_at' => format_date($expense_type->updated_at, true),
                'actions' => $actions,
            ];
        });

        return response()->json([
            "rows" => $expense_types->items(),
            "total" => $total,
        ]);
    }

    public function get($id)
    {
        $exp = Expense::with(['user', 'expense_type'])->findOrFail($id);
        $exp->amount = format_currency($exp->amount, false, false);
        return response()->json(['exp' => $exp]);
    }

    public function get_expense_type($id)
    {
        $et = ExpenseType::findOrFail($id);
        return response()->json(['et' => $et]);
    }

    public function update(Request $request)
    {
        // Validate the request data
        $formFields = $request->validate([
            'id' => 'required',
            'title' => 'required|unique:expenses,title,' . $request->id,
            'expense_type_id' => 'required',
            'user_id' => 'nullable',
            'amount' => [
                'required',
                function ($attribute, $value, $fail) {
                    $error = validate_currency_format($value, 'amount');
                    if ($error) {
                        $fail($error);
                    }
                }
            ],
            'expense_date' => 'required',
            'note' => 'nullable'
        ], [
            'expense_type_id.required' => 'The expense type field is required.'
        ]);
        $expense_date = $request->input('expense_date');
        $formFields['expense_date'] = format_date($expense_date, false, app('php_date_format'), 'Y-m-d');
        $formFields['amount'] = str_replace(',', '', $request->input('amount'));
        $exp = Expense::findOrFail($request->id);

        if ($exp->update($formFields)) {
            return response()->json(['error' => false, 'message' => 'Expense updated successfully.', 'id' => $exp->id]);
        } else {
            return response()->json(['error' => true, 'message' => 'Expense couldn\'t updated.']);
        }
    }

    public function update_expense_type(Request $request)
    {
        $formFields = $request->validate([
            'id' => ['required'],
            'title' => 'required|unique:expense_types,title,' . $request->id,
            'description' => 'nullable',
        ]);
        $et = ExpenseType::findOrFail($request->id);

        if ($et->update($formFields)) {
            return response()->json(['error' => false, 'message' => 'Expense type updated successfully.', 'id' => $et->id, 'type' => 'expense_type']);
        } else {
            return response()->json(['error' => true, 'message' => 'Expense type couldn\'t updated.']);
        }
    }

    public function destroy($id)
    {
        $exp = Expense::findOrFail($id);
        DeletionService::delete(Expense::class, $id, 'Expense');
        return response()->json(['error' => false, 'message' => 'Expense deleted successfully.', 'id' => $id, 'title' => $exp->title]);
    }

    public function delete_expense_type($id)
    {
        $et = ExpenseType::findOrFail($id);
        $et->expenses()->update(['expense_type_id' => 0]);
        $response = DeletionService::delete(ExpenseType::class, $id, 'Expense type');
        $data = $response->getData();
        if ($data->error) {
            return response()->json(['error' => true, 'message' => $data->message]);
        } else {
            return response()->json(['error' => false, 'message' => 'Expense type deleted successfully.', 'id' => $id, 'title' => $et->title, 'type' => 'expense_type']);
        }
    }

    public function destroy_multiple(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ids' => 'required|array', // Ensure 'ids' is present and an array
            'ids.*' => 'integer|exists:expenses,id' // Ensure each ID in 'ids' is an integer and exists in the table
        ]);

        $ids = $validatedData['ids'];
        $deletedIds = [];
        $deletedTitles = [];
        // Perform deletion using validated IDs
        foreach ($ids as $id) {
            $exp = Expense::findOrFail($id);
            $deletedIds[] = $id;
            $deletedTitles[] = $exp->title;
            DeletionService::delete(Expense::class, $id, 'Expense');
        }

        return response()->json(['error' => false, 'message' => 'Expense(s) deleted successfully.', 'id' => $deletedIds, 'titles' => $deletedTitles, 'type' => 'expense']);
    }

    public function delete_multiple_expense_type(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ids' => 'required|array', // Ensure 'ids' is present and an array
            'ids.*' => 'integer|exists:expense_types,id' // Ensure each ID in 'ids' is an integer and exists in the table
        ]);

        $ids = $validatedData['ids'];
        $deletedIds = [];
        $deletedTitles = [];
        $defaultExpenseTypeIds = [];
        $nonDefaultIds = [];

        // Perform deletion using validated IDs
        foreach ($ids as $id) {
            $et = ExpenseType::findOrFail($id);
            if ($et) {
                if ($et->id == 0) { // Assuming 0 is the ID for default expense type
                    $defaultExpenseTypeIds[] = $id;
                } else {
                    $et->expenses()->update(['expense_type_id' => 0]);
                    $deletedIds[] = $id;
                    $deletedTitles[] = $et->title;
                    DeletionService::delete(ExpenseType::class, $id, 'Expense type');
                    $nonDefaultIds[] = $id;
                }
            }
        }

        if (count($defaultExpenseTypeIds) > 0) {
            if (count($ids) == 1) {
                return response()->json(['error' => true, 'message' => 'Default expense type cannot be deleted.']);
            } else {
                return response()->json(['error' => false, 'message' => 'Expense type(s) deleted successfully except default.', 'id' => $deletedIds, 'titles' => $deletedTitles, 'type' => 'expense_type']);
            }
        } else {
            return response()->json(['error' => false, 'message' => 'Expense type(s) deleted successfully.', 'id' => $deletedIds, 'titles' => $deletedTitles, 'type' => 'expense_type']);
        }
    }

    public function duplicate($id)
    {
        // Use the general duplicateRecord function
        $title = (request()->has('title') && !empty(trim(request()->title))) ? request()->title : '';
        $duplicated = duplicateRecord(Expense::class, $id, [], $title);
        if (!$duplicated) {
            return response()->json(['error' => true, 'message' => 'Expense duplication failed.']);
        }
        return response()->json(['error' => false, 'message' => 'Expense duplicated successfully.', 'id' => $id]);
    }
}
