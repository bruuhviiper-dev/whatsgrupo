<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Grupo Aprovado — WhatsGrupos</title>
</head>
<body style="margin:0;padding:0;background-color:#0F0F1A;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0F0F1A;padding:40px 20px;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#1A1A2E;border-radius:16px;overflow:hidden;">

          {{-- Cabeçalho --}}
          <tr>
            <td style="background:linear-gradient(135deg,#00C896,#00A878);padding:40px 40px 30px;text-align:center;">
              <div style="font-size:56px;margin-bottom:12px;">🎉</div>
              <h1 style="color:#ffffff;margin:0;font-size:28px;font-weight:700;">
                Parabéns! Seu grupo foi aprovado!
              </h1>
              <p style="color:rgba(255,255,255,0.9);margin:8px 0 0;font-size:15px;">
                Agora ele está visível para milhares de usuários
              </p>
            </td>
          </tr>

          {{-- Corpo --}}
          <tr>
            <td style="padding:40px;">
              <p style="color:#E8E8F0;font-size:16px;line-height:1.7;margin:0 0 24px;">
                Ótima notícia! O grupo <strong style="color:#00C896;">{{ $group->name }}</strong>
                foi aprovado e já está disponível no diretório WhatsGrupos! 🚀
              </p>

              {{-- Botão ver grupo --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;">
                <tr>
                  <td align="center">
                    <a href="{{ config('app.url') }}/grupo/{{ $group->slug }}"
                       style="display:inline-block;background:linear-gradient(135deg,#00C896,#00A878);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:700;letter-spacing:0.5px;">
                      👀 Ver Meu Grupo
                    </a>
                  </td>
                </tr>
              </table>

              {{-- Seção VIP --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,rgba(108,63,197,0.2),rgba(255,215,0,0.1));border:1px solid #6C3FC5;border-radius:12px;margin:24px 0;">
                <tr>
                  <td style="padding:24px;">
                    <p style="color:#FFD700;font-size:16px;font-weight:700;margin:0 0 8px;">
                      ⭐ Quer aparecer no topo?
                    </p>
                    <p style="color:#E8E8F0;font-size:14px;line-height:1.6;margin:0 0 16px;">
                      Com o <strong>Super VIP</strong>, seu grupo aparece sempre destacado com borda dourada
                      e fica nas primeiras posições por até 12 horas por impulso!
                    </p>
                    <a href="{{ config('app.url') }}/pacotes-vip"
                       style="display:inline-block;background:linear-gradient(135deg,#6C3FC5,#9B6EFF);color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:50px;font-size:14px;font-weight:700;">
                      🚀 Ver Pacotes VIP
                    </a>
                  </td>
                </tr>
              </table>

              <p style="color:#9090A8;font-size:14px;line-height:1.6;margin:0;">
                Acesse <a href="{{ config('app.url') }}/meus-grupos" style="color:#6C3FC5;">Meus Grupos</a>
                a qualquer momento para gerenciar e impulsionar seu grupo usando seu e-mail de cadastro.
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
