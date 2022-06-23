<?php

namespace App\Mail;

use App\Models\Plantilla;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The plantilla instance.
     *
     * @var \App\Models\Plantilla
     */
    public $plantilla;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Plantilla $plantilla)
    {
        $this->plantilla = $plantilla;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $plantilla = $this->plantilla;
        $address = config('mail.from.address');
        $name = config('mail.from.name');
        return $this->from($address, $name)
            ->view('emails.notifications', compact('plantilla'));
    }
}
