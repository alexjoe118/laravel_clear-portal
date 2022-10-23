<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanRequestDeleted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
	 * @param object $loanRequest
	 * @param string|boolean $message
     * @return void
     */
    public function __construct($loanRequest, $message = false)
    {
        $this->loanRequest = $loanRequest;
		$this->message = $message;
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
        return (new MailMessage)
					->subject('Your Loan Request was declined on Clear Portal')
					->line('Unfortunately your Loan Request was declined')
					->line("{$this->message}")
                    ->line('Amount Requested: $' . $this->loanRequest->requested_amount)
                    ->line("Loan Product: {$this->loanRequest->loanProduct->title}")
					->line('Requested At: ' . Carbon::parse($this->loanRequest->created_at)->format('m/d/Y H:i'))
                    ->action('Go to the Portal', url('/'));
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
