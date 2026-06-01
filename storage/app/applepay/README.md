# Verificação de domínio do Apple Pay (Stripe)

O Apple Pay no checkout (Stripe Embedded Checkout) aparece automaticamente — **sem código de pagamento adicional** — desde que:

1. Apple Pay esteja habilitado no Dashboard da Stripe
   (Settings → Payment Methods → Apple Pay → aceitar os Termos da Apple).
2. O domínio de PRODUÇÃO esteja verificado (HTTPS obrigatório).
3. O cliente esteja no Safari, em dispositivo Apple, com cartão na Wallet.
   (Não aparece em localhost nem em navegadores não-Apple.)

## Como verificar o domínio

No Dashboard da Stripe: Settings → Payment Methods → Apple Pay → **Add new domain**.
A Stripe vai pedir para hospedar um arquivo de verificação.

1. Baixe/copie o conteúdo que a Stripe fornece.
2. Cole esse conteúdo no arquivo (sem extensão):

       storage/app/applepay/apple-developer-merchantid-domain-association

3. A rota `GET /.well-known/apple-developer-merchantid-domain-association`
   (definida em routes/web.php) serve esse conteúdo como text/plain (200).
4. Volte ao Dashboard e clique em verificar.

> Importante (produção): garanta que o Nginx/Apache encaminhe o caminho
> `/.well-known/...` para o Laravel e que NÃO haja redirect com barra final
> (a Stripe exige resposta 200 exata, sem 301/302).
> Exemplo Nginx:
>     location /.well-known { try_files $uri $uri/ /index.php?$query_string; }

## Pendências para ativar (não são código)
- [ ] Habilitar Apple Pay no Dashboard Stripe
- [ ] Colar o conteúdo no arquivo apple-developer-merchantid-domain-association
- [ ] Verificar o domínio de produção no Dashboard
