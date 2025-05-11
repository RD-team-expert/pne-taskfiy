<?php

use App\Models\Tax;
use App\Models\User;
use App\Models\Client;
use App\Models\Update;
use App\Models\Setting;
use App\Models\Template;
use App\Models\LeaveEditor;
use App\Models\Workspace;
use App\Models\LeaveRequest;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Task;
use App\Models\Meeting;
use App\Models\FcmToken;
use App\Models\ActivityLog;
use App\Models\Status;
use Illuminate\Support\Facades\DB;
use App\Models\UserClientPreference;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use App\Notifications\AssignmentNotification;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twilio\Rest\Client as TwilioClient;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;
use Chatify\ChatifyMessenger;
use Google\Client as GoogleClient;
use GuzzleHttp\Client as HttpClient;

if (!function_exists('get_timezone_array')) {
    // 1.Get Time Zone
    function get_timezone_array()
    {
        $list = DateTimeZone::listAbbreviations();
        $idents = DateTimeZone::listIdentifiers();

        $data = $offset = $added = array();
        foreach ($list as $abbr => $info) {
            foreach ($info as $zone) {
                if (
                    !empty($zone['timezone_id'])
                    and
                    !in_array($zone['timezone_id'], $added)
                    and
                    in_array($zone['timezone_id'], $idents)
                ) {
                    $z = new DateTimeZone($zone['timezone_id']);
                    $c = new DateTime("", $z);
                    $zone['time'] = $c->format('h:i A');
                    $offset[] = $zone['offset'] = $z->getOffset($c);
                    $data[] = $zone;
                    $added[] = $zone['timezone_id'];
                }
            }
        }

        array_multisort($offset, SORT_ASC, $data);
        $i = 0;
        $temp = array();
        foreach ($data as $key => $row) {
            $temp[0] = $row['time'];
            $temp[1] = formatOffset($row['offset']);
            $temp[2] = $row['timezone_id'];
            $options[$i++] = $temp;
        }

        return $options;
    }
}
if (!function_exists('formatOffset')) {
    function formatOffset($offset)
    {
        $hours = $offset / 3600;
        $remainder = $offset % 3600;
        $sign = $hours > 0 ? '+' : '-';
        $hour = (int) abs($hours);
        $minutes = (int) abs($remainder / 60);
        if ($hour == 0 and $minutes == 0) {
            $sign = ' ';
        }
        return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0');
    }
}
if (!function_exists('relativeTime')) {
    function relativeTime($time)
    {
        if (!ctype_digit($time))
            $time = strtotime($time);
        $d[0] = array(1, "second");
        $d[1] = array(60, "minute");
        $d[2] = array(3600, "hour");
        $d[3] = array(86400, "day");
        $d[4] = array(604800, "week");
        $d[5] = array(2592000, "month");
        $d[6] = array(31104000, "year");

        $w = array();

        $return = "";
        $now = time();
        $diff = ($now - $time);
        $secondsLeft = $diff;

        for ($i = 6; $i > -1; $i--) {
            $w[$i] = intval($secondsLeft / $d[$i][0]);
            $secondsLeft -= ($w[$i] * $d[$i][0]);
            if ($w[$i] != 0) {
                $return .= abs($w[$i]) . " " . $d[$i][1] . (($w[$i] > 1) ? 's' : '') . " ";
            }
        }

        $return .= ($diff > 0) ? "ago" : "left";
        return $return;
    }
}
if (!function_exists('get_settings')) {

    function get_settings($variable)
    {
        $fetched_data = Setting::all()->where('variable', $variable)->values();
        if (isset($fetched_data[0]['value']) && !empty($fetched_data[0]['value'])) {
            if (isJson($fetched_data[0]['value'])) {
                $fetched_data = json_decode($fetched_data[0]['value'], true);
            }
            return $fetched_data;
        }
    }
}
if (!function_exists('isJson')) {
    function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
if (!function_exists('create_label')) {
    function create_label($variable, $title = '', $locale = '')
    {
        if ($title == '') {
            $title = $variable;
        }
        $value = htmlspecialchars(get_label($variable, $title, $locale), ENT_QUOTES, 'UTF-8');
        return "
        
        <div class='mb-3 col-md-6'>
                    <label class='form-label' for='$variable'>$title</label>
                    <div class='input-group input-group-merge'>
                        <input type='text' name='$variable' class='form-control' value='$value'>
                    </div>
                </div>
        
        ";
    }
}

function get_label($label, $default, $locale = '')
{
    // Check if the database connection is available
    try {
        DB::connection()->getPdo();
        $dbConnected = true;
    } catch (\Exception $e) {
        $dbConnected = false;
    }

    // Only fetch general settings if the database is connected
    $general_settings = $dbConnected ? get_settings('general_settings') : [];

    if ($dbConnected && (!isset($general_settings['priLangAsAuth']) || $general_settings['priLangAsAuth'] == 1 && (Request::is('forgot-password') || Request::is('/') || Request::segment(1) == 'reset-password' || Request::is('signup')))) {
        // Get the default language set by the first admin
        $mainAdminId = getMainAdminId();
        $adminLang = DB::table('users')
            ->where('id', $mainAdminId)
            ->value('lang');

        // If a locale is not provided, use the admin's language as fallback
        if (empty($locale)) {
            $locale = $adminLang ?: app()->getLocale(); // Use admin's language or default app locale
        }
    } else {
        // Use default app locale if DB is not connected
        $locale = $locale ?: app()->getLocale();
    }

    // Check if the label exists in the requested locale
    if (Lang::has('labels.' . $label, $locale)) {
        return trans('labels.' . $label, [], $locale);
    } else {
        return $default;
    }
}


if (!function_exists('empty_state')) {

    function empty_state($url)
    {
        return "
    <div class='card text-center'>
    <div class='card-body'>
        <div class='misc-wrapper'>
            <h2 class='mb-2 mx-2'>Data Not Found </h2>
            <p class='mb-4 mx-2'>Oops! 😖 Data doesn't exists.</p>
            <a href='/$url' class='btn btn-primary'>Create now</a>
            <div class='mt-3'>
                <img src='../assets/img/illustrations/page-misc-error-light.png' alt='page-misc-error-light' width='500' class='img-fluid' data-app-dark-img='illustrations/page-misc-error-dark.png' data-app-light-img='illustrations/page-misc-error-light.png' />
            </div>
        </div>
    </div>
</div>";
    }
}
if (!function_exists('format_date')) {
    function format_date($date, $time = false, $from_format = null, $to_format = null, $apply_timezone = true)
    {
        if ($date) {
            $from_format = $from_format ?? 'Y-m-d';
            $to_format = $to_format ?? get_php_date_time_format();
            $time_format = get_php_date_time_format(true);
            if ($time) {
                if ($apply_timezone) {
                    if (!$date instanceof \Carbon\Carbon) {
                        $dateObj = \Carbon\Carbon::createFromFormat($from_format . ' H:i:s', $date)
                            ->setTimezone(config('app.timezone'));
                    } else {
                        $dateObj = $date->setTimezone(config('app.timezone'));
                    }
                } else {
                    if (!$date instanceof \Carbon\Carbon) {
                        $dateObj = \Carbon\Carbon::createFromFormat($from_format . ' H:i:s', $date);
                    } else {
                        $dateObj = $date;
                    }
                }
            } else {
                if (!$date instanceof \Carbon\Carbon) {
                    $dateObj = \Carbon\Carbon::createFromFormat($from_format, $date);
                } else {
                    $dateObj = $date;
                }
            }


            $timeFormat = $time ? ' ' . $time_format : '';
            $date = $dateObj->format($to_format . $timeFormat);
            return $date;
        } else {
            return '-';
        }
    }
}
if (!function_exists('getAuthenticatedUser')) {
    function getAuthenticatedUser($idOnly = false, $withPrefix = false)
    {
        $prefix = '';

        // Check the 'web' guard (users)
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $prefix = 'u_';
        }

        // Check the 'client' guard (clients)
        elseif (Auth::guard('client')->check()) {
            $user = Auth::guard('client')->user();
            $prefix = 'c_';
        }
        // Check the 'sanctum' guard (API tokens)
        elseif (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
            // Optionally set a prefix for sanctum-authenticated users
            // $prefix = 's_';
        }

        // No user is authenticated
        else {
            return null;
        }

        if ($idOnly) {
            if ($withPrefix) {
                return $prefix . $user->id;
            } else {
                return $user->id;
            }
        }

        return $user;
    }
}

