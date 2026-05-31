#!/usr/bin/env python3
# -*- coding: utf-8 -*-
# Script Python autônomo e robusto para validar links de grupos e canais do WhatsApp.
# Recebe o link como argumento via sys.argv[1] e retorna um JSON no stdout.
# Utiliza apenas a biblioteca padrão do Python.

import sys
import json
import re
import urllib.request
import urllib.error
import html as html_parser

def validate(link):
    """
    Valida o formato do link e tenta obter metadados do grupo via scraping simples.
    Retorna sempre um JSON contendo: valid, name, image, error.
    """
    # Expressão regular flexível para aceitar grupos (chat.whatsapp.com com /invite/ ou /v/ opcionais) e canais (whatsapp.com/channel)
    pattern = r'^https://(chat\.whatsapp\.com/(?:invite/|v/|v=)?(?:[A-Za-z0-9_-]{10,})|whatsapp\.com/channel/(?:[A-Za-z0-9@_-]{10,}))$'

    if not re.match(pattern, link):
        return {
            'valid': False,
            'name': None,
            'image': None,
            'error': 'Formato inválido. Use: https://chat.whatsapp.com/CODIGO ou https://whatsapp.com/channel/CODIGO'
        }

    try:
        # Simula o User-Agent do WhatsApp para que os metadados do Open Graph sejam retornados corretamente
        req = urllib.request.Request(
            link,
            headers={
                'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36 WhatsApp/2.24.5.77 A'
            }
        )

        with urllib.request.urlopen(req, timeout=10) as response:
            html_content = response.read().decode('utf-8', errors='ignore')

        # Buscar todas as tags <meta ...> de forma robusta e case-insensitive
        meta_tags = re.findall(r'<meta\s+([^>]+)>', html_content, re.IGNORECASE)

        name = None
        image = None

        for meta in meta_tags:
            # Verifica se property ou name é og:title ou og:image
            is_title = re.search(r'(property|name)\s*=\s*["\']\s*og:title\s*["\']', meta, re.IGNORECASE)
            is_image = re.search(r'(property|name)\s*=\s*["\']\s*og:image\s*["\']', meta, re.IGNORECASE)

            if is_title:
                content_match = re.search(r'content\s*=\s*["\'](.*?)["\']', meta, re.IGNORECASE)
                if content_match:
                    name = content_match.group(1)
            elif is_image:
                content_match = re.search(r'content\s*=\s*["\'](.*?)["\']', meta, re.IGNORECASE)
                if content_match:
                    image = content_match.group(1)

        # Se og:title não for encontrado, tenta usar a tag <title> como fallback
        if not name:
            title_tag = re.search(r'<title>(.*?)</title>', html_content, re.IGNORECASE | re.DOTALL)
            if title_tag:
                name = title_tag.group(1).strip()
                # Limpa sufixos padrões de convite do WhatsApp
                name = re.sub(r'\s*-\s*WhatsApp\s*Group\s*Invite', '', name, flags=re.IGNORECASE)
                name = re.sub(r'\s*-\s*Convite\s*de\s*grupo\s*do\s*WhatsApp', '', name, flags=re.IGNORECASE)

        # Decodifica entidades HTML que possam vir nos metadados
        if name:
            name = html_parser.unescape(name).strip()
        if image:
            image = html_parser.unescape(image).strip()

        # Garante que um nome padrão seja retornado caso não consiga extrair
        if not name:
            name = "Grupo do WhatsApp"

        return {
            'valid': True,
            'name': name,
            'image': image,
            'error': None
        }

    except urllib.error.HTTPError as e:
        if e.code == 404:
            return {
                'valid': False,
                'name': None,
                'image': None,
                'error': 'Grupo inexistente, privado ou link de convite expirado (Erro 404).'
            }
        return {
            'valid': False,
            'name': None,
            'image': None,
            'error': f'Erro HTTP {e.code} ao acessar o link.'
        }
    except urllib.error.URLError as e:
        return {
            'valid': False,
            'name': None,
            'image': None,
            'error': f'Não foi possível conectar ao WhatsApp (Erro: {e.reason}).'
        }
    except Exception as e:
        return {
            'valid': False,
            'name': None,
            'image': None,
            'error': f'Erro ao processar validação: {str(e)}'
        }

if __name__ == '__main__':
    # Força a saída padrão a usar UTF-8 para evitar erros de encodificação com emojis no Windows
    if hasattr(sys.stdout, 'reconfigure'):
        sys.stdout.reconfigure(encoding='utf-8')
        
    # Obtém o link do primeiro argumento da linha de comando
    link = sys.argv[1] if len(sys.argv) > 1 else ''
    result = validate(link)
    # Imprime o resultado como JSON no stdout para consumo do Laravel
    print(json.dumps(result, ensure_ascii=False))
