<?php

namespace App\Mail;

use App\Models\Notificacion;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The notificacion instance.
     *
     * @var \App\Models\Notificacion
     */
    public $notificacion;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Notificacion $notificacion)
    {
        $this->notificacion = $notificacion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $notificacion = $this->notificacion;
        $address = config('mail.from.address');
        $name = config('mail.from.name');
        return $this->from($address, $name)
            ->view('emails.notifications', compact('notificacion'));
    }
}
