#!/usr/bin/env python3
"""
Verificador de links mortos
Recebe JSON array de {id, link} via stdin
Retorna JSON array de {id, is_active, status_code}
"""
import sys
import json
import urllib.request
import urllib.error

def check_link(group: dict) -> dict:
    try:
        req = urllib.request.Request(
            group['link'],
            headers={'User-Agent': 'WhatsApp/2.24.5.77 A'},
            method='HEAD'
        )
        with urllib.request.urlopen(req, timeout=8) as r:
            code = r.status
            # WhatsApp retorna 200 mesmo para links expirados
            # A classe de serviço Laravel ou a lógica do scraping no PHP pode lidar,
            # mas o Python retorna se o HTTP Code é 200.
            return {'id': group['id'], 'is_active': code == 200, 'status_code': code}
    except urllib.error.HTTPError as e:
        return {'id': group['id'], 'is_active': False, 'status_code': e.code}
    except Exception:
        return {'id': group['id'], 'is_active': False, 'status_code': 0}

if __name__ == '__main__':
    try:
        input_data = sys.stdin.read()
        groups = json.loads(input_data) if input_data else []
    except Exception:
        groups = []
        
    results = [check_link(g) for g in groups]
    print(json.dumps(results))
