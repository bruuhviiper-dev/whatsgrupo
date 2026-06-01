(function() {
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

    var scriptUrl = new URL(currentScript.src);
    var category = scriptUrl.searchParams.get('category') || 'all';
    var rawWidth  = scriptUrl.searchParams.get('width')  || '100%';
    var rawHeight = scriptUrl.searchParams.get('height') || '490';

    // Garante unidade CSS válida
    var width  = /^\d+$/.test(rawWidth)  ? rawWidth  + 'px' : rawWidth;
    var height = /^\d+$/.test(rawHeight) ? rawHeight + 'px' : rawHeight;

    var widgetUrl = "{{ url('/widget') }}/" + encodeURIComponent(category);

    var iframe = document.createElement('iframe');
    iframe.src = widgetUrl;
    iframe.style.width           = width;
    iframe.style.height          = height;
    iframe.style.border          = 'none';
    iframe.style.overflow        = 'hidden';
    iframe.style.borderRadius    = '16px';
    iframe.style.boxShadow       = '0 8px 24px rgba(0, 0, 0, 0.10)';
    iframe.style.backgroundColor = '#F8FAFC';
    iframe.style.display         = 'block';
    iframe.setAttribute('scrolling', 'no');
    iframe.setAttribute('frameborder', '0');
    iframe.setAttribute('allowtransparency', 'true');
    iframe.setAttribute('loading', 'lazy');

    var container = document.createElement('div');
    container.className        = 'whatsgrupos-widget-container';
    container.style.width      = '100%';
    container.style.maxWidth   = '720px';
    container.style.margin     = '12px auto';
    container.appendChild(iframe);

    currentScript.parentNode.insertBefore(container, currentScript);
})();
