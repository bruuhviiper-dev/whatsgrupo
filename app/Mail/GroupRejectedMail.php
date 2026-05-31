<?php

namespace App\Mail;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * E-mail enviado ao usuário notificando que seu grupo foi rejeitado.
 * Inclui o motivo da rejeição para que o usuário possa corrigir e reenviar.
 */
class GroupRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Cria uma nova instância do mailable.
     *
     * @param  Group   $group   O grupo de WhatsApp que foi rejeitado
     * @param  string  $reason  O motivo da rejeição fornecido pelo moderador
     */
    public function __construct(
        public Group $group,
        public string $reason
    ) {
    }

    /**
     * Define o assunto e o remetente do e-mail.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '❌ Seu grupo não foi aprovado — WhatsGrupos',
        );
    }

    /**
     * Define o template Blade a ser utilizado para o corpo do e-mail.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.group-rejected',
        );
    }
}
