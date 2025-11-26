<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\CheckoutOrders;
use App\Models\CheckoutBuild;
use App\Models\User;

class CheckoutConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $checkout;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(CheckoutOrders $order, ?CheckoutBuild $checkout = null, ?User $user = null)
    {
        $this->order = $order;
        $this->checkout = $checkout;
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'Confirmação de Compra - ' . ($this->checkout->produto_name ?? 'Gateway');

        return $this->subject($subject)
            ->view('emails.checkout-confirmation')
            ->with([
                'order' => $this->order,
                'checkout' => $this->checkout,
                'user' => $this->user,
            ]);
    }
}
