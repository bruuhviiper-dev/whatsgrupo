<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Grupo Recebido — WhatsGrupos</title>
</head>
<body style="margin:0;padding:0;background-color:#0F0F1A;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0F0F1A;padding:40px 20px;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#1A1A2E;border-radius:16px;overflow:hidden;">

          {{-- Cabeçalho --}}
          <tr>
            <td style="background:linear-gradient(135deg,#6C3FC5,#00C896);padding:40px 40px 30px;text-align:center;">
              <div style="font-size:48px;margin-bottom:12px;">📋</div>
              <h1 style="color:#ffffff;margin:0;font-size:26px;font-weight:700;letter-spacing:-0.5px;">
                Grupo Recebido!
              </h1>
              <p style="color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:15px;">
                Estamos analisando seu envio
              </p>
            </td>
          </tr>

          {{-- Corpo --}}
          <tr>
            <td style="padding:40px;">
              <p style="color:#E8E8F0;font-size:16px;line-height:1.7;margin:0 0 20px;">
                Olá! Recebemos a solicitação para adicionar o grupo
                <strong style="color:#00C896;">{{ $group->name }}</strong>
                ao diretório WhatsGrupos. 🎉
              </p>

              {{-- Card do grupo --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#0F0F1A;border-radius:12px;margin:24px 0;">
                <tr>
                  <td style="padding:24px;">
                    <p style="color:#9090A8;font-size:12px;text-transform:uppercase;letter-spacing:1px;margin:0 0 8px;">Grupo enviado</p>
                    <p style="color:#ffffff;font-size:20px;font-weight:700;margin:0 0 8px;">{{ $group->name }}</p>
                    <p style="color:#9090A8;font-size:14px;margin:0;">
                      Categoria: <span style="color:#6C3FC5;">{{ $group->category->name ?? 'Não definida' }}</span>
                    </p>
                  </td>
                </tr>
              </table>

              <p style="color:#E8E8F0;font-size:15px;line-height:1.7;margin:0 0 20px;">
                Nossa equipe de moderação irá analisar seu grupo em até <strong style="color:#FFD700;">48 horas</strong>.
                Você receberá outro e-mail assim que a análise for concluída.
              </p>

              <p style="color:#9090A8;font-size:14px;line-height:1.6;margin:0;">
                📌 <strong>O que verificamos?</strong><br>
                Conteúdo adequado, link ativo e conformidade com as nossas diretrizes da comunidade.
              </p>
            </td>
          </tr>

          {{-- Rodapé --}}
          <tr>
            <td style="padding:24px 40px;background:#0F0F1A;text-align:center;border-top:1px solid #2A2A40;">
              <p style="color:#6060A0;font-size:13px;margin:0;">
                Este e-mail foi enviado automaticamente pelo WhatsGrupos.<br>
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
