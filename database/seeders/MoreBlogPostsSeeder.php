<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Support\Str;

class MoreBlogPostsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = BlogCategory::all();

        if ($categories->isEmpty()) {
            return; // No categories found
        }

        $postsData = [
            'Tutoriais' => [
                ['title' => 'Como recuperar mensagens apagadas no WhatsApp sem backup', 'excerpt' => 'Aprenda o passo a passo de como tentar recuperar aquelas mensagens que foram apagadas acidentalmente.', 'keywords' => 'recuperar mensagens, whatsapp sem backup, apagar mensagens'],
                ['title' => 'Guia Completo: Como usar o WhatsApp Web como um profissional', 'excerpt' => 'Descubra atalhos e extensões secretas para dominar o WhatsApp no seu computador.', 'keywords' => 'whatsapp web, atalhos whatsapp, guia whatsapp'],
                ['title' => 'Como criar e gerenciar uma Comunidade no WhatsApp com eficiência', 'excerpt' => 'Tudo que você precisa saber para administrar milhares de membros em comunidades do WhatsApp.', 'keywords' => 'comunidade whatsapp, gerenciar grupos, criar comunidade'],
                ['title' => 'Passo a passo para fazer figurinhas animadas no WhatsApp', 'excerpt' => 'Crie stickers divertidos com movimento usando aplicativos gratuitos no seu celular.', 'keywords' => 'figurinhas animadas, stickers whatsapp, criar figurinhas'],
                ['title' => 'Como proteger seu WhatsApp contra clonagem em 3 passos', 'excerpt' => 'Ative a verificação em duas etapas e garanta a segurança da sua conta de WhatsApp.', 'keywords' => 'segurança whatsapp, clonagem whatsapp, verificação duas etapas'],
                ['title' => 'Como formatar textos no WhatsApp: Negrito, Itálico e Riscado', 'excerpt' => 'Deixe suas mensagens mais dinâmicas aprendendo a formatar o texto no WhatsApp.', 'keywords' => 'formatar texto, negrito whatsapp, itálico'],
            ],
            'Dicas' => [
                ['title' => '5 Segredos do WhatsApp que você provavelmente não conhecia', 'excerpt' => 'Descubra funções ocultas no aplicativo que podem facilitar o seu dia a dia.', 'keywords' => 'segredos whatsapp, funções ocultas, dicas whatsapp'],
                ['title' => 'Como ler mensagens no WhatsApp sem que a outra pessoa saiba', 'excerpt' => 'Dicas e truques para visualizar as mensagens sem ativar o visto azul, mesmo com ele ligado.', 'keywords' => 'ler mensagens escondido, visto azul, privacidade'],
                ['title' => 'Melhores dicas para economizar bateria e dados usando o WhatsApp', 'excerpt' => 'Configure seu aplicativo para não consumir toda a sua internet móvel ou bateria.', 'keywords' => 'economizar dados, bateria whatsapp, configuração'],
                ['title' => 'Como transformar áudios longos em texto automaticamente', 'excerpt' => 'Conheça ferramentas e bots que transcrevem áudios enormes para você ler rapidamente.', 'keywords' => 'transcrever áudio, áudio em texto, bot whatsapp'],
                ['title' => 'Dicas essenciais para vender mais usando o WhatsApp Business', 'excerpt' => 'Aumente as vendas do seu negócio usando respostas rápidas, catálogos e etiquetas.', 'keywords' => 'whatsapp business, vendas whatsapp, marketing'],
                ['title' => 'Como enviar fotos e vídeos em alta qualidade sem perder resolução', 'excerpt' => 'Aprenda a configuração secreta para enviar mídias em HD pelo aplicativo.', 'keywords' => 'fotos hd, vídeos qualidade, enviar foto whatsapp'],
            ],
            'Notícias' => [
                ['title' => 'WhatsApp anuncia nova função de integração com inteligência artificial', 'excerpt' => 'Em breve os usuários poderão interagir com bots de IA diretamente nas conversas do aplicativo.', 'keywords' => 'inteligência artificial, bots whatsapp, novidades meta'],
                ['title' => 'Meta testa recurso de envio de mensagens para outros aplicativos rivais', 'excerpt' => 'Atendendo às normas europeias, WhatsApp se prepara para ser interoperável com apps como Telegram.', 'keywords' => 'interoperabilidade, meta, telegram, whatsapp'],
                ['title' => 'Nova atualização traz edição de mensagens enviadas por até 15 minutos', 'excerpt' => 'Agora você pode corrigir aquele erro de digitação sem precisar apagar a mensagem inteira.', 'keywords' => 'editar mensagens, atualização whatsapp, correção'],
                ['title' => 'WhatsApp agora permite usar duas contas no mesmo aparelho celular', 'excerpt' => 'Fim da necessidade de usar apps clonadores: recurso oficial para múltiplas contas está no ar.', 'keywords' => 'duas contas whatsapp, dual app, novidade'],
                ['title' => 'Bloqueio de prints em fotos de visualização única é implementado', 'excerpt' => 'Maior privacidade: usuários não podem mais tirar captura de tela de mídias temporárias.', 'keywords' => 'bloqueio print, visualização única, privacidade'],
                ['title' => 'WhatsApp atinge marca histórica de 3 bilhões de usuários ativos mensais', 'excerpt' => 'O aplicativo verde da Meta consolida sua posição como o maior mensageiro do planeta.', 'keywords' => 'usuários whatsapp, marca histórica, meta'],
            ],
            'Atualizações' => [
                ['title' => 'Atualização V2.24.1: Novos filtros de conversas chegam aos usuários', 'excerpt' => 'Filtre suas conversas por "Não Lidas", "Pessoais" ou "Empresariais" na tela inicial.', 'keywords' => 'filtros conversas, atualização v2, novidade whatsapp'],
                ['title' => 'Canais do WhatsApp recebem novidades em moderação e enquetes', 'excerpt' => 'Criadores de canais agora possuem mais ferramentas para interagir com o público.', 'keywords' => 'canais whatsapp, enquetes, atualização canais'],
                ['title' => 'Modo escuro do WhatsApp foi aprimorado para telas OLED', 'excerpt' => 'O novo update traz um preto ainda mais profundo, economizando mais bateria em smartphones modernos.', 'keywords' => 'modo escuro, amoled, dark mode whatsapp'],
                ['title' => 'Recurso de "Fixar Mensagem" agora permite fixar até 3 mensagens', 'excerpt' => 'Melhoria importante para a organização de grupos e chats individuais importantes.', 'keywords' => 'fixar mensagem, grupos, organização'],
                ['title' => 'Atualização de segurança: Passkeys agora disponíveis no WhatsApp', 'excerpt' => 'Acesse sua conta usando biometria e dispense as senhas e códigos por SMS tradicionais.', 'keywords' => 'passkeys, biometria, segurança conta'],
                ['title' => 'Mensagens de vídeo instantâneas recebem botão dedicado e controles', 'excerpt' => 'Ficou mais fácil gravar os vídeos curtos redondos nas conversas rápidas.', 'keywords' => 'vídeo instantâneo, mensagem vídeo, update vídeo'],
            ],
            'Comunidade' => [
                ['title' => 'Como ser um bom administrador em grupos do WhatsGrupos', 'excerpt' => 'Regras de ouro para manter seu grupo engajado, livre de spam e sempre crescendo.', 'keywords' => 'administrador, regras grupo, engajamento'],
                ['title' => 'A história de como um grupo do WhatsGrupos ajudou a salvar uma vida', 'excerpt' => 'Conheça o relato emocionante de membros da comunidade que se uniram para uma boa causa.', 'keywords' => 'história, união comunidade, relatos'],
                ['title' => 'Os 10 melhores grupos de amizade que mais cresceram este mês', 'excerpt' => 'Confira o top 10 dos grupos de amizade mais movimentados da nossa plataforma.', 'keywords' => 'top 10 grupos, grupos amizade, crescimento'],
                ['title' => 'Evitando golpes em grupos: dicas de segurança para nossa comunidade', 'excerpt' => 'Série de proteção à comunidade: como identificar e denunciar links maliciosos em grupos.', 'keywords' => 'segurança grupos, evitar golpes, denunciar spam'],
                ['title' => 'Entrevista com os criadores dos maiores grupos de games do Brasil', 'excerpt' => 'Um bate-papo exclusivo sobre a moderação e os desafios de gerenciar milhares de gamers.', 'keywords' => 'entrevista, criadores grupos, gamers'],
                ['title' => 'Como o WhatsGrupos está conectando pessoas com interesses em comum', 'excerpt' => 'Nosso manifesto e os números incríveis de novos relacionamentos gerados na plataforma.', 'keywords' => 'manifesto, comunidade, conexões reais'],
            ],
        ];

        foreach ($categories as $category) {
            $catName = $category->name;
            if (isset($postsData[$catName])) {
                $posts = $postsData[$catName];
                
                foreach ($posts as $index => $post) {
                    $slug = Str::slug($post['title']) . '-' . rand(100, 999);
                    
                    // Create paragraphs based on the excerpt and title
                    $content = "<h2>" . $post['title'] . "</h2>\n";
                    $content .= "<p><strong>" . $post['excerpt'] . "</strong></p>\n";
                    $content .= "<p>O WhatsApp não para de evoluir. E para ficar por dentro dessas inovações, preparamos esse guia completo. Quando o assunto é a comunicação diária, qualquer detalhe faz a diferença.</p>\n";
                    $content .= "<h3>O que você precisa saber</h3>\n";
                    $content .= "<p>Com bilhões de usuários pelo mundo, a plataforma está sempre buscando se adaptar às novas necessidades. " . $post['excerpt'] . " Isso significa que as dinâmicas de " . str_replace(',', ' e', $post['keywords']) . " estão mudando a forma como interagimos.</p>\n";
                    $content .= "<p>Fique de olho em nossas próximas publicações da categoria <strong>{$catName}</strong> para mais informações exclusivas e acompanhe todas as novidades.</p>";

                    BlogPost::create([
                        'blog_category_id' => $category->id,
                        'title'            => $post['title'],
                        'slug'             => $slug,
                        'content'          => $content,
                        'meta_description' => $post['excerpt'],
                        'is_published'     => true,
                        'created_at'       => now()->subDays(rand(1, 30))->subHours(rand(1, 24)),
                        'updated_at'       => now(),
                        'views'            => rand(100, 5000),
                    ]);
                }
            }
        }
    }
}
