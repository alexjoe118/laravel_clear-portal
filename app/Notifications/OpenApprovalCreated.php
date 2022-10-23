<?php

namespace App\Notifications;

use App\Models\OpenApproval;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OpenApprovalCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($openApproval)
    {
        $this->openApproval = $openApproval;
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
		$advisorPhone = $this->openApproval->user->advisor->phone_number
			? formatPhoneNumber($this->openApproval->user->advisor->phone_number)
			: '';

		$advisorCellPhone = $this->openApproval->user->advisor->cell_phone_number
			? formatPhoneNumber($this->openApproval->user->advisor->cell_phone_number)
			: '';

		$new = OpenApproval::where('loan_request_id', $this->openApproval->loan_request_id)
				->where('approval_expires', '>=', now())
				->whereNull('deleted_at')
				->get()
				->count() <= 1;

		$subject = 'You have a new ' . ($new ? 'Open ' : '') . 'Approval on Clear Portal';

		return (new MailMessage)
				->subject($subject)
				->markdown('vendor.notifications.email-with-advisor-info', [
					'greeting' => 'Congratulations!',
					'introLines' => [
						'You have a new '. ($new ? 'Open ' : '') .'Approval available.'
					],
					'topActionText' => 'You can view your approvals here without logging in',
					'topActionUrl' => route('guest.open-approvals', $this->openApproval->user->customer_id),
					'outroLines' => 'To select an approval, please login to your portal or contact your loan advisor.',
					'listItems' => [
						'Use the toggle bar below your approvals to adjust the loan and payment amount',
						'Scroll all the way down the Open Approvals page as you may have been approved for more than one loan product.'
					],
					'loanData' => [
						'Amount Requested: $' . $this->openApproval->loanRequest->requested_amount,
						"Loan Product: {$this->openApproval->loanProduct->title}",
					],
					'actionText' => 'Login to your Portal',
					'actionUrl' => route('user.open-approval.index'),
					'requestedText' => 'Requested On: ' . Carbon::parse($this->openApproval->created_at)->format('m/d/Y H:i'),
					'advisor' => [
						'picture' => $this->openApproval->user->advisor->photo,
						'full_name' => $this->openApproval->user->advisor->full_name,
						'email' => $this->openApproval->user->advisor->email,
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
