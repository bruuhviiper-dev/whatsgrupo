@props(['category'])

@php
    $nome     = $category->name;
    $nomeLower = Str::lower($nome);

    // Perguntas mistas: específicas da categoria + genéricas sobre WhatsApp.
    // A mistura captura queries informacionais de alto volume ("como criar grupo",
    // "quantas pessoas", etc.) e queries de nicho ("grupos de futebol", etc.).
    $faqs = [
        [
            'q' => 'Como entrar em grupos de WhatsApp de ' . $nome . '?',
            'a' => 'Navegue pela lista acima, escolha o grupo de ' . $nomeLower . ' que mais te interessa e clique em "Entrar no Grupo". Você será redirecionado para o site oficial do WhatsApp, que abrirá o aplicativo no seu celular ou computador de forma segura e instantânea.',
        ],
        [
            'q' => 'Os grupos de ' . $nome . ' são gratuitos?',
            'a' => 'Sim, todos os grupos listados no WhatsGrupos são completamente gratuitos. Não há nenhum custo para entrar ou participar. Basta clicar no link de convite e aceitar o convite no WhatsApp.',
        ],
        [
            'q' => 'Como criar um grupo de WhatsApp de ' . $nome . '?',
            'a' => 'No aplicativo do WhatsApp, toque em "Nova conversa" e depois em "Novo grupo". Selecione os primeiros participantes, defina um nome relacionado a ' . $nomeLower . ' e adicione uma foto. Depois, abra o grupo, vá em "Convidar via link" e copie o link. Cadastre esse link aqui no WhatsGrupos para alcançar milhares de pessoas interessadas em ' . $nomeLower . '.',
        ],
        [
            'q' => 'Como divulgar meu grupo de ' . $nome . ' no WhatsGrupos?',
            'a' => 'Clique em "Enviar Grupo" no menu, cole o link de convite do seu grupo de ' . $nomeLower . ', escolha a categoria "' . $nome . '", escreva um nome e uma descrição atraentes e confirme. O cadastro é gratuito e o grupo é publicado após moderação (geralmente em até 48 horas).',
        ],
        [
            'q' => 'Quantas pessoas cabem em um grupo de WhatsApp?',
            'a' => 'Atualmente um grupo de WhatsApp comporta até 1.024 participantes. Para audiências maiores, o WhatsApp oferece os Canais, que permitem alcançar seguidores ilimitados em modo de transmissão. No WhatsGrupos você encontra tanto grupos quanto canais sobre ' . $nomeLower . '.',
        ],
        [
            'q' => 'Como sair de um grupo de WhatsApp de ' . $nome . '?',
            'a' => 'Abra o grupo no WhatsApp, toque no nome do grupo no topo para ver as informações, role até o final e toque em "Sair do grupo". Se você for administrador, designe outro administrador antes de sair para que a comunidade de ' . $nomeLower . ' continue ativa.',
        ],
        [
            'q' => 'Os links de grupos de ' . $nome . ' são verificados?',
            'a' => 'Sim. O WhatsGrupos valida automaticamente os links de convite antes de publicar e remove periodicamente os links expirados ou inválidos. Todos os grupos listados passaram por moderação para garantir que sejam comunidades legítimas sobre ' . $nomeLower . '.',
        ],
        [
            'q' => 'Qual a diferença entre grupo e canal de WhatsApp?',
            'a' => 'No grupo todos os participantes podem conversar entre si (até 1.024 pessoas). Já o canal é uma ferramenta de transmissão de mão única: apenas o administrador publica e os seguidores recebem as atualizações, sem limite de seguidores. Você encontra ambos os formatos sobre ' . $nomeLower . ' aqui no WhatsGrupos.',
        ],
    ];
@endphp

<!-- FAQ de {{ $nome }} — visível na página (exigência do Google para rich results FAQPage) -->
<section class="mt-10 p-6 md:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm"
         x-data="{ faqOpen: null }">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-widest mb-3 inline-block">
                Dúvidas Frequentes
            </span>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">
                Perguntas sobre Grupos de WhatsApp de {{ $nome }}
            </h2>
        </div>

        <div class="space-y-3">
            @foreach($faqs as $i => $faq)
            <div class="border-b border-slate-100 pb-3 last:border-0">
                <button @click="faqOpen === {{ $i }} ? faqOpen = null : faqOpen = {{ $i }}"
                        class="w-full flex justify-between items-center text-left font-bold text-slate-800 hover:text-[#25D366] transition-colors text-sm">
                    <span>{{ $faq['q'] }}</span>
                    <x-heroicon-m-chevron-down
                        class="w-4 h-4 text-slate-400 transition-transform shrink-0 ml-3"
                        x-bind:class="faqOpen === {{ $i }} ? 'rotate-180 text-[#25D366]' : ''" />
                </button>
                <div x-show="faqOpen === {{ $i }}" x-collapse
                     class="mt-2 text-sm text-slate-500 leading-relaxed"
                     style="display: none;">
                    {{ $faq['a'] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- JSON-LD FAQPage — alimentado pelas mesmas perguntas acima (exigência do Google) --}}
<x-seo.faq :faqs="$faqs" />
