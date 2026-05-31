<?php

namespace App\Mail;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * E-mail de confirmação enviado ao usuário quando seu grupo é recebido e está em análise.
 */
class GroupSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Cria uma nova instância do mailable.
     *
     * @param  Group  $group  O grupo de WhatsApp que foi enviado para análise
     */
    public function __construct(public Group $group)
    {
    }

    /**
     * Define o assunto e o remetente do e-mail.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Grupo recebido! Estamos analisando — WhatsGrupos',
        );
    }

    /**
     * Define o template Blade a ser utilizado para o corpo do e-mail.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.group-submitted',
        );
    }
}