if (!function_exists('isUser')) {

    function isUser()
    {
        return Auth::guard('web')->check(); // Assuming 'role' is a field in the user model.
    }
}
if (!function_exists('isClient')) {

    function isClient()
    {
        return Auth::guard('client')->check(); // Assuming 'role' is a field in the user model.
    }
}
if (!function_exists('generateUniqueSlug')) {
    function generateUniqueSlug($title, $model, $id = null)
    {
        $slug = Str::slug($title);
        $count = 2;

        // If an ID is provided, add a where clause to exclude it
        if ($id !== null) {
            while ($model::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = Str::slug($title) . '-' . $count;
                $count++;
            }
        } else {
            while ($model::where('slug', $slug)->exists()) {
                $slug = Str::slug($title) . '-' . $count;
                $count++;
            }
        }

        return $slug;
    }
}
if (!function_exists('duplicateRecord')) {
    function duplicateRecord($model, $id, $relatedTables = [], $title = '')
    {
        $eagerLoadRelations = $relatedTables;
        $eagerLoadRelations = array_filter($eagerLoadRelations, function ($table) {
            return $table !== 'project_tasks'; // Exclude from eager loading
        });

        // Eager load the related tables excluding 'project_tasks'
        $originalRecord = $model::with($eagerLoadRelations)->find($id);
        if (!$originalRecord) {
            return false; // Record not found
        }
        // Start a new database transaction to ensure data consistency
        DB::beginTransaction();

        try {
            // Duplicate the original record
            $duplicateRecord = $originalRecord->replicate();
            // Set the title if provided
            if (!empty($title)) {
                $duplicateRecord->title = $title;
            }
            $duplicateRecord->save();

            foreach ($relatedTables as $relatedTable) {
                if ($relatedTable === 'projects') {
                    foreach ($originalRecord->$relatedTable as $project) {
                        // Duplicate the project
                        $duplicateProject = $project->replicate();
                        $duplicateProject->workspace_id = $duplicateRecord->id; // Set the new workspace ID
                        $duplicateProject->save();
                        // Attach project users
                        foreach ($project->users as $user) {
                            $duplicateProject->users()->attach($user->id);
                        }

                        // Attach project clients
                        foreach ($project->clients as $client) {
                            $duplicateProject->clients()->attach($client->id);
                        }
                        // Duplicate the project's tasks
                        if (in_array('project_tasks', $relatedTables)) {
                            foreach ($project->tasks as $task) {
                                $duplicateTask = $task->replicate();
                                $duplicateTask->workspace_id = $duplicateRecord->id;
                                $duplicateTask->project_id = $duplicateProject->id; // Set the new project ID
                                $duplicateTask->save();


                                // Duplicate task's users (if applicable)
                                foreach ($task->users as $user) {
                                    $duplicateTask->users()->attach($user->id);
                                }
                            }
                        }
                    }
                }
                if ($relatedTable === 'tasks') {
                    // Handle 'tasks' relationship separately
                    foreach ($originalRecord->$relatedTable as $task) {
                        // Duplicate the related task
                        $duplicateTask = $task->replicate();
                        $duplicateTask->project_id = $duplicateRecord->id;
                        $duplicateTask->save();
                        foreach ($task->users as $user) {
                            // Attach the duplicated user to the duplicated task
                            $duplicateTask->users()->attach($user->id);
                        }
                    }
                }
                if ($relatedTable === 'meetings') {
                    foreach ($originalRecord->$relatedTable as $meeting) {
                        $duplicateMeeting = $meeting->replicate();
                        $duplicateMeeting->workspace_id = $duplicateRecord->id; // Set the new workspace ID
                        $duplicateMeeting->save();

                        // Duplicate meeting's users
                        foreach ($meeting->users as $user) {
                            $duplicateMeeting->users()->attach($user->id);
                        }

                        // Duplicate meeting's clients
                        foreach ($meeting->clients as $client) {
                            $duplicateMeeting->clients()->attach($client->id);
                        }
                    }
                }
                if ($relatedTable === 'todos') {
                    // Duplicate todos
                    foreach ($originalRecord->$relatedTable as $todo) {
                        $duplicateTodo = $todo->replicate();
                        $duplicateTodo->workspace_id = $duplicateRecord->id; // Set the new workspace ID

                        $duplicateTodo->creator_type = $todo->creator_type; // Keep original creator type
                        $duplicateTodo->creator_id = $todo->creator_id;     // Keep original creator ID

                        $duplicateTodo->save();
                    }
                }
                if ($relatedTable === 'notes') {
                    foreach ($originalRecord->$relatedTable as $note) {
                        $duplicateNote = $note->replicate();
                        $duplicateNote->workspace_id = $duplicateRecord->id; // Set the new workspace ID
                        $duplicateNote->creator_id = $note->creator_id;      // Retain the creator_id
                        $duplicateNote->save();
                    }
                }
            }
            // Handle many-to-many relationships separately
            if (in_array('users', $relatedTables)) {
                $originalRecord->users()->each(function ($user) use ($duplicateRecord) {
                    $duplicateRecord->users()->attach($user->id);
                });
            }

            if (in_array('clients', $relatedTables)) {
                $originalRecord->clients()->each(function ($client) use ($duplicateRecord) {
                    $duplicateRecord->clients()->attach($client->id);
                });
            }

            if (in_array('tags', $relatedTables)) {
                $originalRecord->tags()->each(function ($tag) use ($duplicateRecord) {
                    $duplicateRecord->tags()->attach($tag->id);
                });
            }

            // Commit the transaction
            DB::commit();

            return $duplicateRecord;
        } catch (\Exception $e) {
            // Handle any exceptions and rollback the transaction on failure
            DB::rollback();
            return false;
        }
    }
}
if (!function_exists('is_admin_or_leave_editor')) {
    function is_admin_or_leave_editor($user = null)
    {
        if (!$user) {
            $user = getAuthenticatedUser();
        }

        // Check if the user is an admin or a leave editor based on their presence in the leave_editors table
        if ($user->hasRole('admin') || LeaveEditor::where('user_id', $user->id)->exists()) {
            return true;
        }
        return false;
    }
}
if (!function_exists('get_php_date_time_format')) {
    function get_php_date_time_format($timeFormat = false)
    {
        $general_settings = get_settings('general_settings');
        if ($timeFormat) {
            return $general_settings['time_format'] ?? 'H:i:s';
        } else {
            $date_format = $general_settings['date_format'] ?? 'DD-MM-YYYY|d-m-Y';
            $date_format = explode('|', $date_format);
            return $date_format[1];
        }
    }
}
if (!function_exists('get_system_update_info')) {
    function get_system_update_info()
    {
        $updatePath = Config::get('constants.UPDATE_PATH');
        $updaterPath = $updatePath . 'updater.json';
        $subDirectory = (File::exists($updaterPath) && File::exists($updatePath . 'update/updater.json')) ? 'update/' : '';

        if (File::exists($updaterPath) || File::exists($updatePath . $subDirectory . 'updater.json')) {
            $updaterFilePath = File::exists($updaterPath) ? $updaterPath : $updatePath . $subDirectory . 'updater.json';
            $updaterContents = File::get($updaterFilePath);

            // Check if the file contains valid JSON data
            if (!json_decode($updaterContents)) {
                throw new \RuntimeException('Invalid JSON content in updater.json');
            }

            $linesArray = json_decode($updaterContents, true);

            if (!isset($linesArray['version'], $linesArray['previous'], $linesArray['manual_queries'], $linesArray['query_path'])) {
                throw new \RuntimeException('Invalid JSON structure in updater.json');
            }
        } else {
            throw new \RuntimeException('updater.json does not exist');
        }

        $dbCurrentVersion = Update::latest()->first();
        $data['db_current_version'] = $dbCurrentVersion ? $dbCurrentVersion->version : '1.0.0';
        if ($data['db_current_version'] == $linesArray['version']) {
            $data['updated_error'] = true;
            $data['message'] = 'Oops!. This version is already updated into your system. Try another one.';
            return $data;
        }
        if ($data['db_current_version'] == $linesArray['previous']) {
            $data['file_current_version'] = $linesArray['version'];
        } else {
            $data['sequence_error'] = true;
            $data['message'] = 'Oops!. Update must performed in sequence.';
            return $data;
        }

        $data['query'] = $linesArray['manual_queries'];
        $data['query_path'] = $linesArray['query_path'];

        return $data;
    }
}
if (!function_exists('escape_array')) {
    function escape_array($array)
    {
        if (empty($array)) {
            return $array;
        }

        $db = DB::connection()->getPdo();

        if (is_array($array)) {
            return array_map(function ($value) use ($db) {
                return $db->quote($value);
            }, $array);
        } else {
            // Handle single non-array value
            return $db->quote($array);
        }
    }
}
if (!function_exists('isEmailConfigured')) {

    function isEmailConfigured()
    {
        $email_settings = get_settings('email_settings');
        if (
            isset($email_settings['email']) && !empty($email_settings['email']) &&
            isset($email_settings['password']) && !empty($email_settings['password']) &&
            isset($email_settings['smtp_host']) && !empty($email_settings['smtp_host']) &&
            isset($email_settings['smtp_port']) && !empty($email_settings['smtp_port'])
        ) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('get_current_version')) {

    function get_current_version()
    {
        $dbCurrentVersion = Update::latest()->first();
        return $dbCurrentVersion ? $dbCurrentVersion->version : '1.0.0';
    }
}

if (!function_exists('isAdminOrHasAllDataAccess')) {
    function isAdminOrHasAllDataAccess($type = null, $id = null)
    {
        // Get authenticated user
        $authenticatedUser = getAuthenticatedUser();
        if ($type == 'user' && $id !== null) {
            $user = User::find($id);
            if ($user) {
                return $user->hasRole('admin') || $user->can('access_all_data');
            }
        } elseif ($type == 'client' && $id !== null) {
            $client = Client::find($id);
            if ($client) {
                return $client->hasRole('admin') || $client->can('access_all_data');
            }
        } elseif ($type === null && $id === null) {
            if ($authenticatedUser) {
                return $authenticatedUser->hasRole('admin') || $authenticatedUser->can('access_all_data');
            }
        }

        return false;
    }
}




if (!function_exists('getControllerNames')) {

    function getControllerNames()
    {
        $controllersPath = app_path('Http/Controllers');
        $files = File::files($controllersPath);

        $excludedControllers = [
            'ActivityLogController',
            'Controller',
            'HomeController',
            'InstallerController',
            'LanguageController',
            'ProfileController',
            'RolesController',
            'SearchController',
            'SettingsController',
            'UpdaterController',
            'EstimatesInvoicesController',
            'PreferenceController',
            'ReportsController',
            'NotificationsController',
            'SwaggerController'
        ];

        $controllerNames = [];

        foreach ($files as $file) {
            $fileName = pathinfo($file, PATHINFO_FILENAME);

            // Skip controllers in the excluded list
            if (in_array($fileName, $excludedControllers)) {
                continue;
            }

            if (str_ends_with($fileName, 'Controller')) {
                // Convert to singular form, snake_case, and remove 'Controller' suffix
                $controllerName = Str::snake(Str::singular(str_replace('Controller', '', $fileName)));
                $controllerNames[] = $controllerName;
            }
        }

        // Add manually defined types
        $manuallyDefinedTypes = [
            'contract_type',
            'media',
            'estimate',
            'invoice',
            'milestone'
            // Add more types as needed
        ];

        $controllerNames = array_merge($controllerNames, $manuallyDefinedTypes);

        return $controllerNames;
    }
}
if (!function_exists('formatSize')) {
    function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
if (!function_exists('getStatusColor')) {
    function getStatusColor($status)
    {
        switch ($status) {
            case 'sent':
                return 'primary';
            case 'accepted':
            case 'fully_paid':
                return 'success';
            case 'draft':
                return 'secondary';
            case 'declined':
            case 'due':
                return 'danger';
            case 'expired':
            case 'partially_paid':
                return 'warning';
            case 'not_specified':
                return 'secondary';
            default:
                return 'info';
        }
    }
}
if (!function_exists('getStatusCount')) {
    function getStatusCount($status, $type)
    {
        $estimates_invoices = isAdminOrHasAllDataAccess() ? Workspace::find(getWorkspaceId())->estimates_invoices($status, $type) : getAuthenticatedUser()->estimates_invoices($status, $type);
        return $estimates_invoices->count();
    }
}

if (!function_exists('format_currency')) {
    function format_currency($amount, $is_currency_symbol = 1, $include_separators = true)
    {
        if ($amount == '') {
            return '';
        }
        $general_settings = get_settings('general_settings');
        $currency_symbol = $general_settings['currency_symbol'] ?? '₹';
        $currency_format = $general_settings['currency_formate'] ?? 'comma_separated';
        $decimal_points = intval($general_settings['decimal_points_in_currency'] ?? '2');
        $currency_symbol_position = $general_settings['currency_symbol_position'] ?? 'before';

        // Determine the appropriate separators based on the currency format and $use_commas parameter
        if ($include_separators) {
            $thousands_separator = ($currency_format == 'comma_separated') ? ',' : '.';
        } else {
            $thousands_separator = '';
        }

        // Format the amount with the determined separators
        $formatted_amount = number_format($amount, $decimal_points, '.', $thousands_separator);
        if ($is_currency_symbol) {
            // Format currency symbol position
            if ($currency_symbol_position === 'before') {
                $currency_amount = $currency_symbol . ' ' . $formatted_amount;
            } else {
                $currency_amount = $formatted_amount . ' ' . $currency_symbol;
            }
            return $currency_amount;
        }
        return $formatted_amount;
    }
}

function get_tax_data($tax_id, $total_amount, $currency_symbol = 0)
{
    // Check if tax_id is not empty
    if ($tax_id != '') {
        // Retrieve tax data from the database using the tax_id
        $tax = Tax::find($tax_id);

        // Check if tax data is found
        if ($tax) {
            // Get tax rate and type
            $taxRate = $tax->amount;
            $taxType = $tax->type;

            // Calculate tax amount based on tax rate and type
            $taxAmount = 0;
            $disp_tax = '';

            if ($taxType == 'percentage') {
                $taxAmount = ($total_amount * $tax->percentage) / 100;
                $disp_tax = format_currency($taxAmount, $currency_symbol) . '(' . $tax->percentage . '%)';
            } elseif ($taxType == 'amount') {
                $taxAmount = $taxRate;
                $disp_tax = format_currency($taxAmount, $currency_symbol);
            }

            // Return the calculated tax data
            return [
                'taxAmount' => $taxAmount,
                'taxType' => $taxType,
                'dispTax' => $disp_tax,
            ];
        }
    }

    // Return empty data if tax_id is empty or tax data is not found
    return [
        'taxAmount' => 0,
        'taxType' => '',
        'dispTax' => '',
    ];
}

if (!function_exists('processNotifications')) {
    function processNotifications($data, $recipients)
    {
        // Define an array of types for which email notifications should be sent
        $emailNotificationTypes = ['project_assignment', 'project_status_updation', 'task_assignment', 'task_status_updation', 'workspace_assignment', 'meeting_assignment', 'leave_request_creation', 'leave_request_status_updation', 'team_member_on_leave_alert'];
        $smsNotificationTypes = ['project_assignment', 'project_status_updation', 'task_assignment', 'task_status_updation', 'workspace_assignment', 'meeting_assignment', 'leave_request_creation', 'leave_request_status_updation', 'team_member_on_leave_alert'];
        if (!empty($recipients)) {
            $type = $data['type'] == 'task_status_updation' ? 'task' : ($data['type'] == 'project_status_updation' ? 'project' : ($data['type'] == 'leave_request_creation' || $data['type'] == 'leave_request_status_updation' || $data['type'] == 'team_member_on_leave_alert' ? 'leave_request' : $data['type']));
            $systemNotificationTemplate = getNotificationTemplate($data['type'], 'system');
            $pushNotificationTemplate = getNotificationTemplate($data['type'], 'push');
            if (
                !$systemNotificationTemplate || $systemNotificationTemplate->status !== 0 ||
                !$pushNotificationTemplate || $pushNotificationTemplate->status !== 0
            ) {
                $notification = Notification::create([
                    'workspace_id' => getWorkspaceId(),
                    'from_id' => getGuardName() == 'client' ? 'c_' . getAuthenticatedUser()->id : 'u_' . getAuthenticatedUser()->id,
                    'type' => $type,
                    'type_id' => $data['type_id'],
                    'action' => $data['action'],
                    'title' => getTitle($data),
                    'message' => get_message($data, NULL, 'system'),
                ]);
            }
            // Exclude creator from receiving notification
            $loggedInUserId = getGuardName() == 'client' ? 'c_' . getAuthenticatedUser()->id : 'u_' . getAuthenticatedUser()->id;
            $recipients = array_diff($recipients, [$loggedInUserId]);
            $recipients = array_unique($recipients);
            $whatsappNotificationTemplate = getNotificationTemplate($data['type'], 'whatsapp');
            $slackNotificationTemplate = getNotificationTemplate($data['type'], 'slack');
            foreach ($recipients as $recipient) {
                $isSystem = 0;
                $isPush = 0;
                $enabledNotifications = getUserPreferences('notification_preference', 'enabled_notifications', $recipient);
                $recipientId = substr($recipient, 2);
                if (substr($recipient, 0, 2) === 'u_') {
                    $recipientModel = User::find($recipientId);
                } elseif (substr($recipient, 0, 2) === 'c_') {
                    $recipientModel = Client::find($recipientId);
                }
                // Check if recipient was found
                if ($recipientModel) {
                    if (!$systemNotificationTemplate || ($systemNotificationTemplate->status !== 0)) {
                        if ((is_array($enabledNotifications) && empty($enabledNotifications)) || (
                            is_array($enabledNotifications) && (
                                in_array('system_' . $data['type'] . '_assignment', $enabledNotifications) ||
                                in_array('system_' . $data['type'], $enabledNotifications)
                            )
                        )) {
                            $isSystem = 1;
                        }
                    }
                    if (!$pushNotificationTemplate || ($pushNotificationTemplate->status !== 0)) {
                        if ((is_array($enabledNotifications) && empty($enabledNotifications)) || (
                            is_array($enabledNotifications) && (
                                in_array('push_' . $data['type'] . '_assignment', $enabledNotifications) ||
                                in_array('push_' . $data['type'], $enabledNotifications)
                            )
                        )) {
                            $isPush = 1;
                            try {
                                sendPushNotification($recipientModel, $data);
                            } catch (\Exception $e) {
                            }
                        }
                    }

                    if ($isSystem || $isPush) {
                        $recipientModel->notifications()->attach($notification->id, [
                            'is_system' => $isSystem,
                            'is_push' => $isPush,
                        ]);
                    }
                    if (in_array($data['type'] . '_assignment', $emailNotificationTypes) || in_array($data['type'], $emailNotificationTypes)) {
                        if ((is_array($enabledNotifications) && empty($enabledNotifications)) || (
                            is_array($enabledNotifications) && (
                                in_array('email_' . $data['type'] . '_assignment', $enabledNotifications) ||
                                in_array('email_' . $data['type'], $enabledNotifications)
                            )
                        )) {
                            try {
                                sendEmailNotification($recipientModel, $data);
                            } catch (\Exception $e) {
                                // dd($e->getMessage());
                            } catch (TransportExceptionInterface $e) {
                                // dd($e->getMessage());
                            } catch (Throwable $e) {
                                // dd($e->getMessage());
                                // Catch any other throwable, including non-Exception errors
                            }
                        }
                    }
                    if (in_array($data['type'] . '_assignment', $smsNotificationTypes) || in_array($data['type'], $smsNotificationTypes)) {
                        if ((is_array($enabledNotifications) && empty($enabledNotifications)) || (
                            is_array($enabledNotifications) && (
                                in_array('sms_' . $data['type'] . '_assignment', $enabledNotifications) ||
                                in_array('sms_' . $data['type'], $enabledNotifications)
                            )
                        )) {
                            try {
                                sendSMSNotification($data, $recipientModel);
                            } catch (\Exception $e) {
                            }
                        }
                    }

                    if (!$whatsappNotificationTemplate || ($whatsappNotificationTemplate->status !== 0)) {
                        if ((is_array($enabledNotifications) && empty($enabledNotifications)) || (
                            is_array($enabledNotifications) && (
                                in_array('whatsapp_' . $data['type'] . '_assignment', $enabledNotifications) ||
                                in_array('whatsapp_' . $data['type'], $enabledNotifications)
                            )
                        )) {
                            try {
                                sendWhatsAppNotification($recipientModel, $data);
                            } catch (\Exception $e) {
                            }
                        }
                    }
                    if (!$slackNotificationTemplate || ($slackNotificationTemplate->status !== 0)) {
                        if ((is_array($enabledNotifications) && empty($enabledNotifications)) || (
                            is_array($enabledNotifications) && (
                                in_array('slack_' . $data['type'] . '_assignment', $enabledNotifications) ||
                                in_array('slack_' . $data['type'], $enabledNotifications)
                            )
                        )) {
                            try {

                                sendSlackNotification($recipientModel, $data);
                            } catch (\Exception $e) {
                            }
                        }
                    }
                }
            }
        }
    }
}

if (!function_exists('sendPushNotification')) {
    function sendPushNotification($recipientModel, $data)
    {
        // Path to your service account key
        $serviceAccountPath = public_path('storage/app/firebase/firebase-service-account.json');

        // Set up the Google Client for authentication
        $googleClient = new GoogleClient();
        $googleClient->setAuthConfig($serviceAccountPath);
        $googleClient->addScope('https://www.googleapis.com/auth/firebase.messaging');

        // Generate an access token
        $accessToken = $googleClient->fetchAccessTokenWithAssertion()['access_token'];
        $projectId = json_decode(file_get_contents($serviceAccountPath), true)['project_id'];

        // Retrieve device tokens based on the recipient model
        if ($recipientModel instanceof User) {
            $deviceTokens = FcmToken::where('user_id', $recipientModel->id)->pluck('fcm_token')->toArray();
        } elseif ($recipientModel instanceof Client) {
            $deviceTokens = FcmToken::where('client_id', $recipientModel->id)->pluck('fcm_token')->toArray();
        }

        if (empty($deviceTokens)) {
            return; // No device tokens found, skip sending
        }

        // Set up Guzzle HTTP client
        $httpClient = new HttpClient([
            'base_uri' => 'https://fcm.googleapis.com/v1/',
            'headers'  => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ],
        ]);

        $title = getTitle($data, $recipientModel, 'push');
        $body = get_message($data, $recipientModel, 'push');

        // Prepare the notification message
        $message = [
            'message' => [
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'android' => [
                    'notification' => [
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $title,
                                'body'  => $body,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // Add additional data based on the notification type
        if ($data['type'] == 'project' || $data['type'] == 'project_status_updation') {
            $project = Project::find($data['type_id']);
            $message['message']['data']['item'] = json_encode([
                'type' => 'project',
                'item' => formatProject($project),
            ]);
        } elseif ($data['type'] == 'task' || $data['type'] == 'task_status_updation') {
            $task = Task::find($data['type_id']);
            $message['message']['data']['item'] = json_encode([
                'type' => 'task',
                'item' => formatTask($task),
            ]);
        } elseif ($data['type'] == 'meeting') {
            $meeting = Meeting::find($data['type_id']);
            $message['message']['data']['item'] = json_encode([
                'type' => 'meeting',
                'item' => formatMeeting($meeting),
            ]);
        } elseif ($data['type'] == 'workspace') {
            $workspace = Workspace::find($data['type_id']);
            $message['message']['data']['item'] = json_encode([
                'type' => 'workspace',
                'item' => formatWorkspace($workspace),
            ]);
        } elseif (in_array($data['type'], ['leave_request_creation', 'team_member_on_leave_alert', 'leave_request_status_updation'])) {
            $leaveRequest = LeaveRequest::find($data['type_id']);
            $message['message']['data']['item'] = json_encode([
                'type' => 'leave_request',
                'item' => formatLeaveRequest($leaveRequest),
            ]);
        }

        foreach ($deviceTokens as $deviceToken) {
            try {
                // Set the current device token
                $message['message']['token'] = $deviceToken;

                // Send the notification
                $response = $httpClient->post("projects/{$projectId}/messages:send", [
                    'json' => $message,
                ]);

                // Uncomment for debugging
                // dd($response);
            } catch (\Exception $e) {
                // Handle the error for the current token
                // Log the error or take appropriate action
                // Uncomment for debugging
                // dd($e->getMessage());
            }
        }
    }
}

if (!function_exists('sendEmailNotification')) {
    function sendEmailNotification($recipientModel, $data)
    {
        $template = getNotificationTemplate($data['type']);

        if (!$template || ($template->status !== 0)) {
            $recipientModel->notify(new AssignmentNotification($recipientModel, $data));
        }
    }
}

if (!function_exists('sendSMSNotification')) {
    function sendSMSNotification($data, $recipient)
    {
        $template = getNotificationTemplate($data['type'], 'sms');

        if (!$template || ($template->status !== 0)) {
            send_sms($recipient, $data);
        }
    }
}

if (!function_exists('getNotificationTemplate')) {
    function getNotificationTemplate($type, $emailOrSMS = 'email')
    {
        $template = Template::where('type', $emailOrSMS)
            ->where('name', $type . '_assignment')
            ->first();

        if (!$template) {
            // If template with $type . '_assignment' name not found, check for template with $type name
            $template = Template::where('type', $emailOrSMS)
                ->where('name', $type)
                ->first();
        }

        return $template;
    }
}


if (!function_exists('send_sms')) {
    function send_sms($recipient, $itemData = NULL, $message = NULL)
    {
        $msg = $itemData ? get_message($itemData, $recipient) : $message;
        try {
            $sms_gateway_settings = get_settings('sms_gateway_settings', true);
            $data = [
                "base_url" => $sms_gateway_settings['base_url'],
                "sms_gateway_method" => $sms_gateway_settings['sms_gateway_method']
            ];

            $data["body"] = [];
            if (isset($sms_gateway_settings["body_formdata"])) {
                foreach ($sms_gateway_settings["body_formdata"] as $key => $value) {
                    $value = parse_sms($value, $recipient->phone, $msg, $recipient->country_code);
                    $data["body"][$key] = $value;
                }
            }

            $data["header"] = [];
            if (isset($sms_gateway_settings["header_data"])) {
                foreach ($sms_gateway_settings["header_data"] as $key => $value) {
                    $value = parse_sms($value, $recipient->phone, $msg, $recipient->country_code);
                    $data["header"][] = $key . ": " . $value;
                }
            }

            $data["params"] = [];
            if (isset($sms_gateway_settings["params_data"])) {
                foreach ($sms_gateway_settings["params_data"] as $key => $value) {
                    $value = parse_sms($value, $recipient->phone, $msg, $recipient->country_code);
                    $data["params"][$key] = $value;
                }
            }
            $response = curl_sms($data["base_url"], $data["sms_gateway_method"], $data["body"], $data["header"]);
            // print_r($response);
            if ($itemData == NULL) {
                return $response;
            }
        } catch (Exception $e) {
            // Handle the exception
            if ($itemData == NULL) {
                throw new Exception('Failed to send SMS: ' . $e->getMessage());
            }
        }
    }
}

function storeFcmToken($recipientModel, $token)
{
    // Check if the token is provided
    if (empty($token)) {
        return false; // No token to store
    }

    // Determine if the recipient is a User or Client
    $userId = null;
    $clientId = null;

    $guardName = getGuardName();

    if ($guardName == 'web') {
        $userId = $recipientModel->id; // Set user ID
    } elseif ($guardName == 'client') {
        $clientId = $recipientModel->id; // Set client ID
    }

    // Check if the token already exists for this user or client
    $query = FcmToken::where('fcm_token', $token);

    if ($userId) {
        $existingToken = $query->where('user_id', $userId)->first();
    } elseif ($clientId) {
        $existingToken = $query->where('client_id', $clientId)->first();
    }

    // If the token does not exist, save it
    if (!$existingToken) {
        FcmToken::create([
            'user_id'   => $userId,
            'client_id' => $clientId,
            'fcm_token' => $token,
        ]);
    }

    return true; // Token stored successfully or already exists
}


if (!function_exists('sendWhatsAppNotification')) {
    function sendWhatsAppNotification($recipient, $itemData = NULL, $message = NULL)
    {   
        $msg = $itemData ? get_message($itemData, $recipient, 'whatsapp') : $message;
        $whatsapp_settings = get_settings('whatsapp_settings', true);
        $general_settings = get_settings('general_settings');
        $company_title = $general_settings['company_title'] ?? 'Taskify';
        $client = new GuzzleHttpClient();
        try {
            $response = $client->post('https://graph.facebook.com/v20.0/' . $whatsapp_settings['whatsapp_phone_number_id'] . '/messages', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $whatsapp_settings['whatsapp_access_token'],
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $recipient->country_code . $recipient->phone,
                    'type' => 'template',
                    'template' => [
                        'name' => 'taskify_saas_notification',
                        'language' => [
                            'code' => 'en'
                        ],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => [
                                    [
                                        'type' => 'text',
                                        'text' => $msg  // This will replace {{1}}
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => $company_title  // This will replace {{2}}
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ]);
            $data = json_decode($response->getBody(), true);
            if ($itemData == NULL) {
                return $data;
            }
            // dd("Message sent successfully. Response: " . print_r($data, true));
        } catch (RequestException $e) {
            // dd("Error sending message: " . $e->getMessage());
            if ($e->hasResponse()) {
                if ($itemData == NULL) {
                    throw new Exception('Failed: ' . $e->getMessage());
                }
                // dd("Response: " . $e->getResponse()->getBody()->getContents());
            }
        }
    }
}

if (!function_exists('sendSlackNotification')) {
    function sendSlackNotification($recipient, $itemData = NULL, $message = NULL)
    {
        $slack_settings = get_settings('slack_settings');
        $message =  $itemData ? get_message($itemData, $recipient, 'slack') : $message;
        $botToken = $slack_settings['slack_bot_token'];

        // Create a Guzzle client for Slack API
        $client = new GuzzleHttpClient([
            'base_uri' => 'https://slack.com/api/',
            'headers' => [
                'Authorization' => 'Bearer ' . $botToken,
                'Content-Type' => 'application/json',
            ],
        ]);

        // Step 4: Look up the Slack user ID by email
        $email = $recipient->email;

        // $email = 'infinitietechnologies10@gmail.com';
        $userId = getSlackUserIdByEmail($client, $email);

        if ($userId) {
            // Step 5: Prepare the message payload
            // Assuming template has a 'content' field
            $slackMessage = [
                'channel' => $userId,
                'text' => $message,
                'username' => 'Taskify Notification',
                'icon_emoji' => ':office:',
            ];

            try {
                // Step 6: Send the Slack message
                $response = $client->post('chat.postMessage', [
                    'json' => $slackMessage
                ]);

                $responseBody = json_decode(
                    $response->getBody(),
                    true
                );
                if ($responseBody['ok']) {
                    if ($itemData === NULL) {
                        return [
                            'status' => 'success',
                            'message' => 'Slack DM sent successfully to user: ' . $userId,
                        ];
                    }
                    // Log::info('Slack DM sent successfully to user: ' . $userId);
                } else {
                    if ($itemData === NULL) {
                        return [
                            'status' => 'error',
                            'message' => 'Failed to send Slack DM: ' . $responseBody['error'],
                        ];
                    }
                    // Log::warning('Failed to send Slack DM to user ' . $userId . ': ' . $responseBody['error']);
                }
            } catch (\Exception $e) {
                if ($itemData === NULL) {
                    return [
                        'status' => 'error',
                        'message' => 'Error sending Slack DM: ' . $e->getMessage(),
                    ];
                }
                // Log::error('Error sending Slack DM to user: ' . $userId . ', Error: ' . $e->getMessage());
            }
        } else {
            if ($itemData === NULL) {
                return [
                    'status' => 'error',
                    'message' => 'Slack user ID not found for email: ' . $email,
                ];
            }
            // Log::warning('Slack user ID not found for email: ' . $email);
        }
    }
}

function getSlackUserIdByEmail($client, $email)
{
    try {
        $response = $client->get('users.lookupByEmail', [
            'query' => ['email' => $email]
        ]);

        $body = json_decode($response->getBody(), true);

        if ($body['ok'] === true) {
            return $body['user']['id']; // Return Slack User ID
        } else {
            Log::error("Failed to get Slack user ID: " . $body['error']);
        }
    } catch (\Exception $e) {
        Log::error('Error getting Slack user ID for email ' . $email . ': ' . $e->getMessage());
    }
}

if (!function_exists('curl_sms')) {
    function curl_sms($url, $method = 'GET', $data = [], $headers = [])
    {
        $ch = curl_init();
        $curl_options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            )
        );

        if (count($headers) != 0) {
            $curl_options[CURLOPT_HTTPHEADER] = $headers;
        }

        if (strtolower($method) == 'post') {
            $curl_options[CURLOPT_POST] = 1;
            $curl_options[CURLOPT_POSTFIELDS] = http_build_query($data);
        } else {
            $curl_options[CURLOPT_CUSTOMREQUEST] = 'GET';
        }
        curl_setopt_array($ch, $curl_options);

        $result = array(
            'body' => json_decode(curl_exec($ch), true),
            'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        );

        return $result;
    }
}

if (!function_exists('parse_sms')) {
    function parse_sms($template, $phone, $msg, $country_code)
    {
        // Implement your parsing logic here
        // This is just a placeholder
        return str_replace(['{only_mobile_number}', '{message}', '{country_code}'], [$phone, $msg, $country_code], $template);
    }
}
if (!function_exists('get_message')) {
    function get_message($data, $recipient, $type = 'sms')
    {        
        $authUser = getAuthenticatedUser();
        $general_settings = get_settings('general_settings');
        $company_title = $general_settings['company_title'] ?? 'Taskify';
        $siteUrl = $general_settings['site_url'] ?? request()->getSchemeAndHttpHost();
        
        $fetched_data = Template::where('type', $type)
            ->where('name', $data['type'] . '_assignment')
            ->first();

        if (!$fetched_data) {
            // If template with $this->data['type'] . '_assignment' name not found, check for template with $this->data['type'] name
            $fetched_data = Template::where('type', $type)
                ->where('name', $data['type'])
                ->first();
        }


        $templateContent = 'Default Content';
        $contentPlaceholders = []; // Initialize outside the switch

        // Customize content based on type
        if ($type === 'system' || $type === 'push') {
            switch ($data['type']) {
                case 'project':
                    $contentPlaceholders = [
                        '{PROJECT_ID}' => $data['type_id'],
                        '{PROJECT_TITLE}' => $data['type_title'],
                        '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                        '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                        '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                        '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                        '{COMPANY_TITLE}' => $company_title,
                        '{PROJECT_URL}' => $siteUrl . '/' . $data['access_url']
                    ];
                    $templateContent = '{ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME} assigned you new project: {PROJECT_TITLE}, ID:#{PROJECT_ID}.';
                    break;
                case 'project_status_updation':
                    $contentPlaceholders = [
                        '{PROJECT_ID}' => $data['type_id'],
                        '{PROJECT_TITLE}' => $data['type_title'],
                        '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                        '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                        '{UPDATER_FIRST_NAME}' => $data['updater_first_name'],
                        '{UPDATER_LAST_NAME}' => $data['updater_last_name'],
                        '{OLD_STATUS}' => $data['old_status'],
                        '{NEW_STATUS}' => $data['new_status'],
                        '{PROJECT_URL}' => $siteUrl . '/' . $data['access_url'],
                        '{COMPANY_TITLE}' => $company_title
                    ];
                    $templateContent = '{UPDATER_FIRST_NAME} {UPDATER_LAST_NAME} has updated the status of project {PROJECT_TITLE}, ID:#{PROJECT_ID}, from {OLD_STATUS} to {NEW_STATUS}.';
                    break;
                case 'task':
                    $contentPlaceholders = [
                        '{TASK_ID}' => $data['type_id'],
                        '{TASK_TITLE}' => $data['type_title'],
                        '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                        '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                        '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                        '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                        '{COMPANY_TITLE}' => $company_title,
                        '{TASK_URL}' => $siteUrl . '/' . $data['access_url']
                    ];
                    $templateContent = '{ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME} assigned you new task: {TASK_TITLE}, ID:#{TASK_ID}.';
                    break;
                case 'task_status_updation':
                    $contentPlaceholders = [
                        '{TASK_ID}' => $data['type_id'],
                        '{TASK_TITLE}' => $data['type_title'],
                        '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                        '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                        '{UPDATER_FIRST_NAME}' => $data['updater_first_name'],
                        '{UPDATER_LAST_NAME}' => $data['updater_last_name'],
                        '{OLD_STATUS}' => $data['old_status'],
                        '{NEW_STATUS}' => $data['new_status'],
                        '{TASK_URL}' => $siteUrl . '/' . $data['access_url'],
                        '{COMPANY_TITLE}' => $company_title
                    ];
                    $templateContent = '{UPDATER_FIRST_NAME} {UPDATER_LAST_NAME} has updated the status of task {TASK_TITLE}, ID:#{TASK_ID}, from {OLD_STATUS} to {NEW_STATUS}.';
                    break;
                case 'workspace':
                    $contentPlaceholders = [
                        '{WORKSPACE_ID}' => $data['type_id'],
                        '{WORKSPACE_TITLE}' => $data['type_title'],
                        '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                        '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                        '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                        '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                        '{COMPANY_TITLE}' => $company_title,
                        '{WORKSPACE_URL}' => $siteUrl . '/workspaces'
                    ];
                    $templateContent = '{ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME} added you in a new workspace {WORKSPACE_TITLE}, ID:#{WORKSPACE_ID}.';
                    break;
                case 'meeting':
                    $contentPlaceholders = [
                        '{MEETING_ID}' => $data['type_id'],
                        '{MEETING_TITLE}' => $data['type_title'],
                        '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                        '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                        '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                        '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                        '{COMPANY_TITLE}' => $company_title,
                        '{MEETING_URL}' => $siteUrl . '/meetings'
                    ];
                    $templateContent = '{ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME} added you in a new meeting {MEETING_TITLE}, ID:#{MEETING_ID}.';
                    break;

                case 'leave_request_creation':
                    $contentPlaceholders = [
                        '{ID}' => $data['type_id'],
                        '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                        '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                        '{REQUESTEE_FIRST_NAME}' => $data['team_member_first_name'],
                        '{REQUESTEE_LAST_NAME}' => $data['team_member_last_name'],
                        '{TYPE}' => $data['leave_type'],
                        '{FROM}' => $data['from'],
                        '{TO}' => $data['to'],
                        '{DURATION}' => $data['duration'],
                        '{REASON}' => $data['reason'],
                        '{COMMENT}' => $data['comment'],
                        '{STATUS}' => $data['status'],
                        '{COMPANY_TITLE}' => $company_title
                    ];
                    $templateContent = 'New Leave Request ID:#{ID} Has Been Created By {REQUESTEE_FIRST_NAME} {REQUESTEE_LAST_NAME}.';
                    break;

                case 'leave_request_status_updation':
                    $contentPlaceholders = [
                        '{ID}' => $data['type_id'],
                        '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                        '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                        '{REQUESTEE_FIRST_NAME}' => $data['team_member_first_name'],
                        '{REQUESTEE_LAST_NAME}' => $data['team_member_last_name'],
                        '{UPDATER_FIRST_NAME}' => $data['updater_first_name'],
                        '{UPDATER_LAST_NAME}' => $data['updater_last_name'],
                        '{TYPE}' => $data['leave_type'],
                        '{FROM}' => $data['from'],
                        '{TO}' => $data['to'],
                        '{DURATION}' => $data['duration'],
                        '{REASON}' => $data['reason'],
                        '{COMMENT}' => $data['comment'],
                        '{OLD_STATUS}' => $data['old_status'],
                        '{NEW_STATUS}' => $data['new_status'],
                        '{COMPANY_TITLE}' => $company_title
                    ];
                    $templateContent = 'Leave Request ID:#{ID} Status Updated From {OLD_STATUS} To {NEW_STATUS}.';
                    break;

                case 'team_member_on_leave_alert':
                    $contentPlaceholders = [
                        '{ID}' => $data['type_id'],
                        '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                        '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                        '{REQUESTEE_FIRST_NAME}' => $data['team_member_first_name'],
                        '{REQUESTEE_LAST_NAME}' => $data['team_member_last_name'],
                        '{TYPE}' => $data['leave_type'],
                        '{FROM}' => $data['from'],
                        '{TO}' => $data['to'],
                        '{DURATION}' => $data['duration'],
                        '{COMPANY_TITLE}' => $company_title
                    ];
                    $templateContent = '{REQUESTEE_FIRST_NAME} {REQUESTEE_LAST_NAME} will be on {TYPE} leave from {FROM} to {TO}.';
                    break;

                case 'birthday_wish':
                    $contentPlaceholders = [
                        '{FIRST_NAME}' => $recipient->first_name,
                        '{LAST_NAME}' => $recipient->last_name,
                        '{BIRTHDAY_COUNT}' => $data['birthday_count'],
                        '{ORDINAL_SUFFIX}' => $data['ordinal_suffix'],
                        '{COMPANY_TITLE}' => $company_title,
                        '{SITE_URL}' => $siteUrl
                    ];
                    $templateContent = 'Hello {FIRST_NAME} {LAST_NAME}, {COMPANY_TITLE} wishes you a very Happy Birthday!';
                    break;

                case 'work_anniversary_wish':
                    $contentPlaceholders = [
                        '{FIRST_NAME}' => $recipient->first_name,
                        '{LAST_NAME}' => $recipient->last_name,
                        '{WORK_ANNIVERSARY_COUNT}' => $data['work_anniversary_count'],
                        '{ORDINAL_SUFFIX}' => $data['ordinal_suffix'],
                        '{COMPANY_TITLE}' => $company_title,
                        '{SITE_URL}' => $siteUrl
                    ];
                    $templateContent = 'Hello {FIRST_NAME} {LAST_NAME}, {COMPANY_TITLE} wishes you a very happy work anniversary!';
                    break;
                 case 'task_reminder':
                    $contentPlaceholders = [
                        '{TASK_ID}' => $data['type_id'],
                        '{TASK_TITLE}' => $data['type_title'],
                        '{TASK_URL}' => $siteUrl . '/' . $data['access_url'],
                        '{COMPANY_TITLE}' => $company_title,
                    ];
                    $templateContent = 'You have a task reminder for Task #{TASK_ID} - "{TASK_TITLE}". You can view the task here: {TASK_URL}.';
                    break;
                case 'recurring_task':
                    $contentPlaceholders = [
                        '{TASK_ID}' => $data['type_id'],
                        '{TASK_TITLE}' => $data['type_title'],
                        '{TASK_URL}' => $siteUrl . '/' . $data['access_url'],
                        '{COMPANY_TITLE}' => $company_title,
                    ];
                    $templateContent = 'The recurring task #{TASK_ID} - "{TASK_TITLE}" has been executed. You can view the new instance here: {TASK_URL}';
                    break;
            }
        } else if ($type === 'slack') {
            switch ($data['type']) {
                case 'project':
                    $contentPlaceholders = [
                        '{PROJECT_ID}' => $data['type_id'],
                        '{PROJECT_TITLE}' => $data['type_title'],
                        '{USER_FIRST_NAME}' => $recipient->first_name,
                        '{USER_LAST_NAME}' => $recipient->last_name,
                        '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                        '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                        '{COMPANY_TITLE}' => $company_title,
                        '{PROJECT_URL}' => $siteUrl . '/' . $data['access_url']
                    ];
                    $templateContent = '*New Project Assigned:* {PROJECT_TITLE}, ID: #{PROJECT_ID}. By {ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME} You can find the project here :{PROJECT_URL}';
                    break;
                case 'project_status_updation':
                    $contentPlaceholders = [
                        '{PROJECT_ID}' => $data['type_id'],
                        '{PROJECT_TITLE}' => $data['type_title'],
                        '{USER_FIRST_NAME}' => $recipient->first_name,
                        '{USER_LAST_NAME}' => $recipient->last_name,
                        '{UPDATER_FIRST_NAME}' => $data['updater_first_name'],
                        '{UPDATER_LAST_NAME}' => $data['updater_last_name'],
                        '{OLD_STATUS}' => $data['old_status'],
                        '{NEW_STATUS}' => $data['new_status'],
                        '{PROJECT_URL}' => $siteUrl . '/' . $data['access_url'],
                        '{COMPANY_TITLE}' => $company_title
                    ];
                    $templateContent = '*Project Status Updated:* By {UPDATER_FIRST_NAME} {UPDATER_LAST_NAME} , {PROJECT_TITLE}, ID: #{PROJECT_ID}. Status changed from `{OLD_STATUS}` to `{NEW_STATUS}`. You can find the project here :{PROJECT_URL}';
                    break;
                case 'task':
                    $contentPlaceholders = [
                        '{TASK_ID}' => $data['type_id'],
                        '{TASK_TITLE}' => $data['type_title'],
                        '{USER_FIRST_NAME}' => $recipient->first_name,
                        '{USER_LAST_NAME}' => $recipient->last_name,
                        '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                        '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                        '{COMPANY_TITLE}' => $company_title,
                        '{TASK_URL}' => $siteUrl . '/' . $data['access_url']
                    ];
                    $templateContent = '*New Task Assigned:* {TASK_TITLE}, ID: #{TASK_ID}. By {ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME} You can find the task here : {TASK_URL}';
                    break;
                case 'task_status_updation':
                    $contentPlaceholders = [
                        '{TASK_ID}' => $data['type_id'],
                        '{TASK_TITLE}' => $data['type_title'],
                        '{USER_FIRST_NAME}' => $recipient->first_name,
                        '{USER_LAST_NAME}' => $recipient->last_name,
                        '{UPDATER_FIRST_NAME}' => $data['updater_first_name'],
                        '{UPDATER_LAST_NAME}' => $data['updater_last_name'],
                        '{OLD_STATUS}' => $data['old_status'],
                        '{NEW_STATUS}' => $data['new_status'],
                        '{TASK_URL}' => $siteUrl . '/' . $data['access_url'],
                        '{COMPANY_TITLE}' => $company_title
                    ];
                    $templateContent = '*Task Status Updated:* By {UPDATER_FIRST_NAME} {UPDATER_LAST_NAME},  {TASK_TITLE}, ID: #{TASK_ID}. Status changed from `{OLD_STATUS}` to `{NEW_STATUS}`. You can find the Task here : {TASK_URL}';
                    break;
                case 'workspace':
                    $contentPlaceholders = [
                        '{WORKSPACE_ID}' => $data['type_id'],
                        '{WORKSPACE_TITLE}' => $data['type_title'],
                        '{USER_FIRST_NAME}' => $recipient->first_name,
                        '{USER_LAST_NAME}' => $recipient->last_name,
                        '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                        '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                        '{COMPANY_TITLE}' => $company_title,
                        '{WORKSPACE_URL}' => $siteUrl . '/workspaces'
                    ];
                    $templateContent = '*New Workspace Added:* By {ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME},   {WORKSPACE_TITLE}, ID: #{WORKSPACE_ID}. You can find the Workspace here : {WORKSPACE_URL}';
                    break;
                case 'meeting':
                    $contentPlaceholders = [
                        '{MEETING_ID}' => $data['type_id'],
                        '{MEETING_TITLE}' => $data['type_title'],
                        '{USER_FIRST_NAME}' => $recipient->first_name,
                        '{USER_LAST_NAME}' => $recipient->last_name,
                        '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                        '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                        '{COMPANY_TITLE}' => $company_title,
                        '{MEETING_URL}' => $siteUrl . '/meetings'
                    ];
                    $templateContent = 'New Meeting Scheduled:* By {ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME},  {MEETING_TITLE}, ID: #{MEETING_ID}. You can find the Meeting here : {MEETING_URL}';
                    break;
                case 'leave_request_creation':
                    $contentPlaceholders = [
                        '{ID}' => $data['type_id'],
                        '{USER_FIRST_NAME}' => $recipient->first_name,
                        '{USER_LAST_NAME}' => $recipient->last_name,
                        '{REQUESTEE_FIRST_NAME}' => $data['team_member_first_name'],
                        '{REQUESTEE_LAST_NAME}' => $data['team_member_last_name'],
                        '{TYPE}' => $data['leave_type'],
                        '{FROM}' => $data['from'],
                        '{TO}' => $data['to'],
                        '{DURATION}' => $data['duration'],
                        '{REASON}' => $data['reason'],
                        '{COMMENT}' => $data['comment'],
                        '{STATUS}' => $data['status'],
                        '{COMPANY_TITLE}' => $company_title
                    ];
                    $templateContent = '*New {TYPE} Leave Request Created:* ID: #{ID} By {REQUESTEE_FIRST_NAME} {REQUESTEE_LAST_NAME} for {REASON}.  From ( {FROM} ) -  To ( {TO} ).';
                    break;
                case 'leave_request_status_updation':
                    $contentPlaceholders = [
                        '{ID}' => $data['type_id'],
                        '{USER_FIRST_NAME}' => $recipient->first_name,
                        '{USER_LAST_NAME}' => $recipient->last_name,
                        '{REQUESTEE_FIRST_NAME}' => $data['team_member_first_name'],
                        '{REQUESTEE_LAST_NAME}' => $data['team_member_last_name'],
                        '{TYPE}' => $data['leave_type'],
                        '{FROM}' => $data['from'],
                        '{TO}' => $data['to'],
                        '{DURATION}' => $data['duration'],
                        '{REASON}' => $data['reason'],
                        '{COMMENT}' => $data['comment'],
                        '{OLD_STATUS}' => $data['old_status'],
                        '{NEW_STATUS}' => $data['new_status'],
                        '{COMPANY_TITLE}' => $company_title
                    ];
                    $templateContent = '*Leave Request Status Updated:* For {REQUESTEE_FIRST_NAME} {REQUESTEE_LAST_NAME},  ID: #{ID}. Status changed from `{OLD_STATUS}` to `{NEW_STATUS}`.';
                    break;
                case 'team_member_on_leave_alert':
                    $contentPlaceholders = [
                        '{ID}' => $data['type_id'],
                        '{USER_FIRST_NAME}' => $recipient->first_name,
                        '{USER_LAST_NAME}' => $recipient->last_name,
                        '{REQUESTEE_FIRST_NAME}' => $data['team_member_first_name'],
                        '{REQUESTEE_LAST_NAME}' => $data['team_member_last_name'],
                        '{TYPE}' => $data['leave_type'],
                        '{FROM}' => $data['from'],
                        '{TO}' => $data['to'],
                        '{DURATION}' => $data['duration'],
                        '{COMPANY_TITLE}' => $company_title
                    ];
                    $templateContent = '*Team Member Leave Alert:* {REQUESTEE_FIRST_NAME} {REQUESTEE_LAST_NAME} will be on {TYPE} leave from {FROM} to {TO}.';
                    break;

                case 'birthday_wish':
                    $contentPlaceholders = [
                        '{FIRST_NAME}' => $recipient->first_name,
                        '{LAST_NAME}' => $recipient->last_name,
                        '{BIRTHDAY_COUNT}' => $data['birthday_count'],
                        '{ORDINAL_SUFFIX}' => $data['ordinal_suffix'],
                        '{COMPANY_TITLE}' => $company_title,
                        '{SITE_URL}' => $siteUrl
                    ];
                    $templateContent = 'Hello *{FIRST_NAME} {LAST_NAME}*, {COMPANY_TITLE} wishes you a very Happy Birthday!';
                    break;

                case 'work_anniversary_wish':
                    $contentPlaceholders = [
                        '{FIRST_NAME}' => $recipient->first_name,
                        '{LAST_NAME}' => $recipient->last_name,
                        '{WORK_ANNIVERSARY_COUNT}' => $data['work_anniversary_count'],
                        '{ORDINAL_SUFFIX}' => $data['ordinal_suffix'],
                        '{COMPANY_TITLE}' => $company_title,
                        '{SITE_URL}' => $siteUrl
                    ];
                    $templateContent = 'Hello *{FIRST_NAME} {LAST_NAME}*, {COMPANY_TITLE} wishes you a very happy work anniversary!';
                    break;
                case 'task_reminder':
                    $contentPlaceholders = [
                        '{TASK_ID}' => $data['type_id'],
                        '{TASK_TITLE}' => $data['type_title'],
                        '{TASK_URL}' => $siteUrl . '/' . $data['access_url'],
                        '{COMPANY_TITLE}' => $company_title,
                    ];
                    $templateContent = 'You have a task reminder for Task #{TASK_ID} - "{TASK_TITLE}". You can view the task here: {TASK_URL}.';
                    break;
                case 'recurring_task':
                    $contentPlaceholders = [
                        '{TASK_ID}' => $data['type_id'],
                        '{TASK_TITLE}' => $data['type_title'],
                        '{TASK_URL}' => $siteUrl . '/' . $data['access_url'],
                        '{COMPANY_TITLE}' => $company_title,
                    ];
                    $templateContent = 'The recurring task #{TASK_ID} - "{TASK_TITLE}" has been executed. You can view the new instance here: {TASK_URL}';
                    break;
            }
        } else {
            switch ($data['type']) {
                case 'project':
                    $contentPlaceholders = [
                        '{PROJECT_ID}' => $data['type_id'],
                        '{PROJECT_TITLE}' => $data['type_title'],
                        '{FIRST_NAME}' => $recipient->first_name,
                        '{LAST_NAME}' => $recipient->last_name,
                        '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                        '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                        '{COMPANY_TITLE}' => $company_title,
                        '{PROJECT_URL}' => $siteUrl . '/' . $data['access_url'],
                        '{SITE_URL}' => $siteUrl
                    ];
                    $templateContent = 'Hello, {FIRST_NAME} {LAST_NAME} You have been assigned a new project {PROJECT_TITLE}, ID:#{PROJECT_ID}.';
                    break;
                case 'project_status_updation':
                    $contentPlaceholders = [
                        '{PROJECT_ID}' => $data['type_id'],
                        '{PROJECT_TITLE}' => $data['type_title'],
                        '{FIRST_NAME}' => $recipient->first_name,
                        '{LAST_NAME}' => $recipient->last_name,
                        '{UPDATER_FIRST_NAME}' => $data['updater_first_name'],
                        '{UPDATER_LAST_NAME}' => $data['updater_last_name'],
                        '{OLD_STATUS}' => $data['old_status'],
                        '{NEW_STATUS}' => $data['new_status'],
                        '{PROJECT_URL}' => $siteUrl . '/' . $data['access_url'],
                        '{SITE_URL}' => $siteUrl,
                        '{COMPANY_TITLE}' => $company_title
                    ];
                    $templateContent = '{UPDATER_FIRST_NAME} {UPDATER_LAST_NAME} has updated the status of project {PROJECT_TITLE}, ID:#{PROJECT_ID}, from {OLD_STATUS} to {NEW_STATUS}.';
                    break;
                case 'task':
                    $contentPlaceholders = [
                        '{TASK_ID}' => $data['type_id'],
                        '{TASK_TITLE}' => $data['type_title'],
                        '{FIRST_NAME}' => $recipient->first_name,
                        '{LAST_NAME}' => $recipient->last_name,
                        '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                        '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                        '{COMPANY_TITLE}' => $company_title,
                        '{TASK_URL}' => $siteUrl . '/' . $data['access_url'],
                        '{SITE_URL}' => $siteUrl
                    ];
                    $templateContent = 'Hello, {FIRST_NAME} {LAST_NAME} You have been assigned a new task {TASK_TITLE}, ID:#{TASK_ID}.';
                    break;
                case 'task_status_updation':
                    $contentPlaceholders = [
                        '{TASK_ID}' => $data['type_id'],
                        '{TASK_TITLE}' => $data['type_title'],
                        '{FIRST_NAME}' => $recipient->first_name,
                        '{LAST_NAME}' => $recipient->last_name,
                        '{UPDATER_FIRST_NAME}' => $data['updater_first_name'],
                        '{UPDATER_LAST_NAME}' => $data['updater_last_name'],
                        '{OLD_STATUS}' => $data['old_status'],
                        '{NEW_STATUS}' => $data['new_status'],
                        '{TASK_URL}' => $siteUrl . '/' . $data['access_url'],
                        '{SITE_URL}' => $siteUrl,
                        '{COMPANY_TITLE}' => $company_title
                    ];
                    $templateContent = '{UPDATER_FIRST_NAME} {UPDATER_LAST_NAME} has updated the status of task {TASK_TITLE}, ID:#{TASK_ID}, from {OLD_STATUS} to {NEW_STATUS}.';
                    break;
                case 'workspace':
                    $contentPlaceholders = [
                        '{WORKSPACE_ID}' => $data['type_id'],
                        '{WORKSPACE_TITLE}' => $data['type_title'],
                        '{FIRST_NAME}' => $recipient->first_name,
                        '{LAST_NAME}' => $recipient->last_name,
                        '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                        '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                        '{COMPANY_TITLE}' => $company_title,
                        '{WORKSPACE_URL}' => $siteUrl . '/workspaces',
                        '{SITE_URL}' => $siteUrl
                    ];
                    $templateContent = 'Hello, {FIRST_NAME} {LAST_NAME} You have been added in a new workspace {WORKSPACE_TITLE}, ID:#{WORKSPACE_ID}.';
                    break;
                case 'meeting':
                    $contentPlaceholders = [
                        '{MEETING_ID}' => $data['type_id'],
                        '{MEETING_TITLE}' => $data['type_title'],
                        '{FIRST_NAME}' => $recipient->first_name,
                        '{LAST_NAME}' => $recipient->last_name,
                        '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                        '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                        '{COMPANY_TITLE}' => $company_title,
                        '{MEETING_URL}' => $siteUrl . '/meetings',
                        '{SITE_URL}' => $siteUrl
                    ];
                    $templateContent = 'Hello, {FIRST_NAME} {LAST_NAME} You have been added in a new meeting {MEETING_TITLE}, ID:#{MEETING_ID}.';
                    break;

                case 'leave_request_creation':
                    $contentPlaceholders = [
                        '{ID}' => $data['type_id'],
                        '{USER_FIRST_NAME}' => $recipient->first_name,
                        '{USER_LAST_NAME}' => $recipient->last_name,
                        '{REQUESTEE_FIRST_NAME}' => $data['team_member_first_name'],
                        '{REQUESTEE_LAST_NAME}' => $data['team_member_last_name'],
                        '{TYPE}' => $data['leave_type'],
                        '{FROM}' => $data['from'],
                        '{TO}' => $data['to'],
                        '{DURATION}' => $data['duration'],
                        '{REASON}' => $data['reason'],
                        '{COMMENT}' => $data['comment'],
                        '{STATUS}' => $data['status'],
                        '{COMPANY_TITLE}' => $company_title,
                        '{SITE_URL}' => $siteUrl,
                        '{CURRENT_YEAR}' => date('Y')
                    ];
                    $templateContent = 'New Leave Request ID:#{ID} Has Been Created By {REQUESTEE_FIRST_NAME} {REQUESTEE_LAST_NAME}.';
                    break;

                case 'leave_request_status_updation':
                    $contentPlaceholders = [
                        '{ID}' => $data['type_id'],
                        '{USER_FIRST_NAME}' => $recipient->first_name,
                        '{USER_LAST_NAME}' => $recipient->last_name,
                        '{REQUESTEE_FIRST_NAME}' => $data['team_member_first_name'],
                        '{REQUESTEE_LAST_NAME}' => $data['team_member_last_name'],
                        '{TYPE}' => $data['leave_type'],
                        '{FROM}' => $data['from'],
                        '{TO}' => $data['to'],
                        '{DURATION}' => $data['duration'],
                        '{REASON}' => $data['reason'],
                        '{COMMENT}' => $data['comment'],
                        '{OLD_STATUS}' => $data['old_status'],
                        '{NEW_STATUS}' => $data['new_status'],
                        '{COMPANY_TITLE}' => $company_title,
                        '{SITE_URL}' => $siteUrl,
                        '{CURRENT_YEAR}' => date('Y')
                    ];
                    $templateContent = 'Leave Request ID:#{ID} Status Updated From {OLD_STATUS} To {NEW_STATUS}.';
                    break;

                case 'team_member_on_leave_alert':
                    $contentPlaceholders = [
                        '{ID}' => $data['type_id'],
                        '{USER_FIRST_NAME}' => $recipient->first_name,
                        '{USER_LAST_NAME}' => $recipient->last_name,
                        '{REQUESTEE_FIRST_NAME}' => $data['team_member_first_name'],
                        '{REQUESTEE_LAST_NAME}' => $data['team_member_last_name'],
                        '{TYPE}' => $data['leave_type'],
                        '{FROM}' => $data['from'],
                        '{TO}' => $data['to'],
                        '{DURATION}' => $data['duration'],
                        '{COMPANY_TITLE}' => $company_title,
                        '{SITE_URL}' => $siteUrl,
                        '{CURRENT_YEAR}' => date('Y')
                    ];
                    $templateContent = '{REQUESTEE_FIRST_NAME} {REQUESTEE_LAST_NAME} will be on {TYPE} leave from {FROM} to {TO}.';
                    break;

                case 'birthday_wish':
                    $contentPlaceholders = [
                        '{FIRST_NAME}' => $recipient->first_name,
                        '{LAST_NAME}' => $recipient->last_name,
                        '{BIRTHDAY_COUNT}' => $data['birthday_count'],
                        '{ORDINAL_SUFFIX}' => $data['ordinal_suffix'],
                        '{COMPANY_TITLE}' => $company_title,
                        '{SITE_URL}' => $siteUrl
                    ];
                    $templateContent = 'Hello {FIRST_NAME} {LAST_NAME}, {COMPANY_TITLE} wishes you a very Happy Birthday!';
                    break;

                case 'work_anniversary_wish':
                    $contentPlaceholders = [
                        '{FIRST_NAME}' => $recipient->first_name,
                        '{LAST_NAME}' => $recipient->last_name,
                        '{WORK_ANNIVERSARY_COUNT}' => $data['work_anniversary_count'],
                        '{ORDINAL_SUFFIX}' => $data['ordinal_suffix'],
                        '{COMPANY_TITLE}' => $company_title,
                        '{SITE_URL}' => $siteUrl
                    ];
                    $templateContent = 'Hello {FIRST_NAME} {LAST_NAME}, {COMPANY_TITLE} wishes you a very happy work anniversary!';
                    break;
                case 'task_reminder':
                    $contentPlaceholders = [
                        '{TASK_ID}' => $data['type_id'],
                        '{TASK_TITLE}' => $data['type_title'],
                        '{TASK_URL}' => $siteUrl . '/' . $data['access_url'],
                        '{COMPANY_TITLE}' => $company_title,
                        '{SITE_URL}' => $siteUrl
                    ];
                    $templateContent = 'You have a task reminder for Task #{TASK_ID} - {TASK_TITLE}. You can view the task here: {TASK_URL}';
                    break;
                case 'recurring_task':
                    $contentPlaceholders = [
                        '{TASK_ID}' => $data['type_id'],
                        '{TASK_TITLE}' => $data['type_title'],
                        '{TASK_URL}' => $siteUrl . '/' . $data['access_url'],
                        '{COMPANY_TITLE}' => $company_title,
                    ];
                    $templateContent = 'The recurring task #{TASK_ID} - "{TASK_TITLE}" has been executed. You can view the new instance here: {TASK_URL}';
                    break;
            }
        }
        if (filled(Arr::get($fetched_data, 'content'))) {
            $templateContent = $fetched_data->content;
        }
        // Replace placeholders with actual values
        $content = str_replace(array_keys($contentPlaceholders), array_values($contentPlaceholders), $templateContent);

        return $content;
    }
}
if (!function_exists('format_budget')) {
    function format_budget($amount)
    {
        // Check if the input is numeric or can be converted to a numeric value.
        if (!is_numeric($amount)) {
            // If the input is not numeric, return null or handle the error as needed.
            return null;
        }

        // Remove non-numeric characters from the input string.
        $amount = preg_replace('/[^0-9.]/', '', $amount);

        // Convert the input to a float.
        $amount = (float) $amount;

        // Define suffixes for thousands, millions, etc.
        $suffixes = ['', 'K', 'M', 'B', 'T'];

        // Determine the appropriate suffix and divide the amount accordingly.
        $suffixIndex = 0;
        while ($amount >= 1000 && $suffixIndex < count($suffixes) - 1) {
            $amount /= 1000;
            $suffixIndex++;
        }

        // Format the amount with the determined suffix.
        return number_format($amount, 2) . $suffixes[$suffixIndex];
    }
}
if (!function_exists('canSetStatus')) {
    function canSetStatus($status)
    {
        $user = getAuthenticatedUser();
        $isAdminOrHasAllDataAccess = isAdminOrHasAllDataAccess();

        // Ensure the user and their first role exist
        $userRoleId = $user && $user->roles->isNotEmpty() ? $user->roles->first()->id : null;

        // Check if the user has permission for this status
        $hasPermission = $userRoleId && $status->roles->contains($userRoleId) || $isAdminOrHasAllDataAccess;

        return $hasPermission;
    }
}


if (!function_exists('checkPermission')) {
    function checkPermission($permission)
    {
        static $user = null;

        if ($user === null) {
            $user = getAuthenticatedUser();
        }

        return $user->can($permission);
    }
}

if (!function_exists('getUserPreferences')) {
    function getUserPreferences($table, $column = 'visible_columns', $userId = null)
    {
        if ($userId === null) {
            $userId = getAuthenticatedUser(true, true);
        }

        $result = UserClientPreference::where('user_id', $userId)
            ->where('table_name', $table)
            ->first();
        switch ($column) {
            case 'default_view':
                if ($table == 'projects') {
                    return $result && $result->default_view ? (
                        $result->default_view == 'kanban' ? 'projects/kanban' : (
                            $result->default_view == 'list' ? 'projects/list' : (
                                $result->default_view == 'gantt-chart' ? 'projects/gantt-chart' : 'projects'
                            )
                        )
                    ) : 'projects';
                } elseif ($table == 'tasks') {
                    return $result && $result->default_view ? (
                        $result->default_view == 'draggable' ? 'tasks/draggable' : (
                            $result->default_view == 'calendar' ? 'tasks/calendar' : 'tasks'
                        )
                    ) : 'tasks';
                }
                break;
            case 'visible_columns':
                return $result && $result->visible_columns ? $result->visible_columns : [];
                break;
            case 'enabled_notifications':
            case 'enabled_notifications':
                if ($result) {
                    if ($result->enabled_notifications === null) {
                        return null;
                    }
                    return json_decode($result->enabled_notifications, true);
                }
                return [];
                break;
                break;
            default:
                return null;
                break;
        }
    }
}
if (!function_exists('getOrdinalSuffix')) {
    function getOrdinalSuffix($number)
    {
        if (!in_array(($number % 100), [11, 12, 13])) {
            switch ($number % 10) {
                case 1:
                    return 'st';
                case 2:
                    return 'nd';
                case 3:
                    return 'rd';
            }
        }
        return 'th';
    }
}

if (!function_exists('getTitle')) {
    function getTitle($data, $recipient = NULL, $type = 'system')
    {
        static $authUser = null;
        static $companyTitle = null;

        if ($authUser === null) {
            $authUser = getAuthenticatedUser();
        }
        if ($companyTitle === null) {
            $general_settings = get_settings('general_settings');
            $companyTitle = $general_settings['company_title'] ?? 'Taskify';
        }

        $fetched_data = Template::where('type', $type)
            ->where('name', $data['type'] . '_assignment')
            ->first();

        if (!$fetched_data) {
            $fetched_data = Template::where('type', $type)
                ->where('name', $data['type'])
                ->first();
        }
        $subject = 'Default Subject'; // Set a default subject
        $subjectPlaceholders = [];

        // Customize subject based on type
        switch ($data['type']) {
            case 'project':
                $subjectPlaceholders = [
                    '{PROJECT_ID}' => $data['type_id'],
                    '{PROJECT_TITLE}' => $data['type_title'],
                    '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                    '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                    '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                    '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                    '{COMPANY_TITLE}' => $companyTitle
                ];
                break;
            case 'task':
                $subjectPlaceholders = [
                    '{TASK_ID}' => $data['type_id'],
                    '{TASK_TITLE}' => $data['type_title'],
                    '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                    '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                    '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                    '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                    '{COMPANY_TITLE}' => $companyTitle
                ];
                break;
            case 'workspace':
                $subjectPlaceholders = [
                    '{WORKSPACE_ID}' => $data['type_id'],
                    '{WORKSPACE_TITLE}' => $data['type_title'],
                    '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                    '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                    '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                    '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                    '{COMPANY_TITLE}' => $companyTitle
                ];
                break;
            case 'meeting':
                $subjectPlaceholders = [
                    '{MEETING_ID}' => $data['type_id'],
                    '{MEETING_TITLE}' => $data['type_title'],
                    '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                    '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                    '{ASSIGNEE_FIRST_NAME}' => $authUser->first_name,
                    '{ASSIGNEE_LAST_NAME}' => $authUser->last_name,
                    '{COMPANY_TITLE}' => $companyTitle
                ];
                break;
            case 'leave_request_creation':
                $subjectPlaceholders = [
                    '{ID}' => $data['type_id'],
                    '{STATUS}' => $data['status'],
                    '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                    '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                    '{REQUESTEE_FIRST_NAME}' => $data['team_member_first_name'],
                    '{REQUESTEE_LAST_NAME}' => $data['team_member_last_name'],
                    '{COMPANY_TITLE}' => $companyTitle
                ];
                break;
            case 'leave_request_status_updation':
                $subjectPlaceholders = [
                    '{ID}' => $data['type_id'],
                    '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                    '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                    '{UPDATER_FIRST_NAME}' => $data['updater_first_name'],
                    '{UPDATER_LAST_NAME}' => $data['updater_last_name'],
                    '{OLD_STATUS}' => $data['old_status'],
                    '{NEW_STATUS}' => $data['new_status'],
                    '{COMPANY_TITLE}' => $companyTitle
                ];
                break;
            case 'team_member_on_leave_alert':
                $subjectPlaceholders = [
                    '{ID}' => $data['type_id'],
                    '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                    '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                    '{REQUESTEE_FIRST_NAME}' => $data['team_member_first_name'],
                    '{REQUESTEE_LAST_NAME}' => $data['team_member_last_name'],
                    '{COMPANY_TITLE}' => $companyTitle
                ];
                break;
            case 'project_status_updation':
                $subjectPlaceholders = [
                    '{PROJECT_ID}' => $data['type_id'],
                    '{PROJECT_TITLE}' => $data['type_title'],
                    '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                    '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                    '{UPDATER_FIRST_NAME}' => $data['updater_first_name'],
                    '{UPDATER_LAST_NAME}' => $data['updater_last_name'],
                    '{OLD_STATUS}' => $data['old_status'],
                    '{NEW_STATUS}' => $data['new_status'],
                    '{COMPANY_TITLE}' => $companyTitle
                ];
                break;
            case 'task_status_updation':
                $subjectPlaceholders = [
                    '{TASK_ID}' => $data['type_id'],
                    '{TASK_TITLE}' => $data['type_title'],
                    '{USER_FIRST_NAME}' => $recipient ? $recipient->first_name : '',
                    '{USER_LAST_NAME}' => $recipient ? $recipient->last_name : '',
                    '{UPDATER_FIRST_NAME}' => $data['updater_first_name'],
                    '{UPDATER_LAST_NAME}' => $data['updater_last_name'],
                    '{OLD_STATUS}' => $data['old_status'],
                    '{NEW_STATUS}' => $data['new_status'],
                    '{COMPANY_TITLE}' => $companyTitle
                ];
                break;
            case 'birthday_wish':
                $subjectPlaceholders = [
                    '{FIRST_NAME}' => $recipient->first_name,
                    '{LAST_NAME}' => $recipient->last_name,
                    '{BIRTHDAY_COUNT}' => $data['birthday_count'],
                    '{ORDINAL_SUFFIX}' => $data['ordinal_suffix'],
                    '{COMPANY_TITLE}' => $companyTitle
                ];
                break;

            case 'work_anniversary_wish':
                $subjectPlaceholders = [
                    '{FIRST_NAME}' => $recipient->first_name,
                    '{LAST_NAME}' => $recipient->last_name,
                    '{WORK_ANNIVERSARY_COUNT}' => $data['work_anniversary_count'],
                    '{ORDINAL_SUFFIX}' => $data['ordinal_suffix'],
                    '{COMPANY_TITLE}' => $companyTitle
                ];
                break;
        }
        if (filled(Arr::get($fetched_data, 'subject'))) {
            $subject = $fetched_data->subject;
        } else {
            if ($data['type'] == 'leave_request_creation') {
                $subject = 'Leave Requested';
            } elseif ($data['type'] == 'leave_request_status_updation') {
                $subject = 'Leave Request Status Updated';
            } elseif ($data['type'] == 'team_member_on_leave_alert') {
                $subject = 'Team Member on Leave Alert';
            } elseif ($data['type'] == 'project_status_updation') {
                $subject = 'Project Status Updated';
            } elseif ($data['type'] == 'task_status_updation') {
                $subject = 'Task Status Updated';
            } elseif ($data['type'] == 'birthday_wish') {
                $subject = 'Happy Birthday!';
            } elseif ($data['type'] == 'work_anniversary_wish') {
                $subject = 'Happy Work Anniversary!';
            } else {
                $subject = 'New ' . ucfirst($data['type']) . ' Assigned';
            }
        }

        $subject = str_replace(array_keys($subjectPlaceholders), array_values($subjectPlaceholders), $subject);

        return $subject;
    }
}
if (!function_exists('hasPrimaryWorkspace')) {
    function hasPrimaryWorkspace()
    {
        $primaryWorkspace = \App\Models\Workspace::where('is_primary', 1)->first();

        return $primaryWorkspace ? $primaryWorkspace->id : 0;
    }
}
if (!function_exists('getWorkspaceId')) {
    function getWorkspaceId()
    {
        $workspaceId = 0;
        $authenticatedUser = getAuthenticatedUser();

        if ($authenticatedUser) {
            if (session()->has('workspace_id')) {
                $workspaceId = session('workspace_id'); // Retrieve workspace_id from session
            } else {
                $workspaceId = request()->header('workspace_id');
            }
        }
        return $workspaceId;
    }
}

if (!function_exists('getGuardName')) {
    function getGuardName()
    {
        static $guardName = null;

        // If the guard name is already determined, return it
        if ($guardName !== null) {
            return $guardName;
        }

        // Check the 'web' guard (users)
        if (Auth::guard('web')->check()) {
            $guardName = 'web';
        }
        // Check the 'client' guard (clients)
        elseif (Auth::guard('client')->check()) {
            $guardName = 'client';
        }
        // Check the 'sanctum' guard (API tokens)
        elseif (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();

            // Determine if the sanctum user is a user or a client
            if ($user instanceof \App\Models\User) {
                $guardName = 'web';
            } elseif ($user instanceof \App\Models\Client) {
                $guardName = 'client';
            }
        }

        return $guardName;
    }
}
if (!function_exists('formatProject')) {
    function formatProject($project)
    {
        $auth_user = getAuthenticatedUser();
        return [
            'id' => $project->id,
            'title' => $project->title,
            'task_count' => isAdminOrHasAllDataAccess() ? count($project->tasks) : $auth_user->project_tasks($project->id)->count(),
            'status' => $project->status->title,
            'status_id' => $project->status->id,
            'priority' => $project->priority ? $project->priority->title : null,
            'priority_id' => $project->priority ? $project->priority->id : null,
            'users' => $project->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'photo' => $user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg')
                ];
            }),
            'user_id' => $project->users->pluck('id')->toArray(),
            'clients' => $project->clients->map(function ($client) {
                return [
                    'id' => $client->id,
                    'first_name' => $client->first_name,
                    'last_name' => $client->last_name,
                    'email' => $client->email,
                    'photo' => $client->photo ? asset('storage/' . $client->photo) : asset('storage/photos/no-image.jpg')
                ];
            }),
            'client_id' => $project->clients->pluck('id')->toArray(),
            'tags' => $project->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'title' => $tag->title
                ];
            }),
            'tag_ids' => $project->tags->pluck('id')->toArray(),
            'start_date' => $project->start_date ? format_date($project->start_date, to_format: 'Y-m-d') : null,
            'end_date' => $project->end_date ? format_date($project->end_date, to_format: 'Y-m-d') : null,
            'budget' => $project->budget ?? null,
            'task_accessibility' => $project->task_accessibility,
            'description' => $project->description,
            'note' => $project->note,
            'favorite' => getFavoriteStatus($project->id),
            'pinned' => getPinnedStatus($project->id),
            'client_can_discuss' => $project->client_can_discuss,
            'created_at' => format_date($project->created_at, to_format: 'Y-m-d'),
            'updated_at' => format_date($project->updated_at, to_format: 'Y-m-d'),
        ];
    }
}

