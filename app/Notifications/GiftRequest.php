<?php

namespace App\Notifications;

use App\Models\Gift;
use App\Models\User;
use App\Models\WonGift;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GiftRequest extends Notification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        private Gift $gift,
        private WonGift $wonGift,
        private User $winner
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->line("Vous avez reçu une demande de cadeau de la part de {$this->winner->name}")
            ->line("Le cadeau est le suivant : {$this->gift->name}")
            ->line("Description : {$this->gift->description}")
            ->action('Confirmer l\'éxecution', route('gifts.pendingDetail', $this->wonGift));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
