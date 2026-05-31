<?php

namespace App\Mail;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * E-mail enviado ao usuário notificando que seu grupo foi aprovado no diretório.
 */
class GroupApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Cria uma nova instância do mailable.
     *
     * @param  Group  $group  O grupo de WhatsApp que foi aprovado
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
            subject: '🎉 Seu grupo foi aprovado no WhatsGrupos!',
        );
    }

    /**
     * Define o template Blade a ser utilizado para o corpo do e-mail.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.group-approved',
        );
    }
}
