<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanFunded extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($loan)
    {
        $this->loan = $loan;
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
					->subject('Your Loan Has Been Funded')
					->greeting('Congratulations! Your loan has been funded!')
					->line('You should see the funds in your account within 1 business day. Thank you for giving us the opportunity to assist. We appreciate you and we greatly appreciate your business!')
                    ->line(
						($this->loan->loan_amount ? 'Loan Amount' : 'Credit Limit') .
						': $' .
						($this->loan->loan_amount ?? $this->loan->credit_limit)
					)
                    ->line("Loan Product: {$this->loan->loanProduct->title}")
					->line('Funded On: ' . Carbon::parse($this->loan->funded_date)->format('m/d/Y'))
                    ->action('Go to Loans', route('user.loan.index'));
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
