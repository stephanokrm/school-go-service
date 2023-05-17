<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

/**
 *
 */
class AbsentNotification extends Notification
{
    use Queueable;

    /**
     * @param Student $student
     */
    public function __construct(private readonly Student $student)
    {
    }


    /**
     * @return string[]
     */
    public function via(): array
    {
        return [FcmChannel::class];
    }

    /**
     * @return FcmMessage
     */
    public function toFcm(): FcmMessage
    {
        return FcmMessage::create()
            ->setNotification(
                FcmNotification::create()
                    ->setTitle("{$this->student->getFirstName()} não irá comparecer.")
            );
    }
}
