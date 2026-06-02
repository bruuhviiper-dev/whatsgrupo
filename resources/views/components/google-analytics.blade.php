{{--
  Google Analytics (GA4) — renderizado em TODAS as páginas via layouts.
  Prioridade: script completo colado no admin; senão, gera gtag.js a partir do
  Measurement ID. Só aparece quando habilitado e configurado no painel admin
  (Configurações → Google Analytics).
--}}
@php
    $gaEnabled = \App\Models\Setting::googleAnalyticsEnabled();
    $gaId      = trim((string) \App\Models\Setting::googleAnalyticsId());
    $gaScript  = trim((string) \App\Models\Setting::googleAnalyticsScript());
@endphp
@if($gaEnabled && ($gaScript !== '' || $gaId !== ''))
    @if($gaScript !== '')
        {!! $gaScript !!}
    @else
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif
@endif
