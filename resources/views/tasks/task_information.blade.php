@extends('layout')
@section('title')
<?= get_label('task_details', 'Task details') ?>
@endsection
@section('content')
<div class="container-fluid">
    <div class="align-items-center d-flex justify-content-between mt-4 mb-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item">
                        <a href="{{url('home')}}"><?= get_label('home', 'Home') ?></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{url(getUserPreferences('tasks', 'default_view'))}}"><?= get_label('tasks', 'Tasks') ?></a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{$task->title}}
                    </li>
                </ol>
            </nav>
        </div>
        <div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="fw-bold">{{ $task->title }}
                                <a href="javascript:void(0);" class="mx-2">
                                    <i class='bx {{getFavoriteStatus($task->id) ? "bxs" : "bx"}}-star favorite-icon text-warning' data-id="{{$task->id}}" data-type="tasks" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="{{getFavoriteStatus($task->id) ? get_label('remove_favorite', 'Click to remove from favorite') : get_label('add_favorite', 'Click to mark as favorite')}}" data-favorite="{{getFavoriteStatus($task->id) ? 1 : 0}}"></i>
                                </a>
                                <a href="javascript:void(0);">
                                    <i class='bx {{getPinnedStatus($task->id, \App\Models\Task::class) ? "bxs" : "bx"}}-pin pinned-icon text-success' data-id="{{$task->id}}" data-type="tasks" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="{{getPinnedStatus($task->id, \App\Models\Task::class) ? get_label('click_unpin', 'Click to Unpin') : get_label('click_pin', 'Click to Pin')}}" data-pinned="{{getPinnedStatus($task->id, \App\Models\Task::class)}}" data-require_reload="0"></i>
                                </a>
                            </h2>
                            <div class="row">
                                <div class="col-md-6 mt-3 mb-3">
                                    <label class="form-label" for="start_date"><?= get_label('users', 'Users') ?></label>
                                    <?php
                                    $users = $task->users;
                                    $clients = $task->project->clients;
                                    if (count($users) > 0) { ?>
                                        <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center flex-wrap">
                                            @foreach($users as $user)
                                            <li class="avatar avatar-sm pull-up" title="{{ $user->first_name }} {{ $user->last_name }}"><a href="{{ url('/users/profile/' . $user->id) }}">
                                                    <img src="{{$user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg')}}" class="rounded-circle" alt="{{$user->first_name}} {{$user->last_name}}">
                                                </a></li>
                                            @endforeach
                                            <a href="javascript:void(0)" class="btn btn-icon btn-sm btn-outline-primary btn-sm rounded-circle edit-task update-users-clients" data-id="{{$task->id}}"><span class="bx bx-edit"></span></a>
                                        </ul>
                                    <?php } else { ?>
                                        <p><span class="badge bg-primary"><?= get_label('not_assigned', 'Not assigned') ?></span><a href="javascript:void(0)" class="btn btn-icon btn-sm btn-outline-primary btn-sm rounded-circle edit-task update-users-clients" data-id="{{$task->id}}"><span class="bx bx-edit"></span></a></p>
                                    <?php } ?>
                                </div>
                                <div class="col-md-6  mt-3 mb-3">
                                    <label class="form-label" for="end_date"><?= get_label('clients', 'Clients') ?></label>
                                    <?php
                                    if (count($clients) > 0) { ?>
                                        <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center flex-wrap">
                                            @foreach($clients as $client)
                                            <li class="avatar avatar-sm pull-up" title="{{ $client->first_name }} {{ $client->last_name }}"><a href="{{ url('/clients/profile/' . $client->id) }}">
                                                    <img src="{{$client->photo ? asset('storage/' . $client->photo) : asset('storage/photos/no-image.jpg')}}" class="rounded-circle" alt="{{$client->first_name}} {{$client->last_name}}">
                                                </a></li>
                                            @endforeach
                                        </ul>
                                    <?php } else { ?>
                                        <p><span class="badge bg-primary"><?= get_label('not_assigned', 'Not assigned') ?></span></p>
                                    <?php } ?>
                                </div>
                                <div class="col-md-{{$task->note ? '7' : '6'}} mb-3">
                                    <label class="form-label"><?= get_label('status', 'Status') ?></label>
                                    <div class="d-flex align-items-center">
                                        <select class="form-select form-select-sm select-bg-label-{{$task->status->color}}" id="statusSelect" data-id="{{ $task->id }}" data-original-status-id="{{ $task->status->id }}" data-original-color-class="select-bg-label-{{$task->status->color}}" data-type="task">
                                            @foreach($statuses as $status)
                                            @php
                                            $disabled = canSetStatus($status) ? '' : 'disabled';
                                            @endphp
                                            <option value="{{ $status->id }}" class="badge bg-label-{{ $status->color }}" {{ $task->status->id == $status->id ? 'selected' : '' }} {{ $disabled }}>
                                                {{ $status->title }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @if($task->note)
                                        <i class="bx bx-notepad ms-1 text-primary" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" title="" data-bs-original-title="{{$task->note}}"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-{{$task->note ? '5' : '6'}} mb-3">
                                    <label for="prioritySelect" class="form-label"><?= get_label('priority', 'Priority') ?></label>
                                    <select class="form-select form-select-sm select-bg-label-{{$task->priority?$task->priority->color:'secondary'}}" id="prioritySelect" data-id="{{ $task->id }}" data-original-priority-id="{{ $task->priority ? $task->priority->id : '' }}" data-original-color-class="select-bg-label-{{$task->priority?$task->priority->color:'secondary'}}" data-type="task">
                                        <option value="" class="badge bg-label-secondary">-</option>
                                        @foreach($priorities as $priority)
                                        <option value="{{ $priority->id }}" class="badge bg-label-{{ $priority->color }}" {{ $task->priority && $task->priority->id == $priority->id ? 'selected' : '' }}>
                                            {{ $priority->title }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-0" />
                <div class="card-body">
                    <div class="row">
                        <div class="row">
                            <!-- Project Card -->
                            <div class="col-12 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-title d-flex align-items-start justify-content-between">
                                            <div class="avatar flex-shrink-0">
                                                <i class="menu-icon tf-icons bx bx-briefcase bx-md text-info"></i>
                                            </div>
                                        </div>
                                        <span class="fw-semibold d-block mb-1"><?= get_label('project', 'Project') ?></span>
                                        <h3 class="card-title mb-2">
                                            @if (isset($task->project) && checkPermission('manage_projects'))
                                            <a href="{{ url('projects/information/' . $task->project->id) }}" class="text-decoration-none">
                                                {{ $task->project->title }}
                                            </a>
                                            @else
                                            {{ $task->project->title ?? '-' }}
                                            @endif
                                        </h3>


                                    </div>
                                </div>
                            </div>

                            <!-- Starts at Card -->
                            <div class="col-lg-4 col-md-12 col-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-title d-flex align-items-start justify-content-between">
                                            <div class="avatar flex-shrink-0">
                                                <i class="menu-icon tf-icons bx bx-calendar-check bx-md text-success"></i>
                                            </div>
                                        </div>
                                        <span class="fw-semibold d-block mb-1"><?= get_label('starts_at', 'Starts at') ?></span>
                                        <h3 class="card-title mb-2">{{ format_date($task->start_date) }}</h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Ends at Card -->
                            <div class="col-lg-4 col-md-12 col-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-title d-flex align-items-start justify-content-between">
                                            <div class="avatar flex-shrink-0">
                                                <i class="menu-icon tf-icons bx bx-calendar-x bx-md text-danger"></i>
                                            </div>
                                        </div>
                                        <span class="fw-semibold d-block mb-1"><?= get_label('ends_at', 'Ends at') ?></span>
                                        <h3 class="card-title mb-2">{{ format_date($task->due_date) }}</h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Duration Card -->
                            <div class="col-lg-4 col-md-12 col-6 mb-4">
                                @php
                                use Carbon\Carbon;
                                $fromDate = $task->start_date ? Carbon::parse($task->start_date) : null;
                                $toDate = $task->due_date ? Carbon::parse($task->due_date) : null;
                                $durationText = $fromDate && $toDate ? ($fromDate->diffInDays($toDate) + 1) . ' day' . (($fromDate->diffInDays($toDate) > 1) ? 's' : '') : '-';
                                @endphp
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-title d-flex align-items-start justify-content-between">
                                            <div class="avatar flex-shrink-0">
                                                <i class="menu-icon tf-icons bx bx-time bx-md text-primary"></i>
                                            </div>
                                        </div>
                                        <span class="fw-semibold d-block mb-1"><?= get_label('duration', 'Duration') ?></span>
                                        <h3 class="card-title mb-2">{{ $durationText }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description Card-->
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title">
                                        <h5><?= get_label('description', 'Description') ?></h5>
                                    </div>
                                    <p>
                                        <!-- Add your project description here -->
                                        <?= (filled($task->description)) ? $task->description : '-' ?>
                                    </p>
                                </div>
                            </div>

                        </div>

                        <!-- Task Remider Card-->
                        <div class="col-md-12 mb-4">
                            <div class="shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">
                             <h5>{{ get_label('reminder_details', 'Reminders Details') }}</h5>
                            </div>
                            <span class="badge {{ $task->reminders->first()?->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $task->reminders->first()?->is_active ? get_label('active', 'Active') : get_label('inactive', 'Inactive') }}
                            </span>
                        </div>
                        @if ($task->reminders->isNotEmpty())
                            <div class="card-body">
                                @php
                                    $reminder = $task->reminders->first();
                                    $frequencyType = ucfirst($reminder->frequency_type);
                                    $timeOfDay = \Carbon\Carbon::parse($reminder->time_of_day)->format('h:i A');
                                @endphp

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ get_label('frequency', 'Frequency') }}</label>
                                            <p class="form-control mb-0" readonly>
                                                @switch($reminder->frequency_type)
                                                    @case('daily')
                                                        <i class="bx bx-time text-dark me-1"></i>
                                                        {{ get_label('daily_at', 'Daily at') }} {{ $timeOfDay }}
                                                    @break

                                                    @case('weekly')
                                                        <i class="bx bx-calendar text-dark me-1"></i>
                                                        @php
                                                            $dayNames = [
                                                                1 => 'Monday',
                                                                2 => 'Tuesday',
                                                                3 => 'Wednesday',
                                                                4 => 'Thursday',
                                                                5 => 'Friday',
                                                                6 => 'Saturday',
                                                                7 => 'Sunday',
                                                            ];
                                                            $dayName = $reminder->day_of_week
                                                                ? get_label(
                                                                    strtolower($dayNames[$reminder->day_of_week]),
                                                                    $dayNames[$reminder->day_of_week],
                                                                )
                                                                : get_label('any_day', 'Any Day');
                                                        @endphp
                                                        {{ get_label('weekly_on', 'Weekly on') }} {{ $dayName }}
                                                        {{ get_label('at', 'at') }} {{ $timeOfDay }}
                                                    @break

                                                    @case('monthly')
                                                        <i class="bx bx-calendar-alt text-dark me-1"></i>
                                                        @php
                                                            $dayOfMonth =
                                                                $reminder->day_of_month ?:
                                                                get_label('any_day', 'Any Day');
                                                            if (is_numeric($dayOfMonth)) {
                                                                $dayOfMonth .= date(
                                                                    'S',
                                                                    mktime(0, 0, 0, 1, $dayOfMonth, 2000),
                                                                ); // Adds st, nd, rd, th
                                                            }
                                                        @endphp
                                                        {{ get_label('monthly_on_the', 'Monthly on the') }} {{ $dayOfMonth }}
                                                        {{ get_label('at', 'at') }} {{ $timeOfDay }}
                                                    @break
                                                @endswitch
                                            </p>
                                        </div>

                                        @if ($reminder->last_sent_at)
                                            <div class="mb-3">
                                                <label
                                                    class="form-label">{{ get_label('last_reminder_sent', 'Last Reminder Sent') }}</label>
                                                <p class="form-control mb-0" readonly>
                                                    <i class='bx bxs-alarm-add text-primary me-1'></i>
                                                    {{ \Carbon\Carbon::parse($reminder->last_sent_at)->diffForHumans() }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ get_label('created_on', 'Created On') }}</label>
                                            <p class="form-control mb-0" readonly>
                                                <i class='bx bxs-calendar-event text-danger me-1'></i>
                                                {{ format_date($reminder->created_at) }}
                                            </p>
                                        </div>

                                        <div class="mb-3">
                                            <label
                                                class="form-label">{{ get_label('last_updated', 'Last Updated') }}</label>
                                            <p class="form-control mb-0" readonly>
                                                <i class='bx bx-calendar text-warning me-1'></i>
                                                {{ format_date($reminder->updated_at, true) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card-body">
                                <p class="text-muted mb-0">
                                    <i class="fas fa-bell-slash me-2"></i>
                                    {{ get_label('no_reminders_set', 'No reminders set for this task') }}
                                </p>
                            </div>
                        @endif
                            </div>
                        </div>

                        
                        <!-- Recurring Task-->
                        <div class="col-md-12 mb-4">
                        <div class="shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="card-title">
                                 <h5>{{ get_label('recurrence_details', 'Recurrence Details') }}</h5>
                                </div>
                                <span class="badge {{ $task->recurringTask?->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $task->recurringTask?->is_active ? get_label('active', 'Active') : get_label('inactive', 'Inactive') }}
                                </span>
                            </div>
                            @if ($task->recurringTask)
                                <div class="card-body">
                                    @php
                                        $recurringTask = $task->recurringTask;
                                        $frequencyType = ucfirst($recurringTask->frequency);

                                    @endphp

                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ get_label('frequency', 'Frequency') }}</label>
                                                <p class="form-control mb-0" readonly>
                                                    @switch($recurringTask->frequency)
                                                        @case('daily')
                                                            <i class="bx bx-time text-dark me-1"></i>
                                                            {{ get_label('daily_at', 'Daily at') }} {{ '00:00' }}
                                                        @break

                                                        @case('weekly')
                                                            <i class="bx bx-calendar text-dark me-1"></i>
                                                            @php
                                                                $dayNames = [
                                                                    1 => 'Monday',
                                                                    2 => 'Tuesday',
                                                                    3 => 'Wednesday',
                                                                    4 => 'Thursday',
                                                                    5 => 'Friday',
                                                                    6 => 'Saturday',
                                                                    7 => 'Sunday',
                                                                ];
                                                                $dayName = $recurringTask->day_of_week
                                                                    ? get_label(
                                                                        strtolower($dayNames[$recurringTask->day_of_week]),
                                                                        $dayNames[$recurringTask->day_of_week],
                                                                    )
                                                                    : get_label('any_day', 'Any Day');
                                                            @endphp
                                                            {{ get_label('weekly_on', 'Weekly on') }} {{ $dayName }}
                                                            {{ get_label('at', 'at') }} {{ '00:00' }}
                                                        @break

                                                        @case('monthly')
                                                            <i class="bx bx-calendar-alt text-dark me-1"></i>
                                                            @php
                                                                $dayOfMonth =
                                                                    $recurringTask->day_of_month ?:
                                                                    get_label('any_day', 'Any Day');
                                                                if (is_numeric($dayOfMonth)) {
                                                                    $dayOfMonth .= date(
                                                                        'S',
                                                                        mktime(0, 0, 0, 1, $dayOfMonth, 2000),
                                                                    ); // Adds st, nd, rd, th
                                                                }
                                                            @endphp
                                                            {{ get_label('monthly_on_the', 'Monthly on the') }} {{ $dayOfMonth }}
                                                            {{ get_label('at', 'at') }} {{ '00:00' }}
                                                        @break

                                                        @case('yearly')
                                                            <i class="bx bx-calendar-alt text-dark me-1"></i>
                                                            @php
                                                                $dayOfMonth =
                                                                    $recurringTask->day_of_month ?:
                                                                    get_label('any_day', 'Any Day');
                                                                if (is_numeric($dayOfMonth)) {
                                                                    $dayOfMonth .= date(
                                                                        'S',
                                                                        mktime(0, 0, 0, 1, $dayOfMonth, 2000),
                                                                    ); // Adds st, nd, rd, th
                                                                }
                                                            @endphp
                                                            {{ get_label('yearly_on_the', 'Yearly on the') }} {{ $dayOfMonth }}
                                                            {{ get_label('of', 'of') }}
                                                            {{ \Carbon\Carbon::create()->month($recurringTask->month_of_year)->format('F') }}
                                                            {{ get_label('at', 'at') }} {{ '00:00' }}
                                                        @break

                                                    @endswitch
                                                </p>
                                            </div>

                                            @if ($recurringTask->starts_from)
                                                <div class="mb-3">
                                                    <label
                                                        class="form-label">{{ get_label('starts_from', 'Starts From') }}</label>
                                                    <p class="form-control mb-0" readonly>
                                                        <i class='bx bxs-alarm-add text-primary me-1'></i>
                                                        {{ format_date($recurringTask->starts_from) }}
                                                    </p>
                                                </div>
                                            @endif
                                            <div class="mb-3">
                                                <label
                                                    class="form-label">{{ get_label('completed_occurrences', 'Completed Occurrences') }}</label>
                                                <p class="form-control mb-0" readonly>
                                                    <i class='bx bxs-analyse text-success me-1'></i>
                                                    {{ $recurringTask->completed_occurrences ?? 0 }} {{get_label('completed_occurances', 'Completed Occurrences')}}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="col-md-6">


                                            <div class="mb-3">
                                                <label class="form-label">{{ get_label('created_on', 'Created On') }}</label>
                                                <p class="form-control mb-0" readonly>
                                                    <i class='bx bxs-calendar-event text-danger me-1'></i>
                                                    {{ format_date($recurringTask->created_at,true) }}
                                                </p>
                                            </div>

                                            <div class="mb-3">
                                                <label
                                                    class="form-label">{{ get_label('last_updated', 'Last Updated') }}</label>
                                                <p class="form-control mb-0" readonly>
                                                    <i class='bx bx-calendar text-warning me-1'></i>
                                                    {{ format_date($recurringTask->updated_at, true) }}
                                                </p>
                                            </div>
                                            <div class="mb-3">
                                                <label
                                                    class="form-label">{{ get_label('number_of_occurrences', 'Number of Occurrences') }}</label>
                                                <p class="form-control mb-0" readonly>
                                                    <i class='bx bxs-analyse text-primary me-1'></i>
                                                    {{ $recurringTask->number_of_occurrences }} {{get_label('number_of_occurances', 'Number of Occurrences')}}
                                                </p>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card-body">
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-bell-slash me-2"></i>
                                        {{ get_label('no_recurrence_set', 'No recurrence set for this task') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        </div>

                    </div>
                </div>
            </div>
            <input type="hidden" id="media_type_id" value="{{$task->id}}">
        </div>
        @if(Auth::guard('web')->check() || $task->client_can_discuss || $auth_user->can('manage_media') || $auth_user->can('manage_activity_log'))
        <div class="nav-align-top mt-2">
            <ul class="nav nav-tabs" role="tablist">
                @php
                $activeTab = '';
                @endphp

                @if (Auth::guard('web')->check() || $task->client_can_discuss)
                <li class="nav-item">
                    <button type="button" class="nav-link {{ empty($activeTab) ? 'active' : '' }}" role="tab"
                        data-bs-toggle="tab" data-bs-target="#navs-top-discussions" aria-controls="navs-top-discussions">
                        <i class="menu-icon tf-icons bx bxs-chat text-danger"></i><?= get_label('discussions', 'Discussions') ?>
                    </button>
                </li>
                @php
                if (empty($activeTab)) {
                $activeTab = 'discussions';
                }
                @endphp
                @endif

                @if ($auth_user->can('manage_media'))
                <li class="nav-item">
                    <button type="button" class="nav-link {{ empty($activeTab) ? 'active' : '' }}" role="tab"
                        data-bs-toggle="tab" data-bs-target="#navs-top-media" aria-controls="navs-top-media">
                        <i class="menu-icon tf-icons bx bx-image-alt text-success"></i><?= get_label('media', 'Media') ?>
                    </button>
                </li>
                @php
                if (empty($activeTab)) {
                $activeTab = 'media';
                }
                @endphp
                @endif

                <li class="nav-item">
                    <button type="button" class="nav-link {{ empty($activeTab) ? 'active' : '' }}" role="tab"
                        data-bs-toggle="tab" data-bs-target="#navs-top-status_timeline"
                        aria-controls="navs-top-status_timeline">
                        <i class="menu-icon tf-icons bx bx-align-justify text-dark"></i>{{ get_label('status_timeline', 'Status Timeline') }}
                    </button>
                </li>
                @php
                    if (empty($activeTab)) {
                        $activeTab = 'status_timeline';
                    }
                @endphp

                @if ($auth_user->can('manage_activity_log'))
                <li class="nav-item">
                    <button type="button" class="nav-link {{ $activeTab == 'activity_log' ? 'active' : '' }}" role="tab"
                        data-bs-toggle="tab" data-bs-target="#navs-top-activity-log" aria-controls="navs-top-activity-log">
                        <i class="menu-icon tf-icons bx bx-line-chart text-info"></i><?= get_label('activity_log', 'Activity log') ?>
                    </button>
                </li>
                @php
                if (empty($activeTab)) {
                $activeTab = 'activity_log';
                }
                @endphp
                @endif
            </ul>

            <div class="tab-content">
                @if (Auth::guard('web')->check() || $task->client_can_discuss)
                <div class="tab-pane fade {{ $activeTab == 'discussions' ? 'active show' : '' }}" id="navs-top-discussions"
                    role="tabpanel">
                    <!-- Discussions content -->
                    <x-task-discussions-card :task="$task" />
                </div>
                @endif
                @if ($auth_user->can('manage_media'))
                <div class="tab-pane fade {{ $activeTab == 'media' ? 'active show' : '' }}" id="navs-top-media" role="tabpanel">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div></div>
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add_media_modal">
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('add_media', 'Add Media') ?>">
                                    <i class="bx bx-plus"></i>
                                </button>
                            </a>
                        </div>
                        @php
                        $visibleColumns = getUserPreferences('task_media');
                        @endphp
                        <div class="table-responsive text-nowrap">
                            <input type="hidden" id="data_type" value="task-media">
                            <input type="hidden" id="data_table" value="task_media_table">
                            <input type="hidden" id="save_column_visibility">
                            <table id="task_media_table" data-toggle="table" data-loading-template="loadingTemplate" data-url="{{ url('/tasks/get-media/' . $task->id) }}" data-icons-prefix="bx" data-icons="icons" data-show-refresh="true" data-total-field="total" data-trim-on-search="false" data-data-field="rows" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-side-pagination="server" data-show-columns="true" data-pagination="true" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-query-params="queryParamsTaskMedia">
                                <thead>
                                    <tr>
                                        <th data-checkbox="true"></th>
                                        <th data-field="id" data-visible="{{ (in_array('id', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('id', 'ID') ?></th>
                                        <th data-field="file" data-visible="{{ (in_array('file', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('file', 'File') ?></th>
                                        <th data-field="file_name" data-sortable="true" data-visible="{{ (in_array('file_name', $visibleColumns)) ? 'true' : 'false' }}">{{ get_label('file_name', 'File name') }}</th>
                                        <th data-field="file_size" data-visible="{{ (in_array('file_size', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('file_size', 'File size') ?></th>
                                        <th data-field="created_at" data-sortable="true" data-visible="{{ (in_array('created_at', $visibleColumns)) ? 'true' : 'false' }}">{{ get_label('created_at', 'Created at') }}</th>
                                        <th data-field="updated_at" data-sortable="true" data-visible="{{ (in_array('updated_at', $visibleColumns)) ? 'true' : 'false' }}">{{ get_label('updated_at', 'Updated at') }}</th>
                                        <th data-field="actions" data-visible="{{ (in_array('actions', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false' }}" data-sortable="false">{{ get_label('actions', 'Actions') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
 
                <div class="tab-pane fade {{ $activeTab == 'status_timeline' ? 'active show' : '' }}"
                    id="navs-top-status_timeline" role="tabpanel">
                    <!-- Status timeline content -->
                    <x-status-timeline :timelines="$task->statusTimelines->sortByDesc('changed_at')" />
                </div>
                @if ($auth_user->can('manage_activity_log'))
                <div class="tab-pane fade {{ $activeTab == 'activity_log' ? 'active show' : '' }}" id="navs-top-activity-log" role="tabpanel">
                    <div class="col-12">
                        <div class="row mt-4">
                            <div class="mb-3 col-md-4">
                                <div class="input-group input-group-merge">
                                    <input type="text" id="activity_log_between_date" class="form-control" placeholder="<?= get_label('date_between', 'Date between') ?>" autocomplete="off">
                                </div>
                            </div>
                            @if ($auth_user->can('manage_users'))
                            <div class="col-md-4 mb-3">
                                <select class="form-select users_select" id="user_filter" aria-label="Default select example" data-placeholder="<?= get_label('select_actioned_by_users', 'Select Actioned By Users') ?>" multiple>
                                </select>
                            </div>
                            @endif
                            @if ($auth_user->can('manage_clients'))
                            <div class="col-md-4 mb-3">
                                <select class="form-select clients_select" id="client_filter" aria-label="Default select example" data-placeholder="<?= get_label('select_actioned_by_clients', 'Select Actioned By Clients') ?>" multiple>
                                </select>
                            </div>
                            @endif
                            <div class="col-md-4 mb-3">
                                <select class="form-select js-example-basic-multiple" id="activity_filter" aria-label="Default select example" data-placeholder="<?= get_label('select_activities', 'Select Activities') ?>" data-allow-clear="true" multiple>
                                    <option value="created"><?= get_label('created', 'Created') ?></option>
                                    <option value="updated"><?= get_label('updated', 'Updated') ?></option>
                                    <option value="duplicated"><?= get_label('duplicated', 'Duplicated') ?></option>
                                    <option value="uploaded"><?= get_label('uploaded', 'Uploaded') ?></option>
                                    <option value="deleted"><?= get_label('deleted', 'Deleted') ?></option>
                                    <option value="updated_status"><?= get_label('updated_status', 'Updated status') ?></option>
                                    <option value="updated_priority"><?= get_label('updated_priority', 'Updated priority') ?></option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select class="form-select js-example-basic-multiple" id="type_filter" aria-label="Default select example" data-placeholder="<?= get_label('select_types', 'Select types') ?>" data-allow-clear="true" multiple>
                                    <option value="task">{{ get_label('task', 'Task') }}</option>
                                    <option value="media">{{ get_label('media', 'Media') }}</option>
                                </select>
                            </div>
                        </div>
                        @php
                        $visibleColumns = getUserPreferences('activity_log');
                        @endphp
                        <div class="table-responsive text-nowrap">
                            <input type="hidden" id="activity_log_between_date_from">
                            <input type="hidden" id="activity_log_between_date_to">
                            <input type="hidden" id="data_type" value="activity-log">
                            <input type="hidden" id="data_table" value="activity_log_table">
                            <input type="hidden" id="type_id" value="{{$task->id}}">
                            <input type="hidden" id="save_column_visibility">
                            <input type="hidden" id="multi_select">
                            <table id="activity_log_table" data-toggle="table" data-loading-template="loadingTemplate" data-url="{{ url('/activity-log/list') }}" data-icons-prefix="bx" data-icons="icons" data-show-refresh="true" data-total-field="total" data-trim-on-search="false" data-data-field="rows" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-side-pagination="server" data-show-columns="true" data-pagination="true" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-query-params="queryParams">
                                <thead>
                                    <tr>
                                        <th data-checkbox="true"></th>
                                        <th data-field="id" data-visible="{{ (in_array('id', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('id', 'ID') ?></th>
                                        <th data-field="actor_id" data-visible="{{ (in_array('actor_id', $visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('actioned_by_id', 'Actioned By ID') ?></th>
                                        <th data-field="actor_name" data-visible="{{ (in_array('actor_name', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('actioned_by', 'Actioned By') ?></th>
                                        <th data-field="actor_type" data-visible="{{ (in_array('actor_type', $visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('actioned_by_type', 'Actioned By Type') ?></th>
                                        <th data-field="type_id" data-visible="{{ (in_array('type_id', $visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('type_id', 'Type ID') ?></th>
                                        <th data-field="parent_type_id" data-visible="{{ (in_array('parent_type_id', $visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('parent_type_id', 'Parent type ID') ?></th>
                                        <th data-field="activity" data-visible="{{ (in_array('activity', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('activity', 'Activity') ?></th>
                                        <th data-field="type" data-visible="{{ (in_array('type', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('type', 'Type') ?></th>
                                        <th data-field="parent_type" data-visible="{{ (in_array('parent_type', $visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('parent_type', 'Parent type') ?></th>
                                        <th data-field="type_title" data-visible="{{ (in_array('type_title', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('type_title', 'Type title') ?></th>
                                        <th data-field="parent_type_title" data-visible="{{ (in_array('parent_type_title', $visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('parent_type_title', 'Parent type title') ?></th>
                                        <th data-field="message" data-visible="{{ (in_array('message', $visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('message', 'Message') ?></th>
                                        <th data-field="created_at" data-visible="{{ (in_array('created_at', $visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('created_at', 'Created at') ?></th>
                                        <th data-field="updated_at" data-visible="{{ (in_array('updated_at', $visibleColumns)) ? 'true' : 'false' }}" data-sortable="true"><?= get_label('updated_at', 'Updated at') ?></th>
                                        <th data-field="actions" data-visible="{{ (in_array('actions', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false' }}"><?= get_label('actions', 'Actions') ?></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
        <div class="modal fade" id="add_media_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form class="modal-content form-horizontal" id="media-upload" action="{{url('tasks/upload-media')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('add_media', 'Add Media') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-primary alert-dismissible" role="alert"><?= $media_storage_settings['media_storage_type'] == 's3' ? get_label('storage_type_set_as_aws_s3', 'Storage type is set as AWS S3 storage') : get_label('storage_type_set_as_local', 'Storage type is set as local storage') ?>, <a href="{{ url('settings/media-storage') }}"><?= get_label('click_here_to_change', 'Click here to change.') ?></a></div>
                        <div class="alert alert-info">
                            <?= get_label('allowed_file_types', 'Allowed file types') ?>: <strong><?= str_replace(',', ', ', $general_settings['allowed_file_types']) ?></strong>
                        </div>
                        <div class="dropzone dz-clickable" id="media-upload-dropzone">
                        </div>
                        <div class="form-group mt-4 text-center">
                            <button class="btn btn-primary" id="upload_media_btn"><?= get_label('upload', 'Upload') ?></button>
                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="form-group" id="error_box">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <?= get_label('close', 'Close') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        var label_delete = '<?= get_label('delete', 'Delete') ?>';
    </script>
    <script src="{{asset('assets/js/pages/task-information.js')}}"></script>
    @endsection