if (!function_exists('formatTask')) {
    function formatTask($task)
    {
        return [
            'id' => $task->id,
            'workspace_id' => $task->workspace_id,
            'title' => $task->title,
            'status' => $task->status->title,
            'status_id' => $task->status->id,
            'priority' => $task->priority ? $task->priority->title : null,
            'priority_id' => $task->priority ? $task->priority->id : null,
            'users' => $task->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'photo' => $user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg')
                ];
            }),
            'user_id' => $task->users->pluck('id')->toArray(),
            'clients' => $task->project->clients->map(function ($client) {
                return [
                    'id' => $client->id,
                    'first_name' => $client->first_name,
                    'last_name' => $client->last_name,
                    'email' => $client->email,
                    'photo' => $client->photo ? asset('storage/' . $client->photo) : asset('storage/photos/no-image.jpg')
                ];
            }),
            'start_date' => $task->start_date ? format_date($task->start_date, to_format: 'Y-m-d') : null,
            'due_date' => $task->due_date ? format_date($task->due_date, to_format: 'Y-m-d') : null,
            'project' => $task->project->title,
            'project_id' => $task->project->id,
            'description' => $task->description,
            'note' => $task->note,
            'favorite' => getFavoriteStatus($task->id, \App\Models\Task::class),
            'pinned' => getPinnedStatus($task->id, \App\Models\Task::class),
            'client_can_discuss' => $task->client_can_discuss,
            'created_at' => format_date($task->created_at, to_format: 'Y-m-d'),
            'updated_at' => format_date($task->updated_at, to_format: 'Y-m-d'),
        ];
    }
}
if (!function_exists('formatWorkspace')) {
    function formatWorkspace($workspace)
    {
        $authUser = getAuthenticatedUser();
        return [
            'id' => $workspace->id,
            'title' => $workspace->title,
            'primaryWorkspace' => $workspace->is_primary,
            'defaultWorkspace' => $authUser->default_workspace_id == $workspace->id ? 1 : 0,
            'users' => $workspace->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'photo' => $user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg')
                ];
            }),
            'user_ids' => $workspace->users->pluck('id')->toArray(),
            'clients' => $workspace->clients->map(function ($client) {
                return [
                    'id' => $client->id,
                    'first_name' => $client->first_name,
                    'last_name' => $client->last_name,
                    'email' => $client->email,
                    'photo' => $client->photo ? asset('storage/' . $client->photo) : asset('storage/photos/no-image.jpg')
                ];
            }),
            'client_ids' => $workspace->clients->pluck('id')->toArray(),
            'created_at' => format_date($workspace->created_at, to_format: 'Y-m-d'),
            'updated_at' => format_date($workspace->updated_at, to_format: 'Y-m-d'),
        ];
    }
}
if (!function_exists('formatMeeting')) {
    function formatMeeting($meeting)
    {
        $currentDateTime = Carbon::now(config('app.timezone'));
        $status = (($currentDateTime < \Carbon\Carbon::parse($meeting->start_date_time, config('app.timezone'))) ? 'Will start in ' . $currentDateTime->diff(\Carbon\Carbon::parse($meeting->start_date_time, config('app.timezone')))->format('%a days %H hours %I minutes %S seconds') : (($currentDateTime > \Carbon\Carbon::parse($meeting->end_date_time, config('app.timezone')) ? 'Ended before ' . \Carbon\Carbon::parse($meeting->end_date_time, config('app.timezone'))->diff($currentDateTime)->format('%a days %H hours %I minutes %S seconds') : 'Ongoing')));
        return [
            'id' => $meeting->id,
            'title' => $meeting->title,
            'start_date' => \Carbon\Carbon::parse($meeting->start_date_time)->format('Y-m-d'),
            'start_time' => \Carbon\Carbon::parse($meeting->start_date_time)->format('H:i'),
            'end_date' => \Carbon\Carbon::parse($meeting->end_date_time)->format('Y-m-d'),
            'end_time' => \Carbon\Carbon::parse($meeting->end_date_time)->format('H:i'),
            'users' => $meeting->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'photo' => $user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg')
                ];
            }),
            'user_ids' => $meeting->users->pluck('id')->toArray(),
            'clients' => $meeting->clients->map(function ($client) {
                return [
                    'id' => $client->id,
                    'first_name' => $client->first_name,
                    'last_name' => $client->last_name,
                    'email' => $client->email,
                    'photo' => $client->photo ? asset('storage/' . $client->photo) : asset('storage/photos/no-image.jpg')
                ];
            }),
            'client_ids' => $meeting->clients->pluck('id')->toArray(),
            'status' => $status,
            'ongoing' => $status == 'Ongoing' ? 1 : 0,
            'join_url' => url('meetings/join/web-view/' . $meeting->id),
            'created_at' => format_date($meeting->created_at, to_format: 'Y-m-d'),
            'updated_at' => format_date($meeting->updated_at, to_format: 'Y-m-d')
        ];
    }
}

