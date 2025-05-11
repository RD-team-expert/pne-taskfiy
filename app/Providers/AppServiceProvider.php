<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\Tag;
use App\Models\User;
use App\Models\Status;
use App\Models\Priority;
use App\Models\Setting;
use App\Models\Language;
use Illuminate\Support\Facades\View;
use App\Models\Client;
use Faker\Extension\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use App\Services\CustomPathGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PathGenerator::class, CustomPathGenerator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();

        View::composer('modals', function ($view) {
            $users = User::all();
            $clients = Client::all();
    
            // Pass users and clients data to the view
            $view->with('toSelectWorkspaceUsers', $users)->with('toSelectWorkspaceClients', $clients);
        });

        try {
            DB::connection()->getPdo();
            // The table exists in the database
            $languages = Language::all();
            $statuses = Status::all();
            $priorities = Priority::all();
            $tags = Tag::all();
            $general_settings = get_settings('general_settings');

            $general_settings['full_logo'] = !isset($general_settings['full_logo']) || empty($general_settings['full_logo']) ? 'storage/logos/default_full_logo.png' : 'storage/' . $general_settings['full_logo'];
            $general_settings['half_logo'] = !isset($general_settings['half_logo']) || empty($general_settings['half_logo']) ? 'storage/logos/default_half_logo.png' : 'storage/' . $general_settings['half_logo'];
            $general_settings['favicon'] = !isset($general_settings['favicon']) || empty($general_settings['favicon']) ? 'storage/logos/default_favicon.png' : 'storage/' . $general_settings['favicon'];

            $general_settings['company_title'] = $general_settings['company_title'] ?? 'Taskify';
            $general_settings['site_url'] = $general_settings['site_url'] ?? '';

            $general_settings['currency_symbol'] = $general_settings['currency_symbol'] ?? '₹';
            $general_settings['currency_full_form'] = $general_settings['currency_full_form'] ?? 'Indian Rupee';
            $general_settings['currency_code'] = $general_settings['currency_code'] ?? 'INR';
            $general_settings['currency_symbol_position'] = $general_settings['currency_symbol_position'] ?? 'before';
            $general_settings['currency_formate'] = $general_settings['currency_formate'] ?? 'comma_separated';
            $general_settings['decimal_points_in_currency'] = $general_settings['decimal_points_in_currency'] ?? '2';

            $general_settings['toast_time_out'] = $general_settings['toast_time_out'] ?? '5';
            $general_settings['toast_position'] = $general_settings['toast_position'] ?? 'toast-top-right';

            $general_settings['allowed_max_upload_size'] = $general_settings['allowed_max_upload_size'] ?? '512';
            $general_settings['max_files_allowed'] = $general_settings['max_files'] ?? '10';
            $general_settings['allowed_file_types'] = $general_settings['allowed_file_types'] ?? '.png,.jpg,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar,.txt';

            $pusher_settings = get_settings('pusher_settings');
            $pusher_settings['pusher_app_id'] = $pusher_settings['pusher_app_id'] ?? '';
            $pusher_settings['pusher_app_key'] = $pusher_settings['pusher_app_key'] ?? '';
            $pusher_settings['pusher_app_secret'] = $pusher_settings['pusher_app_secret'] ?? '';
            $pusher_settings['pusher_app_cluster'] = $pusher_settings['pusher_app_cluster'] ?? '';

            $email_settings = get_settings('email_settings');
            $email_settings['email'] =  $email_settings['email'] ?? '';
            $email_settings['password'] = $email_settings['password'] ?? '';
            $email_settings['smtp_host'] = $email_settings['smtp_host'] ?? '';
            $email_settings['smtp_port'] = $email_settings['smtp_port'] ?? '';
            $email_settings['email_content_type'] = $email_settings['email_content_type'] ?? '';
            $email_settings['smtp_encryption'] = $email_settings['smtp_encryption'] ?? '';

            $media_storage_settings = get_settings('media_storage_settings');
            $media_storage_settings['media_storage_type'] =  $media_storage_settings['media_storage_type'] ?? '';
            $media_storage_settings['s3_key'] =  $media_storage_settings['s3_key'] ?? '';
            $media_storage_settings['s3_secret'] =  $media_storage_settings['s3_secret'] ?? '';
            $media_storage_settings['s3_region'] =  $media_storage_settings['s3_region'] ?? '';
            $media_storage_settings['s3_bucket'] =  $media_storage_settings['s3_bucket'] ?? '';


            $date_format = $general_settings['date_format'] = $general_settings['date_format'] ?? 'DD-MM-YYYY|d-m-Y';
            $date_format = explode('|', $date_format);
            $js_date_format = $date_format[0];
            $php_date_format = $date_format[1];

            $this->app->singleton('php_date_format', function () use ($php_date_format) {
                return $php_date_format;
            });

            $company_info = get_settings('company_information');
            $company_info['companyEmail'] = $company_info['companyEmail'] ?? '';
            $company_info['companyPhone'] = $company_info['companyPhone'] ?? '';
            $company_info['companyAddress'] = $company_info['companyAddress'] ?? '';
            $company_info['companyCity'] = $company_info['companyCity'] ?? '';
            $company_info['companyState'] = $company_info['companyState'] ?? '';
            $company_info['companyCountry'] = $company_info['companyCountry'] ?? '';
            $company_info['companyZip'] = $company_info['companyZip'] ?? '';
            $company_info['companyWebsite'] = $company_info['companyWebsite'] ?? '';
            $company_info['companyVatNumber'] = $company_info['companyVatNumber'] ?? '';

            $data = ['general_settings' => $general_settings, 'email_settings' => $email_settings, 'pusher_settings' => $pusher_settings, 'media_storage_settings' => $media_storage_settings, 'company_info' => $company_info, 'languages' => $languages, 'js_date_format' => $js_date_format, 'php_date_format' => $php_date_format, 'statuses' => $statuses, 'tags' => $tags, 'priorities' => $priorities];
            view()->share($data);

            config()->set('app.timezone', $general_settings['timezone']);

            config()->set('chatify.name', $general_settings['company_title']);
            config()->set('chatify.pusher.key', $pusher_settings['pusher_app_key']);
            config()->set('chatify.pusher.secret', $pusher_settings['pusher_app_secret']);
            config()->set('chatify.pusher.app_id', $pusher_settings['pusher_app_id']);
            config()->set('chatify.pusher.options.cluster', $pusher_settings['pusher_app_cluster']);

            config()->set('mail.mailers.smtp.host', $email_settings['smtp_host']);
            config()->set('mail.mailers.smtp.port', $email_settings['smtp_port']);
            config()->set('mail.mailers.smtp.encryption', $email_settings['smtp_encryption']);
            config()->set('mail.mailers.smtp.username', $email_settings['email']);
            config()->set('mail.mailers.smtp.password', $email_settings['password']);

            config()->set('mail.from.name', $general_settings['company_title']);
            config()->set('mail.from.address', $email_settings['email']);


            config()->set('filesystems.disks.s3.key', $media_storage_settings['s3_key']);
            config()->set('filesystems.disks.s3.secret', $media_storage_settings['s3_secret']);
            config()->set('filesystems.disks.s3.region', $media_storage_settings['s3_region']);
            config()->set('filesystems.disks.s3.bucket', $media_storage_settings['s3_bucket']);

            config()->set('media-library.max_file_size', 1024 * 1024 * $general_settings['allowed_max_upload_size']);

        } catch (\Exception $e) {
        }
    }
}
