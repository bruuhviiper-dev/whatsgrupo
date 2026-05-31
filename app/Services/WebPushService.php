<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

/**
 * Serviço responsável por assinar e disparar notificações Web Push (VAPID) para navegadores dos assinantes.
 */
class WebPushService
{
    private WebPush $webPush;
    
    public function __construct()
    {
        $auth = [
            'VAPID' => [
                'subject'    => config('app.url'),
                'publicKey'  => config('webpush.vapid_public'),
                'privateKey' => config('webpush.vapid_private'),
            ]
        ];
        $this->webPush = new WebPush($auth);
    }
    
    /**
     * Envia notificação Web Push para todos os assinantes interessados em uma categoria específica.
     * Remove assinaturas que se tornaram inválidas ou expiradas na rede de push do browser.
     */
    public function sendToCategory(int $categoryId, string $title, string $body, string $url): array
    {
        // Busca assinantes interessados no JSON de categorias no banco de dados (SQLite compatível com whereJsonContains)
        $subscriptions = PushSubscription::whereJsonContains('category_ids', $categoryId)->get();
        
        $results = ['sent' => 0, 'failed' => 0];
        
        foreach ($subscriptions as $sub) {
            // Verifica se a assinatura tem as chaves válidas
            if (empty($sub->endpoint) || empty($sub->public_key) || empty($sub->auth_token)) {
                continue;
            }

            $this->webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'keys' => [
                        'p256dh' => $sub->public_key,
                        'auth'   => $sub->auth_token
                    ]
                ]),
                json_encode([
                    'title' => $title,
                    'body'  => $body,
                    'url'   => $url
                ], JSON_UNESCAPED_UNICODE)
            );
        }
        
        // Efetua o envio de todas as requisições em lote de forma paralela
        foreach ($this->webPush->flush() as $report) {
            if ($report->isSuccess()) {
                $results['sent']++;
            } else {
                $results['failed']++;
                
                // Exclui a assinatura do banco de dados caso seja identificada como inválida ou expirada
                $endpoint = $report->getEndpoint();
                if ($endpoint) {
                    PushSubscription::where('endpoint', $endpoint)->delete();
                }
            }
        }
        
        return $results;
    }
}
