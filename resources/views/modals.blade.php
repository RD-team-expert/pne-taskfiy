@php
    use App\Models\Workspace;
    use Spatie\Permission\Models\Role;

    $auth_user = getAuthenticatedUser();
    $roles = Role::where('name', '!=', 'admin')->get();
    $isAdminOrHasAllDataAccess = isAdminOrHasAllDataAccess();
    $guard = getGuardName();
@endphp
@if (Request::is('projects') ||
        Request::is('projects/*') ||
        Request::is('tasks') ||
        Request::is('tasks/*') ||
        Request::is('status/manage') ||
        Request::is('users') ||
        Request::is('clients'))
    <div class="modal fade" id="create_status_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content form-submit-event" action="{{ url('status/store') }}" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_status', 'Create status') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="nameBasic" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select select-bg-label-primary" id="color" name="color">
                                <option class="badge bg-label-primary" value="primary"
                                    {{ old('color') == 'primary' ? 'selected' : '' }}>
                                    <?= get_label('primary', 'Primary') ?>
                                </option>
                                <option class="badge bg-label-secondary" value="secondary"
                                    {{ old('color') == 'secondary' ? 'selected' : '' }}>
                                    <?= get_label('secondary', 'Secondary') ?></option>
                                <option class="badge bg-label-success" value="success"
                                    {{ old('color') == 'success' ? 'selected' : '' }}>
                                    <?= get_label('success', 'Success') ?></option>
                                <option class="badge bg-label-danger" value="danger"
                                    {{ old('color') == 'danger' ? 'selected' : '' }}>
                                    <?= get_label('danger', 'Danger') ?></option>
                                <option class="badge bg-label-warning" value="warning"
                                    {{ old('color') == 'warning' ? 'selected' : '' }}>
                                    <?= get_label('warning', 'Warning') ?></option>
                                <option class="badge bg-label-info" value="info"
                                    {{ old('color') == 'info' ? 'selected' : '' }}><?= get_label('info', 'Info') ?>
                                </option>
                                <option class="badge bg-label-dark" value="dark"
                                    {{ old('color') == 'dark' ? 'selected' : '' }}><?= get_label('dark', 'Dark') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    @if ($isAdminOrHasAllDataAccess)
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label
                                    class="form-label"><?= get_label('roles_can_set_status', 'Roles Can Set the Status') ?>
                                    <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                        data-bs-offset="0,4" data-bs-placement="top" title=""
                                        data-bs-original-title="{{ get_label('roles_can_set_status_info', 'Including Admin and Roles with All Data Access Permission, Users/Clients Under Selected Role(s) Will Have Permission to Set This Status.') }}"></i></label>
                                <select class="form-control js-example-basic-multiple" name="role_ids[]"
                                    multiple="multiple"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                    data-allow-clear="true">
                                    @isset($roles)
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('create', 'Create') ?></label></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('status/manage'))
    <div class="modal fade" id="edit_status_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ url('status/update') }}" class="modal-content form-submit-event" method="POST">
                <input type="hidden" name="id" id="status_id">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_status', 'Update status') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="status_title" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select select-bg-label-primary" id="status_color" name="color"
                                required>
                                <option class="badge bg-label-primary" value="primary">
                                    <?= get_label('primary', 'Primary') ?>
                                </option>
                                <option class="badge bg-label-secondary" value="secondary">
                                    <?= get_label('secondary', 'Secondary') ?></option>
                                <option class="badge bg-label-success" value="success">
                                    <?= get_label('success', 'Success') ?></option>
                                <option class="badge bg-label-danger" value="danger">
                                    <?= get_label('danger', 'Danger') ?></option>
                                <option class="badge bg-label-warning" value="warning">
                                    <?= get_label('warning', 'Warning') ?></option>
                                <option class="badge bg-label-info" value="info"><?= get_label('info', 'Info') ?>
                                </option>
                                <option class="badge bg-label-dark" value="dark"><?= get_label('dark', 'Dark') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    @if ($isAdminOrHasAllDataAccess)
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label
                                    class="form-label"><?= get_label('roles_can_set_status', 'Roles Can Set the Status') ?>
                                    <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                        data-bs-offset="0,4" data-bs-placement="top" title=""
                                        data-bs-original-title="{{ get_label('roles_can_set_status_info', 'Including Admin and Roles with All Data Access Permission, Users/Clients Under Selected Role(s) Will Have Permission to Set This Status.') }}"></i></label>
                                <select class="form-control js-example-basic-multiple" name="role_ids[]"
                                    multiple="multiple"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                    data-allow-clear="true">
                                    @isset($roles)
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('update', 'Update') ?></label></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('projects') ||
        Request::is('projects/*') ||
        Request::is('tasks') ||
        Request::is('tasks/*') ||
        Request::is('priority/manage') ||
        Request::is('users') ||
        Request::is('clients'))
    <div class="modal fade" id="create_priority_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content form-submit-event" action="{{ url('priority/store') }}" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_priority', 'Create Priority') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="nameBasic" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select select-bg-label-primary" id="color" name="color">
                                <option class="badge bg-label-primary" value="primary"
                                    {{ old('color') == 'primary' ? 'selected' : '' }}>
                                    <?= get_label('primary', 'Primary') ?>
                                </option>
                                <option class="badge bg-label-secondary" value="secondary"
                                    {{ old('color') == 'secondary' ? 'selected' : '' }}>
                                    <?= get_label('secondary', 'Secondary') ?></option>
                                <option class="badge bg-label-success" value="success"
                                    {{ old('color') == 'success' ? 'selected' : '' }}>
                                    <?= get_label('success', 'Success') ?></option>
                                <option class="badge bg-label-danger" value="danger"
                                    {{ old('color') == 'danger' ? 'selected' : '' }}>
                                    <?= get_label('danger', 'Danger') ?></option>
                                <option class="badge bg-label-warning" value="warning"
                                    {{ old('color') == 'warning' ? 'selected' : '' }}>
                                    <?= get_label('warning', 'Warning') ?></option>
                                <option class="badge bg-label-info" value="info"
                                    {{ old('color') == 'info' ? 'selected' : '' }}><?= get_label('info', 'Info') ?>
                                </option>
                                <option class="badge bg-label-dark" value="dark"
                                    {{ old('color') == 'dark' ? 'selected' : '' }}><?= get_label('dark', 'Dark') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('create', 'Create') ?></label></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('priority/manage'))
    <div class="modal fade" id="edit_priority_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ url('priority/update') }}" class="modal-content form-submit-event" method="POST">
                <input type="hidden" name="id" id="priority_id">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_priority', 'Update Priority') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="priority_title" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select select-bg-label-primary" id="priority_color" name="color"
                                required>
                                <option class="badge bg-label-primary" value="primary">
                                    <?= get_label('primary', 'Primary') ?>
                                </option>
                                <option class="badge bg-label-secondary" value="secondary">
                                    <?= get_label('secondary', 'Secondary') ?></option>
                                <option class="badge bg-label-success" value="success">
                                    <?= get_label('success', 'Success') ?></option>
                                <option class="badge bg-label-danger" value="danger">
                                    <?= get_label('danger', 'Danger') ?></option>
                                <option class="badge bg-label-warning" value="warning">
                                    <?= get_label('warning', 'Warning') ?></option>
                                <option class="badge bg-label-info" value="info"><?= get_label('info', 'Info') ?>
                                </option>
                                <option class="badge bg-label-dark" value="dark"><?= get_label('dark', 'Dark') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('update', 'Update') ?></label></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('projects') ||
        Request::is('projects/*') ||
        Request::is('tags/manage') ||
        Request::is('users') ||
        Request::is('clients'))
    <div class="modal fade" id="create_tag_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="{{ url('tags/store') }}" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_tag', 'Create tag') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="nameBasic" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select select-bg-label-primary" id="color" name="color">
                                <option class="badge bg-label-primary" value="primary"
                                    {{ old('color') == 'primary' ? 'selected' : '' }}>
                                    <?= get_label('primary', 'Primary') ?>
                                </option>
                                <option class="badge bg-label-secondary" value="secondary"
                                    {{ old('color') == 'secondary' ? 'selected' : '' }}>
                                    <?= get_label('secondary', 'Secondary') ?></option>
                                <option class="badge bg-label-success" value="success"
                                    {{ old('color') == 'success' ? 'selected' : '' }}>
                                    <?= get_label('success', 'Success') ?></option>
                                <option class="badge bg-label-danger" value="danger"
                                    {{ old('color') == 'danger' ? 'selected' : '' }}>
                                    <?= get_label('danger', 'Danger') ?></option>
                                <option class="badge bg-label-warning" value="warning"
                                    {{ old('color') == 'warning' ? 'selected' : '' }}>
                                    <?= get_label('warning', 'Warning') ?></option>
                                <option class="badge bg-label-info" value="info"
                                    {{ old('color') == 'info' ? 'selected' : '' }}><?= get_label('info', 'Info') ?>
                                </option>
                                <option class="badge bg-label-dark" value="dark"
                                    {{ old('color') == 'dark' ? 'selected' : '' }}><?= get_label('dark', 'Dark') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('create', 'Create') ?></label></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('tags/manage'))
    <div class="modal fade" id="edit_tag_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="{{ url('tags/update') }}" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" name="id" id="tag_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_tag', 'Update tag') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="tag_title" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select select-bg-label-primary" id="tag_color" name="color">
                                <option class="badge bg-label-primary" value="primary">
                                    <?= get_label('primary', 'Primary') ?>
                                </option>
                                <option class="badge bg-label-secondary" value="secondary">
                                    <?= get_label('secondary', 'Secondary') ?></option>
                                <option class="badge bg-label-success" value="success">
                                    <?= get_label('success', 'Success') ?></option>
                                <option class="badge bg-label-danger" value="danger">
                                    <?= get_label('danger', 'Danger') ?></option>
                                <option class="badge bg-label-warning" value="warning">
                                    <?= get_label('warning', 'Warning') ?></option>
                                <option class="badge bg-label-info" value="info"><?= get_label('info', 'Info') ?>
                                </option>
                                <option class="badge bg-label-dark" value="dark"><?= get_label('dark', 'Dark') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('update', 'Update') ?></label></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('home') || Request::is('todos'))
    <div class="modal fade" id="create_todo_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content form-submit-event" action="{{ url('todos/store') }}" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_todo', 'Create todo') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('priority', 'Priority') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select" name="priority">
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                    <?= get_label('low', 'Low') ?></option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>
                                    <?= get_label('medium', 'Medium') ?></option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                    <?= get_label('high', 'High') ?></option>
                            </select>
                        </div>
                    </div>
                    <label for="description" class="form-label"><?= get_label('description', 'Description') ?></label>
                    <textarea class="form-control" name="description"
                        placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_todo_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ url('todos/update') }}" class="modal-content form-submit-event" method="POST">
                <input type="hidden" name="id" id="todo_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_todo', 'Update todo') ?></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="todo_title" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('priority', 'Priority') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select" id="todo_priority" name="priority">
                                <option value="low"><?= get_label('low', 'Low') ?></option>
                                <option value="medium"><?= get_label('medium', 'Medium') ?></option>
                                <option value="high"><?= get_label('high', 'High') ?></option>
                            </select>
                        </div>
                    </div>
                    <label for="description" class="form-label"><?= get_label('description', 'Description') ?></label>
                    <textarea class="form-control" id="todo_description" name="description"
                        placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('update', 'Update') ?></span></button>
                </div>
            </form>
        </div>
    </div>
@endif
<div class="modal fade" id="default_language_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('set_primary_lang_alert', 'Are you want to set as your primary language?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary" id="confirm"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="set_default_view_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('set_default_view_alert', 'Are You Want to Set as Default View?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary" id="confirm"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmSaveColumnVisibility" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('save_column_visibility_alert', 'Are You Want to Save Column Visibility?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary" id="confirm"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="leaveWorkspaceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= get_label('confirm_leave_workspace', 'Are you sure you want leave this workspace?') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-danger" id="confirm"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="create_language_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <form class="modal-content form-submit-event" action="{{ url('settings/languages/store') }}" method="POST">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_language', 'Create language') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="name"
                            placeholder="For Example: English" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('code', 'Code') ?> <span
                                class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="code" placeholder="For Example: en" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn"
                    class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="edit_language_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <form class="modal-content form-submit-event" action="{{ url('settings/languages/update') }}"
            method="POST">
            <input type="hidden" name="id" id="language_id">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_language', 'Update language') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="name" id="language_title"
                            placeholder="For Example: English" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn"
                    class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
            </div>
        </form>
    </div>
