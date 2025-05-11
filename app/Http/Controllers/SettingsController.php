<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use App\Models\Template;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;

class SettingsController extends Controller
{
    public function index()
    {
        $timezones = get_timezone_array();
        return view('settings.general_settings', compact('timezones'));
    }

    public function security()
    {
        return view('settings.security_settings');
    }

    public function pusher()
    {
        return view('settings.pusher_settings');
    }

    public function email()
    {
        return view('settings.email_settings');
    }

    public function sms_gateway()
    {
        return view('settings.sms_gateway_settings');
    }

    public function media_storage()
    {
        return view('settings.media_storage_settings');
    }

    public function templates()
    {
        return view('settings.template_settings');
    }

    public function companyInfo()
    {
        return view('settings.company_info_settings');
    }

    public function store_general_settings(Request $request)
    {
        $request->validate([
            'company_title' => ['required'],
            'site_url' => ['required'],
            'timezone' => ['required'],
            'currency_full_form' => ['required'],
            'currency_symbol' => ['required'],
            'currency_code' => ['required'],
            'date_format' => ['required'],
            'toast_time_out' => ['nullable', 'numeric', 'min:0.1'],
            'allowed_max_upload_size' => ['nullable', 'numeric', 'min:1'],
        ]);

        // Retrieve existing settings
        $fetched_data = Setting::where('variable', 'general_settings')->first();
        $settings = $fetched_data ? json_decode($fetched_data->value, true) : [];

        // Extract form values
        $form_val = $request->except('_token', '_method', 'redirect_url');

        // Handle logo uploads
        $form_val['full_logo'] = $request->hasFile('full_logo')
            ? $request->file('full_logo')->store('logos', 'public')
            : ($settings['full_logo'] ?? '');

        if ($request->hasFile('full_logo') && !empty($settings['full_logo'])) {
            Storage::disk('public')->delete($settings['full_logo']);
        }

        $form_val['half_logo'] = $request->hasFile('half_logo')
            ? $request->file('half_logo')->store('logos', 'public')
            : ($settings['half_logo'] ?? '');

        if ($request->hasFile('half_logo') && !empty($settings['half_logo'])) {
            Storage::disk('public')->delete($settings['half_logo']);
        }

        $form_val['favicon'] = $request->hasFile('favicon')
            ? $request->file('favicon')->store('logos', 'public')
            : ($settings['favicon'] ?? '');

        if ($request->hasFile('favicon') && !empty($settings['favicon'])) {
            Storage::disk('public')->delete($settings['favicon']);
        }

        $form_val['toast_time_out'] = $request->filled('toast_time_out') ? $request->input('toast_time_out') : 5;
        $form_val['priLangAsAuth'] = $request->has('priLangAsAuth') && $request->input('priLangAsAuth') == 'on' ? 1 : 0;
        $form_val['upcomingBirthdays'] = $request->has('upcomingBirthdays') && $request->input('upcomingBirthdays') == 'on' ? 1 : 0;
        $form_val['upcomingWorkAnniversaries'] = $request->has('upcomingWorkAnniversaries') && $request->input('upcomingWorkAnniversaries') == 'on' ? 1 : 0;
        $form_val['membersOnLeave'] = $request->has('membersOnLeave') && $request->input('membersOnLeave') == 'on' ? 1 : 0;

        // Merge new settings with existing settings
        $merged_settings = array_merge($settings, $form_val);

        // Prepare data for saving
        $data = [
            'variable' => 'general_settings',
            'value' => json_encode($merged_settings),
        ];

        // Update or create settings
        if ($fetched_data === null) {
            Setting::create($data);
        } else {
            $fetched_data->update($data);
        }

        session()->put('date_format', $request->input('date_format'));
        Session::flash('message', 'Settings saved successfully.');

        return response()->json(['error' => false]);
    }


