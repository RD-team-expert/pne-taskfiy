<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Client;
use App\Models\Notification as ModelNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use App\Notifications\BirthdayWishNotification;
use App\Notifications\WorkAnniversaryWishNotification;

class SendWishes extends Command
{
    protected $signature = 'send:wishes';
    protected $description = 'Send birthday and work anniversary wishes to users and clients';

    public function handle()
    {
        $today = Carbon::now()->format('m-d');

        // Retrieve global notification settings
        $globalSettings = ['email', 'sms', 'push', 'whatsapp', 'system', 'slack'];
        $notificationTypes = ['birthday_wish', 'work_anniversary_wish'];

        $globalNotifications = [];
        foreach ($notificationTypes as $type) {
            foreach ($globalSettings as $channel) {
                $globalNotifications[$type][$channel] = !getNotificationTemplate($type, $channel) ||
                    getNotificationTemplate($type, $channel)->status === 1;
            }
        }

        $currentYear = today()->year;

        // Handle Birthday Wishes for Users
        $birthdayUsers = User::where('status', 1)
            ->whereRaw("DATE_FORMAT(dob, '%m-%d') = ?", [$today])
            ->whereRaw("YEAR(dob) < ?", [$currentYear])
            ->get();
        $this->sendNotifications($birthdayUsers, 'birthday_wish', $globalNotifications);

        // Handle Work Anniversary Wishes for Users
        $workAnniversaryUsers = User::where('status', 1)
            ->whereRaw("DATE_FORMAT(doj, '%m-%d') = ?", [$today])
            ->whereRaw("YEAR(doj) < ?", [$currentYear])
            ->get();

        $this->sendNotifications($workAnniversaryUsers, 'work_anniversary_wish', $globalNotifications);

        // Handle Birthday Wishes for Clients
        $birthdayClients = Client::where('status', 1)
            ->whereRaw("DATE_FORMAT(dob, '%m-%d') = ?", [$today])
            ->whereRaw("YEAR(dob) < ?", [$currentYear])
            ->get();

        $this->sendNotifications($birthdayClients, 'birthday_wish', $globalNotifications);

        // Handle Work Anniversary Wishes for Clients
        $workAnniversaryClients = Client::where('status', 1)
            ->whereRaw("DATE_FORMAT(doj, '%m-%d') = ?", [$today])
            ->whereRaw("YEAR(doj) < ?", [$currentYear])
            ->get();

        $this->sendNotifications($workAnniversaryClients, 'work_anniversary_wish', $globalNotifications);

        $this->info('Birthday and work anniversary wishes sent successfully.');
    }

    private function sendNotifications($recipients, $type, $globalNotifications)
    {
        $currentYear = today()->year;
        foreach ($recipients as $recipient) {
            $recipientId = ($recipient instanceof User) ? 'u_' . $recipient->id : 'c_' . $recipient->id;
            $enabledNotifications = getUserPreferences('notification_preference', 'enabled_notifications', $recipientId);

            $notificationData = [
                'first_name' => $recipient->first_name,
                'last_name' => $recipient->last_name,
            ];
            if ($type === 'birthday_wish') {
                $birthdayDateYear = \Carbon\Carbon::parse($recipient->dob)->year;
                $yearDifference = $currentYear - $birthdayDateYear;
                $notificationData['birthday_count'] = $yearDifference;
                $notificationData['ordinal_suffix'] = getOrdinalSuffix($yearDifference);
                $notificationData['type'] = 'birthday_wish';
            } elseif ($type === 'work_anniversary_wish') {
                $joiningYear = \Carbon\Carbon::parse($recipient->doj)->year;
                $yearDifference = $currentYear - $joiningYear;
                $notificationData['work_anniversary_count'] = $yearDifference;
                $notificationData['ordinal_suffix'] = getOrdinalSuffix($yearDifference);
                $notificationData['type'] = 'work_anniversary_wish';
            }

            // Check if either system or push is enabled globally for the recipient
            $isSystemEnabled = $this->isNotificationEnabled($enabledNotifications, $type, 'system');
            $isPushEnabled = $this->isNotificationEnabled($enabledNotifications, $type, 'push');

            // Proceed only if either system or push is enabled globally for the recipient
            if ($globalNotifications[$type]['system'] || $globalNotifications[$type]['push']) {
                // Initialize title and message as empty
                $title = '';
                $message = '';

                // If system is enabled, get the title and message for the system notification
                if ($globalNotifications[$type]['system'] && $isSystemEnabled) {
                    $title = getTitle($notificationData, $recipient, 'system');  // Get title for system channel
                    $message = get_message($notificationData, $recipient, 'system');  // Get message for system channel
                }

                // If push is enabled, get the title and message for the push notification
                if ($globalNotifications[$type]['push'] && $isPushEnabled) {
                    // Only update title and message for push if they are different from system (if system is also enabled)
                    $pushTitle = getTitle($notificationData, $recipient, 'push');
                    $pushMessage = get_message($notificationData, $recipient, 'push');

                    // If system was set, avoid overriding title and message with empty data for push
                    if (empty($title) && empty($message)) {
                        $title = $pushTitle;
                        $message = $pushMessage;
                    }
                }

                // Only create and attach the notification if title and message are not empty
                if (!empty($title) && !empty($message)) {
                    // Create the notification once with the calculated title and message
                    $notification = ModelNotification::create([
                        'workspace_id' => getWorkspaceId(),
                        'type' => $notificationData['type'],  // Notification type
                        'title' => $title,
                        'message' => $message,
                    ]);

                    // Attach the notification to the recipient with the appropriate flags
                    $recipient->notifications()->attach($notification->id, [
                        'is_system' => $isSystemEnabled ? 1 : 0,
                        'is_push' => $isPushEnabled ? 1 : 0,
                    ]);
                }
            }
            foreach (['email', 'sms', 'push', 'whatsapp', 'slack'] as $channel) {
                if ($globalNotifications[$type][$channel] && $this->isNotificationEnabled($enabledNotifications, $type, $channel)) {
                    try {
                        $notificationClass = $type === 'birthday_wish'
                            ? BirthdayWishNotification::class
                            : WorkAnniversaryWishNotification::class;

                        Notification::send($recipient, new $notificationClass([
                            'channel' => match ($channel) {
                                'email' => 'mail',       // Map 'email' to 'mail'
                                'sms' => \App\Channels\SmsChannel::class,
                                'whatsapp' => \App\Channels\WhatsappChannel::class,
                                'push' => \App\Channels\PushChannel::class,
                                'slack' => \App\Channels\SlackChannel::class,
                                default => $channel,     // Use the channel as-is for others
                            },
                            'notification_data' => $notificationData,
                        ]));
                    } catch (\Exception $e) {
                        // dd($e);
                        // Log exception or handle as necessary
                    }
                }
            }
        }
    }


    private function isNotificationEnabled($enabledNotifications, $type, $channel)
    {
        return (
            (is_array($enabledNotifications) && empty($enabledNotifications)) ||
            (is_array($enabledNotifications) && in_array("{$channel}_{$type}", $enabledNotifications))
        );
    }
}
