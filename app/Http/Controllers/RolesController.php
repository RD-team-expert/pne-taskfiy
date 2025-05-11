<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\DeletionService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Illuminate\Database\QueryException;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        return view('settings.permission_settings', ['roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $projects = Permission::where('name', 'like', '%projects%')->get()->sortBy('name');
        $tasks = Permission::where('name', 'like', '%tasks%')->get()->sortBy('name');
        $users = Permission::where('name', 'like', '%users%')->get()->sortBy('name');
        $clients = Permission::where('name', 'like', '%clients%')->get()->sortBy('name');
        return view('roles.create_role', ['projects' => $projects, 'tasks' => $tasks, 'users' => $users, 'clients' => $clients]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $formFields = $request->validate([
                'name' => ['required']
            ]);

            $formFields['guard_name'] = 'web';



            $role = Role::create($formFields);
            $filteredPermissions = array_filter($request->input('permissions'), function ($permission) {
                return $permission != 0;
            });
            $role->permissions()->sync($filteredPermissions);
            Artisan::call('cache:clear');

            Session::flash('message', 'Role created successfully.');
            return response()->json(['error' => false]);
        } catch (RoleAlreadyExists $e) {
            // Handle the exception
            return response()->json(['error' => true, 'message' => 'A role `' . $formFields['name'] . '` already exists.']);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['error' => true, 'message' => 'An error occurred while creating the role.']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $role = Role::findOrFail($id);
        $role_permissions = $role->permissions;
        $guard = $role->guard_name == 'client' ? 'client' : 'web';
        return view('roles.edit_role', ['role' => $role, 'role_permissions' => $role_permissions, 'guard' => $guard, 'user' => getAuthenticatedUser()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $formFields = $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);

        try {
            $role = Role::findOrFail($id);
            $role->name = $formFields['name'];
            $role->save();

            $filteredPermissions = array_filter($request->input('permissions'), function ($permission) {
                return $permission != 0;
            });
            $role->permissions()->sync($filteredPermissions);

            Artisan::call('cache:clear');

            Session::flash('message', 'Role updated successfully.');
            return response()->json(['error' => false]);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                // Unique constraint violation
                return response()->json(['error' => true, 'message' => 'A role `' . $formFields['name'] . '` already exists.']);
            }
            return response()->json(['error' => true, 'message' => 'An error occurred while updating the role.']);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => 'An error occurred while updating the role.']);
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
        $response = DeletionService::delete(Role::class, $id, 'Role');
        return $response;
    }

    public function create_permission()
    {
        // $createProjectsPermission = Permission::findOrCreate('create_tasks', 'client');
        Permission::create(['name' => 'edit_projects', 'guard_name' => 'client']);
    }

    /**
     * List or search roles.
     *
     * This endpoint retrieves a list of roles based on various filters. The request allows filtering by search term and pagination parameters.
     *
     * @group Role/Permission Management
     *
     * @urlParam id int optional The ID of the role to retrieve. Example: 1
     *
     * @queryParam search string optional The search term to filter roles by id, name or guard_name. Example: Admin
     * @queryParam sort string optional The field to sort by. all fields are sortable. Defaults to "created_at". Example: name
     * @queryParam order string optional The sort order, either "asc" or "desc". Defaults to "desc". Example: asc
     * @queryParam limit int optional The number of roles per page for pagination. Defaults to 10. Example: 10
     * @queryParam offset int optional The offset for pagination, indicating the starting point of results. Defaults to 0. Example: 0
     *
     * @response 200 {
     *     "error": false,
     *     "message": "Roles retrieved successfully.",
     *     "total": 1,
     *     "data": [
     *         {
     *             "id": 1,
     *             "name": "Admin",
     *             "guard_name": "web",
     *             "created_at": "10-10-2023 17:50:09",
     *             "updated_at": "23-07-2024 19:08:16"
     *         }
     *     ]
     * }
     *
     * @response 200 {
     *     "error": true,
     *     "message": "Role not found.",
     *     "total": 0,
     *     "data": []
     * }
     *
     * @response 200 {
     *   "error": true,
     *   "message": "Roles not found",
     *   "total": 0,
     *   "data": []
     * }
     * 
     * @response 500 {
     *     "error": true,
     *     "message": "An error occurred while retrieving the roles."
     * }
     */
    public function apiList(Request $request, $id = null)
    {
        try {
            if ($id) {
                $role = Role::find($id, ['id', 'name', 'guard_name', 'created_at', 'updated_at']);

                if (!$role) {
                    return formatApiResponse(
                        false,
                        'Role not found.',
                        [
                            'total' => 0,
                            'data' => []
                        ]
                    );
                }

                return formatApiResponse(
                    false,
                    'Role retrieved successfully.',
                    [
                        'total' => 1,
                        'data' => formatNote($role)
                    ]
                );
            }

            // Extract query parameters
            $search = $request->input('search', '');
            $sort = $request->input('sort', 'created_at');
            $order = $request->input('order', 'desc');
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            // Build the query
            $query = Role::when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                        ->orWhere('guard_name', 'LIKE', "%$search%")
                        ->orWhere('id', 'LIKE', "%$search%");
                });
            })->where('guard_name', 'web');

            // Apply sorting
            $query->orderBy($sort, $order);

            // Get the total count before applying limit and offset
            $total = $query->count();

            // Apply limit and offset
            $roles = $query->limit($limit)
                ->offset($offset)
                ->get(['id', 'name', 'guard_name', 'created_at', 'updated_at']);

            if ($roles->isEmpty()) {
                return formatApiResponse(
                    true,
                    'Roles not found',
                    [
                        'total' => 0,
                        'data' => []
                    ]
                );
            }

            $formattedRoles = $roles->map(function ($role) {
                return formatRole($role);
            });

            return formatApiResponse(
                false,
                'Roles retrieved successfully.',
                [
                    'total' => $total,
                    'data' => $formattedRoles
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while retrieving the roles.'
            ], 500);
        }
    }

    /**
     * Check user permissions.
     *
     * This endpoint checks the module-wise permissions assigned to the authenticated user.
     * If a specific permission is provided in the URL, it checks only that permission for the authenticated user.
     * Otherwise, it returns all permissions for the authenticated user.
     *
     * @authenticated
     * 
     * @urlParam permission string optional The specific permission to check. Example: "edit-post"
     * 
     * Here is the module-wise permissions list.
     * 
     * Activity Log:
     * - manage_activity_log
     * - delete_activity_log
     
     * Allowances:
     * - create_allowances
     * - manage_allowances
     * - edit_allowances
     * - delete_allowances
        
     * Clients:
     * - create_clients
     * - manage_clients
     * - edit_clients
     * - delete_clients
        
     * Contract Types:
     * - create_contract_types
     * - manage_contract_types
     * - edit_contract_types
     * - delete_contract_types
        
     * Contracts:
     * - create_contracts
     * - manage_contracts
     * - edit_contracts
     * - delete_contracts
        
     * Deductions:
     * - create_deductions
     * - manage_deductions
     * - edit_deductions
     * - delete_deductions
        
     * Estimates/Invoices:
     * - create_estimates_invoices
     * - manage_estimates_invoices
     * - edit_estimates_invoices
     * - delete_estimates_invoices
        
     * Expense Types:
     * - create_expense_types
     * - manage_expense_types
     * - edit_expense_types
     * - delete_expense_types
        
     * Expenses:
     * - create_expenses
     * - manage_expenses
     * - edit_expenses
     * - delete_expenses
        
     * Items:
     * - create_items
     * - manage_items
     * - edit_items
     * - delete_items
        
     * Media:
     * - create_media
     * - manage_media
     * - delete_media
        
     * Meetings:
     * - create_meetings
     * - manage_meetings
     * - edit_meetings
     * - delete_meetings
        
     * Milestones:
     * - create_milestones
     * - manage_milestones
     * - edit_milestones
     * - delete_milestones
        
     * Payment Methods:
     * - create_payment_methods
     * - manage_payment_methods
     * - edit_payment_methods
     * - delete_payment_methods
        
     * Payments:
     * - create_payments
     * - manage_payments
     * - edit_payments
     * - delete_payments
        
     * Payslips:
     * - create_payslips
     * - manage_payslips
     * - edit_payslips
     * - delete_payslips
        
     * Priorities:
     * - create_priorities
     * - manage_priorities
     * - edit_priorities
     * - delete_priorities
        
     * Projects:
     * - create_projects
     * - manage_projects
     * - edit_projects
     * - delete_projects
        
     * Statuses:
     * - create_statuses
     * - manage_statuses
     * - edit_statuses
     * - delete_statuses
        
     * System Notifications:
     * - manage_system_notifications
     * - delete_system_notifications
        
     * Tags:
     * - create_tags
     * - manage_tags
     * - edit_tags
     * - delete_tags
        
     * Tasks:
     * - create_tasks
     * - manage_tasks
     * - edit_tasks
     * - delete_tasks
        
     * Taxes:
     * - create_taxes
     * - manage_taxes
     * - edit_taxes
     * - delete_taxes
        
     * Timesheet:
     * - create_timesheet
     * - manage_timesheet
     * - delete_timesheet
        
     * Units:
     * - create_units
     * - manage_units
     * - edit_units
     * - delete_units
        
     * Users:
     * - create_users
     * - manage_users
     * - edit_users
     * - delete_users
        
     * Workspaces:
     * - create_workspaces
     * - manage_workspaces
     * - edit_workspaces
     * - delete_workspaces
     * 
     * @authenticated
     *
     * @group Role/Permission Management     
     *
     * @response 200 {
     *     "error": false,
     *     "message": "Permissions check completed.",
     *     "data": {
     *         "permissions": {
     *             "create_projects": true,
     *             "manage_projects": false,
     *             ...
     *         }
     *     }
     * }
     *
     * @response 500 {
     *     "error": true,
     *     "message": "An error occurred while checking the permission."
     * }
     *
     */

     public function checkPermissions($specificPermission = null)
     {
         try {
             $user = getAuthenticatedUser();
             $permissionResults = [];
     
             if ($specificPermission) {
                 // Check a specific permission if passed
                 $permissionResults['has_permission'] = $user ? $user->can($specificPermission) : false;
             } else {
                 // Check all permissions if no specific permission is passed
                 $permissions = getAllPermissions();
                 foreach ($permissions as $permission) {
                     $permissionResults[$permission] = $user ? $user->can($permission) : false;
                 }
             }
     
             return formatApiResponse(
                 false,
                 'Permission check completed.',
                 ['data' => ['permissions' => $permissionResults]]
             );
         } catch (\Exception $e) {
             return response()->json([
                 'error' => true,
                 'message' => 'An error occurred while checking the permissions.'
             ], 500);
         }
     }
     
}
