<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Boost Gratuito Ativado! — WhatsGrupos</title>
</head>
<body style="margin:0;padding:0;background-color:#0F0F1A;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0F0F1A;padding:40px 20px;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#1A1A2E;border-radius:16px;overflow:hidden;">

          {{-- Cabeçalho --}}
          <tr>
            <td style="background:linear-gradient(135deg,#6C3FC5,#9B6EFF);padding:40px 40px 30px;text-align:center;">
              <div style="font-size:56px;margin-bottom:12px;">🔥</div>
              <h1 style="color:#ffffff;margin:0;font-size:28px;font-weight:700;">
                Destaque VIP Grátis Ativado!
              </h1>
              <p style="color:rgba(255,255,255,0.9);margin:8px 0 0;font-size:15px;">
                Seu grupo bateu a meta de convites e está no topo!
              </p>
            </td>
          </tr>

          {{-- Corpo --}}
          <tr>
            <td style="padding:40px;">
              <p style="color:#E8E8F0;font-size:16px;line-height:1.7;margin:0 0 24px;">
                Parabéns! O seu grupo <strong style="color:#00C896;">{{ $group->name }}</strong>
                atingiu a meta de **5 convidados que entraram no grupo** através do seu link de indicação único! 🚀
              </p>

              <p style="color:#E8E8F0;font-size:15px;line-height:1.6;margin:0 0 24px;">
                Como recompensa pelo seu esforço em compartilhar, **ativamos 6 horas de Destaque Super VIP gratuito** para o seu grupo agora mesmo! 
                Durante as próximas 6 horas, seu grupo aparecerá fixado no topo da página inicial e de sua categoria com a borda dourada pulsante!
              </p>

              {{-- Status do VIP --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="background:rgba(255,215,0,0.05);border:1px solid #FFD700;border-radius:12px;margin:24px 0;">
                <tr>
                  <td style="padding:20px;text-align:center;">
                    <p style="color:#FFD700;font-size:18px;font-weight:900;margin:0 0 4px;">
                      ⭐ SUPER VIP ATIVADO! ⭐
                    </p>
                    <p style="color:#E8E8F0;font-size:13px;margin:0;">
                      Expira em: <strong>{{ $group->vip_expires_at ? $group->vip_expires_at->format('d/m/Y \à\s H:i') : now()->addHours(6)->format('d/m/Y \à\s H:i') }}</strong>
                    </p>
                  </td>
                </tr>
              </table>

              {{-- Botão ver grupo --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;">
                <tr>
                  <td align="center">
                    <a href="{{ config('app.url') }}/grupo/{{ $group->slug }}"
                       style="display:inline-block;background:linear-gradient(135deg,#00C896,#00A878);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:700;letter-spacing:0.5px;">
                      👀 Ver Meu Grupo no Topo
                    </a>
                  </td>
                </tr>
              </table>

              <p style="color:#9090A8;font-size:14px;line-height:1.6;margin:0;">
                Continue compartilhando seu link de indicação! Você pode obter novos boosts assim que este expirar. Gerencie seus grupos a qualquer momento em 
                <a href="{{ config('app.url') }}/meus-grupos" style="color:#6C3FC5;text-decoration:none;font-weight:bold;">Meus Grupos</a>.
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
