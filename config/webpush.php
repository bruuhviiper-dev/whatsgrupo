<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VAPID Keys para Web Push Notifications
    |--------------------------------------------------------------------------
    |
    | As chaves pública e privada geradas de acordo com o padrão VAPID
    | são necessárias para assinar e criptografar as mensagens Web Push.
    |
    */

    'vapid_public'  => env('VAPID_PUBLIC_KEY'),
    'vapid_private' => env('VAPID_PRIVATE_KEY'),
];
