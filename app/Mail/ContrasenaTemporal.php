<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContrasenaTemporal extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $usuario,
        public string $contrasenaTemporal,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu contraseña temporal — Ferretería BANDEK',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contrasena-temporal',
        );
    }
}