</div>
@if (Request::is('leave-requests') || Request::is('leave-requests/*'))
    <div class="modal fade" id="create_leave_request_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content form-submit-event" action="{{ url('leave-requests/store') }}" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" name="table" value="lr_table">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_leave_requet', 'Create leave request') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        @if (is_admin_or_leave_editor())
                            <div class="col-12 mb-3">
                                <label class="form-label"
                                    for="user_id"><?= get_label('select_user', 'Select user') ?> <span
                                        class="asterisk">*</span></label>
                                <select class="form-select users_select"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                    name="user_id" data-single-select="true">
                                    <option value="{{ $auth_user->id }}" selected>{{ $auth_user->first_name }}
                                        {{ $auth_user->last_name }}</option>
                                </select>
                            </div>
                        @endif
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <label class="form-check-label" for="partialLeave">
                                    <input class="form-check-input" type="checkbox" name="partialLeave"
                                        id="partialLeave">
                                    <?= get_label('partial_leave', 'Partial Leave') ?>?
                                </label>
                            </div>
                        </div>
                        <div class="col-5 leave-from-date-div mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('from_date', 'From date') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="start_date" name="from_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="col-5 leave-to-date-div mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('to_date', 'To date') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="lr_end_date" name="to_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="col-2 leave-from-time-div d-none mb-3">
                            <label class="form-label" for=""><?= get_label('from_time', 'From Time') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="time" name="from_time" class="form-control"
                                value="{{ old('from_time') }}">
                        </div>
                        <div class="col-2 leave-to-time-div d-none mb-3">
                            <label class="form-label" for=""><?= get_label('to_time', 'To Time') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="time" name="to_time" class="form-control" value="{{ old('to_time') }}">
                        </div>
                        <div class="col-2 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('days', 'Days') ?></label>
                            <input type="text" id="total_days" class="form-control" value="1"
                                placeholder="" disabled>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input leaveVisibleToAll" type="checkbox"
                                    name="leaveVisibleToAll" id="leaveVisibleToAll">
                                <label class="form-check-label"
                                    for="leaveVisibleToAll"><?= get_label('visible_to_all', 'Visible To All') ?>? <i
                                        class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                        data-bs-offset="0,4" data-bs-placement="top" title=""
                                        data-bs-html="true"
                                        data-bs-original-title="{{ get_label('leave_visible_to_info', 'Disabled: Requestee, Admin, and Leave Editors, along with selected users, will be able to know when the requestee is on leave. Enabled: All team members will be able to know when the requestee is on leave.') }}"></i></label>
                            </div>
                        </div>
                        <div class="col-12 leaveVisibleToDiv mb-3">
                            <select class="form-select users_select"
                                data-placeholder="<?= get_label('type_to_search_users_leave_visible_to', 'Type To Search Users Leave Visible To') ?>"
                                name="visible_to_ids[]" data-leave-visible-to-users="true" multiple>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label"><?= get_label('reason', 'Reason') ?> <span
                                class="asterisk">*</span></label>
                        <textarea class="form-control" name="reason"
                            placeholder="<?= get_label('please_enter_leave_reason', 'Please enter leave reason') ?>"></textarea>
                    </div>
                    @if (is_admin_or_leave_editor())
                        <div class="col-12 mb-3">
                            <label for="comment" class="form-label"><?= get_label('comment', 'Comment') ?></label>
                            <textarea class="form-control" name="comment"
                                placeholder="<?= get_label('optional_comment_placeholder', 'Please Enter Comment, if Any') ?>"></textarea>
                        </div>
                    @endif
                    @if (is_admin_or_leave_editor())
                        <div class="row">
                            <div class="col-12 d-flex justify-content-center">
                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check" name="status" id="create_lr_pending"
                                        value="pending" checked>
                                    <label class="btn btn-outline-primary"
                                        for="create_lr_pending"><?= get_label('pending', 'Pending') ?></label>
                                    <input type="radio" class="btn-check" name="status" id="create_lr_approved"
                                        value="approved">
                                    <label class="btn btn-outline-primary"
                                        for="create_lr_approved"><?= get_label('approved', 'Approved') ?></label>
                                    <input type="radio" class="btn-check" name="status" id="create_lr_rejected"
                                        value="rejected">
                                    <label class="btn btn-outline-primary"
                                        for="create_lr_rejected"><?= get_label('rejected', 'Rejected') ?></label>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_leave_request_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content form-submit-event" action="{{ url('leave-requests/update') }}"
                method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" name="table" value="lr_table">
                <input type="hidden" name="id" id="lr_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_leave_request', 'Update leave request') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        @if (is_admin_or_leave_editor())
                            <div class="col-12 mb-3">
                                <label class="form-label"><?= get_label('user', 'User') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="text" id="leaveUser" class="form-control" disabled>
                            </div>
                        @endif
                        <div class="col-12">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="updatePartialLeave"
                                    name="partialLeave">
                                <label class="form-check-label"
                                    for="updatePartialLeave"><?= get_label('partial_leave', 'Partial Leave') ?>?</label>
                            </div>
                        </div>
                        <div class="col-5 leave-from-date-div mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('from_date', 'From date') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="update_start_date" name="from_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="col-5 leave-to-date-div mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('to_date', 'To date') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="update_end_date" name="to_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="col-2 leave-from-time-div d-none mb-3">
                            <label class="form-label" for=""><?= get_label('from_time', 'From Time') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="time" name="from_time" class="form-control">
                        </div>
                        <div class="col-2 leave-to-time-div d-none mb-3">
                            <label class="form-label" for=""><?= get_label('to_time', 'To Time') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="time" name="to_time" class="form-control">
                        </div>
                        <div class="col-2 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('days', 'Days') ?></label>
                            <input type="text" id="update_total_days" class="form-control" value="1"
                                placeholder="" disabled>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input leaveVisibleToAll" type="checkbox"
                                    name="leaveVisibleToAll" id="updateLeaveVisibleToAll">
                                <label class="form-check-label"
                                    for="updateLeaveVisibleToAll"><?= get_label('visible_to_all', 'Visible To All') ?>?
                                    <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                        data-bs-offset="0,4" data-bs-placement="top" title=""
                                        data-bs-html="true"
                                        data-bs-original-title="{{ get_label('leave_visible_to_info', 'Disabled: Requestee, Admin, and Leave Editors, along with selected users, will be able to know when the requestee is on leave. Enabled: All team members will be able to know when the requestee is on leave.') }}"></i></label>
                            </div>
                        </div>
                        <div class="col-12 leaveVisibleToDiv mb-3">
                            <select class="form-select users_select"
                                data-placeholder="<?= get_label('type_to_search_users_leave_visible_to', 'Type To Search Users Leave Visible To') ?>"
                                name="visible_to_ids[]" data-leave-visible-to-users="true" multiple>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label"><?= get_label('reason', 'Reason') ?> <span
                                    class="asterisk">*</span></label>
                            <textarea class="form-control" name="reason"
                                placeholder="<?= get_label('please_enter_leave_reason', 'Please enter leave reason') ?>"></textarea>
                        </div>
                        @if (is_admin_or_leave_editor())
                            <div class="col-12 mb-3">
                                <label for="comment"
                                    class="form-label"><?= get_label('comment', 'Comment') ?></label>
                                <textarea class="form-control" name="comment"
                                    placeholder="<?= get_label('optional_comment_placeholder', 'Please Enter Comment, if Any') ?>"></textarea>
                            </div>
                        @endif
                        @php
                            $isAdminOrLr = is_admin_or_leave_editor();
                            $disabled = !$isAdminOrLr ? 'disabled' : '';
                        @endphp
                        <div class="col-12 d-flex justify-content-center">
                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="status" id="update_lr_pending"
                                    value="pending" {{ $disabled }}>
                                <label class="btn btn-outline-primary"
                                    for="update_lr_pending"><?= get_label('pending', 'Pending') ?></label>
                                <input type="radio" class="btn-check" name="status" id="update_lr_approved"
                                    value="approved" {{ $disabled }}>
                                <label class="btn btn-outline-primary"
                                    for="update_lr_approved"><?= get_label('approved', 'Approved') ?></label>
                                <input type="radio" class="btn-check" name="status" id="update_lr_rejected"
                                    value="rejected" {{ $disabled }}>
                                <label class="btn btn-outline-primary"
                                    for="update_lr_rejected"><?= get_label('rejected', 'Rejected') ?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('update', 'Update') ?></label></button>
                </div>
            </form>
        </div>
    </div>
@endif
<div class="modal fade" id="create_contract_type_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <form class="modal-content form-submit-event" action="{{ url('contracts/store-contract-type') }}"
            method="POST">
            <input type="hidden" name="dnr">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">
                    <?= get_label('create_contract_type', 'Create contract type') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span
                                class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="type"
                            placeholder="<?= get_label('please_enter_contract_type', 'Please enter contract type') ?>" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn"
                    class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="edit_contract_type_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <form class="modal-content form-submit-event" action="{{ url('contracts/update-contract-type') }}"
            method="POST">
            <input type="hidden" name="dnr">
            <input type="hidden" id="update_contract_type_id" name="id">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">
                    <?= get_label('update_contract_type', 'Update contract type') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span
                                class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="type" id="contract_type"
                            placeholder="<?= get_label('please_enter_contract_type', 'Please enter contract type') ?>" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn"
                    class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
            </div>
        </form>
    </div>