if (!function_exists('formatNotification')) {
    function formatNotification($notification)
    {

        $readAt = isset($notification->notification_user_read_at)
            ? format_date($notification->notification_user_read_at, true)
            : (isset($notification->client_notifications_read_at)
                ? format_date($notification->client_notifications_read_at, true)
                : (isset($notification->pivot) && isset($notification->pivot->read_at)
                    ? format_date($notification->pivot->read_at, true)
                    : null));
        $labelRead = get_label('read', 'Read');
        $labelUnread = get_label('unread', 'Unread');
        $status = is_null($readAt) ? $labelUnread : $labelRead;

        // Handle is_system logic, including pivot
        $isSystem = $notification->notification_user_is_system
            ?? $notification->client_notifications_is_system
            ?? ($notification->pivot->is_system ?? null);

        // Handle is_push logic, including pivot
        $isPush = $notification->notification_user_is_push
            ?? $notification->client_notifications_is_push
            ?? ($notification->pivot->is_push ?? null);

        return [
            'id' => $notification->id,
            'title' => $notification->title,
            'users' => $notification->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'photo' => $user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg')
                ];
            }),
            'clients' => $notification->clients->map(function ($client) {
                return [
                    'id' => $client->id,
                    'first_name' => $client->first_name,
                    'last_name' => $client->last_name,
                    'email' => $client->email,
                    'photo' => $client->photo ? asset('storage/' . $client->photo) : asset('storage/photos/no-image.jpg')
                ];
            }),
            'type' => ucfirst(str_replace('_', ' ', $notification->type)),
            'type_id' => $notification->type_id,
            'message' => $notification->message,
            'status' => $status,
            'is_system' => $isSystem,
            'is_push' => $isPush,
            'read_at' => $readAt,
            'created_at' => format_date($notification->created_at, to_format: 'Y-m-d'),
            'updated_at' => format_date($notification->updated_at, to_format: 'Y-m-d')
        ];
    }
}