    public function store_security_settings(Request $request)
    {
        // Validate security settings
        $request->validate([
            'max_attempts' => 'nullable|integer|min:1',
            'lock_time' => 'required_with:max_attempts|nullable|integer|min:1',
            'max_files_allowed' => 'nullable|integer|min:1',
            'allowed_file_types' => 'nullable|string',
        ]);

        // Extract relevant request data
        $form_val = $request->except('_token', '_method', 'dnr');

        // Retrieve existing general settings
        $generalSettingsArray = get_settings('general_settings');

        $form_val['allowSignup'] = $request->has('allowSignup') && $request->input('allowSignup') == 'on' ? 1 : 0;

        // Define valid file type extensions
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'tiff', 'ico', 'psd', 'heic'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'odt', 'ods', 'odp', 'csv', 'md'];
        $archiveExtensions = ['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz', 'iso'];
        $audioExtensions = ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a', 'wma', 'aiff', 'opus', 'amr'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm', '3gp', 'm4v', 'mpg', 'mpeg'];
        $executableExtensions = ['exe', 'bat', 'sh', 'bin', 'msi', 'cmd', 'jar', 'apk'];
        $codeExtensions = ['html', 'htm', 'css', 'js', 'php', 'java', 'py', 'rb', 'pl', 'cpp', 'c', 'h', 'cs', 'xml', 'json', 'yml', 'sql'];
        $fontExtensions = ['ttf', 'otf', 'woff', 'woff2', 'eot'];
        $miscExtensions = ['ics', 'vcf', 'swf', 'epub', 'mobi', 'azw', 'bak'];

        // Merge all valid extensions
        $validExtensions = array_merge(
            $imageExtensions,
            $documentExtensions,
            $archiveExtensions,
            $audioExtensions,
            $videoExtensions,
            $executableExtensions,
            $codeExtensions,
            $fontExtensions,
            $miscExtensions
        );

        // Validate allowed file types
        $adminInput = $request->input('allowed_file_types', ''); // Get the input or default to an empty string
        $adminExtensions = explode(',', str_replace(' ', '', $adminInput)); // Clean and convert to array

        $invalidExtensions = [];
        foreach ($adminExtensions as $extension) {
            $extension = ltrim($extension, '.'); // Remove leading period
            if (!in_array($extension, $validExtensions)) {
                $invalidExtensions[] = $extension; // Collect invalid extensions
            }
        }

        // If invalid extensions found, return an error
        if (!empty($invalidExtensions)) {
            return response()->json([
                'error' => true,
                'message' => 'Invalid file extensions: ' . implode(', ', $invalidExtensions)
            ]);
        }

        // Save valid extensions to the general settings
        $allowedFileTypes = implode(',', $adminExtensions);
        $generalSettingsArray['allowed_file_types'] = $allowedFileTypes; // Add to settings array

        // Merge request data into general settings
        foreach ($form_val as $key => $value) {
            // Additional settings
            $generalSettingsArray[$key] = $value;
        }

        // Prepare data for saving
        $data = [
            'variable' => 'general_settings',
            'value' => json_encode($generalSettingsArray),
        ];

        // Check if general settings exist, then update or create
        $fetched_data = Setting::where('variable', 'general_settings')->first();
        if ($fetched_data === null) {
            Setting::create($data);
        } else {
            $fetched_data->update($data);
        }

        return response()->json(['error' => false, 'message' => 'Settings saved successfully.']);
    }


    public function store_pusher_settings(Request $request)
    {
        $request->validate([
            'pusher_app_id' => ['required'],
            'pusher_app_key' => ['required'],
            'pusher_app_secret' => ['required'],
            'pusher_app_cluster' => ['required']
        ]);
        $fetched_data = Setting::where('variable', 'pusher_settings')->first();
        $form_val = $request->except('_token', '_method', 'dnr');
        $data = [
            'variable' => 'pusher_settings',
            'value' => json_encode($form_val),
        ];

        if ($fetched_data == null) {
            Setting::create($data);
        } else {
            Setting::where('variable', 'pusher_settings')->update($data);
        }
        return response()->json(['error' => false, 'message' => 'Settings saved successfully.']);
    }

    public function store_email_settings(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'smtp_host' => ['required'],
            'smtp_port' => ['required'],
            'email_content_type' => ['required'],
            'smtp_encryption' => ['required']
        ]);
        $fetched_data = Setting::where('variable', 'email_settings')->first();
        $form_val = $request->except('_token', '_method', 'dnr');
        $data = [
            'variable' => 'email_settings',
            'value' => json_encode($form_val),
        ];

        if ($fetched_data == null) {
            Setting::create($data);
        } else {
            Setting::where('variable', 'email_settings')->update($data);
        }
        return response()->json(['error' => false, 'message' => 'Settings saved successfully.']);
    }

    public function store_media_storage_settings(Request $request)
    {
        $request->validate([
            'media_storage_type' => config('constants.ALLOW_MODIFICATION') === 0 ? 'required|in:local' : 'required|in:local,s3',
            's3_key' => $request->input('media_storage_type') === 's3' ? 'required' : 'nullable',
            's3_secret' => $request->input('media_storage_type') === 's3' ? 'required' : 'nullable',
            's3_region' => $request->input('media_storage_type') === 's3' ? 'required' : 'nullable',
            's3_bucket' => $request->input('media_storage_type') === 's3' ? 'required' : 'nullable',
        ]);
        $fetched_data = Setting::where('variable', 'media_storage_settings')->first();
        $form_val = $request->except('_token', '_method', 'dnr');
        $data = [
            'variable' => 'media_storage_settings',
            'value' => json_encode($form_val),
        ];

        if ($fetched_data == null) {
            Setting::create($data);
        } else {
            Setting::where('variable', 'media_storage_settings')->update($data);
        }
        return response()->json(['error' => false, 'message' => 'Settings saved successfully.']);
    }

    public function store_sms_gateway_settings(Request $request)
    {
        $request->validate([
            'base_url' => 'required|string',
            'sms_gateway_method' => 'required|string|in:POST,GET',
            'header_key' => 'nullable|array',
            'header_value' => 'nullable|array',
            'body_key' => 'nullable|array',
            'body_value' => 'nullable|array',
            'params_key' => 'nullable|array',
            'params_value' => 'nullable|array',
            'text_format_data' => 'nullable|string',
        ]);

        // Prepare the data to store
        $data = [
            'base_url' => $request->base_url,
            'sms_gateway_method' => $request->sms_gateway_method,
            'header_data' => $request->header_key && $request->header_value ? array_combine($request->header_key, $request->header_value) : [],
            'body_formdata' => $request->body_key && $request->body_value ? array_combine($request->body_key, $request->body_value) : [],
            'params_data' => $request->params_key && $request->params_value ? array_combine($request->params_key, $request->params_value) : [],
            'text_format_data' => $request->text_format_data,
        ];

        // Convert data to JSON
        $jsonData = json_encode($data);

        // Check if the setting exists
        $existingSetting = Setting::where('variable', 'sms_gateway_settings')->first();

        if ($existingSetting) {
            // Update existing setting
            $existingSetting->update(['value' => $jsonData]);
        } else {
            // Create new setting
            Setting::create([
                'variable' => 'sms_gateway_settings',
                'value' => $jsonData,
            ]);
        }
        return response()->json(['error' => false, 'message' => 'Settings saved successfully.']);
    }

    public function store_whatsapp_settings(Request $request)
    {
        $request->validate([
            'whatsapp_access_token' => 'required|string',
            'whatsapp_phone_number_id' => 'required|string',
        ]);

        // Prepare the data to store
        $data = [
            'whatsapp_access_token' => $request->whatsapp_access_token,
            'whatsapp_phone_number_id' => $request->whatsapp_phone_number_id,
        ];
        // Convert data to JSON
        $jsonData = json_encode($data);

        // Check if the setting exists
        $existingSetting = Setting::where('variable', 'whatsapp_settings')->first();

        if ($existingSetting) {
            // Update existing setting
            $existingSetting->update(['value' => $jsonData]);
        } else {
            // Create new setting
            Setting::create([
                'variable' => 'whatsapp_settings',
                'value' => $jsonData,
            ]);
        }
        return response()->json(['error' => false, 'message' => 'Settings saved successfully.']);
    }

    public function store_slack_settings(Request $request)
    {
        $request->validate([
            'slack_bot_token' => 'required|string',

        ]);

        // Prepare the data to store
        $data = [
            'slack_bot_token' => $request->slack_bot_token,

        ];
        // Convert data to JSON
        $jsonData = json_encode($data);

        // Check if the setting exists
        $existingSetting = Setting::where('variable', 'slack_settings')->first();

        if ($existingSetting) {
            // Update existing setting
            $existingSetting->update(['value' => $jsonData]);
        } else {
            // Create new setting
            Setting::create([
                'variable' => 'slack_settings',
                'value' => $jsonData,
            ]);
        }
        return response()->json(['error' => false, 'message' => 'Settings saved successfully.']);
    }

    public function store_template(Request $request)
    {
        $formFields = $request->validate([
            'type' => 'required',
            'name' => 'required',
            'subject' => [
                function ($attribute, $value, $fail) use ($request) {
                    if (($request->input('type') === 'email' || $request->input('type') === 'system') && $request->input('status') === '1' && empty($value)) {
                        $fail('This field is required when status is active.');
                    }
                },
            ],
            'content' => [
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('status') === '1' && empty($value)) {
                        $fail('The message field is required when status is active.');
                    }
                },
            ],
            'status' => 'required',
        ], [
            'type.required' => 'The type field is required.',
            'name.required' => 'The name field is required.',
            'status.required' => 'The status field is required.'
        ]);

        $type = $request->input('type');
        $name = $request->input('name');

        $fetched_data = Template::where('type', $type)
            ->where('name', $name)
            ->first();
        if ($fetched_data == null) {
            // When creating a new record, provide a default value for the status field
            Template::create($formFields);
        } else {
            // Use an array of conditions for the update query
            Template::where([
                ['type', '=', $type],
                ['name', '=', $name]
            ])->update($formFields);
        }
        return response()->json(['error' => false, 'message' => 'Saved successfully.']);
    }

    public function terms_privacy_about()
    {
        $privacy_policy = get_settings('privacy_policy');
        $terms_conditions = get_settings('terms_conditions');
        $about_us = get_settings('about_us');

        return view('settings.terms_privacy_about', ['privacy_policy' => $privacy_policy, 'terms_conditions' => $terms_conditions, 'about_us' => $about_us]);
    }

    public function store_terms_privacy_about(Request $request)
    {
        // Validate that a variable name and its content are provided
        $request->validate([
            'variable' => ['required', 'in:privacy_policy,terms_conditions,about_us'],
            'value' => ['required'],
        ]);

        // Fetch existing settings based on the variable name
        $fetched_data = Setting::where('variable', $request->variable)->first();

        // Prepare data to be stored in the database
        $data = [
            'variable' => $request->variable,
            'value' => json_encode([$request->variable => $request->value]), // Store as {"variable": "value"}
        ];

        // If no existing settings found, create new; otherwise, update existing
        if ($fetched_data == null) {
            Setting::create($data);
        } else {
            $fetched_data->update($data);
        }

        return response()->json([
            'error' => false,
            'message' => ucfirst(str_replace('_', ' ', $request->variable)) . ' Saved Successfully!'
        ]);
    }

    public function store_company_info(Request $request)
    {
        $request->validate([
            'companyEmail' => ['nullable', 'email'],
        ]);
        $fetched_data = Setting::where('variable', 'company_information')->first();
        $form_val = $request->except('_token', '_method');
        $data = [
            'variable' => 'company_information',
            'value' => json_encode($form_val),
        ];

        if ($fetched_data == null) {
            Setting::create($data);
        } else {
            Setting::where('variable', 'company_information')->update($data);
        }

        return response()->json(['error' => false, 'message' => 'Company information saved successfully.']);
    }




    public function get_default_template(Request $request)
    {
        // Get the type and name from the request
        $type = $request->input('type');
        $name = $request->input('name');
        // Define the directory structure based on type and name
        switch ($type) {
            case 'email':
                $directory = 'views/mail/default_templates/';
                switch ($name) {
                    case 'account_creation':
                        $directory .= 'account_creation.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    case 'verify_email':
                        $directory .= 'verify_email.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    case 'forgot_password':
                        $directory .= 'forgot_password.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    case 'project_assignment':
                        $directory .= 'project_assignment.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    case 'task_assignment':
                        $directory .= 'task_assignment.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    case 'workspace_assignment':
                        $directory .= 'workspace_assignment.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    case 'meeting_assignment':
                        $directory .= 'meeting_assignment.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    case 'leave_request_creation':
                        $directory .= 'leave_request_creation.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    case 'leave_request_status_updation':
                        $directory .= 'leave_request_status_updation.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    case 'team_member_on_leave_alert':
                        $directory .= 'team_member_on_leave_alert.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    case 'project_status_updation':
                        $directory .= 'project_status_updation.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    case 'task_status_updation':
                        $directory .= 'task_status_updation.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    case 'birthday_wish':
                        $directory .= 'birthday_wish.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    case 'work_anniversary_wish':
                        $directory .= 'work_anniversary_wish.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    case 'task_reminder':
                        $directory .= 'task_reminder.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    case 'recurring_task':
                        $directory .= 'recurring_task.blade.php';
                        // Include or return the file content based on $directory
                        break;
                    default:
                        return response()->json(['error' => true, 'message' => 'Unknown email template name.']);
                        break;
                }
                // Return or include the file based on the constructed $directory
                break;

            case 'sms':
            case 'whatsapp':
                switch ($name) {
                    case 'project_assignment':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'Hello, {FIRST_NAME} {LAST_NAME} You have been assigned a new project {PROJECT_TITLE}, ID:#{PROJECT_ID}.']);
                        break;
                    case 'project_status_updation':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => '{UPDATER_FIRST_NAME} {UPDATER_LAST_NAME} has updated the status of project {PROJECT_TITLE}, ID:#{PROJECT_ID}, from {OLD_STATUS} to {NEW_STATUS}.']);
                        break;
                    case 'task_assignment':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'Hello, {FIRST_NAME} {LAST_NAME} You have been assigned a new task {TASK_TITLE}, ID:#{TASK_ID}.']);
                        break;
                    case 'task_status_updation':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => '{UPDATER_FIRST_NAME} {UPDATER_LAST_NAME} has updated the status of task {TASK_TITLE}, ID:#{TASK_ID}, from {OLD_STATUS} to {NEW_STATUS}.']);
                        break;
                    case 'workspace_assignment':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'Hello, {FIRST_NAME} {LAST_NAME} You have been added in a new workspace {WORKSPACE_TITLE}, ID:#{WORKSPACE_ID}.']);
                        break;
                    case 'meeting_assignment':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'Hello, {FIRST_NAME} {LAST_NAME} You have been added in a new meeting {MEETING_TITLE}, ID:#{MEETING_ID}.']);
                        break;
                    case 'leave_request_creation':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'New Leave Request ID:#{ID} Has Been Created By {REQUESTEE_FIRST_NAME} {REQUESTEE_LAST_NAME}.']);
                        break;
                    case 'leave_request_status_updation':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'Leave Request ID:#{ID} Status Updated From {OLD_STATUS} To {NEW_STATUS}.']);
                        break;
                    case 'team_member_on_leave_alert':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => '{REQUESTEE_FIRST_NAME} {REQUESTEE_LAST_NAME} will be on {TYPE} leave from {FROM} to {TO}.']);
                        break;
                    case 'birthday_wish':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'Hello {FIRST_NAME} {LAST_NAME}, {COMPANY_TITLE} wishes you a very Happy Birthday!']);
                        break;
                    case 'work_anniversary_wish':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'Hello {FIRST_NAME} {LAST_NAME}, {COMPANY_TITLE} wishes you a very happy work anniversary!']);
                        break;
                     case 'task_reminder':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'You have a task reminder for Task #{TASK_ID} - "{TASK_TITLE}". You can view the task here: {TASK_URL}']);
                    case 'recurring_task':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'The recurring task #{TASK_ID} - "{TASK_TITLE}" has been executed. You can view the new instance here: {TASK_URL}']);
                    default:
                        return response()->json(['error' => true, 'message' => 'Unknown SMS template name.']);
                        break;
                }
                break;

            case 'system':
            case 'push':
                switch ($name) {
                    case 'project_assignment':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => '{ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME} assigned you new project: {PROJECT_TITLE}, ID:#{PROJECT_ID}.']);
                        break;
                    case 'project_status_updation':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => '{UPDATER_FIRST_NAME} {UPDATER_LAST_NAME} has updated the status of project {PROJECT_TITLE}, ID:#{PROJECT_ID}, from {OLD_STATUS} to {NEW_STATUS}.']);
                        break;
                    case 'task_assignment':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => '{ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME} assigned you new task: {TASK_TITLE}, ID:#{TASK_ID}.']);
                        break;
                    case 'task_status_updation':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => '{UPDATER_FIRST_NAME} {UPDATER_LAST_NAME} has updated the status of task {TASK_TITLE}, ID:#{TASK_ID}, from {OLD_STATUS} to {NEW_STATUS}.']);
                        break;
                    case 'workspace_assignment':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => '{ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME} added you in a new workspace {WORKSPACE_TITLE}, ID:#{WORKSPACE_ID}.']);
                        break;
                    case 'meeting_assignment':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => '{ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME} added you in a new meeting {MEETING_TITLE}, ID:#{MEETING_ID}.']);
                        break;
                    case 'leave_request_creation':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'New Leave Request ID:#{ID} Has Been Created By {REQUESTEE_FIRST_NAME} {REQUESTEE_LAST_NAME}.']);
                        break;
                    case 'leave_request_status_updation':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'Leave Request ID:#{ID} Status Updated From {OLD_STATUS} To {NEW_STATUS}.']);
                        break;
                    case 'team_member_on_leave_alert':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => '{REQUESTEE_FIRST_NAME} {REQUESTEE_LAST_NAME} will be on {TYPE} leave from {FROM} to {TO}.']);
                        break;
                    case 'birthday_wish':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'Hello {FIRST_NAME} {LAST_NAME}, {COMPANY_TITLE} wishes you a very Happy Birthday!']);
                        break;
                    case 'work_anniversary_wish':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'Hello {FIRST_NAME} {LAST_NAME}, {COMPANY_TITLE} wishes you a very happy work anniversary!']);
                        break;
                    case 'announcement':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => '{CREATOR_FIRST_NAME} {CREATOR_LAST_NAME} has made a new announcement titled "{ANNOUNCEMENT_TITLE}". Shared by {COMPANY_TITLE} ({CURRENT_YEAR}).']);
                        break;
                    case 'task_reminder':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'You have a task reminder for Task #{TASK_ID} - "{TASK_TITLE}".']);
                    case 'recurring_task':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'The recurring task #{TASK_ID} - "{TASK_TITLE}" has been executed.']);
                    default:
                        return response()->json(['error' => true, 'message' => 'Unknown SMS template name.']);
                        break;
                }
                break;

            case 'slack':
                switch ($name) {
                    case 'project_assignment':
                        return response()->json([
                            'error' => false,
                            'message' => 'Reset to default successfully.',
                            'content' => '*New Project Assigned:* {PROJECT_TITLE}, ID: #{PROJECT_ID}. By {ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME} You can find the project here :{PROJECT_URL}'
                        ]);
                        break;
                    case 'project_status_updation':
                        return response()->json([
                            'error' => false,
                            'message' => 'Reset to default successfully.',
                            'content' => '*Project Status Updated:* By {UPDATER_FIRST_NAME} {UPDATER_LAST_NAME} , {PROJECT_TITLE}, ID: #{PROJECT_ID}. Status changed from `{OLD_STATUS}` to `{NEW_STATUS}`. You can find the project here :{PROJECT_URL}'
                        ]);
                        break;
                    case 'task_assignment':
                        return response()->json([
                            'error' => false,
                            'message' => 'Reset to default successfully.',
                            'content' => '*New Task Assigned:* {TASK_TITLE}, ID: #{TASK_ID}. By {ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME} You can find the task here : {TASK_URL}'
                        ]);
                        break;
                    case 'task_status_updation':
                        return response()->json([
                            'error' => false,
                            'message' => 'Reset to default successfully.',
                            'content' => '*Task Status Updated:* By {UPDATER_FIRST_NAME} {UPDATER_LAST_NAME},  {TASK_TITLE}, ID: #{TASK_ID}. Status changed from `{OLD_STATUS}` to `{NEW_STATUS}`. You can find the Task here : {TASK_URL}'
                        ]);
                        break;
                    case 'workspace_assignment':
                        return response()->json([
                            'error' => false,
                            'message' => 'Reset to default successfully.',
                            'content' => '*New Workspace Added:* By {ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME},   {WORKSPACE_TITLE}, ID: #{WORKSPACE_ID}. You can find the Workspace here : {WORKSPACE_URL}'
                        ]);
                        break;
                    case 'meeting_assignment':
                        return response()->json([
                            'error' => false,
                            'message' => 'Reset to default successfully.',
                            'content' => '*New Meeting Scheduled:* By {ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME},  {MEETING_TITLE}, ID: #{MEETING_ID}. You can find the Meeting here : {MEETING_URL}'
                        ]);
                        break;
                    case 'leave_request_creation':
                        return response()->json([
                            'error' => false,
                            'message' => 'Reset to default successfully.',
                            'content' => '*New {TYPE} Leave Request Created:* ID: #{ID} By {REQUESTEE_FIRST_NAME} {REQUESTEE_LAST_NAME} for {REASON}.  From ( {FROM} ) -  To ( {TO} ).'
                        ]);
                        break;
                    case 'leave_request_status_updation':
                        return response()->json([
                            'error' => false,
                            'message' => 'Reset to default successfully.',
                            'content' => '*Leave Request Status Updated:* For {REQUESTEE_FIRST_NAME} {REQUESTEE_LAST_NAME},  ID: #{ID}. Status changed from `{OLD_STATUS}` to `{NEW_STATUS}`.'
                        ]);
                        break;
                    case 'team_member_on_leave_alert':
                        return response()->json([
                            'error' => false,
                            'message' => 'Reset to default successfully.',
                            'content' => '*Team Member Leave Alert:* {REQUESTEE_FIRST_NAME} {REQUESTEE_LAST_NAME} will be on {TYPE} leave from {FROM} to {TO}.'
                        ]);
                        break;
                    case 'birthday_wish':
                        return response()->json([
                            'error' => false,
                            'message' => 'Reset to default successfully.',
                            'content' => 'Hello *{FIRST_NAME} {LAST_NAME}*, {COMPANY_TITLE} wishes you a very Happy Birthday!'
                        ]);
                        break;
                    case 'work_anniversary_wish':
                        return response()->json([
                            'error' => false,
                            'message' => 'Reset to default successfully.',
                            'content' => 'Hello *{FIRST_NAME} {LAST_NAME}*, {COMPANY_TITLE} wishes you a very happy work anniversary!'
                        ]);
                        break;

                    case 'task_reminder':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'You have a task reminder for Task #{TASK_ID} - "{TASK_TITLE}". You can view the task here: {TASK_URL}.']);
                        break;
                    case 'recurring_task':
                        return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => 'The recurring task #{TASK_ID} - "{TASK_TITLE}" has been executed. You can view the new instance here: {TASK_URL}.']);
                        break;
                }

            default:
                return response()->json(['error' => true, 'message' => 'Unknown template type.']);
                break;
        }


        // Construct the default template path
        $defaultTemplatePath = resource_path($directory);

        // Check if the default template file exists
        if (File::exists($defaultTemplatePath)) {
            // Read the content of the default template file
            $defaultTemplateContent = File::get($defaultTemplatePath);

            // Return the default template content as a response
            return response()->json(['error' => false, 'message' => 'Reset to default successfully.', 'content' => $defaultTemplateContent]);
        } else {
            // If the default template file does not exist, return an error response
            return response()->json(['error' => true, 'message' => 'Default template not found.']);
        }
    }
    public function testNotificationSettings(Request $request)
    {
        $recipientNumber = $request->input('recipientNumber');
        $recipientCountryCode = $request->input('recipientCountryCode');
        $message = $request->input('message');
        $type = $request->input('type');
        if ($type == 'slack') {
            $recipient = (object) [
                'email' => $request->input('recipientEmail'),
                'country_code' => $recipientCountryCode
            ];
        } else {
            $recipient = (object) [
                'phone' => $recipientNumber,
                'country_code' => $recipientCountryCode
            ];
        }

        try {
            if ($type === 'sms') {
                $response = send_sms($recipient, null, $message);
            } elseif ($type === 'whatsapp') {
                $response = sendWhatsAppNotification($recipient, null, $message);
            } elseif ($type === 'slack') {
                $response = sendSlackNotification($recipient, null, $message);
            } else {
                throw new Exception('Invalid notification type');
            }
            return response()->json(['response' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Retrieve the settings for a specific variable.
     * 
     * This endpoint returns the settings for a given variable. The user must be authenticated.
     * 
     * @authenticated
     * 
     * @group Setting Management
     * 
     * @urlParam variable string required The variable type for which settings are to be retrieved. Must be one of the following: general_settings, pusher_settings, email_settings, media_storage_settings, sms_gateway_settings, whatsapp_settings, privacy_policy, about_us, terms_conditions. Example: general_settings
     *
     * @response 200 {
     *   "error": false,
     *   "message": "Settings retrieved successfully",
     *   "settings": {
     *     "company_title": "Taskify",
     *     "currency_full_form": "Indian Rupee",
     *     "currency_symbol": "₹",
     *     "currency_code": "INR",
     *     "currency_symbol_position": "before",
     *     "currency_formate": "comma_separated",
     *     "decimal_points_in_currency": "2",
     *     "allowed_max_upload_size": "2000",
     *     "allowSignup": 1,
     *     "timezone": "Asia/Kolkata",
     *     "date_format": "DD-MM-YYYY|d-m-Y",
     *     "time_format": "H:i:s",
     *     "toast_position": "toast-bottom-center",
     *     "toast_time_out": "2",
     *     "footer_text": "<p>made with ❤️ by <a href=\"https://www.infinitietech.com/\" target=\"_blank\" rel=\"noopener\">Infinitie Technologies</a></p>",
     *     "full_logo": "https://test-taskify.infinitietech.com/storage/logos/zEy4tSCAFSMczWbOoxBZ3B43Nc9eeqMlNBXDrOzn.png",
     *     "half_logo": null,
     *     "favicon": "https://test-taskify.infinitietech.com/storage/logos/2FZTNY1qDTz7CTtwWC8Hh1eY4l7cIHgOXG2stVIU.png"
     *   }
     * }
     *
     * @response 200 {
     *   "error": true,
     *   "message": "Un Authorized Action!"
     * }
     *
     * @response 200 {
     *   "error": true,
     *   "message": "Setting not found"
     * }
     */
    public function show(Request $request, $variable)
    {
        $settings = get_settings($variable);
        if ($settings) {
            if ($variable === 'general_settings') {
                $url_keys = ['full_logo', 'half_logo', 'favicon'];
                foreach ($url_keys as $key) {
                    if (isset($settings[$key]) && !empty($settings[$key])) {
                        // Generate the URL for assets in storage
                        $settings[$key] = asset('storage/' . $settings[$key]);
                    } else {
                        // Set to null if not set or empty
                        $settings[$key] = null;
                    }
                }
            }
            return Response::json([
                'error' => false,
                'message' => 'Settings retrieved successfully',
                'settings' => $settings,
            ]);
        } else {
            return Response::json([
                'error' => true,
                'message' => 'Setting not found',
            ]);
        }
    }
}
