(function() {
    // Busca a tag do script atual de forma robusta
    var currentScript = document.currentScript || (function() {
        var scripts = document.getElementsByTagName('script');
        for (var i = scripts.length - 1; i >= 0; i--) {
            if (scripts[i].src && scripts[i].src.indexOf('widget.js') !== -1) {
                return scripts[i];
            }
        }
        return null;
    })();

    if (!currentScript) return;

    // Analisa a URL do script para obter parâmetros de personalização
    var scriptUrl = new URL(currentScript.src);
    var category = scriptUrl.searchParams.get('category') || 'all';
    
    // Configurações de tamanho responsivo parametrizáveis
    var width = scriptUrl.searchParams.get('width') || '100%';
    var height = scriptUrl.searchParams.get('height') || '490px';

    // Rota absoluta do widget no Laravel WhatsGrupos
    var widgetUrl = "{{ url('/widget') }}/" + encodeURIComponent(category);

    // Cria o iframe do widget
    var iframe = document.createElement('iframe');
    iframe.src = widgetUrl;
    iframe.style.width = width;
    iframe.style.height = height;
    iframe.style.border = 'none';
    iframe.style.overflow = 'hidden';
    iframe.style.borderRadius = '16px';
    iframe.style.boxShadow = '0 12px 36px rgba(0, 0, 0, 0.6)';
    iframe.style.backgroundColor = '#0F0F1A';
    iframe.style.display = 'block';
    iframe.setAttribute('scrolling', 'no');
    iframe.setAttribute('frameborder', '0');
    iframe.setAttribute('allowtransparency', 'true');

    // Cria um container wrapper elegante para controle responsivo adicional
    var container = document.createElement('div');
    container.className = 'whatsgrupos-widget-container';
    container.style.width = '100%';
    container.style.maxWidth = '720px';
    container.style.margin = '12px auto';
    container.style.padding = '0';
    container.appendChild(iframe);

    // Insere o container antes do script atual
    currentScript.parentNode.insertBefore(container, currentScript);
})();
