#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
WhatsGrupos – Analisador de imagem NSFW
════════════════════════════════════════
Recebe uma URL ou path de imagem como argv[1] e retorna JSON:

  {
    "safe":   true/false,
    "score":  0.0-1.0,     # maior score NSFW detectado
    "labels": ["FEMALE_BREAST_EXPOSED", ...],
    "error":  null / "mensagem de erro"
  }

Usa nudenet (ONNX, sem API key, funciona offline após 1ª carga do modelo).
Instalação: pip install nudenet   (modelo baixado automaticamente na 1ª execução)
"""

import sys
import json
import os
import tempfile
import urllib.request
import urllib.error

# ─────────────────────────────────────────────────────────────────────────────
# Labels considerados NSFW explícito (bloqueio direto se score ≥ THRESHOLD)
# ─────────────────────────────────────────────────────────────────────────────
NSFW_EXPLICIT = {
    'FEMALE_BREAST_EXPOSED',
    'FEMALE_GENITALIA_EXPOSED',
    'MALE_GENITALIA_EXPOSED',
    'ANUS_EXPOSED',
}

# Labels intermediários (semi-nudez — bloqueados com threshold mais alto)
NSFW_SEMI = {
    'BUTTOCKS_EXPOSED',
}

THRESHOLD_EXPLICIT = 0.45   # probabilidade mínima para labels explícitos
THRESHOLD_SEMI     = 0.70   # probabilidade mínima para semi-nudez


def _download_to_tmp(url: str) -> str:
    """Baixa uma URL de imagem para arquivo temporário e retorna o path."""
    suffix = '.jpg'
    for ext in ('.png', '.webp', '.gif', '.jpeg'):
        if ext in url.lower():
            suffix = ext
            break

    tmp = tempfile.NamedTemporaryFile(delete=False, suffix=suffix)
    try:
        req = urllib.request.Request(url, headers={
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Referer':    'https://www.whatsapp.com/',
            'Accept':     'image/webp,image/apng,image/*,*/*;q=0.8',
        })
        with urllib.request.urlopen(req, timeout=15) as r:
            tmp.write(r.read())
        tmp.close()
        return tmp.name
    except Exception:
        tmp.close()
        os.unlink(tmp.name)
        raise


def check(image_input: str) -> dict:
    """
    Analisa a imagem e retorna um dicionário com o resultado.
    image_input pode ser uma URL (http/https) ou um path local.
    """
    try:
        from nudenet import NudeDetector
    except ImportError:
        # nudenet não instalado: passa sem bloquear (fail-open seguro)
        return {'safe': True, 'score': 0.0, 'labels': [], 'error': 'nudenet_not_installed'}

    tmp_path = None
    path = image_input

    if image_input.startswith('http'):
        try:
            tmp_path = _download_to_tmp(image_input)
            path = tmp_path
        except Exception as e:
            return {'safe': True, 'score': 0.0, 'labels': [], 'error': f'download_failed: {e}'}

    try:
        detector = NudeDetector()
        detections = detector.detect(path)

        nsfw_hits = []
        for det in detections:
            label = det.get('class', '')
            score = float(det.get('score', 0))
            if label in NSFW_EXPLICIT and score >= THRESHOLD_EXPLICIT:
                nsfw_hits.append((label, score))
            elif label in NSFW_SEMI and score >= THRESHOLD_SEMI:
                nsfw_hits.append((label, score))

        if nsfw_hits:
            max_score = max(s for _, s in nsfw_hits)
            labels    = list({l for l, _ in nsfw_hits})
            return {'safe': False, 'score': round(max_score, 3), 'labels': labels, 'error': None}

        return {'safe': True, 'score': 0.0, 'labels': [], 'error': None}

    except Exception as e:
        # Em caso de erro na análise: passa sem bloquear (fail-open)
        return {'safe': True, 'score': 0.0, 'labels': [], 'error': str(e)}
    finally:
        if tmp_path:
            try:
                os.unlink(tmp_path)
            except Exception:
                pass


if __name__ == '__main__':
    if hasattr(sys.stdout, 'reconfigure'):
        sys.stdout.reconfigure(encoding='utf-8')

    if len(sys.argv) < 2:
        print(json.dumps({'safe': True, 'score': 0.0, 'labels': [], 'error': 'no_input'}))
        sys.exit(0)

    result = check(sys.argv[1])
    print(json.dumps(result, ensure_ascii=False))
