<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Workspace;
use App\Models\ContractType;
use Illuminate\Http\Request;
use App\Services\DeletionService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ContractsController extends Controller
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
        $contracts = isAdminOrHasAllDataAccess() ? $this->workspace->contracts() : $this->user->contracts();
        $contracts = $contracts->count();
        return view('contracts.list', ['contracts' => $contracts]);
    }

    public function store(Request $request)
    {
        if (isClient()) {
            $request->merge(['client_id' => $this->user->id]);
        }
        $formFields = $request->validate([
            'title' => ['required'],
            'value' => [
                'required',
                function ($attribute, $value, $fail) {
                    $error = validate_currency_format($value, 'value');
                    if ($error) {
                        $fail($error);
                    }
                }
            ],
            'start_date' => [
                'required',
                function ($attribute, $value, $fail) {
                    $endDate = request()->input('end_date');
                    $errors = validate_date_format_and_order($value, $endDate);

                    // Check and handle errors for start_date specifically
                    if (!empty($errors['start_date'])) {
                        foreach ($errors['start_date'] as $error) {
                            $fail($error);
                        }
                    }
                },
            ],
            'end_date' => [
                'required',
                function ($attribute, $value, $fail) {
                    $startDate = request()->input('start_date');
                    $errors = validate_date_format_and_order($startDate, $value);

                    // Check and handle errors for end_date specifically
                    if (!empty($errors['end_date'])) {
                        foreach ($errors['end_date'] as $error) {
                            $fail($error);
                        }
                    }
                },
            ],
            'client_id' => ['required'],
            'project_id' => ['required'],
            'contract_type_id' => ['required'],
            'description' => ['nullable']
        ], [
            'client_id.required' => 'The client field is required.',
            'project_id.required' => 'The project field is required.',
            'contract_type_id.required' => 'The contract type field is required.'
        ]);

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $formFields['start_date'] = format_date($start_date, false, app('php_date_format'), 'Y-m-d');
        $formFields['end_date'] = format_date($end_date, false, app('php_date_format'), 'Y-m-d');
        $formFields['value'] = str_replace(',', '', $request->input('value'));
        $formFields['workspace_id'] = $this->workspace->id;
        $formFields['created_by'] = isClient() ? 'c_' . $this->user->id : 'u_' . $this->user->id;
        if ($contract = Contract::create($formFields)) {
            return response()->json(['error' => false, 'message' => 'Contract created successfully.', 'id' => $contract->id]);
        } else {
            return response()->json(['error' => true, 'message' => 'Contract couldn\'t created.']);
        }
    }

    public function update(Request $request)
    {
        $formFields = $request->validate([
            'id' => 'required|exists:contracts,id',
            'title' => ['required'],
            'value' => [
                'required',
                function ($attribute, $value, $fail) {
                    $error = validate_currency_format($value, 'value');
                    if ($error) {
                        $fail($error);
                    }
                }
            ],
            'start_date' => [
                'required',
                function ($attribute, $value, $fail) {
                    $endDate = request()->input('end_date');
                    $errors = validate_date_format_and_order($value, $endDate);

                    // Check and handle errors for start_date specifically
                    if (!empty($errors['start_date'])) {
                        foreach ($errors['start_date'] as $error) {
                            $fail($error);
                        }
                    }
                },
            ],
            'end_date' => [
                'required',
                function ($attribute, $value, $fail) {
                    $startDate = request()->input('start_date');
                    $errors = validate_date_format_and_order($startDate, $value);

                    // Check and handle errors for end_date specifically
                    if (!empty($errors['end_date'])) {
                        foreach ($errors['end_date'] as $error) {
                            $fail($error);
                        }
                    }
                },
            ],
            'client_id' => ['required'],
            'project_id' => ['required'],
            'contract_type_id' => ['required'],
            'description' => ['nullable'],
            'signed_pdf' => ['nullable', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:10240']
        ], [
            'client_id.required' => 'The client field is required.',
            'project_id.required' => 'The project field is required.',
            'contract_type_id.required' => 'The contract type field is required.',
            'signed_pdf.mimes' => 'The file must be a PDF.',
            'signed_pdf.max' => 'The file size must be less than 10 MB.'
        ]);

        $contract = Contract::findOrFail($formFields['id']);
        if ($request->hasFile('signed_pdf')) {
            // Delete the old file if it exists
            if ($contract->signed_pdf && Storage::disk('public')->exists('contracts/' . $contract->signed_pdf)) {
                Storage::disk('public')->delete('contracts/' . $contract->signed_pdf);
            }
        
            $file = $request->file('signed_pdf');
            $filePath = $file->store('contracts/', 'public'); // Store file in public disk
        
            // Extract the file name from the stored path
            $fileName = basename($filePath);
        
            $formFields['signed_pdf'] = $fileName;
        }        
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $formFields['start_date'] = format_date($start_date, false, app('php_date_format'), 'Y-m-d');
        $formFields['end_date'] = format_date($end_date, false, app('php_date_format'), 'Y-m-d');
        $formFields['value'] = str_replace(',', '', $request->input('value'));
        if ($contract->update($formFields)) {
            return response()->json(['error' => false, 'message' => 'Contract updated successfully.', 'id' => $formFields['id']]);
        } else {
            return response()->json(['error' => true, 'message' => 'Contract couldn\'t updated.']);
        }
    }

    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $statuses = request('statuses', []);
        $type_ids = request('type_ids', []);
        $project_ids = request('project_ids', []);
        $client_ids = request('client_ids', []);
        $date_between_from = request('date_between_from') ?: "";
        $date_between_to = request('date_between_to') ?: "";
        $start_date_from = (request('start_date_from')) ? request('start_date_from') : "";
        $start_date_to = (request('start_date_to')) ? request('start_date_to') : "";
        $end_date_from = (request('end_date_from')) ? request('end_date_from') : "";
        $end_date_to = (request('end_date_to')) ? request('end_date_to') : "";
        $where = ['contracts.workspace_id' => $this->workspace->id];

        $contracts = Contract::select(
            'contracts.*',
            DB::raw('CONCAT(clients.first_name, " ", clients.last_name) AS client_name'),
            'contract_types.type as contract_type',
            'projects.title as project_title'
        )
            ->leftJoin('users', 'contracts.created_by', '=', 'users.id')
            ->leftJoin('clients', 'contracts.client_id', '=', 'clients.id')
            ->leftJoin('contract_types', 'contracts.contract_type_id', '=', 'contract_types.id')
            ->leftJoin('projects', 'contracts.project_id', '=', 'projects.id');


        if (!isAdminOrHasAllDataAccess()) {
            $contracts = $contracts->where(function ($query) {
                $query->where('contracts.created_by', isClient() ? 'c_' . $this->user->id : 'u_' . $this->user->id)
                    ->orWhere('contracts.client_id', $this->user->id);
            });
        }

        if (!empty($project_ids)) {
            $contracts = $contracts->whereIn('contracts.project_id', $project_ids);
        }
        if (!empty($type_ids)) {
            $contracts = $contracts->whereIn('contracts.contract_type_id', $type_ids);
        }
        if (!empty($client_ids)) {
            $contracts = $contracts->whereIn('contracts.client_id', $client_ids);
        }
        if (!empty($statuses)) {
            $contracts = $contracts->where(function ($query) use ($statuses) {
                foreach ($statuses as $status) {
                    if ($status === 'partially_signed') {
                        $query->orWhere(function ($subquery) {
                            $subquery->where(function ($inner) {
                                $inner->whereNotNull('promisor_sign')
                                    ->whereNull('promisee_sign');
                            })
                                ->orWhere(function ($inner) {
                                    $inner->whereNull('promisor_sign')
                                        ->whereNotNull('promisee_sign');
                                });
                        });
                    } elseif ($status === 'signed') {
                        $query->orWhere(function ($subquery) {
                            $subquery->whereNotNull('promisor_sign')
                                ->whereNotNull('promisee_sign');
                        });
                    } elseif ($status === 'not_signed') {
                        $query->orWhere(function ($subquery) {
                            $subquery->whereNull('promisor_sign')
                                ->whereNull('promisee_sign');
                        });
                    }
                }
            });
        }
        if ($date_between_from && $date_between_to) {
            $contracts = $contracts->where('contracts.start_date', '>=', $date_between_from)
                ->where('contracts.end_date', '<=', $date_between_to);
        }
        if ($start_date_from && $start_date_to) {
            $contracts = $contracts->whereBetween('contracts.start_date', [$start_date_from, $start_date_to]);
        }
        if ($end_date_from && $end_date_to) {
            $contracts  = $contracts->whereBetween('contracts.end_date', [$end_date_from, $end_date_to]);
        }
        if ($search) {
            $contracts = $contracts->where(function ($query) use ($search) {
                $query->where('contracts.title', 'like', '%' . $search . '%')
                    ->orWhere('value', 'like', '%' . $search . '%')
                    ->orWhere('contracts.description', 'like', '%' . $search . '%')
                    ->orWhere('contracts.id', 'like', '%' . $search . '%')
                    ->orWhere(DB::raw('CONCAT("' . get_label('contract_id_prefix', 'Contract ID prefix') . '", contracts.id)'), 'like', '%' . $search . '%');
            });
        }

        $contracts->where($where);
        $total = $contracts->count();

        $canCreate = checkPermission('create_contracts');
        $canEdit = checkPermission('edit_contracts');
        $canDelete = checkPermission('delete_contracts');

        $contracts = $contracts->orderBy($sort, $order)
            ->paginate(request("limit"))
            ->through(function ($contract) use ($canEdit, $canDelete, $canCreate) {
                // Format "from_date" and "to_date" with labels
                $formattedDates = format_date($contract->start_date, false) . ' ' . get_label('to', 'To') . ' ' . format_date($contract->end_date, false);

                $promisorSign = $contract->promisor_sign;
                $promiseeSign = $contract->promisee_sign;

                $statusBadge = '';

                $promisor_sign_status = !is_null($promisorSign) ? '<span class="badge bg-success">' . get_label('signed', 'Signed') . '</span>' : '<span class="badge bg-danger">' . get_label('not_signed', 'Not signed') . '</span>';
                $promisee_sign_status = !is_null($promiseeSign) ? '<span class="badge bg-success">' . get_label('signed', 'Signed') . '</span>' : '<span class="badge bg-danger">' . get_label('not_signed', 'Not signed') . '</span>';

                if (!is_null($promisorSign) && !is_null($promiseeSign)) {
                    $statusBadge = '<span class="badge bg-success">' . get_label('signed', 'Signed') . '</span>';
                } elseif (!is_null($promisorSign) || !is_null($promiseeSign)) {
                    $statusBadge = '<span class="badge bg-warning">' . get_label('partially_signed', 'Partially signed') . '</span>';
                } else {
                    $statusBadge = '<span class="badge bg-danger">' . get_label('not_signed', 'Not signed') . '</span>';
                }

                $actions = '';
                if ($canEdit) {
                    $actions .= '<a href="javascript:void(0);" class="edit-contract" data-bs-toggle="modal" data-bs-target="#edit_contract_modal" data-id="' . $contract->id . '" title="' . get_label('update', 'Update') . '"><i class="bx bx-edit mx-1"></i></a>';
                }
                if ($canDelete) {
                    $actions .= '<button title=' . get_label('delete', 'Delete') . ' type="button" class="btn delete" data-id="' . $contract->id . '" data-type="contracts" data-table="contracts_table"><i class="bx bx-trash text-danger"></i></button>';
                }
                if ($canCreate) {
                    $actions .= '<a href="javascript:void(0);" class="duplicate" data-id="' . $contract->id . '" data-title="' . $contract->title . '" data-type="contracts" data-table="contracts_table" title=' . get_label('duplicate', 'Duplicate') . '><i class="bx bx-copy text-warning mx-2"></i></a>';
                }
                // Check if signed PDF exists
                if (isset($contract->signed_pdf) && !empty($contract->signed_pdf) && Storage::disk('public')->exists('contracts/' . $contract->signed_pdf)) {
                    $actions .= '<a href="' . Storage::url('contracts/' . $contract->signed_pdf) . '" title="' . get_label('contract_pdf', 'Contract PDF') . '" target="_blank"><i class="bx bx-file text-success ms-4"></i></a>';
                }
                $actions = $actions ?: '-';
                return [
                    'id' => $contract->id,
                    'title' => $contract->title,
                    'value' => format_currency($contract->value),
                    'start_date' => format_date($contract->start_date),
                    'end_date' => format_date($contract->end_date),
                    'duration' => $formattedDates,
                    'client' => formatClientHtml($contract->client),
                    'project' => "<a href='" . route('projects.info', ['id' => $contract->project_id]) . "'>{$contract->project_title}</a>",
                    'contract_type' => $contract->contract_type,
                    'description' => $contract->description,
                    'promisor_sign' => $promisor_sign_status,
                    'promisee_sign' => $promisee_sign_status,
                    'status' => $statusBadge,
                    'created_by' => strpos($contract->created_by, 'u_') === 0 ? formatUserHtml(User::find(substr($contract->created_by, 2))) : formatClientHtml(Client::find(substr($contract->created_by, 2))),
                    'created_at' => format_date($contract->created_at, true),
                    'updated_at' => format_date($contract->updated_at, true),
                    'actions' => $actions
                ];
            });


        return response()->json([
            "rows" => $contracts->items(),
            "total" => $total,
        ]);
    }

    public function get($id)
    {
        $contract = Contract::with(['client', 'project', 'contract_type'])->findOrFail($id);
        $contract->value = format_currency($contract->value, false, false);
        return response()->json(['error' => false, 'contract' => $contract]);
    }

    public function duplicate($id)
    {
        // Use the general duplicateRecord function
        $title = (request()->has('title') && !empty(trim(request()->title))) ? request()->title : '';
        $duplicate = duplicateRecord(Contract::class, $id, [], $title);

        if (!$duplicate) {
            return response()->json(['error' => true, 'message' => 'Contract duplication failed.']);
        }
        return response()->json(['error' => false, 'id' => $id, 'message' => 'Contract duplicated successfully.']);
    }

    public function sign(Request $request, $id)
    {
        $contract = Contract::select(
            'contracts.*',
            'clients.id as client_id',
            'contracts.created_by as created_by_id',
            DB::raw('CONCAT(clients.first_name, " ", clients.last_name) AS client_name'),
            'contract_types.type as contract_type',
            'projects.title as project_title',
            'projects.id as project_id'
        )->where('contracts.id', '=', $id)
            ->leftJoin('users', 'contracts.created_by', '=', 'users.id')
            ->leftJoin('clients', 'contracts.client_id', '=', 'clients.id')
            ->leftJoin('contract_types', 'contracts.contract_type_id', '=', 'contract_types.id')
            ->leftJoin('projects', 'contracts.project_id', '=', 'projects.id')->first();

        if (strpos($contract->created_by, 'u_') === 0) {
            // The ID corresponds to a user
            $creator = User::find(substr($contract->created_by, 2)); // Remove the 'u_' prefix
            if ($creator !== null) {
                if (checkPermission('manage_users')) {
                    $contract->creator = '<a href="' . route('users.profile', ['id' => $creator->id]) . '">' . $creator->first_name . ' ' . $creator->last_name . '</a>';
                } else {
                    $contract->creator = $creator->first_name . ' ' . $creator->last_name;
                }
            }
        } elseif (strpos($contract->created_by, 'c_') === 0) {
            // The ID corresponds to a client
            $creator = Client::find(substr($contract->created_by, 2)); // Remove the 'c_' prefix
            if ($creator !== null) {
                if (checkPermission('manage_clients')) {
                    $contract->creator = '<a href="' . url('/clients/profile/' . $creator->id) . '">' . $creator->first_name . ' ' . $creator->last_name . '</a>';
                } else {
                    $contract->creator = $creator->first_name . ' ' . $creator->last_name;
                }
            }
        }

        if (!isset($contract->creator)) {
            $contract->creator = '-';
        }

        return view('contracts.sign', compact('contract'));
    }

    public function create_sign(Request $request)
    {
        $formFields = $request->validate([
            'id' => 'required',
            'signatureImage' => 'required'
        ]);
        $contract = Contract::findOrFail($formFields['id']);
        $base64Data = $request->input('signatureImage');
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Data));
        // $imageData = base64_decode($base64Data);
        $filename = 'signature_' . uniqid() . '.png';
        Storage::put('public/signatures/' . $filename, $imageData);
        if (($this->user->id == $contract->created_by || isAdminOrHasAllDataAccess()) && !isClient()) {
            $contract->promisor_sign = $filename;
        } elseif (($this->user->id == $contract->client_id) && isClient()) {
            $contract->promisee_sign = $filename;
        }
        if ($contract->save()) {
            Session::flash('message', 'Signature created successfully.');
            return response()->json(['error' => false, 'id' => $formFields['id'], 'activity_message' => trim($this->user->first_name) . ' ' . trim($this->user->last_name) . ' signed contract ' . trim($contract->title)]);
        } else {
            return response()->json(['error' => true, 'message' => 'Signature couldn\'t created.']);
        }
    }

    public function delete_sign($id)
    {
        $contract = Contract::findOrFail($id);
        if (($this->user->id == $contract->created_by || isAdminOrHasAllDataAccess()) && !isClient()) {
            Storage::delete('public/signatures/' . $contract->promisor_sign);
            Contract::where('id', $id)->update(['promisor_sign' => null]);
            Session::flash('message', 'Signature deleted successfully.');
            return response()->json(['error' => false, 'id' => $id, 'activity_message' => trim($this->user->first_name) . ' ' . trim($this->user->last_name) . ' unsigned contract ' . trim($contract->title)]);
        } elseif ($this->user->id == $contract->client_id && isClient()) {
            Storage::delete('public/signatures/' . $contract->promisee_sign);
            Contract::where('id', $id)->update(['promisee_sign' => null]);
            Session::flash('message', 'Signature deleted successfully.');
            return response()->json(['error' => false, 'id' => $id, 'activity_message' => trim($this->user->first_name) . ' ' . trim($this->user->last_name) . ' unsigned contract ' . trim($contract->title)]);
        } else {
            Session::flash('error', 'Un authorized access.');
            return response()->json(['error' => true]);
        }
    }

    public function destroy($id)
    {
        $contract = Contract::findOrFail($id);
        if ($response = DeletionService::delete(Contract::class, $id, 'Contract')) {
            Storage::delete('public/signatures/' . $contract->promisor_sign);
            Storage::delete('public/signatures/' . $contract->promisee_sign);
            // Check if the contract has a signed PDF and delete it
            if ($contract->signed_pdf && Storage::disk('public')->exists('contracts/' . $contract->signed_pdf)) {
                Storage::disk('public')->delete('contracts/' . $contract->signed_pdf);
            }
        }
        return $response;
    }

    public function destroy_multiple(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ids' => 'required|array', // Ensure 'ids' is present and an array
            'ids.*' => 'integer|exists:contracts,id' // Ensure each ID in 'ids' is an integer and exists in the table
        ]);

        $ids = $validatedData['ids'];
        $deletedContracts = [];
        $deletedContractTitles = [];
        // Perform deletion using validated IDs

        foreach ($ids as $id) {
            $contract = Contract::findOrFail($id);
            if ($contract) {
                $deletedContracts[] = $id;
                $deletedContractTitles[] = $contract->title;
                if (DeletionService::delete(Contract::class, $id, 'Contract')) {
                    Storage::delete('public/signatures/' . $contract->promisor_sign);
                    Storage::delete('public/signatures/' . $contract->promisee_sign);
                    // Check and delete signed PDF
                    if ($contract->signed_pdf && Storage::disk('public')->exists('contracts/' . $contract->signed_pdf)) {
                        Storage::disk('public')->delete('contracts/' . $contract->signed_pdf);
                    }
                }
            }
        }
        return response()->json(['error' => false, 'message' => 'Contract(s) deleted successfully.', 'id' => $deletedContracts, 'titles' => $deletedContractTitles]);
    }

    public function contract_types(Request $request)
    {
        $contract_types = ContractType::forWorkspace($this->workspace->id);
        $contract_types = $contract_types->count();
        return view('contracts.contract_types', ['contract_types' => $contract_types]);
    }

    public function store_contract_type(Request $request)
    {
        // Validate the request data
        $formFields = $request->validate([
            'type' => 'required|unique:contract_types,type', // Validate the type
        ]);
        $formFields['workspace_id'] = $this->workspace->id;

        if ($ct = ContractType::create($formFields)) {
            return response()->json(['error' => false, 'message' => 'Contract type created successfully.', 'id' => $ct->id, 'title' => $ct->type, 'type' => 'contract_type', 'ct' => $ct]);
        } else {
            return response()->json(['error' => true, 'message' => 'Contract type couldn\'t created.']);
        }
    }

    public function contract_types_list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $contract_types = ContractType::forWorkspace($this->workspace->id);
        if ($search) {
            $contract_types = $contract_types->where(function ($query) use ($search) {
                $query->where('type', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }

        $total = $contract_types->count();
        $canEdit = checkPermission('edit_contract_types');
        $canDelete = checkPermission('delete_contract_types');
        $contract_types = $contract_types->orderBy($sort, $order)
            ->paginate(request("limit"))
            ->through(function ($contract_type) use ($canEdit, $canDelete) {
                $actions = '';

                if ($canEdit) {
                    $actions .= '<a href="javascript:void(0);" class="edit-contract-type" data-bs-target="#edit_contract_type_modal" data-id="' . $contract_type->id . '" title="' . get_label('update', 'Update') . '">' .
                        '<i class="bx bx-edit mx-1"></i>' .
                        '</a>';
                }

                if ($canDelete) {
                    $actions .= '<button title="' . get_label('delete', 'Delete') . '" type="button" class="btn delete" data-id="' . $contract_type->id . '" data-type="contract-type">' .
                        '<i class="bx bx-trash text-danger mx-1"></i>' .
                        '</button>';
                }

                $actions = $actions ?: '-';

                return [
                    'id' => $contract_type->id,
                    'type' => $contract_type->type . ($contract_type->id == 0 ? ' <span class="badge bg-success">' . get_label('default', 'Default') . '</span>' : ''),
                    'created_at' => format_date($contract_type->created_at, true),
                    'updated_at' => format_date($contract_type->updated_at, true),
                    'actions' => $actions,
                ];
            });

        return response()->json([
            "rows" => $contract_types->items(),
            "total" => $total,
        ]);
    }

    public function get_contract_type($id)
    {
        $ct = ContractType::findOrFail($id);
        return response()->json(['ct' => $ct]);
    }

    public function update_contract_type(Request $request)
    {
        $formFields = $request->validate([
            'id' => ['required'],
            'type' => 'required|unique:contract_types,type,' . $request->id,
        ]);
        $ct = ContractType::findOrFail($request->id);
        if ($ct->update($formFields)) {
            return response()->json(['error' => false, 'message' => 'Contract type updated successfully.', 'id' => $ct->id, 'title' => $formFields['type'], 'type' => 'contract_type']);
        } else {
            return response()->json(['error' => true, 'message' => 'Contract type couldn\'t updated.']);
        }
    }

    public function delete_contract_type($id)
    {
        $ct = ContractType::findOrFail($id);
        $ct->contracts()->update(['contract_type_id' => 0]);
        $response = DeletionService::delete(ContractType::class, $id, 'Contract type');
        $data = $response->getData();
        if ($data->error) {
            return response()->json(['error' => true, 'message' => $data->message]);
        } else {
            return response()->json(['error' => false, 'message' => 'Contract type deleted successfully.', 'id' => $id, 'title' => $ct->type, 'type' => 'contract_type']);
        }
    }

    public function delete_multiple_contract_type(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ids' => 'required|array', // Ensure 'ids' is present and an array
            'ids.*' => 'integer|exists:contract_types,id' // Ensure each ID in 'ids' is an integer and exists in the table
        ]);

        $ids = $validatedData['ids'];
        $deletedContractTypes = [];
        $deletedContractTypeTitles = [];
        $defaultContractTypeIds = [];
        $nonDefaultIds = [];

        // Perform deletion using validated IDs
        foreach ($ids as $id) {
            $ct = ContractType::findOrFail($id);
            if ($ct) {
                if ($ct->id == 0) { // Assuming 0 is the ID for default contract type
                    $defaultContractTypeIds[] = $id;
                } else {
                    $ct->contracts()->update(['contract_type_id' => 0]);
                    $deletedContractTypes[] = $id;
                    $deletedContractTypeTitles[] = $ct->type;
                    DeletionService::delete(ContractType::class, $id, 'Contract type');
                    $nonDefaultIds[] = $id;
                }
            }
        }

        if (count($defaultContractTypeIds) > 0) {
            if (count($ids) == 1) {
                return response()->json(['error' => true, 'message' => 'Default contract type cannot be deleted.']);
            } else {
                return response()->json(['error' => false, 'message' => 'Contract type(s) deleted successfully except default.', 'id' => $deletedContractTypes, 'titles' => $deletedContractTypeTitles, 'type' => 'contract_type']);
            }
        } else {
            return response()->json(['error' => false, 'message' => 'Contract type(s) deleted successfully.', 'id' => $deletedContractTypes, 'titles' => $deletedContractTypeTitles, 'type' => 'contract_type']);
        }
    }
}
