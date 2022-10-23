<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanCreatedAdmin extends Notification
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
		$mail = (new MailMessage)
					->subject('An Approval Has Been Selected')
					->greeting('A user has selected terms for an Open Approval!')
					->line("Business Name: {$this->loan->user->business->name}")
					->line("Customer ID: #{$this->loan->user->customer_id}")
                    ->line(
						($this->loan->loan_amount ? 'Loan Amount' : 'Credit Limit') .
						': $' .
						($this->loan->loan_amount ?? $this->loan->credit_limit)
					)
					->line("Term Length: {$this->loan->term_length_formatted}")
                    ->line("Loan Product: {$this->loan->loanProduct->title}");

		if ($this->loan->openApproval->payment_frequency) {
			$mail->line('Payment Frequency: ' . $this->loan->openApproval->payment_frequency)
				->line('Closing Costs: ' . $this->loan->openApproval->closing_costs_formatted);
		}

		$mail->line('Submitted At: ' . Carbon::parse($this->loan->created_at)->format('m/d/Y H:i'))
			->action('Go to Loan', route('admin.loan.edit', ['id' => $this->loan->id]));

		return $mail;
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
