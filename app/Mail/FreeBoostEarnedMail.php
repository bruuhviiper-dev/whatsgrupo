<?php

namespace App\Mail;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * E-mail de notificação para o proprietário informando que ele ganhou destaque VIP gratuito.
 */
class FreeBoostEarnedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Group $group;

    /**
     * Cria uma nova instância de mensagem.
     */
    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    /**
     * Define o envelope de assunto do e-mail.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Parabéns! Seu grupo "' . $this->group->name . '" ganhou 6 Horas de Destaque VIP Grátis!',
        );
    }

    /**
     * Define o conteúdo (Template Blade) do e-mail.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.free-boost-earned',
        );
    }

    /**
     * Anexos se houver.
     */
    public function attachments(): array
    {
        return [];
    }
}