if (!function_exists('formatLeaveRequest')) {
    function formatLeaveRequest($leaveRequest)
    {
        $leaveRequest = LeaveRequest::select(
            'leave_requests.*',
            'users.photo AS user_photo',
            DB::raw('CONCAT(users.first_name, " ", users.last_name) AS user_name'),
            DB::raw('CONCAT(action_users.first_name, " ", action_users.last_name) AS action_by_name'),
            'leave_requests.action_by as action_by_id'
        )
            ->leftJoin('users', 'leave_requests.user_id', '=', 'users.id')
            ->leftJoin('users AS action_users', 'leave_requests.action_by', '=', 'action_users.id')
            ->where('leave_requests.workspace_id', getWorkspaceId())
            ->find($leaveRequest->id);
        // Calculate the duration in hours if both from_time and to_time are provided
        $fromDate = Carbon::parse($leaveRequest->from_date);
        $toDate = Carbon::parse($leaveRequest->to_date);

        $fromDateDayOfWeek = $fromDate->format('D');
        $toDateDayOfWeek = $toDate->format('D');

        if ($leaveRequest->from_time && $leaveRequest->to_time) {
            $duration = 0;
            // Loop through each day
            while ($fromDate->lessThanOrEqualTo($toDate)) {
                // Create Carbon instances for the start and end times of the leave request for the current day
                $fromDateTime = Carbon::parse($fromDate->toDateString() . ' ' . $leaveRequest->from_time);
                $toDateTime = Carbon::parse($fromDate->toDateString() . ' ' . $leaveRequest->to_time);

                // Calculate the duration for the current day and add it to the total duration
                $duration += $fromDateTime->diffInMinutes($toDateTime) / 60; // Duration in hours

                // Move to the next day
                $fromDate->addDay();
            }
        } else {
            // Calculate the inclusive duration in days
            $duration = $fromDate->diffInDays($toDate) + 1;
        }
        if ($leaveRequest->visible_to_all == 1) {
            $visibleTo = [];
        } else {
            $visibleTo = $leaveRequest->visibleToUsers->isEmpty()
                ? null
                : $leaveRequest->visibleToUsers->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'photo' => $user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg')
                    ];
                });
        }
        $visibleToIds = $leaveRequest->visibleToUsers->pluck('id')->toArray();

        return [
            'id' => $leaveRequest->id,
            'user_id' => $leaveRequest->user_id,
            'user_name' => $leaveRequest->user_name,
            'user_photo' => $leaveRequest->user_photo ? asset('storage/' . $leaveRequest->user_photo) : asset('storage/photos/no-image.jpg'),
            'action_by' => $leaveRequest->action_by_name,
            'action_by_id' => $leaveRequest->action_by_id,
            'from_date' => $leaveRequest->from_date,
            'from_time' => Carbon::parse($leaveRequest->from_time)->format('h:i A'),
            'to_date' => $leaveRequest->to_date,
            'to_time' => Carbon::parse($leaveRequest->to_time)->format('h:i A'),
            'type' => $leaveRequest->from_time && $leaveRequest->to_time ? 'Partial' : 'Full',
            'leaveVisibleToAll' => $leaveRequest->visible_to_all ? 'on' : 'off',
            'partialLeave' => $leaveRequest->from_time && $leaveRequest->to_time ? 'on' : 'off',
            'duration' => ($leaveRequest->from_time && $leaveRequest->to_time)
                ? (string) $duration
                : (string) number_format($duration, 2),
            'reason' => $leaveRequest->reason,
            'comment' => $leaveRequest->comment,
            'status' => $leaveRequest->status,
            'visible_to' => $visibleTo ?? [],
            'visible_to_ids' => $visibleToIds ?? [],
            'created_at' => format_date($leaveRequest->created_at, to_format: 'Y-m-d'),
            'updated_at' => format_date($leaveRequest->updated_at, to_format: 'Y-m-d'),
        ];
    }
}

