<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanRequestStatus extends Notification
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

	public function toMail($notifiable)
	{
		$advisorPhone = $this->loanRequest->user->advisor->phone_number
			? formatPhoneNumber($this->loanRequest->user->advisor->phone_number)
			: '';

		$advisorCellPhone = $this->loanRequest->user->advisor->cell_phone_number
			? formatPhoneNumber($this->loanRequest->user->advisor->cell_phone_number)
			: '';

		return (new MailMessage)
				->subject('Your Loan Request Has Been Approved')
				->markdown('vendor.notifications.email-with-advisor-info', [
					'greeting' => 'Congratulations!',
					'introLines' => [
						"Your Loan Request has {$this->loanRequest->openApprovals->count()} new Approvals.",
					],
					'topActionText' => 'You can view your approvals here without logging in',
					'topActionUrl' => route('guest.open-approvals', $this->loanRequest->user->customer_id),
					'outroLines' => 'To select an approval, please login to your portal or contact your loan advisor.',
					'listItems' => [
						'Use the toggle bar below your approvals to adjust the loan and payment amount',
						'Scroll all the way down the Open Approvals page as you may have been approved for more than one loan product.',
					],
					'loanData' => [
						'Amount Requested: $' . $this->loanRequest->requested_amount,
						"Loan Product: {$this->loanRequest->openApprovals->pluck('loanProduct.title')->unique()->join(', ')}",
					],
					'actionText' => 'Login to your Portal',
					'actionUrl' => route('user.open-approval.index'),
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
