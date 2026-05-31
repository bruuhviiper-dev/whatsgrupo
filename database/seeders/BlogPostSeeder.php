<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        // Limpa posts anteriores
        BlogPost::truncate();

        $posts = [
            [
                'title' => 'Como entrar em um grupo de WhatsApp pelo link?',
                'meta_description' => 'Aprenda o passo a passo simples de como entrar em grupos de WhatsApp públicos e privados utilizando links de convites no celular ou computador.',
                'content' => '### O que é um Link de Convite?
Um link de convite do WhatsApp é uma URL oficial gerada pelo administrador de um grupo que permite que qualquer pessoa com acesso ao link entre no grupo diretamente, sem precisar que o administrador adicione o número manualmente.

### Passo a passo para entrar pelo Celular (Android ou iPhone):
1. **Clique no link**: Toque no link de convite (normalmente no formato `https://chat.whatsapp.com/codigo`).
2. **Abra o aplicativo**: O celular detectará o link automaticamente e solicitará a abertura do WhatsApp.
3. **Visualize as informações**: Uma janela pop-up será exibida contendo a imagem do grupo, o nome do grupo e o número de participantes.
4. **Confirme a entrada**: Toque no botão verde "Entrar no Grupo" para fazer parte da comunidade!

### Passo a passo para entrar pelo Computador (WhatsApp Web/Desktop):
1. **Clique no link**: Clique no convite do grupo usando seu navegador.
2. **Direcionamento**: O navegador abrirá a página oficial do WhatsApp com o botão "Entrar na conversa".
3. **Sincronização**: Ao clicar, o WhatsApp Web ou o aplicativo do computador será carregado mostrando as informações do grupo.
4. **Confirmação**: Confirme a entrada para começar a interagir!

> **Dica de Segurança**: Só entre em grupos cujos temas e assuntos sejam do seu interesse. Lembre-se que em grupos públicos o seu número de telefone pode ficar visível para outros membros da comunidade.',
            ],
            [
                'title' => 'O que é e como funcionam os Canais do WhatsApp?',
                'meta_description' => 'Descubra a nova funcionalidade de Canais do WhatsApp. Entenda a diferença para os grupos tradicionais e saiba como seguir e receber novidades de forma anônima.',
                'content' => '### O que são os Canais do WhatsApp?
Os **Canais do WhatsApp** são uma ferramenta de transmissão unidirecional (de um para muitos), onde os administradores podem enviar mensagens de texto, fotos, vídeos, figurinhas e enquetes para um número ilimitado de seguidores. Eles ficam localizados na aba "Atualizações" do aplicativo, separados das suas conversas diárias.

### Como eles funcionam?
- **Unidirecional**: Apenas os criadores do canal podem enviar mensagens. Os seguidores podem apenas ler, reagir com emojis e votar em enquetes.
- **Privacidade Total**: A privacidade é uma das maiores vantagens dos canais. Os seguidores não conseguem ver o número de telefone do administrador, nem de outros seguidores. O administrador também não tem acesso ao seu número de telefone, a menos que você já o tenha adicionado aos seus contatos.
- **Histórico Limitado**: O WhatsApp mantém o histórico das mensagens nos servidores por apenas 30 dias, liberando espaço no dispositivo de forma inteligente.

### Como encontrar e seguir Canais?
1. Abra o WhatsApp e vá até a aba **Atualizações** (antigo Status).
2. Role até a seção de Canais e toque em **Encontrar Canais** ou no ícone de "+".
3. Você pode pesquisar canais de notícias, marcas, criadores de conteúdo e times de futebol.
4. Clique no ícone de "+" ao lado do canal para começar a segui-lo.
5. Lembre-se de ativar o **sininho de notificações** no topo do canal para ser avisado sempre que novas novidades forem postadas!',
            ],
            [
                'title' => 'Como criar um grupo de WhatsApp de sucesso em 2026?',
                'meta_description' => 'Dicas essenciais e práticas de marketing, engajamento e moderação para criar e gerenciar um grupo de WhatsApp altamente engajado e de sucesso.',
                'content' => '### Passos fundamentais para criar um grupo de sucesso:

1. **Defina um Tema Claro e Focado**:
   Grupos genéricos tendem a flopar rapidamente. Escolha um nicho bem definido (ex: "Apostas de Futebol", "Figurinhas Engraçadas", "Devs Laravel Brasil"). Quanto mais específico for o tema, mais engajados serão os membros.

2. **Crie uma Identidade Visual Atraente**:
   Coloque um avatar/imagem nítido e profissional. O nome do grupo deve ser autoexplicativo e conter no máximo 100 caracteres. Adicione uma descrição detalhada nas configurações para situar os novos membros.

3. **Estabeleça Regras Claras**:
   Mantenha a integridade da comunidade deixando as regras logo no topo ou na mensagem de boas-vindas. Termos como "Proibido SPAM", "Respeitar membros" e "Sem conteúdo adulto" devem ser rigorosamente aplicados.

4. **Divulgue no local correto**:
   Para conseguir os primeiros 100 membros rapidamente, cadastre o link do seu grupo gratuitamente no **WhatsGrupos**. Nosso diretório entrega tráfego orgânico segmentado para centenas de administradores diariamente!

5. **Tenha Moderação Ativa**:
   Ninguém gosta de grupos abandonados cheios de spam e discussões ofensivas. Nomeie administradores de confiança para banir perfis indesejados de forma ágil e manter o papo saudável.',
            ],
            [
                'title' => 'Quais as diferenças entre Grupos, Canais e Comunidades no WhatsApp?',
                'meta_description' => 'Entenda de uma vez por todas as diferenças entre Grupos, Canais e Comunidades no WhatsApp para escolher o melhor formato para a sua necessidade.',
                'content' => 'O WhatsApp oferece diferentes formatos para conectar pessoas. Entenda a diferença e escolha a melhor opção para a sua audiência:

### 1. Grupos de WhatsApp
- **Propósito**: Conversas interativas e bidirecionais entre pessoas com interesses em comum.
- **Capacidade**: Até 1024 membros por grupo.
- **Interação**: Todos os membros podem enviar mensagens (a menos que o administrador restrinja para apenas admins).
- **Privacidade**: Média. Os números de telefone de todos os membros ficam visíveis na lista de participantes.

### 2. Canais de WhatsApp
- **Propósito**: Transmissão unidirecional de informações e novidades por marcas, veículos de mídia e influenciadores.
- **Capacidade**: Ilimitada. Não há limite de seguidores.
- **Interação**: Apenas administradores enviam mensagens; seguidores reagem com emojis ou votam em enquetes.
- **Privacidade**: Altíssima. Os números de telefone e dados dos seguidores e do criador são 100% ocultos de outros membros.

### 3. Comunidades do WhatsApp
- **Propósito**: Organizar e centralizar múltiplos grupos sob uma mesma estrutura (ex: uma escola contendo um grupo para cada sala).
- **Capacidade**: Pode conter até 100 grupos vinculados e um grupo geral de avisos de até 2000 membros.
- **Interação**: O grupo de avisos da comunidade é restrito aos administradores. Os subgrupos vinculados funcionam como conversas normais.
- **Privacidade**: Alta. O número dos membros fica oculto no grupo de avisos, aparecendo apenas para administradores.',
            ],
            [
                'title' => 'É seguro entrar em grupos de WhatsApp públicos?',
                'meta_description' => 'Esclarecemos os mitos e verdades sobre a segurança ao participar de grupos de WhatsApp de acesso público e compartilhamos dicas vitais para proteger sua privacidade.',
                'content' => 'Entrar em grupos públicos de WhatsApp é uma excelente forma de fazer novos amigos, aprender novos temas e expandir redes de contatos. No entanto, é importante manter cuidados essenciais com a sua privacidade e segurança de dados:

### Os Riscos Mais Comuns:
- **Exposição do Número de Telefone**: Ao entrar em um grupo, qualquer participante pode visualizar seu número de telefone e nome na lista de membros. Isso abre margem para abordagens invasivas no privado.
- **Links Maliciosos e Golpes**: Pessoas mal-intencionadas podem enviar mensagens contendo links de phishing ou golpes prometendo dinheiro fácil, pix ou prêmios.
- **Exposição a Arquivos Nocivos**: Membros podem compartilhar links de downloads ou arquivos executáveis contendo malware ou vírus.

### 5 Dicas Vitais para se Proteger em Grupos:
1. **Não clique em links desconhecidos**: Nunca clique em links que prometem vantagens financeiras absurdas, promoções imperdíveis ou que pedem seus dados pessoais.
2. **Configure sua privacidade**: No WhatsApp, vá em Configurações > Privacidade e restrinja quem pode ver sua foto de perfil, "visto por último", "recado" e o recurso de "adicionar a grupos" apenas para **Meus Contatos**.
3. **Denuncie e Bloqueie Spammers**: Se algum membro do grupo te chamar no privado de forma inconveniente ou oferecendo golpes, bloqueie-o imediatamente e faça a denúncia para o WhatsApp.
4. **Cuidado com o que compartilha**: Nunca divulgue senhas, códigos de ativação, endereço ou dados financeiros no chat do grupo.
5. **Use o WhatsGrupos**: Nosso diretório faz varreduras e moderações para remover grupos perigosos, spam ou inativos, proporcionando um ambiente muito mais limpo para você encontrar novas comunidades.',
            ],
            [
                'title' => 'Como divulgar meu grupo de WhatsApp para conseguir participantes?',
                'meta_description' => 'Estratégias gratuitas e pagas de alta conversão para divulgar links de convite de grupos de WhatsApp e atrair centenas de novos membros de forma rápida.',
                'content' => 'Se você criou um grupo recentemente e quer fazê-lo crescer de forma rápida e qualificada, siga estas estratégias comprovadas:

### 1. Cadastre-se em Diretórios Especializados (Recomendado)
A forma mais rápida de obter tráfego orgânico gratuito e constante é cadastrando o link de convite do seu grupo no **WhatsGrupos**. Nossa plataforma recebe milhares de acessos diários de pessoas buscando grupos exatamente da sua área de atuação. O cadastro leva menos de 2 minutos e trará novos membros de forma totalmente automatizada!

### 2. Utilize Redes Sociais Estrategicamente
- **TikTok e Instagram Reels**: Crie vídeos curtos dinâmicos apresentando os benefícios do seu grupo ou mostrando o conteúdo legal que você compartilha lá. Coloque o link de convite na bio do seu perfil.
- **Pinterest**: Publique pins atraentes visualmente com links diretos para o seu grupo de WhatsApp.
- **Twitter / X**: Busque por palavras-chave relacionadas ao seu grupo e interaja nas discussões anexando seu convite de forma sutil.

### 3. Seja VIP e Destaque seu Link
No **WhatsGrupos**, você pode impulsionar suas chances de sucesso adquirindo nossos pacotes VIP de baixo custo. O seu grupo ficará fixado de forma destacada no topo da página inicial e da categoria selecionada, atraindo cliques premium com alta taxa de conversão!

### 4. Trocas de Divulgação
Faça parcerias com outros administradores de grupos com temas parecidos (mas não concorrentes) e façam indicações mútuas no chat das comunidades uma vez por semana.',
            ],
            [
                'title' => 'Como reaver o link de um grupo do qual sou administrador?',
                'meta_description' => 'Guia rápido de como localizar, copiar, compartilhar ou redefinir (revogar) o link de convite oficial de um grupo do qual você possui cargo de administrador.',
                'content' => 'Se você é o criador ou administrador de um grupo de WhatsApp e precisa do link de convite oficial para divulgar em sites como o **WhatsGrupos**, siga este tutorial simples:

### Como encontrar o link no Celular:
1. Abra a conversa do grupo do qual você é administrador.
2. Toque no **nome do grupo** no topo da tela para abrir os detalhes.
3. Role a página para baixo até a seção de participantes.
4. Toque na opção **Convidar via link**.
5. Aqui você terá quatro opções:
   - **Enviar link via WhatsApp**: Compartilha diretamente para conversas recentes.
   - **Copiar link**: Salva a URL na área de transferência (ideal para colar no WhatsGrupos).
   - **Compartilhar link**: Envia para outros aplicativos no seu celular.
   - **Redefinir link**: Cancela o link antigo e cria um totalmente novo.

### Atenção: Quando redefinir (revogar) o link?
Se o seu grupo começar a receber spammers, bots invasivos ou pessoas indesejadas, você deve usar a opção **Redefinir link**. O WhatsApp desativará instantaneamente o convite antigo e gerará um novo hash. Lembre-se de atualizar o novo link no WhatsGrupos para que novas pessoas continuem entrando na sua comunidade!',
            ],
            [
                'title' => 'Como silenciar ou sair de um grupo de WhatsApp silenciosamente?',
                'meta_description' => 'Dicas úteis de usabilidade no WhatsApp para silenciar notificações barulhentas ou sair de grupos sem disparar alertas constrangedores no chat.',
                'content' => 'Às vezes participamos de grupos que se tornam excessivamente barulhentos ou que fogem das nossas prioridades diárias. O WhatsApp dispõe de ótimas ferramentas para gerenciar essas interações sem constrangimentos:

### Como silenciar notificações de um grupo para sempre:
Se você quer continuar no grupo, mas não quer que seu celular apite a cada nova mensagem, você pode silenciá-lo:
1. Abra o grupo e toque no **nome do grupo** no topo.
2. Ative a chave **Silenciar notificações** (ou toque em Notificações > Silenciar).
3. Selecione o período desejado: **8 horas**, **1 semana** ou **Sempre**.
4. Desmarque a opção "Exibir notificações" caso queira que o grupo sequer acenda a tela do celular.

### Como sair de um grupo silenciosamente:
Desde atualizações recentes do WhatsApp, **sair de um grupo não notifica mais todo o chat** com aquela mensagem clássica "Fulano saiu do grupo". Agora, apenas os administradores serão avisados sobre a sua saída:
1. Abra o grupo indesejado.
2. Toque nos três pontinhos no topo direito (ou no nome do grupo).
3. Selecione a opção **Mais** > **Sair do grupo** (ou role os detalhes do grupo e toque em **Sair do grupo**).
4. Confirme tocando em **Sair**.
5. Pronto! Você sairá da comunidade de forma discreta e tranquila.',
            ],
            [
                'title' => 'O que é o WhatsGrupos e como ele funciona?',
                'meta_description' => 'Conheça o WhatsGrupos: o principal diretório brasileiro para encontrar, pesquisar e cadastrar links de convites de grupos e canais públicos do WhatsApp.',
                'content' => 'O **WhatsGrupos** é um serviço web gratuito de utilidade pública que atua como um grande catálogo e motor de busca para links de convites do WhatsApp no Brasil. Nossa missão é conectar administradores de comunidades que buscam crescimento com usuários que procuram grupos e canais qualificados e ativos.

### Como funciona para quem busca grupos?
- **Fácil Navegação**: Nós organizamos milhares de grupos cadastrados em mais de 40 categorias claras (Amizades, Esportes, Games, Negócios, Religião, etc.).
- **Barra de Busca Dinâmica**: Você pode pesquisar termos de interesse e encontrar grupos perfeitamente focados no que procura.
- **Qualidade Garantida**: Nosso validador de links inteligente faz limpezas periódicas nos bancos de dados para inativar links expirados ou mortos de forma autônoma.

### Como funciona para quem tem grupos?
- **Envio Simples**: Qualquer administrador pode cadastrar seu grupo gratuitamente na aba "Enviar Grupo".
- **Auto-detecção**: Ao colar a URL do grupo, nosso validador via Python acessa os dados e preenche automaticamente o título e imagem de perfil do WhatsApp para você, garantindo mais credibilidade!
- **Sistema de Impulso VIP**: Quer crescer dez vezes mais rápido? Nosso site oferece pacotes VIP que fixam seu grupo no topo da homepage e das categorias, maximizando cliques de entrada.',
            ],
            [
                'title' => 'Como denunciar grupos de WhatsApp com conteúdo ilegal ou ofensivo?',
                'meta_description' => 'Aprenda a fazer denúncias formais de grupos ou canais do WhatsApp que violam os Termos de Serviço ou divulgam conteúdos ilegais ou nocivos.',
                'content' => 'O WhatsApp preza pela segurança digital de sua rede de usuários. Se você entrou em um grupo que divulga conteúdo abusivo, violento, golpes financeiros, assédio ou qualquer atividade ilegal, denuncie imediatamente:

### 1. Como denunciar um grupo diretamente no aplicativo:
1. Abra a conversa do grupo infrator.
2. Toque no **nome do grupo** no topo para exibir a página de detalhes.
3. Role até o final da página e toque na opção vermelha **Denunciar grupo** (ou Denunciar canal).
4. Uma caixa de diálogo perguntará se você deseja também sair do grupo e bloquear os membros. Recomendamos marcar a caixa.
5. Toque em **Denunciar**. O WhatsApp receberá o histórico das últimas 5 mensagens enviadas no grupo para análise interna da equipe de segurança.

### 2. Como denunciar um grupo cadastrado no WhatsGrupos:
Se você encontrou um link impróprio em nosso diretório que passou pela nossa moderação inicial, ajude a nossa comunidade a se manter limpa:
1. Vá até a página de **Contato** do WhatsGrupos (link no rodapé).
2. Preencha seu nome e e-mail.
3. Selecione o assunto **Denúncia**.
4. Insira no campo de mensagem o link do grupo ou o nome dele e descreva a atividade ilícita praticada.
5. Nossa equipe de moderação humana analisará e removerá o grupo permanentemente do nosso diretório em até 12 horas úteis.',
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::updateOrCreate(
                ['slug' => Str::slug($post['title'])],
                [
                    'title'            => $post['title'],
                    'meta_description' => $post['meta_description'],
                    'content'          => $post['content'],
                    'views'            => rand(50, 450), // Visualizações iniciais aleatórias
                    'is_published'     => true,
                    'created_at'       => now()->subDays(rand(1, 30)),
                    'updated_at'       => now(),
                ]
            );
        }
    }
}