if (!function_exists('formatUser')) {
    function formatUser($user, $isSignup = false)
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role' => $user->getRoleNames()->count() > 0 ? $user->getRoleNames()->first() : null,
            'role_id' => $user->roles()->count() > 0 ? $user->roles()->first()->id : null,
            'email' => $user->email,
            'phone' => $user->phone,
            'country_code' => $user->country_code,
            'country_iso_code' => $user->country_iso_code,
            'password' => $user->password,
            'password_confirmation' => $user->password,
            'type' => 'member',
            'dob' => $user->dob ? format_date($user->dob, to_format: 'Y-m-d') : null,
            'doj' => $user->doj ? format_date($user->doj, to_format: 'Y-m-d') : null,
            'address' => $user->address,
            'city' => $user->city,
            'state' => $user->state,
            'country' => $user->country,
            'zip' => $user->zip,
            'profile' => $user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg'),
            'status' => $user->status,
            'fcm_token' => $user->fcm_token,
            'created_at' => format_date($user->created_at, to_format: 'Y-m-d'),
            'updated_at' => format_date($user->updated_at, to_format: 'Y-m-d'),
            'assigned' => $isSignup ? [
                'projects' => 0,
                'tasks' => 0
            ] : (
                isAdminOrHasAllDataAccess('user', $user->id) ? [
                    'projects' => Workspace::find(getWorkspaceId())->projects()->count(),
                    'tasks' => Workspace::find(getWorkspaceId())->tasks()->count(),
                ] : [
                    'projects' => $user->projects()->count(),
                    'tasks' => $user->tasks()->count()
                ]
            )
        ];
    }
}

if (!function_exists('formatClient')) {
    function formatClient($client, $isSignup = false)
    {
        return [
            'id' => $client->id,
            'first_name' => $client->first_name,
            'last_name' => $client->last_name,
            'role' => $client->getRoleNames()->first(),
            'company' => $client->company,
            'email' => $client->email,
            'phone' => $client->phone,
            'country_code' => $client->country_code,
            'country_iso_code' => $client->country_iso_code,
            'password' => $client->password,
            'password_confirmation' => $client->password,
            'type' => 'client',
            'dob' => $client->dob ? format_date($client->dob, to_format: 'Y-m-d') : null,
            'doj' => $client->doj ? format_date($client->doj, to_format: 'Y-m-d') : null,
            'address' => $client->address ? $client->address : null,
            'city' => $client->city,
            'state' => $client->state,
            'country' => $client->country,
            'zip' => $client->zip,
            'profile' => $client->photo ? asset('storage/' . $client->photo) : asset('storage/photos/no-image.jpg'),
            'status' => $client->status,
            'fcm_token' => $client->fcm_token,
            'internal_purpose' => $client->internal_purpose,
            'email_verification_mail_sent' => $client->email_verification_mail_sent,
            'email_verified_at' => $client->email_verified_at,
            'created_at' => format_date($client->created_at, to_format: 'Y-m-d'),
            'updated_at' => format_date($client->updated_at, to_format: 'Y-m-d'),
            'assigned' => $isSignup ? [
                'projects' => 0,
                'tasks' => 0
            ] : (
                isAdminOrHasAllDataAccess('client', $client->id) ? [
                    'projects' => Workspace::find(getWorkspaceId())->projects()->count(),
                    'tasks' => Workspace::find(getWorkspaceId())->tasks()->count(),
                ] : [
                    'projects' => $client->projects()->count(),
                    'tasks' => $client->tasks()->count()
                ]
            )
        ];
    }
}

if (!function_exists('formatNote')) {
    function formatNote($note)
    {
        return [
            'id' => $note->id,
            'title' => $note->title,
            'color' => $note->color,
            'description' => $note->description,
            'workspace_id' => $note->workspace_id,
            'creator_id' => $note->creator_id,
            'created_at' => format_date($note->created_at, to_format: 'Y-m-d'),
            'updated_at' => format_date($note->updated_at, to_format: 'Y-m-d'),
        ];
    }
}

if (!function_exists('formatTodo')) {
    function formatTodo($todo)
    {
        return [
            'id' => $todo->id,
            'title' => $todo->title,
            'description' => $todo->description,
            'priority' => $todo->priority,
            'is_completed' => $todo->is_completed,
            'created_at' => format_date($todo->created_at, to_format: 'Y-m-d'),
            'updated_at' => format_date($todo->updated_at, to_format: 'Y-m-d'),
        ];
    }
}

if (!function_exists('formatRole')) {
    function formatRole($role)
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'guard_name' => $role->guard_name,
            'created_at' => format_date($role->created_at, to_format: 'Y-m-d'),
            'updated_at' => format_date($role->updated_at, to_format: 'Y-m-d'),
        ];
    }
}


if (!function_exists('validate_date_format_and_order')) {
    /**
     * Validate if a date matches the format specified and ensure the start date is before or equal to the end date.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $format
     * @param string $startDateLabel
     * @param string $endDateLabel
     * @param string $startDateKey
     * @param string $endDateKey
     * @return array
     */
    function validate_date_format_and_order(
        $startDate,
        $endDate,
        $format = null,
        $startDateLabel = 'start date',
        $endDateLabel = 'end date',
        $startDateKey = 'start_date',
        $endDateKey = 'end_date'
    ) {
        $matchFormat = $format ?? get_php_date_time_format();

        $errors = [];

        // Validate start date format
        if ($startDate && !validate_date_format($startDate, $matchFormat)) {
            $errors[$startDateKey][] = 'The ' . $startDateLabel . ' does not follow the format set in settings.';
        }

        // Validate end date format
        if ($endDate && !validate_date_format($endDate, $matchFormat)) {
            $errors[$endDateKey][] = 'The ' . $endDateLabel . ' does not follow the format set in settings.';
        }

        // Validate date order
        if ($startDate && $endDate) {
            $parsedStartDate = \DateTime::createFromFormat($matchFormat, $startDate);
            $parsedEndDate = \DateTime::createFromFormat($matchFormat, $endDate);

            if ($parsedStartDate && $parsedEndDate && $parsedStartDate > $parsedEndDate) {
                $errors[$startDateKey][] = 'The ' . $startDateLabel . ' must be before or equal to the ' . $endDateLabel . '.';
            }
        }

        return $errors;
    }
}


if (!function_exists('validate_date_format')) {
    /**
     * Validate if a date matches the format specified in settings.
     *
     * @param string $date
     * @param string|null $format
     * @return bool
     */
    function validate_date_format($date, $format = null)
    {
        $format = $format ?? get_php_date_time_format();
        $parsedDate = \DateTime::createFromFormat($format, $date);
        return $parsedDate && $parsedDate->format($format) === $date;
    }
}

if (!function_exists('validate_currency_format')) {
    function validate_currency_format($value, $label)
    {
        $regex = '/^(?:\d{1,3}(?:,\d{3})*|\d+)(\.\d+)?$/';
        if (!preg_match($regex, $value)) {
            return "The $label format is invalid.";
        }
        return null;
    }
}

if (!function_exists('formatApiResponse')) {
    function formatApiResponse($error, $message, array $optionalParams = [], $statusCode = 200)
    {
        $response = [
            'error' => $error,
            'message' => $message,
        ];

        // Merge optional parameters into the response if they are provided
        $response = array_merge($response, $optionalParams);

        return response()->json($response, $statusCode);
    }
}

if (!function_exists('isSanctumAuth')) {
    function isSanctumAuth()
    {
        return Auth::guard('web')->check() || Auth::guard('client')->check() ? false : true;
    }
}

if (!function_exists('formatApiValidationError')) {
    function formatApiValidationError($isApi, $errors, $defaultMessage = 'Validation errors occurred')
    {
        if ($isApi) {
            $messages = collect($errors)->flatten()->implode("\n");
            return response()->json([
                'error' => true,
                'message' => $messages,
            ], 422);
        } else {
            return response()->json([
                'error' => true,
                'message' => $defaultMessage,
                'errors' => $errors,
            ], 422);
        }
    }
}

function getMimeTypeMap()
{
    return [
        // Image MIME Types
        '.jpg' => 'image/jpeg',
        '.jpeg' => 'image/jpeg',
        '.png' => 'image/png',
        '.gif' => 'image/gif',
        '.bmp' => 'image/bmp',
        '.svg' => 'image/svg+xml',
        '.webp' => 'image/webp',
        '.tiff' => 'image/tiff',
        '.ico' => 'image/vnd.microsoft.icon',
        '.psd' => 'image/vnd.adobe.photoshop',
        '.heic' => 'image/heic',

        // Document MIME Types
        '.pdf' => 'application/pdf',
        '.doc' => 'application/msword',
        '.docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        '.xls' => 'application/vnd.ms-excel',
        '.xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        '.ppt' => 'application/vnd.ms-powerpoint',
        '.pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        '.txt' => 'text/plain',
        '.rtf' => 'application/rtf',
        '.odt' => 'application/vnd.oasis.opendocument.text',
        '.ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        '.odp' => 'application/vnd.oasis.opendocument.presentation',
        '.csv' => 'text/csv',
        '.md' => 'text/markdown',

        // Archive MIME Types
        '.zip' => 'application/zip',
        '.rar' => 'application/x-rar-compressed',
        '.7z' => 'application/x-7z-compressed',
        '.tar' => 'application/x-tar',
        '.gz' => 'application/gzip',
        '.bz2' => 'application/x-bzip2',
        '.xz' => 'application/x-xz',
        '.iso' => 'application/x-iso9660-image',

        // Audio MIME Types
        '.mp3' => 'audio/mpeg',
        '.wav' => 'audio/wav',
        '.ogg' => 'audio/ogg',
        '.flac' => 'audio/flac',
        '.aac' => 'audio/aac',
        '.m4a' => 'audio/x-m4a',
        '.wma' => 'audio/x-ms-wma',
        '.aiff' => 'audio/aiff',
        '.opus' => 'audio/opus',
        '.amr' => 'audio/amr',

        // Video MIME Types
        '.mp4' => 'video/mp4',
        '.avi' => 'video/x-msvideo',
        '.mov' => 'video/quicktime',
        '.wmv' => 'video/x-ms-wmv',
        '.flv' => 'video/x-flv',
        '.mkv' => 'video/x-matroska',
        '.webm' => 'video/webm',
        '.3gp' => 'video/3gpp',
        '.m4v' => 'video/x-m4v',
        '.mpg' => 'video/mpeg',
        '.mpeg' => 'video/mpeg',

        // Executable MIME Types
        '.exe' => 'application/vnd.microsoft.portable-executable',
        '.bat' => 'application/x-msdownload',
        '.sh' => 'application/x-sh',
        '.bin' => 'application/octet-stream',
        '.msi' => 'application/x-msi',
        '.cmd' => 'application/x-msdownload',
        '.jar' => 'application/java-archive',
        '.apk' => 'application/vnd.android.package-archive',

        // Code MIME Types
        '.html' => 'text/html',
        '.htm' => 'text/html',
        '.css' => 'text/css',
        '.js' => 'application/javascript',
        '.php' => 'application/x-httpd-php',
        '.java' => 'text/x-java-source',
        '.py' => 'text/x-python',
        '.rb' => 'application/x-ruby',
        '.pl' => 'application/x-perl',
        '.cpp' => 'text/x-c++',
        '.c' => 'text/x-c',
        '.h' => 'text/x-c',
        '.cs' => 'text/x-csharp',
        '.xml' => 'application/xml',
        '.json' => 'application/json',
        '.yml' => 'text/yaml',
        '.sql' => 'application/sql',

        // Font MIME Types
        '.ttf' => 'font/ttf',
        '.otf' => 'font/otf',
        '.woff' => 'font/woff',
        '.woff2' => 'font/woff2',
        '.eot' => 'application/vnd.ms-fontobject',

        // Miscellaneous MIME Types
        '.ics' => 'text/calendar',
        '.vcf' => 'text/x-vcard',
        '.swf' => 'application/x-shockwave-flash',
        '.epub' => 'application/epub+zip',
        '.mobi' => 'application/x-mobipocket-ebook',
        '.azw' => 'application/vnd.amazon.ebook',
        '.bak' => 'application/octet-stream'
    ];
}

function getMainAdminId()
{
    $mainAdminId = DB::table('model_has_roles')
        ->where('role_id', 1)
        ->orderBy('model_id')
        ->value('model_id');
    return $mainAdminId;
}

