@extends('admin.layout')

@section('title', 'Configurações')
@section('page-title', 'Configurações')

@section('content')

<form method="POST" action="{{ route('admin.settings.save') }}" enctype="multipart/form-data">
@csrf

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-base font-black text-slate-900">Google AdSense & Configurações</h2>
        <p class="text-slate-400 text-sm mt-0.5">Gerencie scripts, meta tags, ads.txt e favicon do site.</p>
    </div>
    <button type="submit"
            class="btn btn-green shadow-sm flex-shrink-0" style="padding:10px 20px;font-size:13px;">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Salvar Configurações
    </button>
</div>

{{-- Status rápido --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
    @php
    $statuses = [
        ['AdSense', $settings['adsense_enabled'] == '1', $settings['adsense_enabled'] == '1' ? 'Ativo' : 'Inativo'],
        ['Script', (bool)$settings['adsense_script'], $settings['adsense_script'] ? 'Configurado' : 'Pendente'],
        ['ads.txt', (bool)$settings['adsense_client_id'], $settings['adsense_client_id'] ? 'Pronto' : 'Aguardando ID'],
    ];
    @endphp
    @foreach($statuses as $s)
    <div class="card p-4 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
             style="{{ $s[1] ? 'background:#f0fdf4;' : 'background:#f8fafc;' }}">
            @if($s[1])
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
        </div>
        <div>
            <p class="text-xs font-bold text-slate-700">{{ $s[0] }}</p>
            <p class="text-[11px] font-semibold {{ $s[1] ? 'text-emerald-600' : 'text-slate-400' }}">{{ $s[2] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Ativar/Desativar --}}
<div class="card p-5 mb-4">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-slate-900 text-sm">Habilitar AdSense no Site</h3>
            <p class="text-slate-400 text-xs mt-0.5">Quando desativado, nenhum banner ou script será exibido.</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
            <input type="checkbox" name="adsense_enabled" value="1" class="sr-only peer"
                {{ $settings['adsense_enabled'] == '1' ? 'checked' : '' }}>
            <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 transition-colors duration-200"></div>
            <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
        </label>
    </div>
</div>

{{-- Publisher ID --}}
<div class="card p-5 mb-4">
    <h3 class="font-bold text-slate-900 text-sm mb-0.5">Publisher ID (Client ID)</h3>
    <p class="text-slate-400 text-xs mb-3">Seu ID do Google AdSense no formato <code class="bg-slate-100 px-1.5 py-0.5 rounded text-[10px] font-mono">ca-pub-XXXXXXXXXXXXXXXX</code></p>
    <input type="text" name="adsense_client_id"
           value="{{ old('adsense_client_id', $settings['adsense_client_id']) }}"
           placeholder="ca-pub-6065414081065177"
           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-400/50 focus:border-emerald-400 transition bg-slate-50">
</div>

{{-- Script --}}
<div class="card p-5 mb-4">
    <h3 class="font-bold text-slate-900 text-sm mb-0.5">Script do AdSense</h3>
    <p class="text-slate-400 text-xs mb-3">Cole o snippet <code class="bg-slate-100 px-1.5 py-0.5 rounded text-[10px] font-mono">&lt;script async src="...adsbygoogle.js"&gt;</code> fornecido pelo Google.</p>
    <textarea name="adsense_script" rows="4"
              placeholder='<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-XXXXXXXXXXXXXXXX" crossorigin="anonymous"></script>'
              class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-400/50 focus:border-emerald-400 transition resize-none bg-slate-50">{{ old('adsense_script', $settings['adsense_script']) }}</textarea>
    <p class="text-slate-400 text-[11px] mt-2">Este script é injetado no <code class="bg-slate-100 px-1 rounded">&lt;head&gt;</code> de todas as páginas quando o AdSense está habilitado.</p>
</div>

{{-- Meta Tag --}}
<div class="card p-5 mb-4">
    <h3 class="font-bold text-slate-900 text-sm mb-0.5">Meta Tag de Verificação</h3>
    <p class="text-slate-400 text-xs mb-3">Ex: <code class="bg-slate-100 px-1.5 py-0.5 rounded text-[10px] font-mono">&lt;meta name="google-adsense-account" content="ca-pub-..."&gt;</code></p>
    <textarea name="adsense_meta_tag" rows="3"
              placeholder='<meta name="google-adsense-account" content="ca-pub-XXXXXXXXXXXXXXXX">'
              class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-400/50 focus:border-emerald-400 transition resize-none bg-slate-50">{{ old('adsense_meta_tag', $settings['adsense_meta_tag']) }}</textarea>
</div>

{{-- ads.txt --}}
<div class="card p-5 mb-4">
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div class="flex-1">
            <h3 class="font-bold text-slate-900 text-sm mb-0.5">Arquivo ads.txt</h3>
            <p class="text-slate-400 text-xs mb-3">Gerado automaticamente com base no seu Publisher ID.</p>
            @if($settings['adsense_client_id'])
            <div class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 font-mono text-xs text-slate-700">
                google.com, {{ $settings['adsense_client_id'] }}, DIRECT, f08c47fec0942fa0
            </div>
            @else
            <div class="bg-amber-50 border border-amber-200 rounded-lg px-3 py-2.5 text-xs text-amber-700">
                ⚠️ Preencha o Publisher ID acima para gerar o conteúdo do ads.txt.
            </div>
            @endif
            <p class="text-slate-400 text-[11px] mt-2">Servido em <code class="bg-slate-100 px-1 rounded">/ads.txt</code> automaticamente.</p>
        </div>
        <a href="{{ route('admin.settings.ads-txt') }}"
           class="btn btn-blue flex-shrink-0 {{ !$settings['adsense_client_id'] ? 'opacity-40 pointer-events-none' : '' }}"
           style="padding:8px 16px;font-size:12px;">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Baixar ads.txt
        </a>
    </div>
</div>

{{-- Favicon --}}
<div class="card p-5">
    <h3 class="font-bold text-slate-900 text-sm mb-0.5">Favicon do Site</h3>
    <p class="text-slate-400 text-xs mb-4">Recomendado: 32×32 px, formato .ico ou .png.</p>
    <div class="flex items-center gap-4">
        @if($settings['favicon'])
            <div class="w-10 h-10 rounded-lg border border-slate-200 flex items-center justify-center bg-slate-50 overflow-hidden flex-shrink-0">
                <img src="{{ Storage::disk('public')->url($settings['favicon']) }}" alt="Favicon" class="w-8 h-8 object-contain">
            </div>
        @else
            <div class="w-10 h-10 rounded-lg border border-dashed border-slate-300 flex items-center justify-center bg-slate-50 text-slate-400 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        @endif
        <input type="file" name="favicon" accept="image/*"
               class="text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer">
    </div>
</div>

</form>

@endsection
