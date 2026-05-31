@php
    use App\Models\Setting;
    $enabled       = Setting::adsenseEnabled();
    $clientId      = Setting::adsenseClientId();
    $blockedRoutes = ['pages.termos', 'pages.privacidade', 'pages.faq', 'pages.contato'];
    $isBlocked     = request()->routeIs($blockedRoutes);
    $slot_id       = $attributes->get('slot', '');
    $format        = $attributes->get('format', 'auto');
    $layout        = $attributes->get('layout', '');
@endphp

@if(!$isBlocked && $enabled && $clientId)
    {{-- Banner AdSense Responsivo (mobile-first) --}}
    <div {{ $attributes->only('class')->merge(['class' => 'w-full my-2 overflow-hidden']) }}>
        <ins class="adsbygoogle"
             style="display:block;width:100%;min-height:50px;"
             data-ad-client="{{ $clientId }}"
             data-ad-slot="{{ $slot_id }}"
             data-ad-format="{{ $format }}"
             @if($layout) data-ad-layout="{{ $layout }}" @endif
             data-full-width-responsive="true"></ins>
        <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
    </div>
@endif
