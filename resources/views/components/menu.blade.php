<?php

use App\Models\User;
use App\Models\Workspace;
use App\Models\LeaveRequest;
use Chatify\ChatifyMessenger;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$user = getAuthenticatedUser();
if (isAdminOrHasAllDataAccess()) {
    $workspaces = Workspace::all()->take(5);
    $total_workspaces = Workspace::count();
} else {
    $workspaces = $user->workspaces;
    $total_workspaces = count($workspaces);
    $workspaces = $user->workspaces->skip(0)->take(5);
}
$current_workspace_id = getWorkspaceId();
$current_workspace = Workspace::find($current_workspace_id);
// Check if the current workspace is in the list of workspaces retrieved
$workspace_ids = $workspaces->pluck('id')->toArray();
if (!in_array($current_workspace_id, $workspace_ids)) {
    // If not, prepend the current workspace to the list
    $current_workspace = Workspace::find($current_workspace_id);
    $workspaces->prepend($current_workspace);
    // If there are more than 5 workspaces, remove the last one
    if ($workspaces->count() > 5) {
        $workspaces->pop();
    }
}
$current_workspace_title = $current_workspace->title ?? 'No workspace(s) found';
$messenger = new ChatifyMessenger();
$unread = $messenger->totalUnseenMessages();
$pending_todos_count = $user->todos(0)->count();
$ongoing_meetings_count = $user->meetings('ongoing')->count();
$query = LeaveRequest::where('status', 'pending')
    ->where('workspace_id', $current_workspace_id);
if (!is_admin_or_leave_editor()) {
    $query->where('user_id', $user->id);
}
$pendingLeaveRequestsCount = $query->count();
?>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme menu-container">
    <div class="app-brand demo">
        <a href="{{ url('home') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{asset($general_settings['full_logo'])}}" width="200px" alt="" />
            </span>
            <!-- <span class="app-brand-text demo menu-text fw-bolder ms-2">Taskify</span> -->
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>
    <div class="btn-group dropend px-2">
        <button type="button" class="btn btn-primary {{getAuthenticatedUser()->hasVerifiedEmail() || getAuthenticatedUser()->hasRole('admin')?'dropdown-toggle':''}}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ strlen($current_workspace_title) > 22 ? substr($current_workspace_title, 0, 22) . '...' : $current_workspace_title }}
        </button>
        @if(getAuthenticatedUser()->hasVerifiedEmail() || getAuthenticatedUser()->hasRole('admin'))
        <ul class="dropdown-menu">
            @if ($total_workspaces > 0)
            @foreach ($workspaces as $workspace)
            <?php $checked = $workspace->id == $current_workspace_id ? "<i class='menu-icon tf-icons bx bx-check-square text-primary'></i>" : "<i class='menu-icon tf-icons bx bx-square text-solid'></i>" ?>
            <li>
                <a class="dropdown-item" href="{{ url('/workspaces/switch/' . $workspace->id) }}">
                    {!! $checked !!}
                    {{$workspace->title}}
                    {{-- Check if the workspace is marked as primary and display the badge --}}
                    @if($workspace->is_primary)
                    <span class="badge bg-success">{{ get_label('primary', 'Primary') }}</span>
                    @endif

                    {{-- Check if the workspace is the user's default and display the badge --}}
                    @if($user->default_workspace_id == $workspace->id)
                    <span class="badge bg-primary">{{ get_label('default', 'Default') }}</span>
                    @endif

                </a>
            </li>
            @endforeach
            <li>
                <hr class="dropdown-divider" />
            </li>
            @endif
            @if ($user->can('manage_workspaces'))
            <li>
                <a class="dropdown-item" href="{{ url('workspaces') }}">
                    <i class='menu-icon tf-icons bx bx-bar-chart-alt-2 text-success'></i>
                    {!! get_label('manage_workspaces', 'Manage workspaces') !!}
                    {!! $total_workspaces > 5 ? '<span class="badge bg-primary"> + ' . ($total_workspaces - 5) . '</span>' : '' !!}
                </a>
            </li>
            @if ($user->can('create_workspaces'))
            <li>
                <span data-bs-toggle="modal" data-bs-target="#createWorkspaceModal">
                    <a class="dropdown-item" href="javascript:void(0);">
                        <i class='menu-icon tf-icons bx bx-plus text-warning'></i>
                        {!! get_label('create_workspace', 'Create workspace') !!}
                    </a>
                </span>
            </li>
            @endif
            @if ($user->can('edit_workspaces'))
            <li>
                <a class="dropdown-item edit-workspace" href="javascript:void(0);" data-id="{{ getWorkspaceId() }}">
                    <i class='menu-icon tf-icons bx bx-edit text-primary'></i>
                    {!! get_label('edit_workspace', 'Edit workspace') !!}
                </a>
            </li>
            @endif
            @endif
            @if($current_workspace)
            <li>
                <a class="dropdown-item" href="#" id="remove-participant">
                    <i class='menu-icon tf-icons bx bx-exit text-danger'></i>
                    {!! get_label('remove_me_from_workspace', 'Remove me from workspace') !!}
                </a>
            </li>
            @endif
        </ul>
        @endif
    </div>

    <div class="px-2 pt-3">
        <div class="input-group input-group-merge">
            <input type="text" id="menu-search" class="form-control custom-search-input" placeholder="{{ get_label('search_menu', 'Search Menu...') }}">
        </div>
    </div>
    <ul class="menu-inner pb-1">
        <hr class="dropdown-divider" />
        @php
        $menuOrder = json_decode(DB::table('menu_orders')->where(getGuardName() == 'web' ? 'user_id' : 'client_id', getAuthenticatedUser()->id)->value('menu_order'), true);
        $menus = getMenus();

        // Sort menus based on saved order
        $sortedMenus = [];
        if ($menuOrder) {
        foreach ($menuOrder as $order) {
        $menu = collect($menus)->firstWhere('id', $order['id']);
        if ($menu) {
        // Sort submenus if present
        if (!empty($order['submenus'])) {
        $submenuIds = collect($order['submenus'])->pluck('id')->toArray(); // Get the array of submenu IDs from saved order
        $menu['submenus'] = collect($menu['submenus'])->sortBy(function ($submenu) use ($submenuIds) {
        return array_search($submenu['id'], $submenuIds);
        })->toArray();
        }
        $sortedMenus[] = $menu;
        }
        }
        } else {
        // If no order is saved, return the default order of menus
        $sortedMenus = $menus;
        }
        @endphp
        @foreach ($sortedMenus as $menu)
        @if (!isset($menu['show']) || $menu['show'] === 1)
        <li class="{{ $menu['class'] }}">
            <a href="{{ $menu['url'] ?? 'javascript:void(0)' }}" class="menu-link {{ isset($menu['submenus']) ? 'menu-toggle' : '' }}">
                <i class="menu-icon tf-icons {{ $menu['icon'] }}"></i>
                <div>
                    {{ $menu['label'] }}
                    @if (isset($menu['badge']) && $menu['badge'])
                    {!! $menu['badge'] !!}
                    @endif
                </div>
            </a>

            @if (isset($menu['submenus']))
            <ul class="menu-sub">
                @foreach ($menu['submenus'] as $submenu)
                @if (!isset($submenu['show']) || $submenu['show'] === 1)
                <li class="{{ $submenu['class'] }}">
                    <a href="{{ $submenu['url'] }}" class="menu-link">
                        <div>{{ $submenu['label'] }}</div>
                    </a>
                </li>
                @endif
                @endforeach
            </ul>
            @endif
        </li>
        @endif
        @endforeach
    </ul>
</aside>