<?php

// Configurações para integração com o Asaas (gateway de pagamentos)
return [
    /*
    |--------------------------------------------------------------------------
    | API Key do Asaas
    |--------------------------------------------------------------------------
    | Sua chave de API pode ser obtida em:
    | https://www.asaas.com/apiAccessControl/index
    | → Aba "Chaves de API"
    |
    | Para sandbox (testes):  $aact_... (ambiente sandbox)
    | Para produção:          $aact_... (ambiente produção)
    */
    'api_key' => env('ASAAS_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Modo Sandbox
    |--------------------------------------------------------------------------
    | true  → usa sandbox.asaas.com (testes, sem cobranças reais)
    | false → usa api.asaas.com     (produção, cobranças reais)
    */
    'sandbox' => env('ASAAS_SANDBOX', true),

    /*
    |--------------------------------------------------------------------------
    | Token de Autenticação do Webhook
    |--------------------------------------------------------------------------
    | Token definido na configuração de webhook no painel do Asaas.
    | Usado para validar que a notificação veio realmente do Asaas.
    | Configure em: https://www.asaas.com/apiAccessControl/index
    | → Aba "Webhooks" → campo "Token de autenticação"
    */
    'webhook_token' => env('ASAAS_WEBHOOK_TOKEN', ''),
];
