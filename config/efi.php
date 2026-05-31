<?php

// Retorna as configurações para integração com a Efí Bank (ex-Gerencianet)
return [
    'client_id' => env('EFI_CLIENT_ID'),
    'client_secret' => env('EFI_CLIENT_SECRET'),
    'sandbox' => env('EFI_SANDBOX', true),
    'pix_key' => env('EFI_PIX_KEY'),
];