if (!function_exists('getMenus')) {
    function getMenus()
    {
        $user = getAuthenticatedUser();
        $current_workspace_id = getWorkspaceId();
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
        return [
            [
                'id' => 'dashboard',
                'label' => get_label('dashboard', 'Dashboard'),
                'url' => url('home'),
                'icon' => 'bx bx-home-circle text-danger',
                'class' => 'menu-item' . (Request::is('home') ? ' active' : '')
            ],
            [
                'id' => 'projects',
                'label' => get_label('projects', 'Projects'),
                'url' => url('projects'),
                'icon' => 'bx bx-briefcase-alt-2 text-success',
                'class' => 'menu-item' . (Request::is('projects') || Request::is('tags/*') || Request::is('projects/*') ? ' active open' : ''),
                'show' => ($user->can('manage_projects') || $user->can('manage_tags')) ? 1 : 0,
                'submenus' => [
                    [
                        'id' => 'manage_projects',
                        'label' => get_label('manage_projects', 'Manage projects'),
                        'url' => url(getUserPreferences('projects', 'default_view')),
                        'class' => 'menu-item' . (Request::is('projects') || (Request::is('projects/*') && !Request::is('projects/*/favorite') && !Request::is('projects/favorite') && !Request::is('projects/bulk-upload')) ? ' active' : ''),
                        'show' => ($user->can('manage_projects')) ? 1 : 0
                    ],
                    [
                        'id' => 'favorite_projects',
                        'label' => get_label('favorite_projects', 'Favorite projects'),
                        'url' => url(getUserPreferences('projects', 'default_view') . '/favorite'),
                        'class' => 'menu-item' . (Request::is('projects/favorite') || Request::is('projects/list/favorite') || Request::is('projects/kanban/favorite') ? ' active' : ''),
                        'show' => ($user->can('manage_projects')) ? 1 : 0
                    ],
                    [
                        'id' => 'projects_bulk_upload',
                        'label' => get_label('bulk_upload', 'Bulk Upload'),
                        'url' => route('projects.showBulkUploadForm'),
                        'class' => 'menu-item' . (Request::is('projects/bulk-upload') ? ' active' : ''),
                        'show' => ($user->can('manage_projects') && $user->can('create_projects')) ? 1 : 0
                    ],
                    [
                        'id' => 'tags',
                        'label' => get_label('tags', 'Tags'),
                        'url' => url('tags/manage'),
                        'class' => 'menu-item' . (Request::is('tags/*') ? ' active' : ''),
                        'show' => ($user->can('manage_tags')) ? 1 : 0
                    ],
                ],
            ],
            [
                'id' => 'tasks',
                'label' => get_label('tasks', 'Tasks'),
                'url' => url('tasks'),
                'icon' => 'bx bx-task text-primary',
                'class' => 'menu-item' . (Request::is('tasks') || Request::is('tasks/*') ? ' active open' : ''),
                'show' => $user->can('manage_tasks') ? 1 : 0,
                'submenus' => [
                    [
                        'id' => 'manage_tasks',
                        'label' => get_label('manage_tasks', 'Manage Tasks'),
                        'url' => url(getUserPreferences('tasks', 'default_view')),
                        'class' => 'menu-item' . (!(request()->query('favorite')) && (Request::is('tasks') || Request::is('tasks/*') && !Request::is('tasks/bulk-upload')) ? ' active' : ''),
                        'show' => ($user->can('manage_tasks')) ? 1 : 0
                    ],
                    [
                        'id' => 'favorite_tasks',
                        'label' => get_label('favorite_tasks', 'Favorite Tasks'),
                        'url' => url(getUserPreferences('tasks', 'default_view') . '?favorite=1'),
                        'class' => 'menu-item' . (request()->query('favorite') && (Request::is('tasks') || Request::is('tasks/calendar') || Request::is('tasks/draggable')) ? ' active' : ''),
                        'show' => ($user->can('manage_tasks')) ? 1 : 0
                    ],
                    [
                        'id' => 'tasks_bulk_upload',
                        'label' => get_label('bulk_upload', 'Bulk Upload'),
                        'url' => route('tasks.showBulkUploadForm'),
                        'class' => 'menu-item' . (Request::is('tasks/bulk-upload') ? ' active' : ''),
                        'show' => ($user->can('manage_tasks') && $user->can('create_tasks')) ? 1 : 0
                    ],
                ],
            ],
            [
                'id' => 'statuses',
                'label' => get_label('statuses', 'Statuses'),
                'url' => url('status/manage'),
                'icon' => 'bx bx-grid-small text-secondary',
                'class' => 'menu-item' . (Request::is('status/manage') ? ' active' : ''),
                'show' => $user->can('manage_statuses') ? 1 : 0
            ],
            [
                'id' => 'priorities',
                'label' => get_label('priorities', 'Priorities'),
                'url' => url('priority/manage'),
                'icon' => 'bx bx-up-arrow-alt text-success',
                'class' => 'menu-item' . (Request::is('priority/manage') ? ' active' : ''),
                'show' => $user->can('manage_priorities') ? 1 : 0
            ],
            [
                'id' => 'workspaces',
                'label' => get_label('workspaces', 'Workspaces'),
                'url' => url('workspaces'),
                'icon' => 'bx bx-check-square text-danger',
                'class' => 'menu-item' . (Request::is('workspaces') || Request::is('workspaces/*') ? ' active' : ''),
                'show' => $user->can('manage_workspaces') ? 1 : 0
            ],
            [
                'id' => 'chat',
                'label' => get_label('chat', 'Chat'),
                'url' => url('chat'),
                'icon' => 'bx bx-chat text-warning',
                'class' => 'menu-item' . (Request::is('chat') || Request::is('chat/*') ? ' active' : ''),
                'badge' => ($unread > 0) ? '<span class="flex-shrink-0 badge badge-center bg-danger w-px-20 h-px-20">' . $unread . '</span>' : '',
                'show' => Auth::guard('web')->check() ? 1 : 0
            ],
            [
                'id' => 'todos',
                'label' => get_label('todos', 'Todos'),
                'url' => url('todos'),
                'icon' => 'bx bx-list-check text-dark',
                'class' => 'menu-item' . (Request::is('todos') || Request::is('todos/*') ? ' active' : ''),
                'badge' => ($pending_todos_count > 0) ? '<span class="flex-shrink-0 badge badge-center bg-danger w-px-20 h-px-20">' . $pending_todos_count . '</span>' : ''
            ],
            [
                'id' => 'meetings',
                'label' => get_label('meetings', 'Meetings'),
                'url' => url('meetings'),
                'icon' => 'bx bx-shape-polygon text-success',
                'class' => 'menu-item' . (Request::is('meetings') || Request::is('meetings/*') ? ' active' : ''),
                'badge' => ($ongoing_meetings_count > 0) ? '<span class="flex-shrink-0 badge badge-center bg-success w-px-20 h-px-20">' . $ongoing_meetings_count . '</span>' : '',
                'show' => $user->can('manage_meetings') ? 1 : 0
            ],
            [
                'id' => 'users',
                'label' => get_label('users', 'Users'),
                'url' => url('users'),
                'icon' => 'bx bx-group text-primary',
                'class' => 'menu-item' . (Request::is('users') || Request::is('users/*') ? ' active' : ''),
                'show' => $user->can('manage_users') ? 1 : 0
            ],
            [
                'id' => 'clients',
                'label' => get_label('clients', 'Clients'),
                'url' => url('clients'),
                'icon' => 'bx bx-group text-warning',
                'class' => 'menu-item' . (Request::is('clients') || Request::is('clients/*') ? ' active' : ''),
                'show' => $user->can('manage_clients') ? 1 : 0
            ],
            [
                'id' => 'contracts',
                'label' => get_label('contracts', 'Contracts'),
                'url' => 'javascript:void(0)',
                'icon' => 'bx bx-news text-success',
                'class' => 'menu-item' . (Request::is('contracts') || Request::is('contracts/*') ? ' active open' : ''),
                'show' => ($user->can('manage_contracts') || $user->can('manage_contract_types')) ? 1 : 0,
                'submenus' => [
                    [
                        'id' => 'manage_contracts',
                        'label' => get_label('manage_contracts', 'Manage contracts'),
                        'url' => url('contracts'),
                        'class' => 'menu-item' . (Request::is('contracts') ? ' active' : ''),
                        'show' => $user->can('manage_contracts') ? 1 : 0
                    ],
                    [
                        'id' => 'contract_types',
                        'label' => get_label('contract_types', 'Contract types'),
                        'url' => url('contracts/contract-types'),
                        'class' => 'menu-item' . (Request::is('contracts/contract-types') ? ' active' : ''),
                        'show' => $user->can('manage_contract_types') ? 1 : 0
                    ],
                ],
            ],
            [
                'id' => 'payslips',
                'label' => get_label('payslips', 'Payslips'),
                'url' => 'javascript:void(0)',
                'icon' => 'bx bx-box text-warning',
                'class' => 'menu-item' . (Request::is('payslips') || Request::is('payslips/*') || Request::is('allowances') || Request::is('deductions') ? ' active open' : ''),
                'show' => ($user->can('manage_payslips') || $user->can('manage_allowances') || $user->can('manage_deductions')) ? 1 : 0,
                'submenus' => [
                    [
                        'id' => 'manage_payslips',
                        'label' => get_label('manage_payslips', 'Manage payslips'),
                        'url' => url('payslips'),
                        'class' => 'menu-item' . (Request::is('payslips') || Request::is('payslips/*') ? ' active' : ''),
                        'show' => $user->can('manage_payslips') ? 1 : 0
                    ],
                    [
                        'id' => 'allowances',
                        'label' => get_label('allowances', 'Allowances'),
                        'url' => url('allowances'),
                        'class' => 'menu-item' . (Request::is('allowances') ? ' active' : ''),
                        'show' => $user->can('manage_allowances') ? 1 : 0
                    ],
                    [
                        'id' => 'deductions',
                        'label' => get_label('deductions', 'Deductions'),
                        'url' => url('deductions'),
                        'class' => 'menu-item' . (Request::is('deductions') ? ' active' : ''),
                        'show' => $user->can('manage_deductions') ? 1 : 0
                    ],
                ],
            ],
            [
                'id' => 'finance',
                'label' => get_label('finance', 'Finance'),
                'url' => 'javascript:void(0)',
                'icon' => 'bx bx-box text-success',
                'class' => 'menu-item' . (Request::is('estimates-invoices') || Request::is('estimates-invoices/*') || Request::is('taxes') || Request::is('payment-methods') || Request::is('payments') || Request::is('units') || Request::is('items') || Request::is('expenses') || Request::is('expenses/*') ? ' active open' : ''),
                'show' => ($user->can('manage_estimates_invoices') || $user->can('manage_expenses') || $user->can('manage_payment_methods') ||
                    $user->can('manage_expense_types') || $user->can('manage_payments') || $user->can('manage_taxes') ||
                    $user->can('manage_units') || $user->can('manage_items')) ? 1 : 0,
                'submenus' => [
                    [
                        'id' => 'expenses',
                        'label' => get_label('expenses', 'Expenses'),
                        'url' => url('expenses'),
                        'class' => 'menu-item' . (Request::is('expenses') ? ' active' : ''),
                        'show' => $user->can('manage_expenses') ? 1 : 0
                    ],
                    [
                        'id' => 'expense_types',
                        'label' => get_label('expense_types', 'Expense types'),
                        'url' => url('expenses/expense-types'),
                        'class' => 'menu-item' . (Request::is('expenses/expense-types') ? ' active' : ''),
                        'show' => $user->can('manage_expense_types') ? 1 : 0
                    ],
                    [
                        'id' => 'estimates_invoices',
                        'label' => get_label('estimates_invoices', 'Estimates/Invoices'),
                        'url' => url('estimates-invoices'),
                        'class' => 'menu-item' . (Request::is('estimates-invoices') || Request::is('estimates-invoices/*') ? ' active' : ''),
                        'show' => $user->can('manage_estimates_invoices') ? 1 : 0
                    ],
                    [
                        'id' => 'payments',
                        'label' => get_label('payments', 'Payments'),
                        'url' => url('payments'),
                        'class' => 'menu-item' . (Request::is('payments') ? ' active' : ''),
                        'show' => $user->can('manage_payments') ? 1 : 0
                    ],
                    [
                        'id' => 'payment_methods',
                        'label' => get_label('payment_methods', 'Payment methods'),
                        'url' => url('payment-methods'),
                        'class' => 'menu-item' . (Request::is('payment-methods') ? ' active' : ''),
                        'show' => $user->can('manage_payment_methods') ? 1 : 0
                    ],
                    [
                        'id' => 'taxes',
                        'label' => get_label('taxes', 'Taxes'),
                        'url' => url('taxes'),
                        'class' => 'menu-item' . (Request::is('taxes') ? ' active' : ''),
                        'show' => $user->can('manage_taxes') ? 1 : 0
                    ],
                    [
                        'id' => 'units',
                        'label' => get_label('units', 'Units'),
                        'url' => url('units'),
                        'class' => 'menu-item' . (Request::is('units') ? ' active' : ''),
                        'show' => $user->can('manage_units') ? 1 : 0
                    ],
                    [
                        'id' => 'items',
                        'label' => get_label('items', 'Items'),
                        'url' => url('items'),
                        'class' => 'menu-item' . (Request::is('items') ? ' active' : ''),
                        'show' => $user->can('manage_items') ? 1 : 0
                    ],
                ],
            ],
            [
                'id' => 'reports',
                'label' => get_label('reports', 'Reports'),
                'url' => 'javascript:void(0)',
                'icon' => 'bx bx-file text-dark',
                'class' => 'menu-item' . (Request::is('reports') || Request::is('reports/*') ? ' active open' : ''),
                'show' => $user->hasRole('admin') || Auth::guard('web')->check() || checkPermission('manage_projects') || checkPermission('manage_tasks') || checkPermission('manage_estimates_invoices') ? 1 : 0,
                'submenus' => [
                    [
                        'id' => 'projects_report',
                        'label' => get_label('projects', 'Projects'),
                        'url' => route('reports.projects'),
                        'class' => 'menu-item' . (Request::is('reports/projects') ? ' active' : ''),
                        'show' => checkPermission('manage_projects') ? 1 : 0,
                    ],
                    [
                        'id' => 'tasks_report',
                        'label' => get_label('tasks', 'Tasks'),
                        'url' => route('reports.tasks'),
                        'class' => 'menu-item' . (Request::is('reports/tasks') ? ' active' : ''),
                        'show' => checkPermission('manage_tasks') ? 1 : 0,
                    ],
                    [
                        'id' => 'estimates_invoices_report',
                        'label' => get_label('estimates_invoices', 'Estimates/Invoices'),
                        'url' => route('reports.invoices-report'),
                        'class' => 'menu-item' . (Request::is('reports/estimates-invoices') ? ' active' : ''),
                        'show' => checkPermission('manage_estimates_invoices') ? 1 : 0,
                    ],
                    [
                        'id' => 'income_vs_expense',
                        'label' => get_label('income_vs_expense', 'Income vs Expense'),
                        'url' => route('reports.income-vs-expense'),
                        'class' => 'menu-item' . (Request::is('reports/income-vs-expense') ? ' active' : ''),
                        'show' => $user->hasRole('admin') ? 1 : 0,
                    ],
                    [
                        'id' => 'leaves',
                        'label' => get_label('leaves', 'Leaves'),
                        'url' => route('reports.leaves'),
                        'class' => 'menu-item' . (Request::is('reports/leaves') ? ' active' : ''),
                        'show' => Auth::guard('web')->check() ? 1 : 0,
                    ]
                ],
            ],
            [
                'id' => 'notes',
                'label' => get_label('notes', 'Notes'),
                'url' => url('notes'),
                'icon' => 'bx bx-notepad text-primary',
                'class' => 'menu-item' . (Request::is('notes') || Request::is('notes/*') ? ' active' : '')
            ],
            [
                'id' => 'leave_requests',
                'label' => get_label('leave_requests', 'Leave requests'),
                'url' => url('leave-requests'),
                'icon' => 'bx bx-right-arrow-alt text-danger',
                'class' => 'menu-item' . (Request::is('leave-requests') || Request::is('leave-requests/*') ? ' active' : ''),
                'badge' => ($pendingLeaveRequestsCount > 0) ? '<span class="flex-shrink-0 badge badge-center bg-danger w-px-20 h-px-20">' . $pendingLeaveRequestsCount . '</span>' : '',
                'show' => Auth::guard('web')->check() ? 1 : 0
            ],
            [
                'id' => 'activity_log',
                'label' => get_label('activity_log', 'Activity log'),
                'url' => url('activity-log'),
                'icon' => 'bx bx-line-chart text-warning',
                'class' => 'menu-item' . (Request::is('activity-log') || Request::is('activity-log/*') ? ' active' : ''),
                'show' => $user->can('manage_activity_log') ? 1 : 0
            ],
            [
                'id' => 'settings',
                'label' => get_label('settings', 'Settings'),
                'icon' => 'bx bx-box text-success',
                'class' => 'menu-item' . (Request::is('settings') || Request::is('roles/*') || Request::is('settings/*') ? ' active open' : ''),
                'show' => $user->hasRole('admin') ? 1 : 0,
                'submenus' => [
                    [
                        'id' => 'general',
                        'label' => get_label('general', 'General'),
                        'url' => url('settings/general'),
                        'class' => 'menu-item' . (Request::is('settings/general') ? ' active' : ''),
                    ],
                    [
                        'id' => 'company',
                        'label' => get_label('company_info', 'Company Information'),
                        'url' => url('settings/company-info'),
                        'class' => 'menu-item' . (Request::is('settings/company-info') ? ' active' : ''),
                    ],
                    [
                        'id' => 'security',
                        'label' => get_label('security', 'Security'),
                        'url' => url('settings/security'),
                        'class' => 'menu-item' . (Request::is('settings/security') ? ' active' : ''),
                    ],
                    [
                        'id' => 'permissions',
                        'label' => get_label('permissions', 'Permissions'),
                        'url' => url('settings/permission'),
                        'class' => 'menu-item' . (Request::is('settings/permission') || Request::is('roles/*') ? ' active' : ''),
                    ],
                    [
                        'id' => 'languages',
                        'label' => get_label('languages', 'Languages'),
                        'url' => url('settings/languages'),
                        'class' => 'menu-item' . (Request::is('settings/languages') || Request::is('settings/languages/create') ? ' active' : ''),
                    ],
                    [
                        'id' => 'email',
                        'label' => get_label('email', 'Email'),
                        'url' => url('settings/email'),
                        'class' => 'menu-item' . (Request::is('settings/email') ? ' active' : ''),
                    ],
                    [
                        'id' => 'sms_gateway',
                        'label' => get_label('messaging_and_integrations', 'Messaging & Integrations'),
                        'url' => url('settings/sms-gateway'),
                        'class' => 'menu-item' . (Request::is('settings/sms-gateway') ? ' active' : ''),
                    ],
                    [
                        'id' => 'pusher',
                        'label' => get_label('pusher', 'Pusher'),
                        'url' => url('settings/pusher'),
                        'class' => 'menu-item' . (Request::is('settings/pusher') ? ' active' : ''),
                    ],
                    [
                        'id' => 'media_storage',
                        'label' => get_label('media_storage', 'Media storage'),
                        'url' => url('settings/media-storage'),
                        'class' => 'menu-item' . (Request::is('settings/media-storage') ? ' active' : ''),
                    ],
                    [
                        'id' => 'notification_templates',
                        'label' => get_label('notification_templates', 'Notification Templates'),
                        'url' => url('settings/templates'),
                        'class' => 'menu-item' . (Request::is('settings/templates') ? ' active' : ''),
                    ],
                    [
                        'id' => 'privacy_policy',
                        'label' => get_label('terms_privacy_about', 'Terms, Privacy & About'),
                        'url' => url('settings/terms-privacy-about'),
                        'class' => 'menu-item' . (Request::is('settings/terms-privacy-about') ? ' active' : ''),
                    ],
                    [
                        'id' => 'system_updater',
                        'label' => get_label('system_updater', 'System updater'),
                        'url' => url('settings/system-updater'),
                        'class' => 'menu-item' . (Request::is('settings/system-updater') ? ' active' : ''),
                    ]
                ]
            ]
        ];
    }
}

if (!function_exists('getAllPermissions')) {
    /**
     * Get an array of all defined permissions.
     *
     * @return array
     */
    function getAllPermissions()
    {
        return [
            // Activity Log
            'manage_activity_log',
            'delete_activity_log',
            // Allowances
            'create_allowances',
            'manage_allowances',
            'edit_allowances',
            'delete_allowances',
            // Clients
            'create_clients',
            'manage_clients',
            'edit_clients',
            'delete_clients',
            // Contract Types
            'create_contract_types',
            'manage_contract_types',
            'edit_contract_types',
            'delete_contract_types',
            // Contracts
            'create_contracts',
            'manage_contracts',
            'edit_contracts',
            'delete_contracts',
            // Deductions
            'create_deductions',
            'manage_deductions',
            'edit_deductions',
            'delete_deductions',
            // Estimates/Invoices
            'create_estimates_invoices',
            'manage_estimates_invoices',
            'edit_estimates_invoices',
            'delete_estimates_invoices',
            // Expense Types
            'create_expense_types',
            'manage_expense_types',
            'edit_expense_types',
            'delete_expense_types',
            // Expenses
            'create_expenses',
            'manage_expenses',
            'edit_expenses',
            'delete_expenses',
            // Items
            'create_items',
            'manage_items',
            'edit_items',
            'delete_items',
            // Media
            'create_media',
            'manage_media',
            'delete_media',
            // Meetings
            'create_meetings',
            'manage_meetings',
            'edit_meetings',
            'delete_meetings',
            // Milestones
            'create_milestones',
            'manage_milestones',
            'edit_milestones',
            'delete_milestones',
            // Payment Methods
            'create_payment_methods',
            'manage_payment_methods',
            'edit_payment_methods',
            'delete_payment_methods',
            // Payments
            'create_payments',
            'manage_payments',
            'edit_payments',
            'delete_payments',
            // Payslips
            'create_payslips',
            'manage_payslips',
            'edit_payslips',
            'delete_payslips',
            // Priorities
            'create_priorities',
            'manage_priorities',
            'edit_priorities',
            'delete_priorities',
            // Projects
            'create_projects',
            'manage_projects',
            'edit_projects',
            'delete_projects',
            // Statuses
            'create_statuses',
            'manage_statuses',
            'edit_statuses',
            'delete_statuses',
            // System Notifications
            'manage_system_notifications',
            'delete_system_notifications',
            // Tags
            'create_tags',
            'manage_tags',
            'edit_tags',
            'delete_tags',
            // Tasks
            'create_tasks',
            'manage_tasks',
            'edit_tasks',
            'delete_tasks',
            // Taxes
            'create_taxes',
            'manage_taxes',
            'edit_taxes',
            'delete_taxes',
            // Timesheet
            'create_timesheet',
            'manage_timesheet',
            'delete_timesheet',
            // Units
            'create_units',
            'manage_units',
            'edit_units',
            'delete_units',
            // Users
            'create_users',
            'manage_users',
            'edit_users',
            'delete_users',
            // Workspaces
            'create_workspaces',
            'manage_workspaces',
            'edit_workspaces',
            'delete_workspaces',
        ];
    }
}

/**
 * Replace plain @mentions in the content with HTML links to the user's profile.
 *
 * @param string $content
 * @return string
 */
if (!function_exists('replaceUserMentionsWithLinks')) {
    function replaceUserMentionsWithLinks($content)
    {
        // Find all @mentions in the content
        preg_match_all('/@([A-Za-z0-9]+\s[A-Za-z0-9]+)/', $content, $matches);
        // Initialize modified content
        $modifiedContent = $content;
        $mentionedUserIds = [];
        $mentionedClientIds = [];
        $workspaceId = getWorkspaceId();
        // Check if any matches were found
        if (!empty($matches[1])) {
            foreach ($matches[1] as $fullName) {
                // Try to find the user by their full name (first_name + last_name)
                $user = User::where(DB::raw("CONCAT(first_name, ' ', last_name)"), '=', $fullName)
                    ->whereHas('workspaces', function ($query) use ($workspaceId) {
                        $query->where('workspaces.id', $workspaceId);
                    })
                    ->first();
                if ($user) {
                    // Add user ID to the list of mentioned user IDs
                    $mentionedUserIds[] = $user->id;

                    // Check permission for managing users
                    // if (checkPermission('manage_users')) {
                    // Create a profile link for the mentioned user
                    $mentionLink = '<a href="' . route('users.profile', ['id' => $user->id]) . '">@' . $fullName . '</a>';
                    // } else {
                    //     // Non-clickable text
                    //     $mentionLink = '@' . $fullName;
                    // }

                    // Replace the plain @mention with the linked or non-clickable version
                    $modifiedContent = str_replace('@' . $fullName, $mentionLink, $modifiedContent);
                } else {
                    // If user not found, check if it's a client
                    $client = Client::where(DB::raw("CONCAT(clients.first_name, ' ', clients.last_name)"), '=', $fullName)
                        ->whereHas('workspaces', function ($query) use ($workspaceId) {
                            $query->where('workspaces.id', $workspaceId);
                        })
                        ->first();

                    if ($client) {
                        // Add client ID to the list of mentioned client IDs
                        $mentionedClientIds[] = $client->id;

                        // Check permission for managing clients
                        // if (checkPermission('manage_clients')) {
                        // Create a profile link for the mentioned client
                        $mentionLink = '<a href="' . route('clients.profile', ['id' => $client->id]) . '">@' . $fullName . '</a>';
                        // } else {
                        //     // Non-clickable text
                        //     $mentionLink = '@' . $fullName;
                        // }

                        // Replace the plain @mention with the linked or non-clickable version
                        $modifiedContent = str_replace('@' . $fullName, $mentionLink, $modifiedContent);
                    }
                }
            }
        }

        // Return the modified content along with both mentioned user and client IDs
        return [$modifiedContent, $mentionedUserIds, $mentionedClientIds];
    }
}


