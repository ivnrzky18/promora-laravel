<?php

namespace App\Notifications;

use App\Models\Promo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewPromoNotification extends Notification
{
    use Queueable;

    public function __construct(private Promo $promo) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification (stored in DB).
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title'    => 'Promo Baru dari ' . $this->promo->seller->business_name,
            'message'  => $this->promo->title,
            'promo_id' => $this->promo->id,
            'type'     => 'new_promo',
        ];
    }
}
