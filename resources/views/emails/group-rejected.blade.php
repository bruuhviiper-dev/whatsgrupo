<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Grupo Não Aprovado — WhatsGrupos</title>
</head>
<body style="margin:0;padding:0;background-color:#0F0F1A;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0F0F1A;padding:40px 20px;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#1A1A2E;border-radius:16px;overflow:hidden;">

          {{-- Cabeçalho --}}
          <tr>
            <td style="background:linear-gradient(135deg,#C43F3F,#8B2020);padding:40px 40px 30px;text-align:center;">
              <div style="font-size:48px;margin-bottom:12px;">❌</div>
              <h1 style="color:#ffffff;margin:0;font-size:26px;font-weight:700;">
                Grupo não aprovado
              </h1>
              <p style="color:rgba(255,255,255,0.8);margin:8px 0 0;font-size:15px;">
                Infelizmente não foi possível aprovar seu envio desta vez
              </p>
            </td>
          </tr>

          {{-- Corpo --}}
          <tr>
            <td style="padding:40px;">
              <p style="color:#E8E8F0;font-size:16px;line-height:1.7;margin:0 0 24px;">
                Olá! Nossa equipe de moderação analisou o grupo
                <strong style="color:#E8E8F0;">{{ $group->name }}</strong>
                e infelizmente ele não pôde ser aprovado no momento.
              </p>

              {{-- Motivo da rejeição --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#2A1010;border-left:4px solid #C43F3F;border-radius:0 8px 8px 0;margin:0 0 24px;">
                <tr>
                  <td style="padding:20px 24px;">
                    <p style="color:#FF8080;font-size:13px;text-transform:uppercase;letter-spacing:1px;margin:0 0 8px;font-weight:700;">
                      Motivo da rejeição
                    </p>
                    <p style="color:#E8E8F0;font-size:15px;line-height:1.6;margin:0;">
                      {{ $reason }}
                    </p>
                  </td>
                </tr>
              </table>

              <p style="color:#E8E8F0;font-size:15px;line-height:1.7;margin:0 0 20px;">
                Você pode corrigir as pendências apontadas e
                <a href="{{ config('app.url') }}/enviar-grupo" style="color:#00C896;text-decoration:none;font-weight:700;">
                  reenviar seu grupo
                </a>
                a qualquer momento.
              </p>

              {{-- Dicas --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#0F0F1A;border-radius:12px;margin:24px 0;">
                <tr>
                  <td style="padding:24px;">
                    <p style="color:#FFD700;font-size:14px;font-weight:700;margin:0 0 12px;">
                      💡 Dicas para aprovação
                    </p>
                    <p style="color:#9090A8;font-size:14px;line-height:1.8;margin:0;">
                      ✔ Link do WhatsApp válido e ativo<br>
                      ✔ Nome e descrição apropriados e sem palavrões<br>
                      ✔ Conteúdo na categoria correta<br>
                      ✔ Sem links suspeitos ou conteúdo adulto
                    </p>
                  </td>
                </tr>
              </table>

              <p style="color:#9090A8;font-size:14px;line-height:1.6;margin:0;">
                Em caso de dúvidas, entre em contato conosco respondendo este e-mail.
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
