<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seu Código de Impulso — WhatsGrupos</title>
</head>
<body style="margin:0;padding:0;background-color:#0F0F1A;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0F0F1A;padding:40px 20px;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#1A1A2E;border-radius:16px;overflow:hidden;">

          {{-- Cabeçalho --}}
          <tr>
            <td style="background:linear-gradient(135deg,#6C3FC5,#FFD700);padding:40px 40px 30px;text-align:center;">
              <div style="font-size:56px;margin-bottom:12px;">⭐</div>
              <h1 style="color:#ffffff;margin:0;font-size:28px;font-weight:700;">
                Seu código de impulso chegou!
              </h1>
              <p style="color:rgba(255,255,255,0.9);margin:8px 0 0;font-size:15px;">
                Pacote {{ $order->boostPackage->name ?? 'VIP' }} — {{ $order->boosts_total }} impulso(s)
              </p>
            </td>
          </tr>

          {{-- Corpo --}}
          <tr>
            <td style="padding:40px;">
              <p style="color:#E8E8F0;font-size:16px;line-height:1.7;margin:0 0 24px;">
                Olá, <strong>{{ $order->buyer_name }}</strong>! Seu pagamento foi confirmado. 🎊<br>
                Aqui está o seu código exclusivo de impulso:
              </p>

              {{-- Código em destaque --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 32px;">
                <tr>
                  <td align="center">
                    <div style="display:inline-block;background:linear-gradient(135deg,#6C3FC5,#9B6EFF);border-radius:16px;padding:32px 48px;">
                      <p style="color:rgba(255,255,255,0.7);font-size:12px;text-transform:uppercase;letter-spacing:2px;margin:0 0 12px;">
                        Seu código VIP
                      </p>
                      <p style="color:#FFD700;font-size:36px;font-weight:700;letter-spacing:6px;margin:0;font-family:monospace;">
                        {{ $order->boost_code }}
                      </p>
                      <p style="color:rgba(255,255,255,0.7);font-size:13px;margin:12px 0 0;">
                        {{ $order->boosts_total }} impulso(s) disponíveis
                      </p>
                    </div>
                  </td>
                </tr>
              </table>

              {{-- Passo a passo --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#0F0F1A;border-radius:12px;margin:0 0 24px;">
                <tr>
                  <td style="padding:28px;">
                    <p style="color:#FFD700;font-size:15px;font-weight:700;margin:0 0 20px;">
                      🚀 Como usar seus impulsos em 5 passos:
                    </p>

                    <table width="100%" cellpadding="0" cellspacing="0">
                      @foreach([
                        ['1', 'Acesse o site', 'Vá para <strong>whatsgrupos.com</strong> no seu navegador'],
                        ['2', 'Meus Grupos', 'Clique em <strong>Meus Grupos</strong> no menu superior'],
                        ['3', 'Informe seu e-mail', 'Digite o e-mail que você usou ao enviar seu grupo'],
                        ['4', 'Clique em Super VIP ⭐', 'Ao lado do seu grupo aprovado, clique no botão <strong>SUPER VIP ⭐</strong>'],
                        ['5', 'Cole o código', 'Insira o código <strong style="color:#FFD700;">{{ $order->boost_code }}</strong> e confirme. Pronto!'],
                      ] as $step)
                      <tr>
                        <td style="vertical-align:top;padding:0 0 16px;">
                          <table cellpadding="0" cellspacing="0">
                            <tr>
                              <td style="vertical-align:top;">
                                <div style="width:32px;height:32px;background:linear-gradient(135deg,#6C3FC5,#9B6EFF);border-radius:50%;text-align:center;line-height:32px;color:#fff;font-weight:700;font-size:14px;flex-shrink:0;">
                                  {{ $step[0] }}
                                </div>
                              </td>
                              <td style="padding-left:14px;vertical-align:top;">
                                <p style="color:#ffffff;font-size:14px;font-weight:700;margin:4px 0 2px;">{{ $step[1] }}</p>
                                <p style="color:#9090A8;font-size:13px;line-height:1.5;margin:0;">{!! $step[2] !!}</p>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      @endforeach
                    </table>
                  </td>
                </tr>
              </table>

              {{-- Botão direto --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;">
                <tr>
                  <td align="center">
                    <a href="{{ config('app.url') }}/meus-grupos"
                       style="display:inline-block;background:linear-gradient(135deg,#00C896,#00A878);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:700;">
                      ⭐ Ir para Meus Grupos
                    </a>
                  </td>
                </tr>
              </table>

              <p style="color:#9090A8;font-size:13px;line-height:1.6;margin:0;text-align:center;">
                Valor pago: <strong style="color:#E8E8F0;">R$ {{ number_format($order->amount, 2, ',', '.') }}</strong> via {{ strtoupper($order->payment_method) }}<br>
                Guarde este e-mail — você precisará do código acima para usar seus impulsos.
              </p>
            </td>
          </tr>

          {{-- Rodapé --}}
          <tr>
            <td style="padding:24px 40px;background:#0F0F1A;text-align:center;border-top:1px solid #2A2A40;">
              <p style="color:#6060A0;font-size:13px;margin:0;">
                WhatsGrupos — O maior diretório de grupos do Brasil<br>
                <a href="{{ config('app.url') }}" style="color:#6C3FC5;text-decoration:none;">whatsgrupos.com</a>
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