</div>
@if (Request::is('contracts'))
    <div class="modal fade" id="create_contract_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content form-submit-event" action="{{ url('contracts/store') }}" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" name="table" value="contracts_table">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_contract', 'Create contract') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" name="title" class="form-control"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('value', 'Value') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">{{ $general_settings['currency_symbol'] }}</span>
                                <input type="text" name="value" class="form-control currency"
                                    placeholder="<?= get_label('please_enter_value', 'Please enter value') ?>">
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('starts_at', 'Starts at') ?>
                                <span class="asterisk">*</span></label>
                            <input type="text" id="start_date" name="start_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('ends_at', 'Ends at') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="end_date" name="end_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        @if (!isClient())
                            <label class="form-label"
                                for=""><?= get_label('select_client', 'Select client') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="col-12 mb-3">
                                <select class="form-select clients_select" name="client_id"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                    data-allow-clear="false" data-single-select="true">
                                </select>
                            </div>
                        @endif
                        <label class="form-label"
                            for=""><?= get_label('select_project', 'Select project') ?> <span
                                class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-select projects_select" name="project_id"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                data-allow-clear="false" data-single-select="true">
                            </select>
                        </div>
                        <label class="form-label"
                            for=""><?= get_label('select_contract_type', 'Select contract type') ?> <span
                                class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-select contract_types_select" name="contract_type_id"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                data-allow-clear="false" data-single-select="true">
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateContractTypeModal"><button
                                        type="button" class="btn btn-sm btn-primary action_create_contract_types"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_contract_type', 'Create contract type') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="{{ url('contracts/contract-types') }}"><button type="button"
                                        class="btn btn-sm btn-primary action_manage_contract_types"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_contract_types', 'Manage contract types') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                    </div>
                    <label for="description"
                        class="form-label"><?= get_label('description', 'Description') ?></label>
                    <textarea class="form-control" name="description" id="contract_description"
                        placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_contract_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content form-submit-event" action="{{ url('contracts/update') }}" method="POST"
                enctype="multipart/form-data">
                <input type="hidden" name="dnr">
                <input type="hidden" name="table" value="contracts_table">
                <input type="hidden" id="contract_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_contract', 'Update contract') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="title" name="title" class="form-control"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('value', 'Value') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">{{ $general_settings['currency_symbol'] }}</span>
                                <input type="text" id="value" name="value"
                                    class="form-control currency"
                                    placeholder="<?= get_label('please_enter_value', 'Please enter value') ?>">
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('starts_at', 'Starts at') ?>
                                <span class="asterisk">*</span></label>
                            <input type="text" id="update_start_date" name="start_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('ends_at', 'Ends at') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="update_end_date" name="end_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <label class="form-label" for=""><?= get_label('select_client', 'Select client') ?>
                            <span class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-select clients_select" id="client_id" name="client_id"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                data-allow-clear="false" data-single-select="true">
                            </select>
                        </div>
                        <label class="form-label"
                            for=""><?= get_label('select_project', 'Select project') ?> <span
                                class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-select projects_select" id="project_id" name="project_id"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                data-allow-clear="false" data-single-select="true">
                            </select>
                        </div>
                        <label class="form-label"
                            for=""><?= get_label('select_contract_type', 'Select contract type') ?> <span
                                class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-select contract_types_select" id="contract_type_id"
                                name="contract_type_id"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                data-allow-clear="false" data-single-select="true">
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateContractTypeModal"><button
                                        type="button" class="btn btn-sm btn-primary action_create_contract_types"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_contract_type', 'Create contract type') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="{{ url('contracts/contract-types') }}"><button type="button"
                                        class="btn btn-sm btn-primary action_manage_contract_types"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_contract_types', 'Manage contract types') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                    </div>
                    <label for="description"
                        class="form-label"><?= get_label('description', 'Description') ?></label>
                    <textarea class="form-control" name="description" id="update_contract_description"
                        placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                    <div class="col-12 mb-3 mt-3">
                        <label class="form-label"><?= get_label('contract_pdf', 'Contract PDF') ?> <small
                                class="text-muted">
                                ({{ get_label('leave_blank_if_no_change', 'Leave it blank if no change') }})</small></label>
                        <div class="dropzone dz-clickable" id="contract-dropzone"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('payslips/create') || Request::is('payslips/edit/*') || Request::is('payment-methods'))
    <div class="modal fade" id="create_pm_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <form class="modal-content form-submit-event" action="{{ url('payment-methods/store') }}"
                method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_payment_method', 'Create payment method') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_pm_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <form class="modal-content form-submit-event" action="{{ url('payment-methods/update') }}"
                method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" id="pm_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_payment_method', 'Update payment method') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title" id="pm_title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('payslips/create') || Request::is('payslips/edit/*') || Request::is('allowances'))
    <div class="modal fade" id="create_allowance_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="{{ url('allowances/store') }}" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_allowance', 'Create allowance') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">{{ $general_settings['currency_symbol'] }}</span>
                                <input class="form-control currency" type="text" name="amount"
                                    placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_allowance_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="{{ url('allowances/update') }}" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" name="id" id="allowance_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_allowance', 'Update allowance') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="allowance_title" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">{{ $general_settings['currency_symbol'] }}</span>
                                <input class="form-control currency" type="text" id="allowance_amount"
                                    name="amount"
                                    placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('payslips/create') || Request::is('payslips/edit/*') || Request::is('deductions'))
    <div class="modal fade" id="create_deduction_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="{{ url('deductions/store') }}" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_deduction', 'Create deduction') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span
                                    class="asterisk">*</span></label>
                            <select id="deduction_type" name="type" class="form-select">
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <option value="amount"><?= get_label('amount', 'Amount') ?></option>
                                <option value="percentage"><?= get_label('percentage', 'Percentage') ?></option>
                            </select>
                        </div>
                        <div class="col-md-12 d-none mb-3" id="amount_div">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">{{ $general_settings['currency_symbol'] }}</span>
                                <input class="form-control currency" type="text" name="amount"
                                    placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>">
                            </div>
                        </div>
                        <div class="col-md-12 d-none mb-3" id="percentage_div">
                            <label class="form-label" for=""><?= get_label('percentage', 'Percentage') ?>
                                <span class="asterisk">*</span></label>
                            <input class="form-control" type="number" name="percentage" min="0"
                                max="100"
                                placeholder="<?= get_label('please_enter_percentage', 'Please enter percentage') ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_deduction_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="{{ url('deductions/update') }}" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" id="deduction_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_deduction', 'Update deduction') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="deduction_title" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span
                                    class="asterisk">*</span></label>
                            <select id="update_deduction_type" name="type" class="form-select">
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <option value="amount"><?= get_label('amount', 'Amount') ?></option>
                                <option value="percentage"><?= get_label('percentage', 'Percentage') ?></option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3" id="update_amount_div">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">{{ $general_settings['currency_symbol'] }}</span>
                                <input class="form-control currency" type="text" id="deduction_amount"
                                    name="amount"
                                    placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>">
                            </div>
                        </div>
                        <div class="col-md-12 mb-3" id="update_percentage_div">
                            <label class="form-label" for=""><?= get_label('percentage', 'Percentage') ?>
                                <span class="asterisk">*</span></label>
                            <input class="form-control" type="number" id="deduction_percentage"
                                name="percentage" min="0" max="100"
                                placeholder="<?= get_label('please_enter_percentage', 'Please enter percentage') ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('taxes'))
    <div class="modal fade" id="create_tax_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="{{ url('taxes/store') }}" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_tax', 'Create tax') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span
                                    class="asterisk">*</span></label>
                            <select id="deduction_type" name="type" class="form-select">
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <option value="amount"><?= get_label('amount', 'Amount') ?></option>
                                <option value="percentage"><?= get_label('percentage', 'Percentage') ?></option>
                            </select>
                        </div>
                        <div class="col-md-12 d-none mb-3" id="amount_div">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">{{ $general_settings['currency_symbol'] }}</span>
                                <input class="form-control currency" type="text" name="amount"
                                    placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>">
                            </div>
                        </div>
                        <div class="col-md-12 d-none mb-3" id="percentage_div">
                            <label class="form-label" for=""><?= get_label('percentage', 'Percentage') ?>
                                <span class="asterisk">*</span></label>
                            <input class="form-control" type="number" name="percentage" min="0"
                                max="100"
                                placeholder="<?= get_label('please_enter_percentage', 'Please enter percentage') ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_tax_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="{{ url('taxes/update') }}" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" id="tax_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_tax', 'Update tax') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="tax_title" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span
                                    class="asterisk">*</span></label>
                            <select id="update_tax_type" name="type" class="form-select" disabled>
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <option value="amount"><?= get_label('amount', 'Amount') ?></option>
                                <option value="percentage"><?= get_label('percentage', 'Percentage') ?></option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3" id="update_amount_div">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">{{ $general_settings['currency_symbol'] }}</span>
                                <input class="form-control currency" type="text" id="tax_amount"
                                    name="amount"
                                    placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>"
                                    disabled>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3" id="update_percentage_div">
                            <label class="form-label" for=""><?= get_label('percentage', 'Percentage') ?>
                                <span class="asterisk">*</span></label>
                            <input class="form-control" type="number" id="tax_percentage" name="percentage"
                                min="0" max="100"
                                placeholder="<?= get_label('please_enter_percentage', 'Please enter percentage') ?>"
                                disabled>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('units'))
    <div class="modal fade" id="create_unit_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="{{ url('units/store') }}" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_unit', 'Create unit') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_unit_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="{{ url('units/update') }}" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" id="unit_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_unit', 'Update unit') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="unit_title" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control" id="unit_description" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('estimates-invoices/create') || Request::is('estimates-invoices/edit/*') || Request::is('items'))
    <div class="modal fade" id="create_item_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="{{ url('items/store') }}" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_item', 'Create item') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('price', 'Price') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control currency" name="price"
                                placeholder="<?= get_label('please_enter_price', 'Please enter price') ?>" />
                        </div>
                        @if (isset($units) && is_iterable($units))
                            <div class="col-md-6 mb-3">
                                <label for="nameBasic" class="form-label"><?= get_label('unit', 'Unit') ?></label>
                                <select class="form-select js-example-basic-multiple" name="unit_id"
                                    data-placeholder="<?= get_label('Please select', 'Please select') ?>"
                                    data-allow-clear="true">
                                    <option></option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_item_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="{{ url('items/update') }}" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" id="item_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_item', 'Update item') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="item_title" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('price', 'Price') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control currency" id="item_price" name="price"
                                placeholder="<?= get_label('please_enter_price', 'Please enter price') ?>" />
                        </div>
                        @if (isset($units) && is_iterable($units))
                            <div class="col-md-6 mb-3">
                                <label for="nameBasic" class="form-label"><?= get_label('unit', 'Unit') ?></label>
                                <select class="form-select js-example-basic-multiple" id="item_unit_id"
                                    name="unit_id"
                                    data-placeholder="<?= get_label('Please select', 'Please select') ?>"
                                    data-allow-clear="true">
                                    <option></option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control" id="item_description" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('notes'))
    <div class="modal fade" id="create_note_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content form-submit-event" action="{{ url('notes/store') }}" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_note', 'Create note') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="nameBasic" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control description" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select select-bg-label-success" name="color">
                                <option class="badge bg-label-success" value="info"
                                    {{ old('color') == 'info' ? 'selected' : '' }}><?= get_label('green', 'Green') ?>
                                </option>
                                <option class="badge bg-label-warning" value="warning"
                                    {{ old('color') == 'warning' ? 'selected' : '' }}>
                                    <?= get_label('yellow', 'Yellow') ?></option>
                                <option class="badge bg-label-danger" value="danger"
                                    {{ old('color') == 'danger' ? 'selected' : '' }}><?= get_label('red', 'Red') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('create', 'Create') ?></label></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_note_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content form-submit-event" action="{{ url('notes/update') }}" method="POST">
                <input type="hidden" name="id" id="note_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_note', 'Update note') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="note_title" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control description" id="note_description" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select select-bg-label-success" id="note_color" name="color">
                                <option class="badge bg-label-info" value="info"
                                    {{ old('color') == 'info' ? 'selected' : '' }}><?= get_label('green', 'Green') ?>
                                </option>
                                <option class="badge bg-label-warning" value="warning"
                                    {{ old('color') == 'warning' ? 'selected' : '' }}>
                                    <?= get_label('yellow', 'Yellow') ?></option>
                                <option class="badge bg-label-danger" value="danger"
                                    {{ old('color') == 'danger' ? 'selected' : '' }}><?= get_label('red', 'Red') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('update', 'Update') ?></label></button>
                </div>
            </form>
        </div>
    </div>
@endif
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('delete_account_alert', 'Are you sure you want to delete your account?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-danger"
                    id="confirmDeleteAccount"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> '</button>
            </div>
            <div class="modal-body">
                <p><?= get_label('delete_alert', 'Are you sure you want to delete?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-danger"
                    id="confirmDelete"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmDeleteSelectedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> '</button>
            </div>
            <div class="modal-body">
                <p><?= get_label('delete_selected_alert', 'Are you sure you want to delete selected record(s)?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-danger"
                    id="confirmDeleteSelections"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="duplicateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('duplicate_warning', 'Are you sure you want to duplicate?') ?></p>
                <div id="titleDiv" class="d-none"><label
                        class="form-label"><?= get_label('update_title', 'Update Title') ?></label><input
                        type="text" class="form-control" id="updateTitle"
                        placeholder="<?= get_label('enter_title_duplicate', 'Enter Title For Item Being Duplicated') ?>">
                </div>
                <div id="selectionDiv" class="mb-3 mt-3">
                    <label class="form-label"><?= get_label('select_data_to_duplicate', 'Select Data to Duplicate') ?>
                        <small
                            class="text-muted">(<?= get_label('users_clients_duplicate_info', 'Users and Clients will be duplicated by default') ?>)</small></label>
                    <div class="d-flex flex-wrap">
                        <div class="me-3">
                            <input type="checkbox" id="duplicateProjects" value="projects"
                                class="duplicate-option">
                            <label for="duplicateProjects"><?= get_label('projects', 'Projects') ?></label>
                        </div>
                        <div class="me-3">
                            <input type="checkbox" id="duplicateProjectTasks" value="project_tasks"
                                class="duplicate-option" disabled>
                            <label
                                for="duplicateProjectTasks"><?= get_label('tasks_if_projects', 'Tasks (if Projects selected)') ?></label>
                        </div>
                        <div class="me-3">
                            <input type="checkbox" id="duplicateMeetings" value="meetings"
                                class="duplicate-option">
                            <label for="duplicateMeetings"><?= get_label('meetings', 'Meetings') ?></label>
                        </div>
                        <div class="me-3">
                            <input type="checkbox" id="duplicateNotes" value="notes" class="duplicate-option">
                            <label for="duplicateNotes"><?= get_label('notes', 'Notes') ?></label>
                        </div>
                        <div class="me-3">
                            <input type="checkbox" id="duplicateTodos" value="todos" class="duplicate-option">
                            <label for="duplicateTodos"><?= get_label('todos', 'Todos') ?></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="confirmDuplicate"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="timerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('time_tracker', 'Time tracker') ?>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="stopwatch">
                    <div class="stopwatch_time">
                        <input type="text" name="hour" id="hour" value="00"
                            class="form-control stopwatch_time_input" readonly>
                        <div class="stopwatch_time_lable"><?= get_label('hours', 'Hours') ?></div>
                    </div>
                    <div class="stopwatch_time">
                        <input type="text" name="minute" id="minute" value="00"
                            class="form-control stopwatch_time_input" readonly>
                        <div class="stopwatch_time_lable"><?= get_label('minutes', 'Minutes') ?></div>
                    </div>
                    <div class="stopwatch_time">
                        <input type="text" name="second" id="second" value="00"
                            class="form-control stopwatch_time_input" readonly>
                        <div class="stopwatch_time_lable"><?= get_label('second', 'Second') ?></div>
                    </div>
                </div>
                <div class="selectgroup selectgroup-pills d-flex justify-content-around mt-3">
                    <label class="selectgroup-item">
                        <span class="selectgroup-button selectgroup-button-icon" data-bs-toggle="tooltip"
                            data-bs-placement="left" data-bs-original-title="<?= get_label('start', 'Start') ?>"
                            id="start" onclick="startTimer()"><i class="bx bx-play"></i></span>
                    </label>
                    <label class="selectgroup-item">
                        <span class="selectgroup-button selectgroup-button-icon" data-bs-toggle="tooltip"
                            data-bs-placement="left" data-bs-original-title="<?= get_label('stop', 'Stop') ?>"
                            id="end" onclick="stopTimer()"><i class="bx bx-stop"></i></span>
                    </label>
                    <label class="selectgroup-item">
                        <span class="selectgroup-button selectgroup-button-icon" data-bs-toggle="tooltip"
                            data-bs-placement="left" data-bs-original-title="<?= get_label('pause', 'Pause') ?>"
                            id="pause" onclick="pauseTimer()"><i class="bx bx-pause"></i></span>
                    </label>
                </div>
                <div class="form-group mb-0 mt-3">
                    <label class="label"><?= get_label('message', 'Message') ?>:</label>
                    <textarea class="form-control" id="time_tracker_message"
                        placeholder="<?= get_label('please_enter_your_message', 'Please enter your message') ?>" name="message"></textarea>
                </div>
                @if (getAuthenticatedUser()->can('manage_timesheet'))
                    <div class="modal-footer justify-content-center">
                        <a href="{{ url('time-tracker') }}" class="btn btn-primary"><i class="bx bxs-time"></i>
                            <?= get_label('view_timesheet', 'View timesheet') ?></a>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="stopTimerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> '</button>
            </div>
            <div class="modal-body">
                <p><?= get_label('stop_timer_alert', 'Are you sure you want to stop the timer?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-danger"
                    id="confirmStop"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
@if (Request::is('estimates-invoices/create') || preg_match('/^estimates-invoices\/edit\/\d+$/', Request::path()))
    <div class="modal fade" id="edit-billing-address" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_billing_details', 'Update billing details') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        '</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('name', 'Name') ?> <span
                                    class="asterisk">*</span></label>
                            <input name="update_name" id="update_name" class="form-control"
                                placeholder="<?= get_label('please_enter_name', 'Please enter name') ?>"
                                value="{{ $estimate_invoice->name ?? '' }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('contact', 'Contact') ?></label>
                            <input name="update_contact" id="update_contact" class="form-control"
                                placeholder="<?= get_label('please_enter_contact', 'Please enter contact') ?>"
                                value="{{ $estimate_invoice->phone ?? '' }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('address', 'Address') ?></label>
                            <textarea class="form-control" placeholder="<?= get_label('please_enter_address', 'Please enter address') ?>"
                                name="update_address" id="update_address" required>{{ $estimate_invoice->address ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('city', 'City') ?></label>
                            <input name="update_city" id="update_city" class="form-control"
                                placeholder="<?= get_label('please_enter_city', 'Please enter city') ?>"
                                value="{{ $estimate_invoice->city ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('state', 'State') ?></label>
                            <input name="update_contact" id="update_state" class="form-control"
                                placeholder="<?= get_label('please_enter_state', 'Please enter state') ?>"
                                value="{{ $estimate_invoice->city ?? '' }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('country', 'Country') ?></label>
                            <input name="update_country" id="update_country" class="form-control"
                                placeholder="<?= get_label('please_enter_country', 'Please enter country') ?>"
                                value="{{ $estimate_invoice->country ?? '' }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('zip_code', 'Zip code') ?></label>
                            <input name="update_zip_code" id="update_zip_code" class="form-control"
                                placeholder="<?= get_label('please_enter_zip_code', 'Please enter zip code') ?>"
                                value="{{ $estimate_invoice->zip_code ?? '' }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="button" class="btn btn-primary"
                        id="apply_billing_details"><?= get_label('apply', 'Apply') ?></button>
                </div>
            </div>
        </div>
    </div>
@endif
@if (Request::is('expenses') || Request::is('expenses/*'))
    <div class="modal fade" id="create_expense_type_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content form-submit-event" action="{{ url('expenses/store-expense-type') }}"
                method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_expense_type', 'Create expense type') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col mb-3">
                            <label for="description"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_expense_type_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content form-submit-event" action="{{ url('expenses/update-expense-type') }}"
                method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" id="update_expense_type_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_expense_type', 'Update expense type') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title" id="expense_type_title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col mb-3">
                            <label for="description"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control" name="description" id="expense_type_description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
    @if (Request::is('expenses'))
        <div class="modal fade" id="create_expense_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form class="modal-content form-submit-event" action="{{ url('expenses/store') }}"
                    method="POST">
                    <input type="hidden" name="dnr">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">
                            <?= get_label('create_expense', 'Create expense') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="text" class="form-control" name="title"
                                    placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                            </div>
                            <div class="col mb-3">
                                <label class="form-label"><?= get_label('expense_type', 'Expense type') ?> <span
                                        class="asterisk">*</span></label>
                                <select class="form-select expense_types_select" name="expense_type_id"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                    data-single-select="true" data-allow-clear="false">
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label"><?= get_label('user', 'User') ?></label>
                                <select class="form-select users_select" name="user_id"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                    data-single-select="true">
                                </select>
                            </div>
                            <div class="col mb-3">
                                <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                        class="asterisk">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">{{ $general_settings['currency_symbol'] }}</span>
                                    <input class="form-control currency" type="text" name="amount"
                                        placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic"
                                    class="form-label"><?= get_label('expense_date', 'Expense date') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="text" id="expense_date" name="expense_date" class="form-control"
                                    placeholder="" autocomplete="off">
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label"><?= get_label('note', 'Note') ?></label>
                                <textarea class="form-control" name="note"
                                    placeholder="<?= get_label('please_enter_note_if_any', 'Please enter note if any') ?>"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <?= get_label('close', 'Close') ?>
                        </button>
                        <button type="submit" id="submit_btn"
                            class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="edit_expense_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form class="modal-content form-submit-event" action="{{ url('expenses/update') }}"
                    method="POST">
                    <input type="hidden" name="dnr">
                    <input type="hidden" id="update_expense_id" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">
                            <?= get_label('update_expense', 'Update expense') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="text" class="form-control" id="expense_title" name="title"
                                    placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                            </div>
                            <div class="col mb-3">
                                <label class="form-label"><?= get_label('expense_type', 'Expense type') ?> <span
                                        class="asterisk">*</span></label>
                                <select class="form-select expense_types_select" id="expense_type_id"
                                    name="expense_type_id"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                    data-single-select="true" data-allow-clear="false">
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label"><?= get_label('user', 'User') ?></label>
                                <select class="form-select users_select" id="expense_user_id" name="user_id"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                    data-single-select="true">
                                </select>
                            </div>
                            <div class="col mb-3">
                                <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                        class="asterisk">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">{{ $general_settings['currency_symbol'] }}</span>
                                    <input class="form-control currency" type="text" id="expense_amount"
                                        name="amount"
                                        placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic"
                                    class="form-label"><?= get_label('expense_date', 'Expense date') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="text" id="update_expense_date" name="expense_date"
                                    class="form-control" placeholder="" autocomplete="off">
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label"><?= get_label('note', 'Note') ?></label>
                                <textarea class="form-control" id="expense_note" name="note"
                                    placeholder="<?= get_label('please_enter_note_if_any', 'Please enter note if any') ?>"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <?= get_label('close', 'Close') ?>
                        </button>
                        <button type="submit" id="submit_btn"
                            class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endif
@if (Request::is('payments'))
    <div class="modal fade" id="create_payment_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content form-submit-event" action="{{ url('payments/store') }}" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_payment', 'Create payment') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label"><?= get_label('user', 'User') ?></label>
                            <select class="form-select users_select" name="user_id"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                data-single-select="true">
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label"><?= get_label('invoice', 'Invoice') ?></label>
                            <select class="form-select invoices_select" name="invoice_id"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                data-single-select="true">
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label"><?= get_label('payment_method', 'Payment method') ?></label>
                            <select class="form-select js-example-basic-multiple" name="payment_method_id"
                                data-placeholder="<?= get_label('Please select', 'Please select') ?>"
                                data-allow-clear="true">
                                <option></option>
                                @isset($payment_methods)
                                    @foreach ($payment_methods as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->title }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">{{ $general_settings['currency_symbol'] }}</span>
                                <input class="form-control currency" type="text" name="amount"
                                    placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('payment_date', 'Payment date') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="payment_date" name="payment_date" class="form-control"
                                placeholder="{{ get_label('please_select', 'Please Select') }}"
                                autocomplete="off">
                        </div>
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('note', 'Note') ?></label>
                            <textarea class="form-control" name="note"
                                placeholder="<?= get_label('please_enter_note_if_any', 'Please enter note if any') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_payment_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content form-submit-event" action="{{ url('payments/update') }}" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" id="update_payment_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_payment', 'Update payment') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label"><?= get_label('user', 'User') ?></label>
                            <select class="form-select users_select" name="user_id" id="payment_user_id"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                data-single-select="true">
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label"><?= get_label('invoice', 'Invoice') ?></label>
                            <select class="form-select invoices_select" name="invoice_id" id="payment_invoice_id"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                data-single-select="true">
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label"><?= get_label('payment_method', 'Payment method') ?></label>
                            <select class="form-select js-example-basic-multiple" name="payment_method_id"
                                id="payment_pm_id"
                                data-placeholder="<?= get_label('Please select', 'Please select') ?>"
                                data-allow-clear="true">
                                <option></option>
                                @isset($payment_methods)
                                    @foreach ($payment_methods as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->title }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">{{ $general_settings['currency_symbol'] }}</span>
                                <input class="form-control currency" type="text" name="amount"
                                    id="payment_amount"
                                    placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('payment_date', 'Payment date') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" name="payment_date" class="form-control"
                                id="update_payment_date" placeholder="" autocomplete="off">
                        </div>
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('note', 'Note') ?></label>
                            <textarea class="form-control" name="note" id="payment_note"
                                placeholder="<?= get_label('please_enter_note_if_any', 'Please enter note if any') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
@endif
<div class="modal fade" id="mark_all_notifications_as_read_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('mark_all_notifications_as_read_alert', 'Are you sure you want to mark all notifications as read?') ?>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="confirmMarkAllAsRead"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="update_notification_status_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('update_notifications_status_alert', 'Are you sure you want to update notification status?') ?>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="confirmNotificationStatus"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="restore_default_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('confirm_restore_default_template', 'Are you sure you want to restore default template?') ?>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="confirmRestoreDefault"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="permission_instuction_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">
                    <?= get_label('permission_settings_instructions', 'Permission Settings Instructions') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul>
                    <li class="mb-2"><b>{{ get_label('all_data_access', 'All Data Access') }}:</b> If this option
                        is selected, users or clients assigned to this role will have unrestricted access to all data,
                        without any specific restrictions or limitations.</li>
                    <li class="mb-2"><b>{{ get_label('allocated_data_access', 'Allocated Data Access') }}:</b> If
                        this option is selected, users or clients assigned to this role will have restricted access to
                        data based on specific assignments and restrictions.</li>
                    <li class="mb-2"><b>{{ get_label('create_permission', 'Create Permission') }}:</b> This
                        determines whether users or clients assigned to this role can create new records. For example,
                        if the create permission is enabled for projects, users or clients in this role will be able to
                        create new projects; otherwise, they won’t have this ability.</li>
                    <li class="mb-2"><b>{{ get_label('manage_permission', 'Manage Permission') }}:</b> This
                        determines whether users or clients assigned to this role can access and interact with specific
                        modules. For instance, if the manage permission is enabled for projects, users or clients in
                        this role will be able to view projects however create, edit, or delete depending on the
                        specific permissions granted. If the manage permission is disabled for projects, users or
                        clients in this role won’t be able to view or interact with projects in any way.</li>
                    <li class="mb-2"><b>{{ get_label('edit_permission', 'Edit Permission') }}:</b> This determines
                        whether users or clients assigned to this role can edit current records. For example, if the
                        edit permission is enabled for projects, users or clients in this role will be able to edit
                        current projects; otherwise, they won’t have this ability.</li>
                    <li><b>{{ get_label('delete_permission', 'Delete Permission') }}:</b> This determines whether
                        users or clients assigned to this role can delete current records. For example, if the delete
                        permission is enabled for projects, users or clients in this role will be able to delete current
                        projects; otherwise, they won’t have this ability.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
            </div>
        </div>
    </div>
</div>
@if (Request::is('tasks') ||
        Request::is('tasks/draggable') ||
        Request::is('tasks/calendar') ||
        Request::is('projects/tasks/calendar/*') ||
        Request::is('projects/information/*') ||
        Request::is('projects/tasks/draggable/*') ||
        Request::is('projects/tasks/list/*') ||
        Request::is('home') ||
        Request::is('users/profile/*') ||
        Request::is('clients/profile/*'))
    <div class="modal fade" id="create_task_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ url('tasks/store') }}" class="form-submit-event modal-content" method="POST">
                @if (
                    !Request::is('projects/tasks/draggable/*') &&
                        !Request::is('tasks/draggable') &&
                        !Request::is('tasks/calendar') &&
                        !Request::is('projects/tasks/calendar/*') &&
                        !Request::is('projects/information/*'))
                    <input type="hidden" name="dnr">
                    <input type="hidden" name="table" value="task_table">
                @endif
                <input type="hidden" name="is_favorite" id="is_favorite" value="0">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_task', 'Create Task') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>"
                                value="{{ old('title') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="status"><?= get_label('status', 'Status') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select statusDropdown" name="status_id">
                                @isset($statuses)
                                    @foreach ($statuses as $status)
                                        @if (canSetStatus($status))
                                            <option value="{{ $status->id }}" data-color="{{ $status->color }}"
                                                {{ old('status') == $status->id ? 'selected' : '' }}>
                                                {{ $status->title }}</option>
                                        @endif
                                    @endforeach
                                @endisset
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateStatusModal"><button type="button"
                                        class="btn btn-sm btn-primary action_create_statuses"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_status', 'Create status') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="{{ url('status/manage') }}"><button type="button"
                                        class="btn btn-sm btn-primary action_manage_statuses"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_statuses', 'Manage statuses') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= get_label('priority', 'Priority') ?></label>
                            <select class="form-select priorityDropdown" name="priority_id"
                                data-placeholder="<?= get_label('please_select', 'Please select') ?>">
                                <option></option>
                                @isset($priorities)
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority->id }}" data-color="{{ $priority->color }}">
                                            {{ $priority->title }}</option>
                                    @endforeach
                                @endisset
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreatePriorityModal"><button
                                        type="button" class="btn btn-sm btn-primary action_create_priorities"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_priority', 'Create Priority') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="{{ url('priority/manage') }}"><button type="button"
                                        class="btn btn-sm btn-primary action_manage_priorities"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_priorities', 'Manage Priorities') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php $project_id = 0;
                    if (!isset($project->id)) {
                    ?>
                        <div class="mb-3">
                            <label class="form-label"
                                for="user_id"><?= get_label('select_project', 'Select project') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-control selectTaskProject projects_select" name="project"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                data-single-select="true" data-allow-clear="false">
                            </select>
                        </div>
                        <?php } else {
                        $project_id = $project->id ?>
                        <input type="hidden" name="project" value="{{ $project_id }}">
                        <div class="mb-3">
                            <label for="project_title" class="form-label"><?= get_label('project', 'Project') ?>
                                <span class="asterisk">*</span></label>
                            <input class="form-control" type="text" value="{{ $project->title }}" readonly>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="row" id="selectTaskUsers">
                        <div class="mb-3">
                            <label class="form-label"
                                for="user_id"><?= get_label('select_users', 'Select users') ?> <span
                                    id="users_associated_with_project"></span><?php if (!empty($project_id)) { ?>
                                (<?= get_label('users_associated_with_project', 'Users associated with project') ?>
                                <b>{{ $project->title }}</b>)
                                <?php } ?></label>
                            <select class="form-control" name="user_id[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"
                                for="start_date"><?= get_label('starts_at', 'Starts at') ?></label>
                            <input type="text" id="task_start_date" name="start_date" class="form-control"
                                placeholder="{{ get_label('please_select', 'Please Select') }}"
                                autocomplete="off">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="due_date"><?= get_label('ends_at', 'Ends at') ?></label>
                            <input type="text" id="task_end_date" name="due_date" class="form-control"
                                placeholder="{{ get_label('please_select', 'Please Select') }}"
                                autocomplete="off">
                        </div>
                    </div>
                    @if ($isAdminOrHasAllDataAccess)
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-check-label"
                                    for="clientCanDiscussTask">{{ get_label('client_can_discuss', 'Client Can Discuss') }}?</label>
                                <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="{{ get_label('client_can_discuss_info_task', 'Allows the client to participate in task discussions.') }}"></i>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="clientCanDiscussTask"
                                        name="clientCanDiscuss">
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="mb-3">
                            <label for="description"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control description" rows="5" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"><?= get_label('note', 'Note') ?></label>
                            <textarea class="form-control" name="note" rows="3"
                                placeholder="<?= get_label('optional_note', 'Optional Note') ?>"></textarea>
                        </div>
                    </div>
                    @if (!$isAdminOrHasAllDataAccess && $guard != 'client')
                        <div class="alert alert-primary" role="alert">
                            <?= get_label('you_will_be_task_participant_automatically', 'You will be task participant automatically.') ?>
                        </div>
                    @endif
                    <!-- Remider Task -->
                    <div class="col-md-12">
                         <div class="mb-3">
                            <label for="reminder-switch"
                                class="form-label">{{ get_label('enable_reminder', 'Enable Reminder') }}</label>
                            <i class="bx bx-info-circle text-primary" data-bs-toggle="tooltip"data-bs-offset="0,4"
                                data-bs-placement="top" data-bs-html="true"title=""
                                data-bs-original-title="<b>{{ get_label('task_reminder', 'Task Reminder') }}:</b> {{ get_label('task_reminder_info', 'Enable this option to set reminders for tasks. You can configure reminder frequencies (daily, weekly, or monthly), specific times, and customize alerts to ensure you stay on track with task deadlines.') }}"></i>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="reminder-switch"
                                    name="enable_reminder">
                                <label class="form-check-label"
                                    for="reminder-switch">{{ get_label('enable_task_reminder', 'Enable Task Reminder') }}</label>
                            </div>
                        </div> 
                        <div id="reminder-settings" class="d-none">
                            <!-- Frequency Type -->
                            <div class="mb-3">
                                <label for="frequency-type"
                                    class="form-label">{{ get_label('frequency_type', 'Frequency Type') }}</label>
                                <select class="form-select" id="frequency-type" name="frequency_type" required>
                                    <option value="daily">{{ get_label('daily', 'Daily') }}</option>
                                    <option value="weekly">{{ get_label('weekly', 'Weekly') }}</option>
                                    <option value="monthly">{{ get_label('monthly', 'Monthly') }}</option>
                                </select>
                            </div>
                            <!-- Day of Week (Weekly Only) -->
                            <div class="d-none mb-3" id="day-of-week-group">
                                <label
                                    for="day-of-week"class="form-label">{{ get_label('day_of_the_week', 'Day of the Week') }}</label>
                                <select class="form-select" id="day-of-week" name="day_of_week">
                                    <option value="">{{ get_label('any_day', 'Any Day') }}</option>
                                    <option value="1">{{ get_label('monday', 'Monday') }}</option>
                                    <option value="2">{{ get_label('tuesday', 'Tuesday') }}</option>
                                    <option value="3">{{ get_label('wednesday', 'Wednesday') }}</option>
                                    <option value="4">{{ get_label('thursday', 'Thursday') }}</option>
                                    <option value="5">{{ get_label('friday', 'Friday') }}</option>
                                    <option value="6">{{ get_label('saturday', 'Saturday') }}</option>
                                    <option value="7">{{ get_label('sunday', 'Sunday') }}</option>
                                </select>
                            </div>
                            <!-- Day of Month (Monthly Only) -->
                            <div class="d-none mb-3" id="day-of-month-group">
                                <label for="day-of-month"
                                    class="form-label">{{ get_label('day_of_the_month', 'Day of the Month') }}</label>
                                <select class="form-select" id="day-of-month" name="day_of_month">
                                    <option value="">{{ get_label('any_day', 'Any Day') }}</option>
                                    @foreach (range(1, 31) as $day)
                                        <option value="{{ $day }}">{{ $day }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Time of Day -->
                            <div class="mb-3">
                                <label for="time-of-day"
                                    class="form-label">{{ get_label('time_of_day', 'Time of Day') }}</label>
                                <input type="time" class="form-control" id="time-of-day" name="time_of_day">
                            </div>
                        </div>
                    </div>
                    <!-- Recuring Task -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="recurring-task-switch" class="form-label">
                                {{ get_label('enable_recurring_task', 'Enable Recurring Task') }}
                            </label>
                            <i class="bx bx-info-circle text-primary" data-bs-toggle="tooltip"
                                data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title=""
                                data-bs-original-title="<b>{{ get_label('recurring_tasks', 'Recurring Tasks') }}:</b> {{ get_label('recurring_tasks_info', 'This option enables the creation of recurring tasks. You can set the frequency (daily, weekly, monthly, yearly), specific days, and manage the recurrence schedule efficiently.') }}">
                            </i>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="recurring-task-switch"
                                    name="enable_recurring_task">
                                <label class="form-check-label" for="recurring-task-switch">
                                    {{ get_label('enable_recurring_task', 'Enable Recurring Task') }}
                                </label>
                            </div>
                        </div>
                        <div id="recurring-task-settings" class="d-none">
                            <!-- Frequency Type -->
                            <div class="mb-3">
                                <label for="recurrence-frequency" class="form-label">
                                    {{ get_label('recurrence_frequency', 'Recurrence Frequency') }}
                                </label>
                                <select class="form-select" id="recurrence-frequency" name="recurrence_frequency"
                                    required>
                                    <option value="daily">{{ get_label('daily', 'Daily') }}</option>
                                    <option value="weekly">{{ get_label('weekly', 'Weekly') }}</option>
                                    <option value="monthly">{{ get_label('monthly', 'Monthly') }}</option>
                                    <option value="yearly">{{ get_label('yearly', 'Yearly') }}</option>
                                </select>
                            </div>
                            <!-- Day of Week (Weekly Only) -->
                            <div class="d-none mb-3" id="recurrence-day-of-week-group">
                                <label for="recurrence-day-of-week" class="form-label">
                                    {{ get_label('day_of_the_week', 'Day of the Week') }}
                                </label>
                                <select class="form-select" id="recurrence-day-of-week"
                                    name="recurrence_day_of_week">
                                    <option value="">{{ get_label('any_day', 'Any Day') }}</option>
                                    <option value="1">{{ get_label('monday', 'Monday') }}</option>
                                    <option value="2">{{ get_label('tuesday', 'Tuesday') }}</option>
                                    <option value="3">{{ get_label('wednesday', 'Wednesday') }}</option>
                                    <option value="4">{{ get_label('thursday', 'Thursday') }}</option>
                                    <option value="5">{{ get_label('friday', 'Friday') }}</option>
                                    <option value="6">{{ get_label('saturday', 'Saturday') }}</option>
                                    <option value="7">{{ get_label('sunday', 'Sunday') }}</option>
                                </select>
                            </div>
                            <!-- Day of Month (Monthly and Yearly Only) -->
                            <div class="d-none mb-3" id="recurrence-day-of-month-group">
                                <label for="recurrence-day-of-month" class="form-label">
                                    {{ get_label('day_of_the_month', 'Day of the Month') }}
                                </label>
                                <select class="form-select" id="recurrence-day-of-month"
                                    name="recurrence_day_of_month">
                                    <option value="">{{ get_label('any_day', 'Any Day') }}</option>
                                    @foreach (range(1, 31) as $day)
                                        <option value="{{ $day }}">{{ $day }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Month of Year (Yearly Only) -->
                            <div class="d-none mb-3" id="recurrence-month-of-year-group">
                                <label for="recurrence-month-of-year" class="form-label">
                                    {{ get_label('month_of_the_year', 'Month of the Year') }}
                                </label>
                                <select class="form-select" id="recurrence-month-of-year"
                                    name="recurrence_month_of_year">
                                    <option value="">{{ get_label('any_month', 'Any Month') }}</option>
                                    @foreach (range(1, 12) as $month)
                                        <option value="{{ $month }}">
                                            {{ \Carbon\Carbon::create()->month($month)->format('F') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Starts From -->
                            <div class="mb-3">
                                <label for="recurrence-starts-from" class="form-label">
                                    {{ get_label('starts_from', 'Starts From') }}
                                </label>
                                <input type="date" class="form-control" id="recurrence-starts-from"
                                    name="recurrence_starts_from">
                            </div>
                            <!-- Number of Occurrences -->
                            <div class="mb-3">
                                <label for="recurrence-occurrences" class="form-label">
                                    {{ get_label('number_of_occurrences', 'Number of Occurrences') }}
                                </label>
                                <input type="number" class="form-control" id="recurrence-occurrences"
                                    name="recurrence_occurrences" min="1">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('tasks') ||
        Request::is('tasks/draggable') ||
        Request::is('tasks/calendar') ||
        Request::is('projects/tasks/calendar/*') ||
        Request::is('projects/tasks/draggable/*') ||
        Request::is('projects/tasks/list/*') ||
        Request::is('tasks/information/*') ||
        Request::is('home') ||
        Request::is('users/profile/*') ||
        Request::is('clients/profile/*') ||
        Request::is('projects/information/*') ||
        Request::is('users') ||
        Request::is('clients'))
    <div class="modal fade" id="edit_task_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ url('tasks/update') }}" class="form-submit-event modal-content" method="POST">
                <input type="hidden" name="id" id="id">
                @if (
                    !Request::is('projects/tasks/draggable/*') &&
                        !Request::is('tasks/draggable') &&
                        !Request::is('tasks/calendar') &&
                        !Request::is('projects/tasks/calendar/*') &&
                        !Request::is('tasks/information/*'))
                    <input type="hidden" name="dnr">
                    <input type="hidden" name="table" value="task_table">
                @endif
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_task', 'Update Task') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" id="title" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>"
                                value="{{ old('title') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="status"><?= get_label('status', 'Status') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select statusDropdown" name="status_id" id="task_status_id">
                                @isset($statuses)
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}" data-color="{{ $status->color }}"
                                            {{ old('status') == $status->id ? 'selected' : '' }}>{{ $status->title }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateStatusModal"><button type="button"
                                        class="btn btn-sm btn-primary action_create_statuses"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_status', 'Create status') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="{{ url('status/manage') }}"><button type="button"
                                        class="btn btn-sm btn-primary action_manage_statuses"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_statuses', 'Manage statuses') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= get_label('priority', 'Priority') ?></label>
                            <select class="form-select priorityDropdown" name="priority_id" id="priority_id"
                                data-placeholder="<?= get_label('please_select', 'Please select') ?>">
                                <option></option>
                                @isset($priorities)
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority->id }}" data-color="{{ $priority->color }}">
                                            {{ $priority->title }}</option>
                                    @endforeach
                                @endisset
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreatePriorityModal"><button
                                        type="button" class="btn btn-sm btn-primary action_create_priorities"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_priority', 'Create Priority') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="{{ url('priority/manage') }}"><button type="button"
                                        class="btn btn-sm btn-primary action_manage_priorities"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_priorities', 'Manage Priorities') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="project_title" class="form-label"><?= get_label('project', 'Project') ?>
                                <span class="asterisk">*</span></label>
                            <input class="form-control" type="text" id="update_project_title" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"
                                for="user_id"><?= get_label('select_users', 'Select users') ?> <span
                                    id="task_update_users_associated_with_project"></span></label>
                            <select class="form-control" name="user_id[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"
                                for="start_date"><?= get_label('starts_at', 'Starts at') ?></label>
                            <input type="text" id="update_start_date" name="start_date" class="form-control"
                                placeholder="{{ get_label('please_select', 'Please Select') }}"
                                autocomplete="off">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="due_date"><?= get_label('ends_at', 'Ends at') ?></label>
                            <input type="text" id="update_end_date" name="due_date" class="form-control"
                                placeholder="{{ get_label('please_select', 'Please Select') }}"
                                autocomplete="off">
                        </div>
                    </div>
                    @if ($isAdminOrHasAllDataAccess)
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-check-label"
                                    for="updateClientCanDiscussTask">{{ get_label('client_can_discuss', 'Client Can Discuss') }}?</label>
                                <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="{{ get_label('client_can_discuss_info_task', 'Allows the client to participate in task discussions.') }}"></i>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                        id="updateClientCanDiscussTask" name="clientCanDiscuss">
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="mb-3">
                            <label for="description"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control description" id="task_description" rows="5" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"><?= get_label('note', 'Note') ?></label>
                            <textarea class="form-control" name="note" rows="3" id="taskNote"
                                placeholder="<?= get_label('optional_note', 'Optional Note') ?>"></textarea>
                        </div>
                    </div>
                    <!-- edit Remider Task -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="reminder-switch"
                                class="form-label">{{ get_label('enable_reminder', 'Enable Reminder') }}</label>
                            <i class="bx bx-info-circle text-primary"
                                data-bs-toggle="tooltip"data-bs-offset="0,4" data-bs-placement="top"
                                data-bs-html="true"title=""
                                data-bs-original-title="<b>{{ get_label('task_reminder', 'Task Reminder') }}:</b> {{ get_label('task_reminder_info', 'Enable this option to set reminders for tasks. You can configure reminder frequencies (daily, weekly, or monthly), specific times, and customize alerts to ensure you stay on track with task deadlines.') }}"></i>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="edit-reminder-switch"
                                    name="enable_reminder">
                                <label class="form-check-label"
                                    for="reminder-switch">{{ get_label('enable_task_reminder', 'Enable Task Reminder') }}</label>
                            </div>
                        </div>
                     </div>
                     
                    <div id="edit-reminder-settings" class="d-none">
                        <!-- Frequency Type -->
                        <div class="mb-3">
                            <label for="frequency-type"
                                class="form-label">{{ get_label('frequency_type', 'Frequency Type') }}</label>
                            <select class="form-select" id="edit-frequency-type" name="frequency_type"
                                required>
                                <option value="daily">{{ get_label('daily', 'Daily') }}</option>
                                <option value="weekly">{{ get_label('weekly', 'Weekly') }}</option>
                                <option value="monthly">{{ get_label('monthly', 'Monthly') }}</option>
                            </select>
                        </div>
                        <!-- Day of Week (Weekly Only) -->
                        <div class="d-none mb-3" id="edit-day-of-week-group">
                            <label for="day-of-week"
                                class="form-label">{{ get_label('day_of_the_week', 'Day of the Week') }}</label>
                            <select class="form-select" id="edit-day-of-week" name="day_of_week">
                                <option value="">{{ get_label('any_day', 'Any Day') }}</option>
                                <option value="1">{{ get_label('monday', 'Monday') }}</option>
                                <option value="2">{{ get_label('tuesday', 'Tuesday') }}</option>
                                <option value="3">{{ get_label('wednesday', 'Wednesday') }}</option>
                                <option value="4">{{ get_label('thursday', 'Thursday') }}</option>
                                <option value="5">{{ get_label('friday', 'Friday') }}</option>
                                <option value="6">{{ get_label('saturday', 'Saturday') }}</option>
                                <option value="7">{{ get_label('sunday', 'Sunday') }}</option>
                            </select>
                        </div>
                        <!-- Day of Month (Monthly Only) -->
                        <div class="d-none mb-3" id="edit-day-of-month-group">
                            <label for="day-of-month"
                                class="form-label">{{ get_label('day_of_the_month', 'Day of the Month') }}</label>
                            <select class="form-select" id="edit-day-of-month" name="day_of_month">
                                <option value="">{{ get_label('any_day', 'Any Day') }}</option>
                                @foreach (range(1, 31) as $day)
                                    <option value="{{ $day }}">{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Time of Day -->
                        <div class="mb-3">
                            <label for="time-of-day"
                                class="form-label">{{ get_label('time_of_day', 'Time of Day') }}</label>
                            <input type="time" class="form-control" id="edit-time-of-day"
                                name="time_of_day">
                        </div>
                        
                    </div>
                   
                    <!--edit Recursion Task -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="edit-recurring-task-switch" class="form-label">
                                {{ get_label('enable_recurring_task', 'Enable Recurring Task') }}
                            </label>
                            <i class="bx bx-info-circle text-primary" data-bs-toggle="tooltip"
                                data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title=""
                                data-bs-original-title="<b>{{ get_label('recurring_tasks', 'Recurring Tasks') }}:</b> {{ get_label('recurring_tasks_info', 'This option enables the creation of recurring tasks. You can set the frequency (daily, weekly, monthly, yearly), specific days, and manage the recurrence schedule efficiently.') }}">
                            </i>
                            <div class="form-check form-switch">

                                <input type="checkbox" class="form-check-input" id="edit-recurring-task-switch"
                                    name="enable_recurring_task"
                                    {{ isset($task) && $task->recurringTask ? 'checked' : '' }}>
                                <label class="form-check-label" for="edit-recurring-task-switch">
                                    {{ get_label('enable_recurring_task', 'Enable Recurring Task') }}
                                </label>
                            </div>
                        </div>
                        <div id="edit-recurring-task-settings"
                            class="{{ isset($task) && $task->recurringTask ? '' : 'd-none' }}">
                            <!-- Frequency Type -->
                            <div class="mb-3">
                                <label for="edit-recurrence-frequency" class="form-label">
                                    {{ get_label('recurrence_frequency', 'Recurrence Frequency') }}
                                </label>
                                <select class="form-select" id="edit-recurrence-frequency"
                                    name="recurrence_frequency" required>
                                    <option value="daily"
                                        {{ isset($task) && optional($task->recurringTask)->frequency == 'daily' ? 'selected' : '' }}>
                                        {{ get_label('daily', 'Daily') }}
                                    </option>
                                    <option value="weekly"
                                        {{ isset($task) && optional($task->recurringTask)->frequency == 'weekly' ? 'selected' : '' }}>
                                        {{ get_label('weekly', 'Weekly') }}
                                    </option>
                                    <option value="monthly"
                                        {{ isset($task) && optional($task->recurringTask)->frequency == 'monthly' ? 'selected' : '' }}>
                                        {{ get_label('monthly', 'Monthly') }}
                                    </option>
                                    <option value="yearly"
                                        {{ isset($task) && optional($task->recurringTask)->frequency == 'yearly' ? 'selected' : '' }}>
                                        {{ get_label('yearly', 'Yearly') }}
                                    </option>
                                </select>
                            </div>
                            <!-- Day of Week (Weekly Only) -->
                            <div class="{{ isset($task) && optional($task->recurringTask)->frequency == 'weekly' ? '' : 'd-none' }} mb-3"
                                id="edit-recurrence-day-of-week-group">
                                <label for="edit-recurrence-day-of-week" class="form-label">
                                    {{ get_label('day_of_the_week', 'Day of the Week') }}
                                </label>
                                <select class="form-select" id="edit-recurrence-day-of-week"
                                    name="recurrence_day_of_week">
                                    <option value="">{{ get_label('any_day', 'Any Day') }}</option>
                                    <option value="1"
                                        {{ isset($task) && optional($task->recurringTask)->day_of_week == '1' ? 'selected' : '' }}>
                                        {{ get_label('monday', 'Monday') }}
                                    </option>
                                    <option value="2"
                                        {{ isset($task) && optional($task->recurringTask)->day_of_week == '2' ? 'selected' : '' }}>
                                        {{ get_label('tuesday', 'Tuesday') }}
                                    </option>
                                    <option value="3"
                                        {{ isset($task) && optional($task->recurringTask)->day_of_week == '3' ? 'selected' : '' }}>
                                        {{ get_label('wednesday', 'Wednesday') }}
                                    </option>
                                    <option value="4"
                                        {{ isset($task) && optional($task->recurringTask)->day_of_week == '4' ? 'selected' : '' }}>
                                        {{ get_label('thursday', 'Thursday') }}
                                    </option>
                                    <option value="5"
                                        {{ isset($task) && optional($task->recurringTask)->day_of_week == '5' ? 'selected' : '' }}>
                                        {{ get_label('friday', 'Friday') }}
                                    </option>
                                    <option value="6"
                                        {{ isset($task) && optional($task->recurringTask)->day_of_week == '6' ? 'selected' : '' }}>
                                        {{ get_label('saturday', 'Saturday') }}
                                    </option>
                                    <option value="7"
                                        {{ isset($task) && optional($task->recurringTask)->day_of_week == '7' ? 'selected' : '' }}>
                                        {{ get_label('sunday', 'Sunday') }}
                                    </option>
                                </select>
                            </div>
                            <!-- Day of Month (Monthly and Yearly Only) -->
                            <div class="{{ isset($task) && in_array(optional($task->recurringTask)->frequency, ['monthly', 'yearly']) ? '' : 'd-none' }} mb-3"
                                id="edit-recurrence-day-of-month-group">
                                <label for="edit-recurrence-day-of-month" class="form-label">
                                    {{ get_label('day_of_the_month', 'Day of the Month') }}
                                </label>
                                <select class="form-select" id="edit-recurrence-day-of-month"
                                    name="recurrence_day_of_month">
                                    <option value="">{{ get_label('any_day', 'Any Day') }}</option>
                                    @foreach (range(1, 31) as $day)
                                        <option value="{{ $day }}"
                                            {{ isset($task) && optional($task->recurringTask)->day_of_month == $day ? 'selected' : '' }}>
                                            {{ $day }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Month of Year (Yearly Only) -->
                            <div class="{{ isset($task) && optional($task->recurringTask)->frequency == 'yearly' ? '' : 'd-none' }} mb-3"
                                id="edit-recurrence-month-of-year-group">
                                <label for="edit-recurrence-month-of-year" class="form-label">
                                    {{ get_label('month_of_the_year', 'Month of the Year') }}
                                </label>
                                <select class="form-select" id="edit-recurrence-month-of-year"
                                    name="recurrence_month_of_year">
                                    <option value="">{{ get_label('any_month', 'Any Month') }}</option>
                                    @foreach (range(1, 12) as $month)
                                        <option value="{{ $month }}"
                                            {{ isset($task) && optional($task->recurringTask)->month_of_year == $month ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Starts From -->
                            <div class="mb-3">
                                <label for="edit-recurrence-starts-from" class="form-label">
                                    {{ get_label('starts_from', 'Starts From') }}
                                </label>
                                <input type="date" class="form-control" id="edit-recurrence-starts-from"
                                    name="recurrence_starts_from"
                                    value="{{ isset($task) && optional($task->recurringTask)->starts_from ? \Carbon\Carbon::parse($task->recurringTask->starts_from)->format('Y-m-d') : '' }}">
                            </div>
                            <!-- Number of Occurrences -->
                            <div class="mb-3">
                                <label for="edit-recurrence-occurrences" class="form-label">
                                    {{ get_label('number_of_occurrences', 'Number of Occurrences') }}
                                </label>
                                <input type="number" class="form-control" id="edit-recurrence-occurrences"
                                    name="recurrence_occurrences" min="1"
                                    value="{{ isset($task) ? optional($task->recurringTask)->number_of_occurrences : '' }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
@endif
<div class="modal fade" id="confirmUpdateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('confirm_update_status', 'Do You Want to Update the Status?') ?></p>
                <textarea class="form-control" id="statusNote" placeholder="<?= get_label('optional_note', 'Optional Note') ?>"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="declineUpdateStatus"
                    data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="confirmUpdateStatus"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmUpdatePriorityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('confirm_update_priority', 'Do You Want to Update the Priority?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="declineUpdatePriority"
                    data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="confirmUpdatePriority"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
@if (
    (Request::is('projects*') && !Request::is('projects/information/*')) ||
        Request::is('home') ||
        Request::is('users/profile/*') ||
        Request::is('clients/profile/*'))
    <div class="modal fade" id="create_project_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ url('projects/store') }}" class="form-submit-event modal-content" method="POST">
                @if (
                    !Request::is('projects') &&
                        !Request::is('projects/kanban') &&
                        !Request::is('projects/favorite') &&
                        !Request::is('projects/kanban/favorite') &&
                        !Request::is('projects/gantt-chart') &&
                        !Request::is('projects/gantt-chart/favorite'))
                    <input type="hidden" name="dnr">
                    <input type="hidden" name="table" value="projects_table">
                @endif
                <input type="hidden" name="is_favorite" id="is_favorite" value="0">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_project', 'Create Project') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>"
                                value="{{ old('title') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="status"><?= get_label('status', 'Status') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-control statusDropdown" name="status_id">
                                @isset($statuses)
                                    @foreach ($statuses as $status)
                                        @if (canSetStatus($status))
                                            <option value="{{ $status->id }}" data-color="{{ $status->color }}"
                                                {{ old('status') == $status->id ? 'selected' : '' }}>
                                                {{ $status->title }}</option>
                                        @endif
                                    @endforeach
                                @endisset
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateStatusModal"><button type="button"
                                        class="btn btn-sm btn-primary action_create_statuses"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_status', 'Create status') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="{{ url('status/manage') }}"><button type="button"
                                        class="btn btn-sm btn-primary action_manage_statuses"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_statuses', 'Manage statuses') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= get_label('priority', 'Priority') ?></label>
                            <select class="form-select priorityDropdown" name="priority_id"
                                data-placeholder="<?= get_label('please_select', 'Please select') ?>">
                                <option></option>
                                @isset($priorities)
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority->id }}" data-color="{{ $priority->color }}">
                                            {{ $priority->title }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreatePriorityModal"><button
                                        type="button" class="btn btn-sm btn-primary action_create_priorities"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_priority', 'Create Priority') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="{{ url('priority/manage') }}"><button type="button"
                                        class="btn btn-sm btn-primary action_manage_priorities"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_priorities', 'Manage Priorities') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="budget" class="form-label"><?= get_label('budget', 'Budget') ?></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">{{ $general_settings['currency_symbol'] }}</span>
                                <input class="form-control currency" type="text" id="budget"
                                    name="budget"
                                    placeholder="<?= get_label('please_enter_budget', 'Please enter budget') ?>"
                                    value="{{ old('budget') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"
                                for="start_date"><?= get_label('starts_at', 'Starts at') ?></label>
                            <input type="text" id="start_date" name="start_date" class="form-control"
                                placeholder="{{ get_label('please_select', 'Please Select') }}"
                                autocomplete="off">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="due_date"><?= get_label('ends_at', 'Ends at') ?></label>
                            <input type="text" id="end_date" name="end_date" class="form-control"
                                placeholder="{{ get_label('please_select', 'Please Select') }}"
                                autocomplete="off">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="">
                                <?= get_label('task_accessibility', 'Task Accessibility') ?>
                                <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                    data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true"
                                    title=""
                                    data-bs-original-title="<b>{{ get_label('assigned_users', 'Assigned Users') }}:</b> {{ get_label('assigned_users_info', 'You Will Need to Manually Select Task Users When Creating Tasks Under This Project.') }} <br><b>{{ get_label('project_users', 'Project Users') }}:</b> {{ get_label('project_users_info', 'When Creating Tasks Under This Project, the Task Users Selection Will Be Automatically Filled With Project Users.') }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top"></i>
                            </label>
                            <select class="form-select" name="task_accessibility">
                                <option value="assigned_users"><?= get_label('assigned_users', 'Assigned Users') ?>
                                </option>
                                <option value="project_users"><?= get_label('project_users', 'Project Users') ?>
                                </option>
                            </select>
                        </div>
                        @if ($isAdminOrHasAllDataAccess)
                            <div class="col-md-6 mb-3">
                                <label class="form-check-label"
                                    for="clientCanDiscussProject">{{ get_label('client_can_discuss', 'Client Can Discuss') }}?</label>
                                <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="{{ get_label('client_can_discuss_info', 'Allows the client to participate in project discussions.') }}"></i>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="clientCanDiscussProject"
                                        name="clientCanDiscuss">
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"
                                for="user_id"><?= get_label('select_users', 'Select users') ?></label>
                            <select class="form-control users_select" name="user_id[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                @if ($guard == 'web')
                                    <option value="{{ $auth_user->id }}" selected>{{ $auth_user->first_name }}
                                        {{ $auth_user->last_name }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"
                                for="client_id"><?= get_label('select_clients', 'Select clients') ?></label>
                            <select class="form-control clients_select" name="client_id[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                @if ($guard == 'client')
                                    <option value="{{ $auth_user->id }}" selected>{{ $auth_user->first_name }}
                                        {{ $auth_user->last_name }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label"
                                for=""><?= get_label('select_tags', 'Select tags') ?></label>
                            <select class="form-control tags_select" name="tag_ids[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateTagModal"><button type="button"
                                        class="btn btn-sm btn-primary action_create_tags" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_tag', 'Create tag') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="{{ url('tags/manage') }}"><button type="button"
                                        class="btn btn-sm btn-primary action_manage_tags" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_tags', 'Manage tags') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="description"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control description" rows="5" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"><?= get_label('note', 'Note') ?></label>
                            <textarea class="form-control" name="note" rows="3"
                                placeholder="<?= get_label('optional_note', 'Optional Note') ?>"></textarea>
                        </div>
                    </div>
                    @if (!$isAdminOrHasAllDataAccess)
                        <div class="alert alert-primary" role="alert">
                            <?= get_label('you_will_be_project_participant_automatically', 'You will be project participant automatically.') ?>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('projects*') ||
        Request::is('home') ||
        Request::is('users/profile/*') ||
        Request::is('clients/profile/*') ||
        Request::is('users') ||
        Request::is('clients'))
    <div class="modal fade" id="edit_project_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ url('projects/update') }}" class="form-submit-event modal-content" method="POST">
                <input type="hidden" name="id" id="project_id">
                @if (
                    !Request::is([
                        'projects',
                        'projects/information/*',
                        'projects/kanban',
                        'projects/favorite',
                        'projects/kanban/favorite',
                        'projects/gantt-chart/favorite',
                    ]))
                    <input type="hidden" name="dnr">
                    <input type="hidden" name="table" value="projects_table">
                @endif

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_project', 'Update Project') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" name="title" id="project_title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>"
                                value="{{ old('title') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="status"><?= get_label('status', 'Status') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-control statusDropdown" name="status_id" id="project_status_id">
                                @isset($statuses)
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}" data-color="{{ $status->color }}"
                                            {{ old('status') == $status->id ? 'selected' : '' }}>{{ $status->title }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateStatusModal"><button type="button"
                                        class="btn btn-sm btn-primary action_create_statuses"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_status', 'Create status') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="{{ url('status/manage') }}"><button type="button"
                                        class="btn btn-sm btn-primary action_manage_statuses"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_statuses', 'Manage statuses') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= get_label('priority', 'Priority') ?></label>
                            <select class="form-select priorityDropdown" name="priority_id"
                                id="project_priority_id"
                                data-placeholder="<?= get_label('please_select', 'Please select') ?>">
                                <option></option>
                                @isset($priorities)
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority->id }}" data-color="{{ $priority->color }}">
                                            {{ $priority->title }}</option>
                                    @endforeach
                                @endisset
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreatePriorityModal"><button
                                        type="button" class="btn btn-sm btn-primary action_create_priorities"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_priority', 'Create Priority') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="{{ url('priority/manage') }}"><button type="button"
                                        class="btn btn-sm btn-primary action_manage_priorities"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_priorities', 'Manage Priorities') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="budget" class="form-label"><?= get_label('budget', 'Budget') ?></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">{{ $general_settings['currency_symbol'] }}</span>
                                <input class="form-control currency" type="text" id="project_budget"
                                    name="budget"
                                    placeholder="<?= get_label('please_enter_budget', 'Please enter budget') ?>"
                                    value="{{ old('budget') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"
                                for="start_date"><?= get_label('starts_at', 'Starts at') ?></label>
                            <input type="text" id="update_start_date" name="start_date" class="form-control"
                                placeholder="{{ get_label('please_select', 'Please Select') }}"
                                autocomplete="off">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="due_date"><?= get_label('ends_at', 'Ends at') ?></label>
                            <input type="text" id="update_end_date" name="end_date" class="form-control"
                                placeholder="{{ get_label('please_select', 'Please Select') }}"
                                autocomplete="off">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="">
                                <?= get_label('task_accessibility', 'Task Accessibility') ?>
                                <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                    data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true"
                                    title=""
                                    data-bs-original-title="<b>{{ get_label('assigned_users', 'Assigned Users') }}:</b> {{ get_label('assigned_users_info', 'You Will Need to Manually Select Task Users When Creating Tasks Under This Project.') }}<br><b>{{ get_label('project_users', 'Project Users') }}:</b> {{ get_label('project_users_info', 'When Creating Tasks Under This Project, the Task Users Selection Will Be Automatically Filled With Project Users.') }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top"></i>
                            </label>
                            <select class="form-select" name="task_accessibility" id="task_accessibility">
                                <option value="assigned_users"><?= get_label('assigned_users', 'Assigned Users') ?>
                                </option>
                                <option value="project_users"><?= get_label('project_users', 'Project Users') ?>
                                </option>
                            </select>
                        </div>
                        @if ($isAdminOrHasAllDataAccess)
                            <div class="col-md-6 mb-3">
                                <label class="form-check-label"
                                    for="updateClientCanDiscussProject">{{ get_label('client_can_discuss', 'Client Can Discuss') }}?</label>
                                <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="{{ get_label('client_can_discuss_info', 'Allows the client to participate in project discussions.') }}"></i>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                        id="updateClientCanDiscussProject" name="clientCanDiscuss">
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"
                                for="user_id"><?= get_label('select_users', 'Select users') ?></label>
                            <select class="form-control users_select" name="user_id[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"
                                for="client_id"><?= get_label('select_clients', 'Select clients') ?></label>
                            <select class="form-control clients_select" name="client_id[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label"
                                for=""><?= get_label('select_tags', 'Select tags') ?></label>
                            <select class="form-control tags_select" name="tag_ids[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateTagModal"><button type="button"
                                        class="btn btn-sm btn-primary action_create_tags" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_tag', 'Create tag') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="{{ url('tags/manage') }}"><button type="button"
                                        class="btn btn-sm btn-primary action_manage_tags" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_tags', 'Manage tags') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="description"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control description" rows="5" name="description" id="project_description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"><?= get_label('note', 'Note') ?></label>
                            <textarea class="form-control" name="note" id="projectNote" rows="3"
                                placeholder="<?= get_label('optional_note', 'Optional Note') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
@endif
@if (Request::is('projects') ||
        Request::is('projects/list') ||
        Request::is('projects/kanban') ||
        Request::is('home') ||
        Request::is('users/profile/*') ||
        Request::is('clients/profile/*') ||
        Request::is('tasks') ||
        Request::is('tasks/draggable') ||
        Request::is('projects/information/*') ||
        Request::is('users') ||
        Request::is('clients'))
    <div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><span id="typePlaceholder"></span>
                        <?= get_label('quick_view', 'Quick View') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 id="quickViewTitlePlaceholder" class="text-muted"></h5>
                    <div class="nav-align-top">
                        <ul class="nav nav-tabs" role="tablist">
                            @if ($auth_user->can('manage_users'))
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab"
                                        data-bs-toggle="tab" data-bs-target="#navs-top-quick-view-users"
                                        aria-controls="navs-top-quick-view-users">
                                        <i
                                            class="menu-icon tf-icons bx bx-group text-primary"></i><?= get_label('users', 'Users') ?>
                                    </button>
                                </li>
                            @endif
                            @if ($auth_user->can('manage_clients'))
                                <li class="nav-item">
                                    <button type="button"
                                        class="nav-link {{ !$auth_user->can('manage_users') ? 'active' : '' }}"
                                        role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-top-quick-view-clients"
                                        aria-controls="navs-top-quick-view-clients">
                                        <i
                                            class="menu-icon tf-icons bx bx-group text-warning"></i><?= get_label('clients', 'Clients') ?>
                                    </button>
                                </li>
                            @endif
                            <li class="nav-item">
                                <button type="button"
                                    class="nav-link {{ !$auth_user->can('manage_users') && !$auth_user->can('manage_clients') ? 'active' : '' }}"
                                    role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-top-quick-view-description"
                                    aria-controls="navs-top-quick-view-description">
                                    <i
                                        class="menu-icon tf-icons bx bx-notepad text-success"></i><?= get_label('description', 'Description') ?>
                                </button>
                            </li>
                        </ul>
                        <input type="hidden" id="type">
                        <input type="hidden" id="typeId">
                        <div class="tab-content">
                            @if ($auth_user->can('manage_users'))
                                <div class="tab-pane fade active show" id="navs-top-quick-view-users"
                                    role="tabpanel">
                                    <div class="table-responsive text-nowrap">
                                        <!-- <input type="hidden" id="data_type" value="users">
                                <input type="hidden" id="data_table" value="usersTable"> -->
                                        <table id="usersTable" data-toggle="table"
                                            data-loading-template="loadingTemplate"
                                            data-url="{{ url('/users/list') }}" data-icons-prefix="bx"
                                            data-icons="icons" data-show-refresh="true" data-total-field="total"
                                            data-trim-on-search="false" data-data-field="rows"
                                            data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                                            data-side-pagination="server" data-show-columns="true"
                                            data-pagination="true" data-sort-name="id" data-sort-order="desc"
                                            data-mobile-responsive="true"
                                            data-query-params="queryParamsUsersClients">
                                            <thead>
                                                <tr>
                                                    <th data-checkbox="true"></th>
                                                    <th data-sortable="true" data-field="id">
                                                        <?= get_label('id', 'ID') ?></th>
                                                    <th data-field="profile"><?= get_label('users', 'Users') ?></th>
                                                    <th data-field="role"><?= get_label('role', 'Role') ?></th>
                                                    <th data-field="phone" data-sortable="true"
                                                        data-visible="false">
                                                        <?= get_label('phone_number', 'Phone number') ?></th>
                                                    <th data-sortable="true" data-field="created_at"
                                                        data-visible="false">
                                                        <?= get_label('created_at', 'Created at') ?></th>
                                                    <th data-sortable="true" data-field="updated_at"
                                                        data-visible="false">
                                                        <?= get_label('updated_at', 'Updated at') ?></th>
                                                    {{-- <th data-formatter="actionFormatterUsers"><?= get_label('actions', 'Actions') ?></th> --}}
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            @if ($auth_user->can('manage_clients'))
                                <div class="tab-pane fade {{ !$auth_user->can('manage_users') ? 'active show' : '' }}"
                                    id="navs-top-quick-view-clients" role="tabpanel">
                                    <div class="table-responsive text-nowrap">
                                        <!-- <input type="hidden" id="data_type" value="clients">
                            <input type="hidden" id="data_table" value="clientsTable"> -->
                                        <table id="clientsTable" data-toggle="table"
                                            data-loading-template="loadingTemplate"
                                            data-url="{{ url('/clients/list') }}" data-icons-prefix="bx"
                                            data-icons="icons" data-show-refresh="true" data-total-field="total"
                                            data-trim-on-search="false" data-data-field="rows"
                                            data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                                            data-side-pagination="server" data-show-columns="true"
                                            data-pagination="true" data-sort-name="id" data-sort-order="desc"
                                            data-mobile-responsive="true"
                                            data-query-params="queryParamsUsersClients">
                                            <thead>
                                                <tr>
                                                    <th data-checkbox="true"></th>
                                                    <th data-sortable="true" data-field="id">
                                                        <?= get_label('id', 'ID') ?></th>
                                                    <th data-field="profile"><?= get_label('client', 'Client') ?></th>
                                                    <th data-field="company" data-sortable="true"
                                                        data-visible="false"><?= get_label('company', 'Company') ?>
                                                    </th>
                                                    <th data-field="phone" data-sortable="true"
                                                        data-visible="false">
                                                        <?= get_label('phone_number', 'Phone number') ?></th>
                                                    <th data-sortable="true" data-field="created_at"
                                                        data-visible="false">
                                                        <?= get_label('created_at', 'Created at') ?></th>
                                                    <th data-sortable="true" data-field="updated_at"
                                                        data-visible="false">
                                                        <?= get_label('updated_at', 'Updated at') ?></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            <div class="tab-pane fade {{ !$auth_user->can('manage_users') && !$auth_user->can('manage_clients') ? 'active show' : '' }}"
                                id="navs-top-quick-view-description" role="tabpanel">
                                <p class="pt-3" id="quickViewDescPlaceholder"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
<div class="modal fade" id="createWorkspaceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ get_label('create_workspace', 'Create Workspace') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('workspaces/store') }}" class="form-submit-event" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3">
                            <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" id="title" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>"
                                value="{{ old('title') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"
                                for="user_id"><?= get_label('select_users', 'Select users') ?></label>
                            <select class="form-control users_select" name="user_ids[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                data-consider-workspace="false">
                                @if ($guard == 'web')
                                    <option value="{{ $auth_user->id }}" selected>{{ $auth_user->first_name }}
                                        {{ $auth_user->last_name }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"
                                for="client_id"><?= get_label('select_clients', 'Select clients') ?></label>
                            <select class="form-control clients_select" name="client_ids[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                data-consider-workspace="false">
                                @if ($guard == 'client')
                                    <option value="{{ $auth_user->id }}" selected>{{ $auth_user->first_name }}
                                        {{ $auth_user->last_name }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        @if ($isAdminOrHasAllDataAccess)
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <label class="form-check-label" for="primaryWorkspace">
                                        <input class="form-check-input" type="checkbox" name="primaryWorkspace"
                                            id="primaryWorkspace">
                                        <?= get_label('primary_workspace', 'Primary Workspace') ?>?
                                        <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                            data-bs-offset="0,4" data-bs-placement="top"
                                            title="{{ get_label('primary_workspace_info', 'This workspace will be assigned upon new signup.') }}"></i>
                                    </label>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if (!$isAdminOrHasAllDataAccess)
                        <div class="alert alert-primary alert-dismissible" role="alert">
                            <?= get_label('you_will_be_workspace_participant_automatically', 'You will be workspace participant automatically.') ?>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"><?= get_label('close', 'Close') ?></button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="editWorkspaceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ get_label('update_workspace', 'Update Workspace') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('workspaces/update') }}" class="form-submit-event" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" name="id" id="workspace_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3">
                            <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" name="title" id="workspace_title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>"
                                value="{{ old('title') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"
                                for="user_id"><?= get_label('select_users', 'Select users') ?></label>
                            <select class="form-control users_select" name="user_ids[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                data-consider-workspace="false">
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"
                                for="client_id"><?= get_label('select_clients', 'Select clients') ?></label>
                            <select class="form-control clients_select" name="client_ids[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                data-consider-workspace="false">
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        @if ($isAdminOrHasAllDataAccess)
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <label class="form-check-label" for="updatePrimaryWorkspace">
                                        <input class="form-check-input" type="checkbox" name="primaryWorkspace"
                                            id="updatePrimaryWorkspace">
                                        <?= get_label('primary_workspace', 'Primary Workspace') ?>?
                                        <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                            data-bs-offset="0,4" data-bs-placement="top"
                                            title="{{ get_label('primary_workspace_info', 'This workspace will be assigned upon new signup.') }}"></i>
                                    </label>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"><?= get_label('close', 'Close') ?></button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
@if (Request::is('meetings'))
    <div class="modal fade" id="createMeetingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ get_label('create_meeting', 'Create Meeting') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ url('meetings/store') }}" class="form-submit-event" method="POST">
                    <input type="hidden" name="dnr">
                    <input type="hidden" name="table" value="meetings_table">
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3">
                                <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                        class="asterisk">*</span></label>
                                <input class="form-control" type="text" name="title"
                                    placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for=""><?= get_label('starts_at', 'Starts at') ?>
                                    <span class="asterisk">*</span></label>
                                <input type="text" id="start_date" name="start_date" class="form-control"
                                    value="">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" for=""><?= get_label('time', 'Time') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="time" name="start_time" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="end_date_time"><?= get_label('ends_at', 'Ends at') ?>
                                    <span class="asterisk">*</span></label>
                                <input type="text" id="end_date" name="end_date" class="form-control"
                                    value="">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" for=""><?= get_label('time', 'Time') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="time" name="end_time" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label"
                                    for="user_id"><?= get_label('select_users', 'Select users') ?></label>
                                <select class="form-control users_select" name="user_ids[]" multiple="multiple"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                    @if ($guard == 'web')
                                        <option value="{{ $auth_user->id }}" selected>{{ $auth_user->first_name }}
                                            {{ $auth_user->last_name }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label"
                                    for="client_id"><?= get_label('select_clients', 'Select clients') ?></label>
                                <select class="form-control clients_select" name="client_ids[]"
                                    multiple="multiple"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                    @if ($guard == 'client')
                                        <option value="{{ $auth_user->id }}" selected>{{ $auth_user->first_name }}
                                            {{ $auth_user->last_name }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        @if (!$isAdminOrHasAllDataAccess)
                            <div class="alert alert-primary alert-dismissible" role="alert">
                                <?= get_label('you_will_be_meeting_participant_automatically', 'You will be meeting participant automatically.') ?>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal"><?= get_label('close', 'Close') ?></button>
                        <button type="submit" id="submit_btn"
                            class="btn btn-primary me-2"><?= get_label('create', 'Create') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editMeetingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ get_label('update_meeting', 'Update Meeting') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ url('meetings/update') }}" class="form-submit-event" method="POST">
                    <input type="hidden" name="dnr">
                    <input type="hidden" name="id" id="meeting_id">
                    <input type="hidden" name="table" value="meetings_table">
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3">
                                <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                        class="asterisk">*</span></label>
                                <input class="form-control" type="text" id="meeting_title" name="title"
                                    placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for=""><?= get_label('starts_at', 'Starts at') ?>
                                    <span class="asterisk">*</span></label>
                                <input type="text" id="update_start_date" name="start_date"
                                    class="form-control" value="">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" for=""><?= get_label('time', 'Time') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="time" id="meeting_start_time" name="start_time"
                                    class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="end_date_time"><?= get_label('ends_at', 'Ends at') ?>
                                    <span class="asterisk">*</span></label>
                                <input type="text" id="update_end_date" name="end_date" class="form-control"
                                    value="">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" for=""><?= get_label('time', 'Time') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="time" id="meeting_end_time" name="end_time"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label"
                                    for="user_id"><?= get_label('select_users', 'Select users') ?></label>
                                <select class="form-control users_select" name="user_ids[]" multiple="multiple"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label"
                                    for="client_id"><?= get_label('select_clients', 'Select clients') ?></label>
                                <select class="form-control clients_select" name="client_ids[]"
                                    multiple="multiple"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal"><?= get_label('close', 'Close') ?></button>
                        <button type="submit" id="submit_btn"
                            class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@if (Request::is('users') || Request::is('clients'))
    <div class="modal fade" id="viewAssignedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><span id="userPlaceholder"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="nav-align-top">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" role="tab"
                                    data-bs-toggle="tab" data-bs-target="#navs-top-view-assigned-projects"
                                    aria-controls="navs-top-view-assigned-projects">
                                    <i
                                        class="menu-icon tf-icons bx bx-briefcase-alt-2 text-success"></i><?= get_label('projects', 'Projects') ?>
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-top-view-assigned-tasks"
                                    aria-controls="navs-top-view-assigned-tasks">
                                    <i
                                        class="menu-icon tf-icons bx bx-task text-primary"></i><?= get_label('tasks', 'Tasks') ?>
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="navs-top-view-assigned-projects"
                                role="tabpanel">
                                @if ($auth_user->can('manage_projects'))
                                    <x-projects-card :viewAssigned="1" />
                                @else
                                    <div class="alert alert-primary" role="alert">
                                        {{ get_label('no_projects_view_permission', 'You don\'t have permission to view projects.') }}
                                    </div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="navs-top-view-assigned-tasks" role="tabpanel">
                                @if ($auth_user->can('manage_tasks'))
                                    <x-tasks-card :viewAssigned="1" :emptyState="0" />
                                @else
                                    <div class="alert alert-primary" role="alert">
                                        {{ get_label('no_tasks_view_permission', 'You don\'t have permission to view tasks.') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

@if (Request::is('settings/sms-gateway'))
    <div class="modal fade" id="testSmsSettingsModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ get_label('test_sms_notification_settings', 'Test SMS Notification Settings') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="testSmsSettingsForm">
                    <div class="modal-body">
                        <small
                            class="text-muted">{{ get_label('test_sms_notification_settings_info', 'This is where you can test your SMS notification settings. Before testing, please update the settings if they haven\'t been updated already.') }}</small>
                        <div class="my-3">
                            <label
                                class="form-label">{{ get_label('recipient_country_code', 'Recipient Country Code') }}
                                (<small
                                    class="text-muted">{{ get_label('notification_test_recipient_country_code_info', 'Enter if required for the platform you are using.') }}</small>)</label>
                            <input type="text" class="form-control" id="testSmsRecipientCountryCode">
                        </div>
                        <div class="mb-3">
                            <label for="testSmsRecipientNumber"
                                class="form-label">{{ get_label('recipient_phone_number', 'Recipient Phone Number') }}
                                <span class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="testSmsRecipientNumber" required>
                        </div>
                        <div class="mb-3">
                            <label for="testSmsMessage" class="form-label">{{ get_label('message', 'Message') }}
                                <span class="asterisk">*</span></label>
                            <textarea class="form-control" id="testSmsMessage" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ get_label('close', 'Close') }}
                        </button>
                        <button type="submit" class="btn btn-primary"
                            id="performTestSmsSettingsButton">{{ get_label('submit', 'Submit') }}</button>
                    </div>
                    <div id="smsTestResponse" class="d-none">
                        <div class="modal-body">
                            <h5>{{ get_label('response', 'Response') }}:</h5>
                            <pre id="smsResponseText"></pre>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="testWhatsappSettingsModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ get_label('test_whatsapp_notification_settings', 'Test WhatsApp Notification Settings') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="testWhatsappSettingsForm">
                    <div class="modal-body">
                        <small
                            class="text-muted">{{ get_label('test_whatsapp_notification_settings_info', 'This is where you can test your WhatsApp notification settings. Before testing, please update the settings if they haven\'t been updated already.') }}</small>
                        <div class="my-3">
                            <label
                                class="form-label">{{ get_label('recipient_country_code', 'Recipient Country Code') }}
                                <span class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="testWhatsappRecipientCountryCode"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="testWhatsappRecipientNumber"
                                class="form-label">{{ get_label('recipient_whatsapp_number', 'Recipient WhatsApp Number') }}
                                <span class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="testWhatsappRecipientNumber" required>
                        </div>
                        <div class="mb-3">
                            <label for="testWhatsappMessage"
                                class="form-label">{{ get_label('message', 'Message') }} <span
                                    class="asterisk">*</span></label>
                            <textarea class="form-control" id="testWhatsappMessage" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ get_label('close', 'Close') }}
                        </button>
                        <button type="submit" class="btn btn-primary"
                            id="performTestWhatsappSettingsButton">{{ get_label('submit', 'Submit') }}</button>
                    </div>
                    <div id="whatsappTestResponse" class="d-none">
                        <div class="modal-body">
                            <h5>{{ get_label('response', 'Response') }}:</h5>
                            <pre id="whatsappResponseText"></pre>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="testSlackSettingsModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ get_label('test_slack_notification_settings', 'Test Slack Notification Settings') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="testSlackSettingsForm">
                    <div class="modal-body">
                        <small
                            class="text-muted">{{ get_label('test_slack_notification_settings_info', 'This is where you can test your Slack notification settings. Before testing, please update the settings if they haven\'t been updated already.') }}</small>
                        <div class="my-3">
                            <label class="form-label">{{ get_label('recipient_email', 'Recipient Email') }} <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="testSlackRecipientEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="testSlackMessage" class="form-label">{{ get_label('message', 'Message') }}
                                <span class="asterisk">*</span></label>
                            <textarea class="form-control" id="testSlackMessage" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ get_label('close', 'Close') }}
                        </button>
                        <button type="submit" class="btn btn-primary"
                            id="performTestSlackSettingsButton">{{ get_label('submit', 'Submit') }}</button>
                    </div>
                    <div id="slackTestResponse" class="d-none">
                        <div class="modal-body">
                            <h5>{{ get_label('response', 'Response') }}:</h5>
                            <pre id="slackResponseText"></pre>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@php
    $fileSettings = get_file_settings();
    $allowedFileTypes = $fileSettings['allowed_file_types'];
    $maxFilesAllowed = $fileSettings['max_files_allowed'];
@endphp
@if (Request::is('projects/information/*'))
    @isset($project)
        <!-- Modal for Reply Submission -->
        <div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form id="replyForm" method="POST" enctype="multipart/form-data"
                    action="{{ route('comments.store', ['id' => $project->id]) }}">
                    @csrf
                    <input type="hidden" id="parent_id" name="parent_id" value="">
                    <input type="hidden" name="model_type" value="App\Models\Project">
                    <input type="hidden" name="model_id" value="{{ $project->id }}">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="replyModalLabel">{{ get_label('post_reply', 'Post Reply') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="content" class="form-label">{{ get_label('reply', 'Reply') }} <span
                                            class="asterisk">*</span></label>
                                    <textarea id="project-reply-content" data-mention-type="project" data-mention-id="{{ $project->id }}"
                                        name="content" rows="4" class="form-control comment"
                                        placeholder="{{ get_label('please_enter_reply', 'Please Enter Reply') }}"></textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="attachments" class="form-label">
                                        {{ get_label('attachments', 'Attachments') }}
                                        <small class="text-muted">
                                            (<b>{{ get_label('allowed_file_types', 'Allowed File Types') }}</b>:
                                            {{ $allowedFileTypes }},
                                            <b>{{ get_label('max_files_allowed', 'Max Files Allowed') }}</b>:
                                            {{ $maxFilesAllowed }})
                                        </small>
                                    </label>
                                    <input type="file" multiple name="attachments[]" id="attachments"
                                        class="form-control" accept="{{ $allowedFileTypes }}"
                                        data-max-files="{{ $maxFilesAllowed }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <?= get_label('close', 'Close') ?>
                            </button>
                            <button type="submit"
                                class="btn btn-primary">{{ get_label('submit', 'Submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal for Comment Submission -->
        <div class="modal fade" id="commentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form class="modal-content" id="comment-form" method="POST" enctype="multipart/form-data"
                    action="{{ route('comments.store', ['id' => $project->id]) }}">
                    @csrf
                    <input type="hidden" name="model_type" value="App\Models\Project">
                    <input type="hidden" name="model_id" value="{{ $project->id }}">
                    <input type="hidden" name="parent_id" value="">

                    <div class="modal-header">
                        <h5 class="modal-title" id="commentModalLabel"><?= get_label('add_comment', 'Add Comment') ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="project-comment-content"
                                    class="form-label"><?= get_label('comment', 'Comment') ?> <span
                                        class="asterisk">*</span></label>
                                <textarea id="project-comment-content" name="content" rows="4" class="form-control comment"
                                    data-mention-type="project" data-mention-id="{{ $project->id }}"
                                    placeholder="<?= get_label('please_enter_comment', 'Please enter comment') ?>"></textarea>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="attachments" class="form-label">
                                    {{ get_label('attachments', 'Attachments') }}
                                    <small class="text-muted">
                                        (<b>{{ get_label('allowed_file_types', 'Allowed File Types') }}</b>:
                                        {{ $allowedFileTypes }},
                                        <b>{{ get_label('max_files_allowed', 'Max Files Allowed') }}</b>:
                                        {{ $maxFilesAllowed }})
                                    </small>

                                </label>
                                <input type="file" multiple name="attachments[]" id="attachments"
                                    class="form-control" accept="{{ $allowedFileTypes }}"
                                    data-max-files="{{ $maxFilesAllowed }}" />
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <?= get_label('close', 'Close') ?>
                        </button>
                        <button type="submit" class="btn btn-primary"><?= get_label('submit', 'Submit') ?></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="EditCommentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form class="modal-content form-submit-event" id="comment-form" method="POST"
                    enctype="multipart/form-data" action="{{ route('comments.update') }}">
                    @csrf
                    <input type="hidden" name="comment_id" id="comment_id" value="">

                    <div class="modal-header">
                        <h5 class="modal-title" id="commentModalLabel"><?= get_label('edit_comment', 'Edit Comment') ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="edit-project-comment-content"
                                    class="form-label"><?= get_label('comment', 'Comment') ?> <span
                                        class="asterisk">*</span></label>
                                <textarea id="edit-project-comment-content" name="content" rows="4" class="form-control comment"
                                    data-mention-type="project" data-mention-id="{{ $project->id }}"
                                    placeholder="<?= get_label('please_enter_comment', 'Please enter comment') ?>"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <?= get_label('close', 'Close') ?>
                        </button>
                        <button type="submit" class="btn btn-primary"
                            id="submit_btn"><?= get_label('update', 'Update') ?></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="DeleteCommentModal" tabindex="-1" aria-labelledby="commentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <form id="delete-comment-form" method="POST" action="{{ route('comments.destroy') }}">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="commentModalLabel">
                                {{ get_label('delete_comment', 'Delete Comment') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="comment_id" id="delete_comment_id" value="">
                            <p>{{ get_label('confirm_delete_comment', 'Are you sure you want to delete this comment?') }}
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <?= get_label('close', 'Close') ?>
                            </button>
                            <button type="button" class="btn btn-danger"
                                id="deleteCommentBtn">{{ get_label('yes', 'Yes') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endisset
@endif
<!-- Tasks Discussions Modal -->
@if (Request::is('tasks/information/*'))
    @isset($task)
        <div class="modal fade" id="task-reply-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form class="modal-content" id="replyForm" method="POST" enctype="multipart/form-data"
                    action="{{ route('tasks.comments.store', ['id' => $task->id]) }}">
                    @csrf
                    <input type="hidden" id="parent_id" name="parent_id" value="">
                    <input type="hidden" name="model_type" value="App\Models\Task">
                    <input type="hidden" name="model_id" value="{{ $task->id }}">

                    <div class="modal-header">
                        <h5 class="modal-title" id="replyModalLabel"><?= get_label('post_reply', 'Post Reply') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="task-reply-content" class="form-label"><?= get_label('reply', 'Reply') ?>
                                    <span class="asterisk">*</span></label>
                                <textarea id="task-reply-content" data-mention-type="task" data-mention-id="{{ $task->id }}"
                                    name="content" rows="4" class="form-control comment"
                                    placeholder="<?= get_label('please_enter_reply', 'Please Enter Reply') ?>"></textarea>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="attachments" class="form-label">
                                    {{ get_label('attachments', 'Attachments') }}
                                    <small class="text-muted">
                                        (<b>{{ get_label('allowed_file_types', 'Allowed File Types') }}</b>:
                                        {{ $allowedFileTypes }},
                                        <b>{{ get_label('max_files_allowed', 'Max Files Allowed') }}</b>:
                                        {{ $maxFilesAllowed }})
                                    </small>
                                </label>
                                <input type="file" multiple name="attachments[]" id="attachments"
                                    class="form-control" accept="{{ $allowedFileTypes }}"
                                    data-max-files="{{ $maxFilesAllowed }}" />
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <?= get_label('close', 'Close') ?>
                        </button>
                        <button type="submit" class="btn btn-primary"><?= get_label('submit', 'Submit') ?></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="task_commentModal" tabindex="-1" aria-labelledby="task_commentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form class="modal-content" id="comment-form" method="POST" enctype="multipart/form-data"
                    action="{{ route('tasks.comments.store', ['id' => $task->id]) }}">
                    @csrf
                    <input type="hidden" name="model_type" value="App\Models\Task">
                    <input type="hidden" name="model_id" value="{{ $task->id }}">
                    <input type="hidden" name="parent_id" value="">

                    <div class="modal-header">
                        <h5 class="modal-title" id="task_commentModalLabel">
                            <?= get_label('add_comment', 'Add Comment') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="task-comment-content"
                                    class="form-label"><?= get_label('comment', 'Comment') ?> <span
                                        class="asterisk">*</span></label>
                                <textarea id="task-comment-content" data-mention-type="task" data-mention-id="{{ $task->id }}"
                                    name="content" rows="4" class="form-control comment"
                                    placeholder="<?= get_label('please_enter_comment', 'Please enter comment') ?>"></textarea>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="attachments" class="form-label">
                                    {{ get_label('attachments', 'Attachments') }}
                                    <small class="text-muted">
                                        (<b>{{ get_label('allowed_file_types', 'Allowed File Types') }}</b>:
                                        {{ $allowedFileTypes }},
                                        <b>{{ get_label('max_files_allowed', 'Max Files Allowed') }}</b>:
                                        {{ $maxFilesAllowed }})
                                    </small>
                                </label>
                                <input type="file" multiple name="attachments[]" id="attachments"
                                    class="form-control" accept="{{ $allowedFileTypes }}"
                                    data-max-files="{{ $maxFilesAllowed }}">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <?= get_label('close', 'Close') ?>
                        </button>
                        <button type="submit" class="btn btn-primary"><?= get_label('submit', 'Submit') ?></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="TaskEditCommentModal" tabindex="-1" aria-labelledby="commentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form class="modal-content form-submit-event" id="comment-form" method="POST"
                    enctype="multipart/form-data" action="{{ route('tasks.comments.update') }}">
                    @csrf
                    <input type="hidden" name="comment_id" id="comment_id" value="">

                    <div class="modal-header">
                        <h5 class="modal-title" id="commentModalLabel"><?= get_label('edit_comment', 'Edit Comment') ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="task-comment-edit-content"
                                class="form-label"><?= get_label('comment', 'Comment') ?> <span
                                    class="asterisk">*</span></label>
                            <textarea id="task-comment-edit-content" data-mention-type="task" data-mention-id="{{ $task->id }}"
                                name="content" rows="4" class="form-control comment"
                                placeholder="<?= get_label('please_enter_comment', 'Please Enter Comment') ?>"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <?= get_label('close', 'Close') ?>
                        </button>
                        <button type="submit" class="btn btn-primary"
                            id="submit_btn"><?= get_label('update', 'Update') ?></button>
                    </div>
                </form>
            </div>
        </div>


        <div class="modal fade" id="TaskDeleteCommentModal" tabindex="-1" aria-labelledby="commentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="commentModalLabel">
                            {{ get_label('delete_comment', 'Delete Comment') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="delete-task-comment-form" method="POST"
                            action="{{ route('tasks.comments.destroy') }}">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="comment_id" id="delete_comment_id" value="">
                            <p>{{ get_label('confirm_delete_comment', 'Are you sure you want to delete this comment?') }}
                            </p>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <?= get_label('close', 'Close') ?>
                        </button>
                        <button type="button" class="btn btn-danger"
                            id="deleteTaskCommentBtn">{{ get_label('yes', 'Yes') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endisset
@endif

<div class="modal fade" id="cron_job_instruction_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Cron Job Setup Instructions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>To enable automatic notifications, you need to set up a cron job on your server. This cron job will
                    trigger Laravel’s task scheduler to run tasks like sending wish notifications.</p>
                <p>Follow these steps to set up the cron job using the hosting control panel (e.g., cPanel or hPanel):
                </p>
                <ol>
                    <li>Log in to your hosting control panel (e.g., cPanel or hPanel).</li>
                    <li>Go to the Cron Jobs section under Advanced or similar settings.</li>
                    <li>Add a new cron job with the following settings:</li>
                    <pre><code>* * * * * /usr/bin/php /path/to/your/project/artisan schedule:run >> /dev/null 2>&1</code></pre>
                    <li>Replace "/path/to/your/project" with the full path to your Laravel project directory.</li>
                    <li>Your cron job is now set up.</li>
                </ol>
                <p>If you prefer, you can set up the cron job manually through SSH:</p>
                <ol>
                    <li>Log in to your server via SSH.</li>
                    <li>Open the crontab by typing: <code>crontab -e</code></li>
                    <li>Add the following line for the cron job:</li>
                    <pre><code>* * * * * /usr/bin/php /path/to/your/project/artisan schedule:run >> /dev/null 2>&1</code></pre>
                    <li>Replace "/path/to/your/project" with the full path to your Laravel project directory.</li>
                    <li>Save and exit. Your cron job is now set up.</li>
                </ol>
                <p>Feel free to reach out to us if you need any help.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