if (!function_exists('sendMentionNotification')) {
    function sendMentionNotification($comment, $mentionedUserIds, $workspaceId, $currentUserId, $mentionedClientIds = [])
    {
        // Ensure mentioned user IDs are unique
        $mentionedUserIds = array_unique($mentionedUserIds);
        // Ensure mentioned client IDs are unique
        $mentionedClientIds = array_unique($mentionedClientIds);

        // Initialize module variables
        $moduleType = '';
        $url = '';
        switch ($comment->commentable_type) {
            case 'App\Models\Task':
                $moduleType = 'task';
                $url = route('tasks.info', ['id' => $comment->commentable_id]) . '#navs-top-discussions';
                break;
            case 'App\Models\Project':
                $moduleType = 'project';
                $url = route('projects.info', ['id' => $comment->commentable_id]) . '#navs-top-discussions';
                break;
            default:
                $moduleType = '';
                break;
        }

        $module = [];
        if ($moduleType) {
            switch ($moduleType) {
                case 'task':
                    $module = Task::find($comment->commentable_id);
                    break;
                case 'project':
                    $module = Project::find($comment->commentable_id);
                    break;
                default:
                    break;
            }
        }

        // Get the authenticated user who is sending the notification
        $authUser = getAuthenticatedUser();

        // Create the notification
        $notification = Notification::create([
            'workspace_id' => $workspaceId,
            'from_id' => 'u_' . $currentUserId,
            'type' => $moduleType . '_comment_mention',
            'type_id' => $module->id,
            'action' => 'mentioned',
            'title' => 'You were mentioned in a comment',
            'message' => $authUser->first_name . ' ' . $authUser->last_name . ' mentioned you in ' . ucfirst($moduleType) . ' <a href="' . $url . '">' . $module->title . '</a>.',
        ]);

        // Attach mentioned users to the notification
        foreach ($mentionedUserIds as $userId) {
            $notification->users()->attach($userId);
        }

        // Attach mentioned clients to the notification
        foreach ($mentionedClientIds as $clientId) {
            $client = Client::find($clientId);
            if ($client) {
                $notification->clients()->attach($clientId);
            }
        }
    }
}


if (!function_exists('get_file_settings')) {
    function get_file_settings()
    {
        $general_settings = get_settings('general_settings');

        // Remove spaces from allowed file types
        $allowed_file_types = isset($general_settings['allowed_file_types'])
            ? str_replace(' ', '', $general_settings['allowed_file_types'])
            : '.png,.jpg,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar,.txt';

        return [
            'allowed_file_types' => $allowed_file_types,
            'max_files_allowed' => isset($general_settings['max_files_allowed'])
                ? $general_settings['max_files_allowed']
                : 10,
        ];
    }
}
if (!function_exists('formatUserHtml')) {
    function formatUserHtml($user)
    {
        if (!$user) {
            return "-";
        }

        // Get the authenticated user
        $authenticatedUser = getAuthenticatedUser();

        // Get the guard name (web or client)
        $guardName = getGuardName();

        // Check if the authenticated user is the same as the user being displayed
        if (($guardName === 'web' && $authenticatedUser->id === $user->id) ||
            ($guardName === 'client' && $authenticatedUser->id === $user->id)
        ) {
            // Don't show the "Make Call" option if it's the logged-in user
            $makeCallIcon = '';
        } else {
            // Check if the phone number or both phone and country code exist
            $makeCallIcon = '';
            if (!empty($user->phone) || (!empty($user->phone) && !empty($user->country_code))) {
                $makeCallLink = 'tel:' . ($user->country_code ? $user->country_code . $user->phone : $user->phone);
                $makeCallIcon = '<a href="' . $makeCallLink . '" class="text-decoration-none" title="' . get_label('make_call', 'Make Call') . '">
                                     <i class="bx bx-phone-call text-primary"></i>
                                   </a>';
            }
        }

        // If the user has 'manage_users' permission, return the full HTML with links
        $profileLink = route('users.profile', ['id' => $user->id]);
        $photoUrl = $user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg');

        // Create the Send Mail link
        $sendMailLink = 'mailto:' . $user->email;
        $sendMailIcon = '<a href="' . $sendMailLink . '" class="text-decoration-none" title="' . get_label('send_mail', 'Send Mail') . '">
                            <i class="bx bx-envelope text-primary"></i>
                          </a>';

        return "<div class='d-flex justify-content-start align-items-center user-name'>
                    <div class='avatar-wrapper me-3'>
                        <div class='avatar avatar-sm pull-up'>
                            <a href='{$profileLink}'>
                                <img src='{$photoUrl}' alt='Photo' class='rounded-circle'>
                            </a>
                        </div>
                    </div>
                    <div class='d-flex flex-column'>
                        <span class='fw-semibold'>{$user->first_name} {$user->last_name} {$makeCallIcon}</span>
                        <small class='text-muted'>{$user->email} {$sendMailIcon}</small>
                    </div>
                </div>";
    }
}


if (!function_exists('formatClientHtml')) {
    function formatClientHtml($client)
    {
        if (!$client) {
            return "-";
        }

        // Get the authenticated user
        $authenticatedUser = getAuthenticatedUser();

        // Get the guard name (web or client)
        $guardName = getGuardName();

        // Check if the authenticated user is the same as the client being displayed
        if (($guardName === 'web' && $authenticatedUser->id === $client->id) ||
            ($guardName === 'client' && $authenticatedUser->id === $client->id)
        ) {
            // Don't show the "Make Call" option if it's the logged-in client
            $makeCallIcon = '';
        } else {
            // Check if the phone number or both phone and country code exist
            $makeCallIcon = '';
            if (!empty($client->phone) || (!empty($client->phone) && !empty($client->country_code))) {
                $makeCallLink = 'tel:' . ($client->country_code ? $client->country_code . $client->phone : $client->phone);
                $makeCallIcon = '<a href="' . $makeCallLink . '" class="text-decoration-none" title="' . get_label('make_call', 'Make Call') . '">
                                     <i class="bx bx-phone-call text-primary"></i>
                                   </a>';
            }
        }

        // If the user has 'manage_clients' permission, return the full HTML with links
        $profileLink = route('clients.profile', ['id' => $client->id]);
        $photoUrl = $client->photo ? asset('storage/' . $client->photo) : asset('storage/photos/no-image.jpg');

        // Create the Send Mail link
        $sendMailLink = 'mailto:' . $client->email;
        $sendMailIcon = '<a href="' . $sendMailLink . '" class="text-decoration-none" title="' . get_label('send_mail', 'Send Mail') . '">
                            <i class="bx bx-envelope text-primary"></i>
                          </a>';

        return "<div class='d-flex justify-content-start align-items-center user-name'>
                    <div class='avatar-wrapper me-3'>
                        <div class='avatar avatar-sm pull-up'>
                            <a href='{$profileLink}'>
                                <img src='{$photoUrl}' alt='Photo' class='rounded-circle'>
                            </a>
                        </div>
                    </div>
                    <div class='d-flex flex-column'>
                        <span class='fw-semibold'>{$client->first_name} {$client->last_name} {$makeCallIcon}</span>
                        <small class='text-muted'>{$client->email} {$sendMailIcon}</small>
                    </div>
                </div>";
    }
}

if (!function_exists('getFavoriteStatus')) {
    function getFavoriteStatus($id, $model = \App\Models\Project::class)
    {
        // Ensure the model is valid and exists
        if (!class_exists($model) || !$model::find($id)) {
            return false; // Return false if the model class doesn't exist or the specific entity doesn't exist
        }

        // Get the authenticated user (either a User or a Client)
        $authUser = getAuthenticatedUser();

        // Get the favorite based on the provided model (e.g., Project, Task, etc.)
        $isFavorited = $authUser->favorites()
            ->where('favoritable_type', $model)
            ->where('favoritable_id', $id)
            ->exists();

        return (int) $isFavorited;
    }
}

if (!function_exists('getPinnedStatus')) {
    function getPinnedStatus($id, $model = \App\Models\Project::class)
    {
        // Ensure the model is valid and exists
        if (!class_exists($model) || !$model::find($id)) {
            return false; // Return false if the model class doesn't exist or the specific entity doesn't exist
        }

        // Get the authenticated user (either a User or a Client)
        $authUser = getAuthenticatedUser();

        // Get the pinned status based on the provided model (e.g., Project, Task, etc.)
        $isPinned = $authUser->pinned()
            ->where('pinnable_type', $model)
            ->where('pinnable_id', $id)
            ->exists();

        return (int) $isPinned;
    }
}

function logActivity($type, $typeId, $title, $operation = 'created', $parentId = null, $parentType = null)
{
    // Retrieve necessary values once
    $authenticatedUser = getAuthenticatedUser();
    $workspaceId = getWorkspaceId();
    $guardName = getGuardName();

    // Construct the actor details
    $actorName = $authenticatedUser->first_name . ' ' . $authenticatedUser->last_name;
    $actorId = $authenticatedUser->id;
    $actorType = $guardName == 'web' ? 'user' : 'client';

    // Construct the activity message
    $message = trim($actorName) . ' ' . trim($operation) . ' ' . trim($type) . ' ' . trim($title);

    // Prepare the log data
    $logData = [
        'workspace_id' => $workspaceId,
        'actor_id' => $actorId,
        'actor_type' => $actorType,
        'type_id' => $typeId,
        'type' => $type,
        'activity' => $operation,
        'message' => $message,
    ];

    // Add parent information if available
    if ($parentId) {
        $logData['parent_type_id'] = $parentId;
    }

    if ($parentType) {
        $logData['parent_type'] = $parentType;
    }

    // Create the activity log entry
    ActivityLog::create($logData);
}

// Function for sending reminders for tasks or birthday or work anniversary
if (!function_exists('sendReminderNotification')) {


    /**
     * Sends reminder notifications to the given recipients based on the given data.
     *
     * @param array $data The reminder data, must contain the type of reminder.
     * @param array $recipients The recipients of the notification, must contain the user or client IDs.
     * @return void
     */
    function sendReminderNotification($data, $recipients)
    {
        Log::info('Sending reminder notification to: ' . json_encode($recipients, JSON_PRETTY_PRINT) . 'With data: ' . json_encode($data, JSON_PRETTY_PRINT));
        if (empty($recipients)) {
            return;
        }

        // Define notification types
        $notificationTypes = ['task_reminder', 'project_reminder', 'leave_request_reminder', 'recurring_task'];
        Log::debug('Checking notification type', ['type' => $data['type'], 'valid_types' => $notificationTypes]);
        // Get notification template based on the type
        $template = getNotificationTemplate($data['type'], 'system');
        if (!$template || $template->status !== 0) {
            $notification = createNotification($data);
        }

        // Process each recipient
        foreach (array_unique($recipients) as $recipient) {
            Log::info('Processing recipient', ['recipient_id' => $recipient]);
            $recipientModel = getRecipientModel($recipient);
            if ($recipientModel) {
                Log::debug('Found recipient model', [
                    'recipient_type' => get_class($recipientModel),
                    'recipient_id' => $recipientModel->id
                ]);
                handleRecipientNotification($recipientModel, $notification, $template, $data, $notificationTypes);
            }
        }
    }

    /**
     * Creates a new notification from the given data.
     *
     * @param array $data An associative array containing the notification details,
     *                    including the 'type', 'type_id', and 'action'.
     * @return \App\Models\Notification The newly created notification instance.
     */
    function createNotification($data)
    {
        return Notification::create(
            [
                'workspace_id' => $data['workspace_id'],
                'from_id' => $data['from_id'],
                'type' => $data['type'],
                'type_id' => $data['type_id'],
                'action' => $data['action'],
                'title' => getTitle($data),
                'message' => get_message($data, null, 'system'),
            ]
        );
    }

    /**
     * Given a recipient identifier, returns the corresponding model instance.
     *
     * A recipient identifier is a string that starts with either 'u_' for a user or
     * 'c_' for a client, followed by the numeric identifier of the user or client.
     * For example, 'u_1' refers to a user with identifier 1, and 'c_2' refers to a
     * client with identifier 2.
     *
     * @param string $recipient The recipient identifier.
     * @return \App\Models\User|\App\Models\Client|null The recipient model instance, or null if not found.
     */
    function getRecipientModel($recipient)
    {
        $recipientId = substr($recipient, 2);
        if (substr($recipient, 0, 2) === 'u_') {
            return User::find($recipientId);
        } elseif (substr($recipient, 0, 2) === 'c_') {
            return Client::find($recipientId);
        }
        return null;
    }

    /**
     * Handles a notification for a recipient based on their notification preferences.
     *
     * This function takes a recipient model, a notification, a template, data about the
     * notification, and an array of notification types. It checks the recipient's
     * preferences for the notification types and sends notifications accordingly.
     * If the notification is already attached to the recipient, it will not be attached again.
     *
     * @param mixed $recipientModel The recipient model to send the notification to.
     * @param mixed $notification The notification to be sent.
     * @param mixed $template The template to use for the notification.
     * @param array $data An associative array containing details about the notification.
     * @param array $notificationTypes An array of notification types to check for.
     */
    function handleRecipientNotification($recipientModel, $notification, $template, $data, $notificationTypes)
    {
        Log::info('Handling recipient notification', [
            'recipient_id' => $recipientModel->id,
            'notification_type' => $data['type']
        ]);
        $enabledNotifications = getUserPreferences('notification_preference', 'enabled_notifications', 'u_' . $recipientModel->id);

        // Attach the notification to the recipient
        attachNotificationIfNeeded($recipientModel, $notification, $template, $enabledNotifications, $data);
        Log::info('Starting notification delivery process', [
            'recipient_id' => $recipientModel->id,
            'notification_types' => $notificationTypes,
            'enabled_notifications' => $enabledNotifications
        ]);
        // Send notifications based on preferences
        sendEmailIfEnabled($recipientModel, $enabledNotifications, $data, $notificationTypes);
        sendSMSIfEnabled($recipientModel, $enabledNotifications, $data, $notificationTypes);
        sendWhatsAppIfEnabled($recipientModel, $enabledNotifications, $data, $notificationTypes);
        sendSlackIfEnabled($recipientModel, $enabledNotifications, $data, $notificationTypes);
    }

    /**
     * Attach a notification to the recipient if the recipient has enabled system notifications for the given type
     * of notification and the notification template is not found or is not enabled.
     *
     * @param mixed $recipientModel The recipient model (User or Client) to which the notification should be attached.
     * @param Notification $notification The notification to be attached to the recipient.
     * @param Template $template The notification template to be checked for enabled status.
     * @param array $enabledNotifications An array of enabled notification types for the recipient.
     * @param array $data The data for the notification, including the type of notification.
     */
    function attachNotificationIfNeeded($recipientModel, $notification, $template, $enabledNotifications, $data)
    {
        Log::debug('Checking if notification needs to be attached', [
            'recipient_id' => $recipientModel->id,
            'notification_id' => $notification ? $notification->id : null,
            'template_exists' => (bool)$template,
            'template_status' => $template ? $template->status : null
        ]);
        if (!$template || $template->status !== 0) {
            if (is_array($enabledNotifications) && (empty($enabledNotifications) || in_array('system_' . $data['type'], $enabledNotifications))) {
                $recipientModel->notifications()->attach($notification->id);
            }
        }
    }

    /**
     * Send an email notification if the recipient has enabled email notifications for the given type of notification.
     *
     * @param mixed $recipientModel The recipient model (User or Client) to which the notification should be sent.
     * @param array $enabledNotifications An array of enabled notification types for the recipient.
     * @param array $data The notification data.
     * @param array $notificationTypes An array of notification types for which email notifications should be sent.
     * @return void
     */
    function sendEmailIfEnabled($recipientModel, $enabledNotifications, $data, $notificationTypes)
    {

        Log::debug('Checking email notification preferences', [
            'recipient_id' => $recipientModel->id,
            'notification_type' => $data['type'],
            'is_type_valid' => in_array($data['type'], $notificationTypes),
            'is_enabled' => isNotificationEnabled($enabledNotifications, 'email_' . $data['type'])
        ]);
        if (in_array($data['type'], $notificationTypes) && isNotificationEnabled($enabledNotifications, 'email_' . $data['type'])) {
            try {
                sendEmailNotification($recipientModel, $data);
            } catch (\Exception $e) {
                Log::error('Email Notification Error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Send SMS notification if enabled.
     *
     * This function sends an SMS notification to the given recipient if the
     * notification type is enabled in the recipient's preferences.
     *
     * @param  \App\Models\User|\App\Models\Client  $recipientModel
     * @param  array  $enabledNotifications
     * @param  array  $data
     * @param  array  $notificationTypes
     * @return void
     */
    function sendSMSIfEnabled($recipientModel, $enabledNotifications, $data, $notificationTypes)
    {
        Log::debug('Checking SMS notification preferences', [
            'recipient_id' => $recipientModel->id,
            'notification_type' => $data['type'],
            'is_type_valid' => in_array($data['type'], $notificationTypes),
            'is_enabled' => isNotificationEnabled($enabledNotifications, 'sms_' . $data['type'])
        ]);
        if (in_array($data['type'], $notificationTypes) && isNotificationEnabled($enabledNotifications, 'sms_' . $data['type'])) {
            try {
                sendSMSNotification($data, $recipientModel);
            } catch (\Exception $e) {
                Log::error('SMS Notification Error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Send WhatsApp notification if enabled.
     *
     * This function sends a WhatsApp notification to the given recipient if the
     * notification type is enabled in the recipient's preferences.
     *
     * @param  \App\Models\User|\App\Models\Client  $recipientModel
     * @param  array  $enabledNotifications
     * @param  array  $data
     * @param  array  $notificationTypes
     * @return void
     */
    function sendWhatsAppIfEnabled($recipientModel, $enabledNotifications, $data, $notificationTypes)
    {
        Log::debug('Checking WhatsApp notification preferences', [
            'recipient_id' => $recipientModel->id,
            'notification_type' => $data['type'],
            'is_type_valid' => in_array($data['type'], $notificationTypes),
            'is_enabled' => isNotificationEnabled($enabledNotifications, 'whatsapp_' . $data['type'])
        ]);
        if (in_array($data['type'], $notificationTypes) && isNotificationEnabled($enabledNotifications, 'whatsapp_' . $data['type'])) {
            try {
                sendWhatsAppNotification($recipientModel, $data );
            } catch (\Exception $e) {
                Log::error('WhatsApp Notification Error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Send a Slack notification if the recipient has enabled Slack notifications for the given type.
     *
     * @param User|Client $recipientModel The recipient model to send the notification to.
     * @param array $enabledNotifications An array of enabled notification types.
     * @param array $data An associative array containing the notification details,
     *                    including the 'type', 'type_id', and 'action'.
     * @param array $notificationTypes An array of notification types.
     */
    function sendSlackIfEnabled($recipientModel, $enabledNotifications, $data, $notificationTypes)
    {
        Log::debug('Checking Slack notification preferences', [
            'recipient_id' => $recipientModel->id,
            'notification_type' => $data['type'],
            'is_type_valid' => in_array($data['type'], $notificationTypes),
            'is_enabled' => isNotificationEnabled($enabledNotifications, 'slack_' . $data['type'])
        ]);
        if (in_array($data['type'], $notificationTypes) && isNotificationEnabled($enabledNotifications, 'slack_' . $data['type'])) {
            try {
                sendSlackNotification($recipientModel,$data );
            } catch (\Exception $e) {
                Log::error('Slack Notification Error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Check if a notification type is enabled for a user/client.
     *
     * @param array $enabledNotifications An array of enabled notification types.
     * @param string $type The notification type to check.
     * @return bool True if the notification type is enabled.
     */
    function isNotificationEnabled($enabledNotifications, $type)
    {

        return is_array($enabledNotifications) && (empty($enabledNotifications) || in_array($type, $enabledNotifications));
    }
}
if (!function_exists('getDefaultStatus')) {
    /**
     * Get the default status ID based on the given status name.
     *
     * @param string $statusName
     * @return object|null
     */
    function getDefaultStatus(string $statusName): ?object
    {
        // Fetch the default status using the Statuses model
        $status = Status::where('title', $statusName)
            ->where('is_default', 1) // Assuming there's an 'is_default' column
            ->first();

        // Return the ID if found, or null
        return $status ? $status : null;
    }
}