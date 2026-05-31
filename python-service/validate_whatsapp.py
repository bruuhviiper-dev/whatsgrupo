#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
validate_whatsapp.py — Validador robusto de links de grupos/canais do WhatsApp.
Recebe o link como sys.argv[1] e retorna JSON no stdout.

Estratégia de extração de nome e imagem:
  1. Faz request com User-Agent de bot (facebookexternalhit) — retorna og tags completas
  2. Fallback com UA do WhatsApp nativo
  3. Fallback com UA de browser comum
  4. Parseia og:title, og:image com regex robusta (suporta atributos em qualquer ordem)
  5. Fallback para <title> e <img> se og tags não disponíveis
"""

import sys
import json
import re
import ssl
import urllib.request
import urllib.error
import html as html_parser

# ── User-Agents para rotação ──────────────────────────────────────────────────
# O facebookexternalhit faz o WhatsApp retornar og:title e og:image corretamente
USER_AGENTS = [
    # Bot de preview do Facebook/WhatsApp — mais confiável para og tags
    'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
    # WhatsApp nativo
    'WhatsApp/2.24.12.76 A',
    # Telegram preview bot
    'TelegramBot (like TwitterBot)',
    # Browser moderno como último fallback
    'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36',
]

# ── SSL sem verificação (alguns CDNs têm certs problemáticos) ─────────────────
_SSL_CTX = ssl.create_default_context()
_SSL_CTX.check_hostname = False
_SSL_CTX.verify_mode = ssl.CERT_NONE


def _fetch(url: str, user_agent: str, timeout: int = 12) -> str | None:
    """Faz GET na URL com o User-Agent especificado. Retorna HTML ou None."""
    try:
        req = urllib.request.Request(
            url,
            headers={
                'User-Agent': user_agent,
                'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language': 'pt-BR,pt;q=0.9,en-US;q=0.8',
                'Accept-Encoding': 'identity',
                'Connection': 'close',
            }
        )
        with urllib.request.urlopen(req, timeout=timeout, context=_SSL_CTX) as resp:
            raw = resp.read()
            # Detecta charset da resposta
            charset = 'utf-8'
            ct = resp.headers.get('Content-Type', '')
            m = re.search(r'charset=([^\s;]+)', ct, re.IGNORECASE)
            if m:
                charset = m.group(1).strip('"\'')
            return raw.decode(charset, errors='replace')
    except Exception:
        return None


def _extract_meta(html: str, property_name: str) -> str | None:
    """
    Extrai content de uma meta tag com property ou name igual a property_name.
    Suporta atributos em qualquer ordem dentro da tag <meta ...>.
    """
    # Padrão 1: <meta property="og:xxx" content="...">
    # Padrão 2: <meta content="..." property="og:xxx">
    patterns = [
        # property/name antes do content
        rf'<meta\s[^>]*(?:property|name)\s*=\s*["\']?\s*{re.escape(property_name)}\s*["\']?\s[^>]*content\s*=\s*["\']([^"\']*)["\']',
        # content antes do property/name
        rf'<meta\s[^>]*content\s*=\s*["\']([^"\']*)["\'][^>]*(?:property|name)\s*=\s*["\']?\s*{re.escape(property_name)}\s*["\']?',
    ]
    for pat in patterns:
        m = re.search(pat, html, re.IGNORECASE | re.DOTALL)
        if m:
            val = html_parser.unescape(m.group(1)).strip()
            if val:
                return val
    return None


def _clean_name(name: str) -> str:
    """Remove sufixos padrão do WhatsApp e limpa o nome."""
    # Remove sufixos em PT e EN
    suffixes = [
        r'\s*[|–-]\s*WhatsApp\s*Group\s*Invite.*',
        r'\s*[|–-]\s*Convite\s*de\s*grupo\s*do\s*WhatsApp.*',
        r'\s*[|–-]\s*WhatsApp.*',
        r'\s*\|\s*WhatsApp.*',
    ]
    for s in suffixes:
        name = re.sub(s, '', name, flags=re.IGNORECASE).strip()
    return name.strip()


def validate(link: str) -> dict:
    """
    Valida o link e extrai nome + imagem do grupo via múltiplos User-Agents.
    Retorna dict com: valid, name, image, error, warning.
    """
    # ── Validação de formato ──────────────────────────────────────────────────
    pattern = (
        r'^https://'
        r'(?:chat\.whatsapp\.com/(?:invite/|v/|v=)?[A-Za-z0-9_\-]{10,}'
        r'|whatsapp\.com/channel/[A-Za-z0-9@_\-]{10,})$'
    )
    if not re.match(pattern, link):
        return {
            'valid': False,
            'name': None,
            'image': None,
            'error': 'Formato inválido. Use: https://chat.whatsapp.com/CODIGO ou https://whatsapp.com/channel/CODIGO',
            'warning': None,
        }

    name = None
    image = None
    last_error = None
    html_content = None

    # ── Tenta cada User-Agent até obter og:title e og:image ──────────────────
    for ua in USER_AGENTS:
        html_content = _fetch(link, ua)
        if not html_content:
            continue

        # Verifica se a página retornou conteúdo útil (não apenas redirect ou erro)
        if len(html_content) < 200:
            continue

        # Extrai og:title
        og_title = _extract_meta(html_content, 'og:title')
        if og_title:
            name = _clean_name(og_title)

        # Extrai og:image
        og_image = _extract_meta(html_content, 'og:image')
        if og_image and og_image.startswith('http'):
            image = og_image

        # Se extraiu ambos, para aqui
        if name and image:
            break

        # Tenta twitter:title / twitter:image como fallback alternativo
        if not name:
            tw_title = _extract_meta(html_content, 'twitter:title')
            if tw_title:
                name = _clean_name(tw_title)

        if not image:
            tw_image = _extract_meta(html_content, 'twitter:image')
            if tw_image and tw_image.startswith('http'):
                image = tw_image

        if name and image:
            break

    # ── Fallback: extrai <title> se og:title não veio ─────────────────────────
    if not name and html_content:
        m = re.search(r'<title[^>]*>(.*?)</title>', html_content, re.IGNORECASE | re.DOTALL)
        if m:
            raw_title = html_parser.unescape(m.group(1)).strip()
            name = _clean_name(raw_title)

    # ── Erro 404 / grupo inexistente ─────────────────────────────────────────
    if html_content is not None and re.search(
        r'(link.*expirou|link.*expired|grupo.*não.*existe|invalid.*link|404)',
        html_content, re.IGNORECASE
    ):
        # Pode ainda ser válido (link funcional), mas avisa
        last_error = 'Link pode estar expirado ou grupo privado — verifique manualmente.'

    # ── Verifica se pelo menos chegou ao WhatsApp ─────────────────────────────
    if html_content is None:
        return {
            'valid': False,
            'name': None,
            'image': None,
            'error': 'Não foi possível conectar ao WhatsApp. Verifique sua conexão.',
            'warning': None,
        }

    # ── Resultado final ───────────────────────────────────────────────────────
    return {
        'valid': True,
        'name': name or None,          # None se não encontrou (não usa fallback genérico)
        'image': image or None,
        'error': None,
        'warning': last_error,
    }


if __name__ == '__main__':
    if hasattr(sys.stdout, 'reconfigure'):
        sys.stdout.reconfigure(encoding='utf-8')

    link = sys.argv[1] if len(sys.argv) > 1 else ''
    result = validate(link)
    print(json.dumps(result, ensure_ascii=False))
