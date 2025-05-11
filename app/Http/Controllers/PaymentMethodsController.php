<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Services\DeletionService;

class PaymentMethodsController extends Controller
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
        $payment_methods = PaymentMethod::forWorkspace($this->workspace->id);
        $payment_methods = $payment_methods->count();
        return view('payment_methods.list', ['payment_methods' => $payment_methods]);
    }
    public function store(Request $request)
    {
        // Validate the request data
        $formFields = $request->validate([
            'title' => 'required|unique:payment_methods,title', // Validate the title
        ]);
        $formFields['workspace_id'] = $this->workspace->id;

        if ($pm = PaymentMethod::create($formFields)) {
            return response()->json(['error' => false, 'message' => 'Payment method created successfully.', 'id' => $pm->id, 'type' => 'payment_method', 'pm' => $pm]);
        } else {
            return response()->json(['error' => true, 'message' => 'Payment method couldn\'t created.']);
        }
    }

    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $payment_methods = PaymentMethod::forWorkspace($this->workspace->id);
        if ($search) {
            $payment_methods = $payment_methods->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        $canEdit = checkPermission('edit_payment_methods');
        $canDelete = checkPermission('delete_payment_methods');

        $total = $payment_methods->count();
        $payment_methods = $payment_methods->orderBy($sort, $order)
            ->paginate(request("limit"))
            ->through(function ($payment_method) use ($canEdit, $canDelete) {
                $actions = '';

                if ($canEdit) {
                    $actions .= '<a href="javascript:void(0);" class="edit-pm" data-id="' . $payment_method->id . '" title="' . get_label('update', 'Update') . '" class="card-link">' .
                        '<i class="bx bx-edit mx-1"></i>' .
                        '</a>';
                }

                if ($canDelete) {
                    $actions .= '<button title="' . get_label('delete', 'Delete') . '" type="button" class="btn delete" data-id="' . $payment_method->id . '" data-type="payment-methods">' .
                        '<i class="bx bx-trash text-danger mx-1"></i>' .
                        '</button>';
                }

                $actions = $actions ?: '-';

                return [
                    'id' => $payment_method->id,
                    'title' => $payment_method->title . ($payment_method->id == 0 ? ' <span class="badge bg-success">' . get_label('default', 'Default') . '</span>' : ''),
                    'created_at' => format_date($payment_method->created_at, true),
                    'updated_at' => format_date($payment_method->updated_at, true),
                    'actions' => $actions,
                ];
            });

        return response()->json([
            "rows" => $payment_methods->items(),
            "total" => $total,
        ]);
    }

    public function get($id)
    {
        $pm = PaymentMethod::findOrFail($id);
        return response()->json(['pm' => $pm]);
    }

    public function update(Request $request)
    {
        $formFields = $request->validate([
            'id' => ['required'],
            'title' => 'required|unique:payment_methods,title,' . $request->id,
        ]);
        $pm = PaymentMethod::findOrFail($request->id);

        if ($pm->update($formFields)) {
            return response()->json(['error' => false, 'message' => 'Payment method updated successfully.', 'id' => $pm->id, 'type' => 'payment_method']);
        } else {
            return response()->json(['error' => true, 'message' => 'Payment method couldn\'t updated.']);
        }
    }

    public function destroy($id)
    {
        $pm = PaymentMethod::findOrFail($id);
        $pm->payslips()->update(['payment_method_id' => 0]);
        $pm->payments()->update(['payment_method_id' => 0]);
        $response = DeletionService::delete(PaymentMethod::class, $id, 'Payment method');
        $data = $response->getData();
        if ($data->error) {
            return response()->json(['error' => true, 'message' => $data->message]);
        } else {
            return response()->json(['error' => false, 'message' => 'Payment method deleted successfully.', 'id' => $id, 'title' => $pm->title, 'type' => 'payment_method']);
        }
    }

    public function destroy_multiple(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ids' => 'required|array', // Ensure 'ids' is present and an array
            'ids.*' => 'integer|exists:payment_methods,id' // Ensure each ID in 'ids' is an integer and exists in the table
        ]);

        $ids = $validatedData['ids'];
        $deletedPms = [];
        $deletedPmTitles = [];
        $defaultPaymentMethodIds = [];
        $nonDefaultIds = [];

        // Perform deletion using validated IDs
        foreach ($ids as $id) {
            $pm = PaymentMethod::findOrFail($id);
            if ($pm->id == 0) { // Assuming 0 is the ID for default payment method
                $defaultPaymentMethodIds[] = $id;
            } else {
                $pm->payslips()->update(['payment_method_id' => 0]);
                $pm->payments()->update(['payment_method_id' => 0]);
                $deletedPms[] = $id;
                $deletedPmTitles[] = $pm->title;
                DeletionService::delete(PaymentMethod::class, $id, 'Payment method');
                $nonDefaultIds[] = $id;
            }
        }

        if (count($defaultPaymentMethodIds) > 0) {
            if (count($ids) == 1) {
                return response()->json(['error' => true, 'message' => 'Default payment method cannot be deleted.']);
            } else {
                return response()->json(['error' => false, 'message' => 'Payment method(s) deleted successfully except default.', 'id' => $deletedPms, 'titles' => $deletedPmTitles, 'type' => 'payment_method']);
            }
        } else {
            return response()->json(['error' => false, 'message' => 'Payment method(s) deleted successfully.', 'id' => $deletedPms, 'titles' => $deletedPmTitles, 'type' => 'payment_method']);
        }
    }
}
