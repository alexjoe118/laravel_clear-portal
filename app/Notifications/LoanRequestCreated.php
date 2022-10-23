<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanRequestCreated extends Notification
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
    public function toMail()
    {
		$advisorPhone = $this->loanRequest->user->advisor->phone_number
			? formatPhoneNumber($this->loanRequest->user->advisor->phone_number)
			: '';

		$advisorCellPhone = $this->loanRequest->user->advisor->cell_phone_number
			? formatPhoneNumber($this->loanRequest->user->advisor->cell_phone_number)
			: '';

        return (new MailMessage)
					->subject('Your Loan Request Has Been Submitted')
					->markdown('vendor.notifications.email-with-advisor-info', [
						'greeting' => 'Congratulations!',
						'introLines' => [
							'Your Loan Request has been submitted and we\'re now working on getting you the best approvals available in the marketplace. We typically have options for you to review within 1 business day. Please be sure to check your portal and email frequently or reach out to your loan advisor anytime for an update.',
							'Amount Requested: $' . $this->loanRequest->requested_amount,
							"Loan Product: {$this->loanRequest->loanProduct->title}",
						],
						'actionText' => 'Go to the Portal',
						'actionUrl' => url('/'),
						'requestedText' => 'Requested On: ' . Carbon::parse($this->loanRequest->created_at)->format('m/d/Y H:i'),
						'advisor' => [
							'picture' => $this->loanRequest->user->advisor->photo,
							'full_name' => $this->loanRequest->user->advisor->full_name,
							'email' => $this->loanRequest->user->advisor->email,
							'phone_number' => $advisorPhone,
							'cell_phone_number' => $advisorCellPhone
						]
					]);
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
