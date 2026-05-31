<?php

namespace App\Mail;

use App\Models\BoostOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * E-mail enviado ao comprador após confirmação do pagamento de um pacote de impulsos.
 * Contém o código de ativação em destaque e o passo a passo de como usar.
 */
class BoostCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Cria uma nova instância do mailable.
     *
     * @param  BoostOrder  $order  O pedido de impulso pago e confirmado
     */
    public function __construct(public BoostOrder $order)
    {
    }

    /**
     * Define o assunto e o remetente do e-mail.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⭐ Seu código de impulso chegou! — WhatsGrupos',
        );
    }

    /**
     * Define o template Blade a ser utilizado para o corpo do e-mail.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.boost-code',
        );
    }
}
