<?php

namespace App\Notifications;

use App\Models\Student;
use App\Models\Trip;
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
     * @param Trip $trip
     * @param Student $student
     */
    public function __construct(private readonly Trip $trip, private readonly Student $student)
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
        $student = $this->student->getFirstName();
        $school = $this->trip->getItinerary()->getSchool()->getName();
        $direction = $this->trip->isRound() ? "ida para" : "volta de";
        $title = "{$student} não irá comparecer na {$direction} {$school}.";

        return FcmMessage::create()->setNotification(FcmNotification::create()->setTitle($title));
    }
}
