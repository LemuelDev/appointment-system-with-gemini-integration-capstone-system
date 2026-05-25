<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ClinicNotification extends Notification
{
    use Queueable;

    private $title;
    private $message;
    private $link;

    public function __construct($title, $message, $link = '#')
    {
        $this->title = $title;
        $this->message = $message;
        $this->link = $link;
    }

    // Tell Laravel to save this directly into our database table
    public function via($notifiable): array
    {
        return ['database'];
    }

    // This data gets stored as JSON inside the 'data' column
    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'link' => $this->link,
        ];
    }
}
