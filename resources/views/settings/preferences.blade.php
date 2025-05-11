@extends('layout')
@section('title')
<?= get_label('preferences', 'Preferences') ?>
@endsection
@php
$enabledNotifications = getUserPreferences('notification_preference','enabled_notifications',getAuthenticatedUser(true,true));
@endphp
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-2 mt-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item">
                        <a href="{{url('home')}}"><?= get_label('home', 'Home') ?></a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= get_label('preferences', 'Preferences') ?>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="list-group list-group-horizontal-md text-md-center mb-4">
                        <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#notification-preferences">{{get_label('notification_preferences', 'Notification Preferences')}}</a>
                        <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#customize-menu-order">{{get_label('customize_menu_order', 'Customize Menu Order')}}</a>
                    </div>
                    <div class="tab-content px-0">
                        <div class="tab-pane fade show active" id="notification-preferences">
                            <form action="{{url('save-notification-preferences')}}" class="form-submit-event" method="POST">
                                <input type="hidden" name="dnr">
                                <div class="table-responsive">
                                    <table class="table table-striped table-borderless border-bottom">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="form-check">
                                                        <input type="checkbox" id="selectAllPreferences" class="form-check-input">
                                                        <label class="form-check-label" for="selectAllPreferences"><?= get_label('select_all', 'Select all') ?></label>
                                                    </div>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th class="text-nowrap">{{get_label('type','Type')}}</th>
                                                <th class="text-nowrap text-center">{{get_label('email','Email')}}</th>
                                                <th class="text-nowrap text-center">{{get_label('sms','SMS')}}</th>
                                                <th class="text-nowrap text-center">{{get_label('whatsapp','WhatsApp')}}</th>
                                                <th class="text-nowrap text-center">{{get_label('system','System')}}</th>
                                                <th class="text-nowrap text-center">{{get_label('slack','Slack')}}</th>
                                                <th class="text-nowrap text-center">{{get_label('push_in_app', 'Push (In APP)')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-nowrap">{{get_label('project_assignment','Project Assignment')}}</td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck1" name="enabled_notifications[]" value="email_project_assignment" {{ (is_array($enabledNotifications) && (in_array('email_project_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck2" name="enabled_notifications[]" value="sms_project_assignment" {{ (is_array($enabledNotifications) && (in_array('sms_project_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck2" name="enabled_notifications[]" value="whatsapp_project_assignment" {{ (is_array($enabledNotifications) && (in_array('whatsapp_project_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck3" name="enabled_notifications[]" value="system_project_assignment" {{ (is_array($enabledNotifications) && (in_array('system_project_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck4" name="enabled_notifications[]" value="slack_project_assignment" {{ (is_array($enabledNotifications) && (in_array('slack_project_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck3" name="enabled_notifications[]" value="push_project_assignment" {{ (is_array($enabledNotifications) && (in_array('push_project_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap">{{get_label('project_status_updation','Project Status Updation')}}</td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck4" name="enabled_notifications[]" value="email_project_status_updation" {{ (is_array($enabledNotifications) && (in_array('email_project_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck5" name="enabled_notifications[]" value="sms_project_status_updation" {{ (is_array($enabledNotifications) && (in_array('sms_project_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck5" name="enabled_notifications[]" value="whatsapp_project_status_updation" {{ (is_array($enabledNotifications) && (in_array('whatsapp_project_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck6" name="enabled_notifications[]" value="system_project_status_updation" {{ (is_array($enabledNotifications) && (in_array('system_project_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck6" name="enabled_notifications[]" value="slack_project_status_updation" {{ (is_array($enabledNotifications) && (in_array('slack_project_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck3" name="enabled_notifications[]" value="push_project_status_updation" {{ (is_array($enabledNotifications) && (in_array('push_project_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap">{{get_label('task_assignment','Task Assignment')}}</td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck7" name="enabled_notifications[]" value="email_task_assignment" {{ (is_array($enabledNotifications) && (in_array('email_task_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck8" name="enabled_notifications[]" value="sms_task_assignment" {{ (is_array($enabledNotifications) && (in_array('sms_task_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck8" name="enabled_notifications[]" value="whatsapp_task_assignment" {{ (is_array($enabledNotifications) && (in_array('whatsapp_task_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck9" name="enabled_notifications[]" value="system_task_assignment" {{ (is_array($enabledNotifications) && (in_array('system_task_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck9" name="enabled_notifications[]" value="slack_task_assignment" {{ (is_array($enabledNotifications) && (in_array('slack_task_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck3" name="enabled_notifications[]" value="push_task_assignment" {{ (is_array($enabledNotifications) && (in_array('push_task_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap">{{get_label('task_status_updation','Task Status Updation')}}</td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck10" name="enabled_notifications[]" value="email_task_status_updation" {{ (is_array($enabledNotifications) && (in_array('email_task_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck11" name="enabled_notifications[]" value="sms_task_status_updation" {{ (is_array($enabledNotifications) && (in_array('sms_task_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck11" name="enabled_notifications[]" value="whatsapp_task_status_updation" {{ (is_array($enabledNotifications) && (in_array('whatsapp_task_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck12" name="enabled_notifications[]" value="system_task_status_updation" {{ (is_array($enabledNotifications) && (in_array('system_task_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck13" name="enabled_notifications[]" value="slack_task_status_updation" {{ (is_array($enabledNotifications) && (in_array('slack_task_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck12" name="enabled_notifications[]" value="push_task_status_updation" {{ (is_array($enabledNotifications) && (in_array('push_task_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap">{{get_label('workspace_assignment','Workspace Assignment')}}</td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck7" name="enabled_notifications[]" value="email_workspace_assignment" {{ (is_array($enabledNotifications) && (in_array('email_workspace_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck8" name="enabled_notifications[]" value="sms_workspace_assignment" {{ (is_array($enabledNotifications) && (in_array('sms_workspace_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck8" name="enabled_notifications[]" value="whatsapp_workspace_assignment" {{ (is_array($enabledNotifications) && (in_array('whatsapp_workspace_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck9" name="enabled_notifications[]" value="system_workspace_assignment" {{ (is_array($enabledNotifications) && (in_array('system_workspace_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck9" name="enabled_notifications[]" value="slack_workspace_assignment" {{ (is_array($enabledNotifications) && (in_array('slack_workspace_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck9" name="enabled_notifications[]" value="push_workspace_assignment" {{ (is_array($enabledNotifications) && (in_array('push_workspace_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap">{{get_label('meeting_assignment','Meeting Assignment')}}</td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck7" name="enabled_notifications[]" value="email_meeting_assignment" {{ (is_array($enabledNotifications) && (in_array('email_meeting_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck8" name="enabled_notifications[]" value="sms_meeting_assignment" {{ (is_array($enabledNotifications) && (in_array('sms_meeting_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck8" name="enabled_notifications[]" value="whatsapp_meeting_assignment" {{ (is_array($enabledNotifications) && (in_array('whatsapp_meeting_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck9" name="enabled_notifications[]" value="system_meeting_assignment" {{ (is_array($enabledNotifications) && (in_array('system_meeting_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck9" name="enabled_notifications[]" value="slack_meeting_assignment" {{ (is_array($enabledNotifications) && (in_array('slack_meeting_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck9" name="enabled_notifications[]" value="push_meeting_assignment" {{ (is_array($enabledNotifications) && (in_array('push_meeting_assignment', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                            </tr>
                                            @if(!isClient())
                                            @php
                                            $isAdminOrLeaveEditor = is_admin_or_leave_editor();
                                            @endphp
                                            <tr>
                                                <td class="text-nowrap">{{get_label('leave_request_creation','Leave Request Creation')}}</td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck7" name="enabled_notifications[]" value="email_leave_request_creation" {{$isAdminOrLeaveEditor ? '' : 'disabled'}} {{ (is_array($enabledNotifications) && (in_array('email_leave_request_creation', $enabledNotifications) || (empty($enabledNotifications) && $isAdminOrLeaveEditor))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck8" name="enabled_notifications[]" value="sms_leave_request_creation" {{$isAdminOrLeaveEditor ? '' : 'disabled'}} {{ (is_array($enabledNotifications) && (in_array('sms_leave_request_creation', $enabledNotifications) || (empty($enabledNotifications) && $isAdminOrLeaveEditor))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck8" name="enabled_notifications[]" value="whatsapp_leave_request_creation" {{$isAdminOrLeaveEditor ? '' : 'disabled'}} {{ (is_array($enabledNotifications) && (in_array('whatsapp_leave_request_creation', $enabledNotifications) || (empty($enabledNotifications) && $isAdminOrLeaveEditor))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck9" name="enabled_notifications[]" value="system_leave_request_creation" {{$isAdminOrLeaveEditor ? '' : 'disabled'}} {{ (is_array($enabledNotifications) && (in_array('system_leave_request_creation', $enabledNotifications) || (empty($enabledNotifications) && $isAdminOrLeaveEditor))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck9" name="enabled_notifications[]" value="slack_leave_request_creation" {{$isAdminOrLeaveEditor ? '' : 'disabled'}} {{ (is_array($enabledNotifications) && (in_array('slack_leave_request_creation', $enabledNotifications) || (empty($enabledNotifications) && $isAdminOrLeaveEditor))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck9" name="enabled_notifications[]" value="push_leave_request_creation" {{$isAdminOrLeaveEditor ? '' : 'disabled'}} {{ (is_array($enabledNotifications) && (in_array('push_leave_request_creation', $enabledNotifications) || (empty($enabledNotifications) && $isAdminOrLeaveEditor))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap">{{get_label('leave_request_status_updation','Leave Request Status Updation')}}</td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck10" name="enabled_notifications[]" value="email_leave_request_status_updation" {{ (is_array($enabledNotifications) && (in_array('email_leave_request_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck11" name="enabled_notifications[]" value="sms_leave_request_status_updation" {{ (is_array($enabledNotifications) && (in_array('sms_leave_request_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck11" name="enabled_notifications[]" value="whatsapp_leave_request_status_updation" {{ (is_array($enabledNotifications) && (in_array('whatsapp_leave_request_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck12" name="enabled_notifications[]" value="system_leave_request_status_updation" {{ (is_array($enabledNotifications) && (in_array('system_leave_request_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck12" name="enabled_notifications[]" value="slack_leave_request_status_updation" {{ (is_array($enabledNotifications) && (in_array('slack_leave_request_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck12" name="enabled_notifications[]" value="push_leave_request_status_updation" {{ (is_array($enabledNotifications) && (in_array('push_leave_request_status_updation', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap">{{get_label('team_member_on_leave_alert','Team Member on Leave Alert')}}</td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck13" name="enabled_notifications[]" value="email_team_member_on_leave_alert" {{ (is_array($enabledNotifications) && (in_array('email_team_member_on_leave_alert', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck14" name="enabled_notifications[]" value="sms_team_member_on_leave_alert" {{ (is_array($enabledNotifications) && (in_array('sms_team_member_on_leave_alert', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck15" name="enabled_notifications[]" value="whatsapp_team_member_on_leave_alert" {{ (is_array($enabledNotifications) && (in_array('whatsapp_team_member_on_leave_alert', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck16" name="enabled_notifications[]" value="system_team_member_on_leave_alert" {{ (is_array($enabledNotifications) && (in_array('system_team_member_on_leave_alert', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck16" name="enabled_notifications[]" value="slack_team_member_on_leave_alert" {{ (is_array($enabledNotifications) && (in_array('slack_team_member_on_leave_alert', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck16" name="enabled_notifications[]" value="push_team_member_on_leave_alert" {{ (is_array($enabledNotifications) && (in_array('push_team_member_on_leave_alert', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td class="text-nowrap">{{get_label('birthday_wish','Birthday Wish')}}</td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck13" name="enabled_notifications[]" value="email_birthday_wish" {{ (is_array($enabledNotifications) && (in_array('email_birthday_wish', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck14" name="enabled_notifications[]" value="sms_birthday_wish" {{ (is_array($enabledNotifications) && (in_array('sms_birthday_wish', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck15" name="enabled_notifications[]" value="whatsapp_birthday_wish" {{ (is_array($enabledNotifications) && (in_array('whatsapp_birthday_wish', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck16" name="enabled_notifications[]" value="system_birthday_wish" {{ (is_array($enabledNotifications) && (in_array('system_birthday_wish', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck16" name="enabled_notifications[]" value="slack_birthday_wish" {{ (is_array($enabledNotifications) && (in_array('slack_birthday_wish', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck16" name="enabled_notifications[]" value="push_birthday_wish" {{ (is_array($enabledNotifications) && (in_array('push_birthday_wish', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap">{{get_label('work_anniversary_wish','Work Anni. Wish')}}</td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck13" name="enabled_notifications[]" value="email_work_anniversary_wish" {{ (is_array($enabledNotifications) && (in_array('email_work_anniversary_wish', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck14" name="enabled_notifications[]" value="sms_work_anniversary_wish" {{ (is_array($enabledNotifications) && (in_array('sms_work_anniversary_wish', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck15" name="enabled_notifications[]" value="whatsapp_work_anniversary_wish" {{ (is_array($enabledNotifications) && (in_array('whatsapp_work_anniversary_wish', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck16" name="enabled_notifications[]" value="system_work_anniversary_wish" {{ (is_array($enabledNotifications) && (in_array('system_work_anniversary_wish', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck16" name="enabled_notifications[]" value="slack_work_anniversary_wish" {{ (is_array($enabledNotifications) && (in_array('slack_work_anniversary_wish', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck16" name="enabled_notifications[]" value="push_work_anniversary_wish" {{ (is_array($enabledNotifications) && (in_array('push_work_anniversary_wish', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                           
                                            </tr>


                                            <tr>
                                                <td class="text-nowrap">{{get_label('reminder_task','Reminder task')}}</td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck13" name="enabled_notifications[]" value="email_task_reminder" {{ (is_array($enabledNotifications) && (in_array('email_task_reminder', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck14" name="enabled_notifications[]" value="sms_task_reminder" {{ (is_array($enabledNotifications) && (in_array('sms_task_reminder', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck15" name="enabled_notifications[]" value="whatsapp_task_reminder" {{ (is_array($enabledNotifications) && (in_array('whatsapp_task_reminder', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck16" name="enabled_notifications[]" value="system_task_reminder" {{ (is_array($enabledNotifications) && (in_array('system_task_reminder', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck16" name="enabled_notifications[]" value="slack_task_reminder" {{ (is_array($enabledNotifications) && (in_array('slack_task_reminder', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck16" name="enabled_notifications[]" value="push_ttask_reminder" {{ (is_array($enabledNotifications) && (in_array('push_task_reminder', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                           
                                            </tr>


                                            <tr>
                                                <td class="text-nowrap">{{get_label('recurring_task','Recurring task')}}</td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck13" name="enabled_notifications[]" value="email_recurring_task" {{ (is_array($enabledNotifications) && (in_array('email_recurring_task', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck14" name="enabled_notifications[]" value="sms_recurring_task" {{ (is_array($enabledNotifications) && (in_array('sms_recurring_task', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck15" name="enabled_notifications[]" value="whatsapp_recurring_task" {{ (is_array($enabledNotifications) && (in_array('whatsapp_recurring_task', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck16" name="enabled_notifications[]" value="system_recurring_task" {{ (is_array($enabledNotifications) && (in_array('system_recurring_task', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck16" name="enabled_notifications[]" value="slack_recurring_task" {{ (is_array($enabledNotifications) && (in_array('slack_recurring_task', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="defaultCheck16" name="enabled_notifications[]" value="push_recurring_task" {{ (is_array($enabledNotifications) && (in_array('push_recurring_task', $enabledNotifications) || empty($enabledNotifications))) ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                           
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary me-2" id="submit_btn"><?= get_label('update', 'Update') ?></button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade show" id="customize-menu-order">
                            <form id="menu-order-form" method="POST">
                                <ul id="sortable-menu">
                                    @foreach ($sortedMenus as $menu)
                                    @if (!isset($menu['show']) || $menu['show'] === 1)
                                    <li data-id="{{ $menu['id'] }}">
                                        <span class="handle bx bx-menu"></span>
                                        <span>{{ $menu['label'] }}</span>

                                        <!-- Check if there are submenus -->
                                        @if (!empty($menu['submenus']))
                                        <ul class="submenu">
                                            @foreach ($menu['submenus'] as $submenu)
                                            @if (!isset($submenu['show']) || $submenu['show'] === 1)
                                            <li data-id="{{ $submenu['id'] }}">
                                                <span class="handle bx bx-menu"></span>
                                                <span>{{ $submenu['label'] }}</span>
                                            </li>
                                            @endif
                                            @endforeach
                                        </ul>
                                        @endif
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary me-2" id="btnSaveMenuOrder"><?= get_label('update', 'Update') ?></button>
                                    <button type="button" class="btn btn-warning" id="btnResetDefaultMenuOrder"><?= get_label('reset_to_default', 'Reset to default') ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmResetDefaultMenuOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('confirm_reset_default_menu', 'Are you sure you want to reset the menu order to the default?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary" id="btnconfirmResetDefaultMenuOrder"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('assets/js/pages/preferences.js')}}"></script>
@endsection