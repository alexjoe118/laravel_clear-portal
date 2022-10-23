<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanRequestCreatedAdmin extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($loanRequest)
    {
        $this->loanRequest = $loanRequest;
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
		$interestInWCO = $this->loanRequest->interest_in_working_capital_options ? 'Yes' : 'No';
		$fundsNeededEstimate = str_replace('-', ' ', $this->loanRequest->funds_needed_estimate);
		$fundsUsage = str_replace('-', ' ', $this->loanRequest->funds_usage);
		$communicationChannel = str_replace('-', ' ', $this->loanRequest->communication_channel);

        return (new MailMessage)
					->subject("New Loan Request - {$this->loanRequest->user->business->name}")
					->greeting('New Loan Request')
					->line("Business Name: {$this->loanRequest->user->business->name}")
					->line("User Customer ID: #{$this->loanRequest->user->customer_id}")
					->line("User Email: {$this->loanRequest->user->email}")
                    ->line('Amount Requested: $' . $this->loanRequest->requested_amount)
                    ->line("Loan Product: {$this->loanRequest->loanProduct->title}")
                    ->line("When funds are needed: $fundsNeededEstimate")
                    ->line("Use of funds: $fundsUsage")
                    ->line("Preferred method of contact: $communicationChannel")
                    ->line("Interested in Working Capital Options: {$interestInWCO}")
					->line('Requested At: ' . Carbon::parse($this->loanRequest->created_at)->format('m/d/Y H:i'))
                    ->action('Loan Request Details', route('admin.loan-request.edit', ['id' => $this->loanRequest->id]));
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
