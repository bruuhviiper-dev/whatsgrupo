@props(['faqs' => []])
{{--
  FAQPage JSON-LD. Recebe faqs = [['q' => 'pergunta', 'a' => 'resposta em texto'], ...].
  As perguntas/respostas DEVEM estar visíveis na página (exigência do Google) — este
  componente apenas emite o structured data correspondente ao conteúdo já renderizado.
--}}
@php
    $entities = [];
    foreach ($faqs as $faq) {
        $q = trim($faq['q'] ?? '');
        $a = trim($faq['a'] ?? '');
        if ($q === '' || $a === '') {
            continue;
        }
        $entities[] = [
            '@type'          => 'Question',
            'name'           => $q,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => $a,
            ],
        ];
    }
@endphp
@if(count($entities))
<script type="application/ld+json">
{!! json_encode([
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => $entities,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif
