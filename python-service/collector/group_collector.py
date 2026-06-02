#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
WhatsGrupos – Coletor Universal v3.0
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Motor de webscraping global para grupos de WhatsApp.

Funcionalidades:
  ✔ Rastreia TODOS os grupos de um site antes de passar pro próximo
  ✔ Deduplicação por hash canônico (imune a variações de URL)
  ✔ Fallback de categoria → "outros" quando não bate com o sistema
  ✔ 3 regras fixas em TODOS os grupos, mesmo sem regras no site
  ✔ Imagem padrão do WhatsApp quando grupo não tem foto
  ✔ Conversão de imagem para WebP delegada ao PHP (GroupCollectorService)
  ✔ Crawling recursivo de paginação e subpáginas dentro do mesmo domínio
  ✔ Buscadores: DuckDuckGo HTML + Bing HTML + Google Cache
  ✔ Extração via Open Graph, JSON-LD, meta tags, seletores CSS adaptativos
  ✔ Suporte a cloudscraper (anti-Cloudflare) com fallback urllib puro
"""

import sys
import json
import re
import time
import random
import os
import hashlib
import urllib.parse
import urllib.request
import urllib.error
from datetime import datetime
from difflib import get_close_matches
from collections import deque

# ──────────────────────────────────────────────────────────────────────────────
# DEPENDÊNCIAS OPCIONAIS
# ──────────────────────────────────────────────────────────────────────────────
try:
    from bs4 import BeautifulSoup
    _HAS_BS4 = True
except ImportError:
    _HAS_BS4 = False
    print(json.dumps([]))
    sys.exit(0)

try:
    import cloudscraper
    _HAS_CLOUD = True
except ImportError:
    _HAS_CLOUD = False

# ──────────────────────────────────────────────────────────────────────────────
# CONSTANTES GLOBAIS
# ──────────────────────────────────────────────────────────────────────────────

# Imagem padrão do WhatsApp para grupos sem foto (Open Graph oficial do WA)
WHATSAPP_DEFAULT_IMG = "https://static.whatsapp.net/rsrc.php/v3/yP/r/rYZqPCBaG70.png"

# 3 regras fixas obrigatórias em todos os grupos
REGRAS_FIXAS = (
    "1. Proibido conteúdo adulto, pornografia ou nudez.\n"
    "2. Proibido spam, links suspeitos ou propaganda não autorizada.\n"
    "3. Respeite todos os membros do grupo. Sem preconceito, bullying ou ofensas."
)

# Regex para capturar qualquer hash de convite WhatsApp.
# Cobre TODAS as variações de URL (imune a /invite/ /join/ /v/ /v= e hash direta),
# alinhado ao WhatsAppLinkValidator do PHP para garantir unicidade entre bot e CRUD.
RE_WA_GROUP   = re.compile(r'chat\.whatsapp\.com/(?:invite/|join/|v/|v=)?([A-Za-z0-9_\-]{10,36})')
RE_WA_CHANNEL = re.compile(r'whatsapp\.com/channel/([A-Za-z0-9_\-@]{10,60})')

# User-agents rotativos
USER_AGENTS = [
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:126.0) Gecko/20100101 Firefox/126.0",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 14_4) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Safari/605.1.15",
    "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 Edg/122.0.0.0",
]

# ──────────────────────────────────────────────────────────────────────────────
# MAPA COMPLETO: texto bruto → slug do sistema WhatsGrupos
# ──────────────────────────────────────────────────────────────────────────────
TERMO_PARA_SLUG = {
    # Amizade
    'amizade': 'amizade', 'amigos': 'amizade', 'friend': 'amizade', 'friends': 'amizade',
    'amigas': 'amizade', 'social': 'amizade',
    # Amor / Romance
    'amor': 'amor-e-romance', 'romance': 'amor-e-romance', 'apaixonado': 'amor-e-romance',
    'casal': 'amor-e-romance', 'relacionamento': 'amor-e-romance',
    # Namoro
    'namoro': 'namoro', 'namorar': 'namoro', 'paquera': 'namoro', 'encontros': 'namoro',
    'dating': 'namoro', 'solteiro': 'namoro', 'solteira': 'namoro',
    # Carros / Motos
    'carros': 'carros-e-motos', 'carro': 'carros-e-motos', 'motos': 'carros-e-motos',
    'moto': 'carros-e-motos', 'veiculos': 'carros-e-motos', 'automóveis': 'carros-e-motos',
    'auto': 'carros-e-motos', 'automotivo': 'carros-e-motos', 'mecanica': 'carros-e-motos',
    'pickup': 'carros-e-motos', 'caminhao': 'carros-e-motos',
    # Cidades / Regiões
    'cidade': 'cidades', 'bairro': 'cidades', 'estado': 'cidades', 'regiao': 'cidades',
    'municipio': 'cidades', 'local': 'cidades', 'comunidade': 'cidades', 'moradores': 'cidades',
    'vizinhanca': 'cidades', 'condominio': 'cidades',
    # Compra / Venda
    'compra': 'compra-e-venda', 'venda': 'compra-e-venda', 'marketplace': 'compra-e-venda',
    'anuncio': 'compra-e-venda', 'classificados': 'compra-e-venda', 'comercio': 'compra-e-venda',
    'loja': 'compra-e-venda', 'oferta': 'compra-e-venda', 'promocao': 'compra-e-venda',
    'produto': 'compra-e-venda', 'bazaar': 'compra-e-venda', 'mercado': 'compra-e-venda',
    # Concursos
    'concurso': 'concursos', 'concurso publico': 'concursos', 'enem': 'concursos',
    'vestibular': 'concursos', 'oab': 'concursos', 'gabarito': 'concursos',
    'fuvest': 'concursos', 'provas': 'concursos', 'edital': 'concursos',
    # Animes / Desenhos
    'anime': 'desenhos-e-animes', 'manga': 'desenhos-e-animes', 'otaku': 'desenhos-e-animes',
    'desenho': 'desenhos-e-animes', 'animacao': 'desenhos-e-animes', 'cartoon': 'desenhos-e-animes',
    'hq': 'desenhos-e-animes', 'quadrinhos': 'desenhos-e-animes', 'cosplay': 'desenhos-e-animes',
    # Divulgação
    'divulgacao': 'divulgacao', 'publicidade': 'divulgacao', 'propaganda': 'divulgacao',
    'marketing': 'divulgacao', 'afiliado': 'divulgacao',
    # Educação
    'educacao': 'educacao', 'estudo': 'educacao', 'escola': 'educacao',
    'faculdade': 'educacao', 'universidade': 'educacao', 'curso': 'educacao',
    'aprendizado': 'educacao', 'ensino': 'educacao', 'aluno': 'educacao',
    'professor': 'educacao', 'aula': 'educacao', 'colegio': 'educacao',
    'graduacao': 'educacao', 'pos-graduacao': 'educacao', 'ead': 'educacao',
    # Emagrecimento
    'emagrecer': 'emagrecimento', 'emagrecimento': 'emagrecimento', 'dieta': 'emagrecimento',
    'academia': 'emagrecimento', 'fit': 'emagrecimento', 'saude': 'emagrecimento',
    'nutricao': 'emagrecimento', 'nutri': 'emagrecimento', 'personal': 'emagrecimento',
    'treino': 'emagrecimento', 'musculacao': 'emagrecimento', 'hiit': 'emagrecimento',
    # Esportes
    'esporte': 'esportes', 'sport': 'esportes', 'fitness': 'esportes',
    'corrida': 'esportes', 'ciclismo': 'esportes', 'natacao': 'esportes',
    'basquete': 'esportes', 'volei': 'esportes', 'tenis': 'esportes',
    'lutas': 'esportes', 'mma': 'esportes', 'ufc': 'esportes',
    # Eventos
    'evento': 'eventos', 'show': 'eventos', 'festa': 'eventos',
    'balada': 'eventos', 'festival': 'eventos', 'concert': 'eventos',
    'forró': 'eventos', 'pagode': 'eventos', 'aniversario': 'eventos',
    # Fãs
    'fas': 'fas', 'fanclub': 'fas', 'idolo': 'fas', 'fan': 'fas',
    'fandom': 'fas', 'kpop': 'fas', 'bts': 'fas', 'blackpink': 'fas',
    # Figurinhas
    'figurinha': 'figurinhas', 'sticker': 'figurinhas', 'pack': 'figurinhas',
    'stickers': 'figurinhas', 'fantasia': 'figurinhas',
    # Filmes / Séries
    'filme': 'filmes-e-series', 'serie': 'filmes-e-series', 'netflix': 'filmes-e-series',
    'cinema': 'filmes-e-series', 'streaming': 'filmes-e-series', 'hbo': 'filmes-e-series',
    'disney': 'filmes-e-series', 'amazon prime': 'filmes-e-series', 'documentario': 'filmes-e-series',
    # Frases e Mensagens
    'frases': 'frases-e-mensagens', 'mensagens': 'frases-e-mensagens', 'reflexao': 'frases-e-mensagens',
    'motivacao': 'frases-e-mensagens', 'inspiracao': 'frases-e-mensagens', 'citacoes': 'frases-e-mensagens',
    # Futebol
    'futebol': 'futebol', 'copa': 'futebol', 'brasileirao': 'futebol',
    'champions': 'futebol', 'flamengo': 'futebol', 'corinthians': 'futebol',
    'palmeiras': 'futebol', 'sao paulo': 'futebol', 'gremio': 'futebol',
    'internacional': 'futebol', 'atletico': 'futebol', 'premier league': 'futebol',
    'la liga': 'futebol', 'serie a': 'futebol', 'fifa': 'futebol',
    # Games / Jogos
    'game': 'games-e-jogos', 'jogo': 'games-e-jogos', 'free fire': 'games-e-jogos',
    'valorant': 'games-e-jogos', 'minecraft': 'games-e-jogos', 'lol': 'games-e-jogos',
    'fortnite': 'games-e-jogos', 'roblox': 'games-e-jogos', 'mobile legends': 'games-e-jogos',
    'pubg': 'games-e-jogos', 'gamer': 'games-e-jogos', 'gaming': 'games-e-jogos',
    'playstation': 'games-e-jogos', 'xbox': 'games-e-jogos', 'nintendo': 'games-e-jogos',
    'steam': 'games-e-jogos', 'rpg': 'games-e-jogos', 'fps': 'games-e-jogos',
    # Ganhar Dinheiro
    'dinheiro': 'ganhar-dinheiro', 'renda extra': 'ganhar-dinheiro',
    'freelancer': 'ganhar-dinheiro', 'trabalho online': 'ganhar-dinheiro',
    'ganhar': 'ganhar-dinheiro', 'dropshipping': 'ganhar-dinheiro',
    'cashback': 'ganhar-dinheiro', 'cupom': 'ganhar-dinheiro',
    # Imobiliária
    'imovel': 'imobiliaria', 'apartamento': 'imobiliaria', 'aluguel': 'imobiliaria',
    'casa': 'imobiliaria', 'imóveis': 'imobiliaria', 'terreno': 'imobiliaria',
    'condominio': 'imobiliaria', 'kitnet': 'imobiliaria', 'quitinete': 'imobiliaria',
    # Investimentos
    'investimento': 'investimentos', 'bolsa': 'investimentos', 'acoes': 'investimentos',
    'cripto': 'investimentos', 'bitcoin': 'investimentos', 'ethereum': 'investimentos',
    'forex': 'investimentos', 'day trade': 'investimentos', 'tesouro': 'investimentos',
    'fundo': 'investimentos', 'cdi': 'investimentos', 'renda fixa': 'investimentos',
    # Links
    'links': 'links', 'link': 'links',
    # Memes / Zoeira
    'meme': 'memes-e-zoeira', 'zoeira': 'memes-e-zoeira', 'humor': 'memes-e-zoeira',
    'piada': 'memes-e-zoeira', 'engraçado': 'memes-e-zoeira', 'memes': 'memes-e-zoeira',
    'shitpost': 'memes-e-zoeira', 'zoar': 'memes-e-zoeira',
    # Moda / Beleza
    'moda': 'moda-e-beleza', 'beleza': 'moda-e-beleza', 'maquiagem': 'moda-e-beleza',
    'roupa': 'moda-e-beleza', 'fashion': 'moda-e-beleza', 'look': 'moda-e-beleza',
    'skincare': 'moda-e-beleza', 'cabelo': 'moda-e-beleza', 'estética': 'moda-e-beleza',
    # Música
    'musica': 'musica', 'sertanejo': 'musica', 'funk': 'musica', 'pagode': 'musica',
    'gospel': 'musica', 'hip hop': 'musica', 'rap': 'musica', 'rock': 'musica',
    'forró': 'musica', 'axé': 'musica', 'eletronica': 'musica', 'dj': 'musica',
    # Namoro (já mapeado acima)
    # Negócios
    'negocio': 'negocios', 'empreendedor': 'negocios', 'empresa': 'negocios',
    'startup': 'negocios', 'mei': 'negocios', 'gestao': 'negocios',
    'lideranca': 'negocios', 'vendas': 'negocios', 'comercial': 'negocios',
    # Notícias
    'noticias': 'noticias', 'informacao': 'noticias', 'news': 'noticias',
    'jornal': 'noticias', 'jornalismo': 'noticias', 'atualidades': 'noticias',
    # Política
    'politica': 'politica', 'governo': 'politica', 'eleicoes': 'politica',
    'deputado': 'politica', 'senado': 'politica', 'vereador': 'politica',
    # Profissões
    'profissao': 'profissoes', 'trabalho': 'profissoes', 'carreira': 'profissoes',
    'empregado': 'profissoes', 'servidor': 'profissoes', 'funcionario': 'profissoes',
    'medico': 'profissoes', 'advogado': 'profissoes', 'engenheiro': 'profissoes',
    'enfermeiro': 'profissoes', 'professor': 'profissoes',
    # Receitas
    'receita': 'receitas', 'culinaria': 'receitas', 'cozinha': 'receitas',
    'gastronomia': 'receitas', 'chef': 'receitas', 'comida': 'receitas',
    'bolo': 'receitas', 'doce': 'receitas', 'salgado': 'receitas',
    # Redes Sociais
    'rede social': 'redes-sociais', 'instagram': 'redes-sociais', 'tiktok': 'redes-sociais',
    'youtube': 'redes-sociais', 'twitter': 'redes-sociais', 'x.com': 'redes-sociais',
    'seguidores': 'redes-sociais', 'influencer': 'redes-sociais',
    # Religião
    'religiao': 'religiao', 'igreja': 'religiao', 'evangelico': 'religiao',
    'crista': 'religiao', 'biblia': 'religiao', 'cristao': 'religiao',
    'catolico': 'religiao', 'espirita': 'religiao', 'umbanda': 'religiao',
    'candomble': 'religiao', 'oracao': 'religiao', 'fe': 'religiao',
    # Tecnologia
    'tecnologia': 'tecnologia', 'programacao': 'tecnologia', 'ti': 'tecnologia',
    'developer': 'tecnologia', 'desenvolvimento': 'tecnologia', 'codigo': 'tecnologia',
    'python': 'tecnologia', 'javascript': 'tecnologia', 'php': 'tecnologia',
    'hacking': 'tecnologia', 'cyber': 'tecnologia', 'ia': 'tecnologia',
    'inteligencia artificial': 'tecnologia', 'chatgpt': 'tecnologia',
    # TV
    'tv': 'tv', 'novela': 'tv', 'reality': 'tv', 'bbb': 'tv',
    'televisao': 'tv', 'programa': 'tv', 'globo': 'tv',
    # Vagas de Emprego
    'vaga': 'vagas-de-emprego', 'emprego': 'vagas-de-emprego', 'contratando': 'vagas-de-emprego',
    'clt': 'vagas-de-emprego', 'pj': 'vagas-de-emprego', 'estagio': 'vagas-de-emprego',
    'curriculo': 'vagas-de-emprego', 'rh': 'vagas-de-emprego',
    # Viagem
    'viagem': 'viagem-e-turismo', 'turismo': 'viagem-e-turismo', 'turista': 'viagem-e-turismo',
    'mochileiro': 'viagem-e-turismo', 'hostel': 'viagem-e-turismo', 'passeio': 'viagem-e-turismo',
    # Vídeos
    'video': 'videos', 'videos': 'videos', 'reels': 'videos',
    'shorts': 'videos', 'canal': 'videos', 'lives': 'videos',
}

# ──────────────────────────────────────────────────────────────────────────────
# DIRETÓRIOS ALVO PRIMÁRIOS (rastreamento completo por site)
# ──────────────────────────────────────────────────────────────────────────────
DIRETORIOS = [
    # ── Confirmados ao vivo: entregam links WA no HTML estático ──────────────
    {
        # Testado: wa=9-11 links por página, paginação via index.php?page=index&p={n}
        'nome': 'AllGrupos',
        'base_url': 'https://www.allgrupos.com.br',
        'seeds': [
            'https://www.allgrupos.com.br',
            'https://www.allgrupos.com.br/grupos/amizade',
            'https://www.allgrupos.com.br/grupos/futebol',
            'https://www.allgrupos.com.br/grupos/tecnologia',
            'https://www.allgrupos.com.br/grupos/games',
            'https://www.allgrupos.com.br/grupos/humor',
            'https://www.allgrupos.com.br/grupos/namoro',
            'https://www.allgrupos.com.br/grupos/negocios',
            'https://www.allgrupos.com.br/grupos/noticias',
            'https://www.allgrupos.com.br/grupos/musica',
        ],
        'paginacao': {'tipo': 'custom_allgrupos', 'inicio': 1, 'max': 100},
        'link_interno': True,
    },
    {
        # Testado: wa=6 links em /grupos, sem paginação padrão (crawl de links internos)
        'nome': 'GrupodeWhatsApp_Online',
        'base_url': 'https://grupodewhatsapp.online',
        'seeds': [
            'https://grupodewhatsapp.online/grupos',
            'https://grupodewhatsapp.online',
        ],
        'paginacao': {'tipo': 'query', 'param': 'page', 'inicio': 1, 'max': 30},
        'link_interno': True,
    },

    # ── Confirmados online (200) — links via BFS de páginas internas ─────────
    {
        'nome': 'LinkGrupos',
        'base_url': 'https://www.linkgrupos.com.br',
        'seeds': ['https://www.linkgrupos.com.br', 'https://www.linkgrupos.com.br/grupos-de-whatsapp'],
        'paginacao': {'tipo': 'query', 'param': 'pagina', 'inicio': 1, 'max': 50},
        'link_interno': True,
    },
    {
        'nome': 'GruposZap',
        'base_url': 'https://gruposzap.com',
        'seeds': ['https://gruposzap.com', 'https://gruposzap.com/grupos'],
        'paginacao': {'tipo': 'query', 'param': 'page', 'inicio': 1, 'max': 50},
        'link_interno': True,
    },
    {
        'nome': 'GrupoWhats',
        'base_url': 'https://grupowhats.com',
        'seeds': ['https://grupowhats.com', 'https://grupowhats.com/grupos'],
        'paginacao': {'tipo': 'query', 'param': 'page', 'inicio': 1, 'max': 50},
        'link_interno': True,
    },
    {
        'nome': 'GruposDeWhatsApp_Net',
        'base_url': 'https://gruposdewhatsapp.net',
        'seeds': ['https://gruposdewhatsapp.net'],
        'paginacao': {'tipo': 'query', 'param': 'page', 'inicio': 1, 'max': 30},
        'link_interno': True,
    },
    {
        'nome': 'GruposdeWhats',
        'base_url': 'https://gruposdewhats.com.br',
        'seeds': ['https://gruposdewhats.com.br', 'https://gruposdewhats.com.br/grupos'],
        'paginacao': {'tipo': 'query', 'param': 'page', 'inicio': 1, 'max': 50},
        'link_interno': True,
    },
    {
        'nome': 'WGrupos',
        'base_url': 'https://www.wgrupos.com',
        'seeds': ['https://www.wgrupos.com/whatsapp', 'https://www.wgrupos.com/whatsapp/2'],
        'paginacao': {'tipo': 'path', 'padrao': '/{n}', 'inicio': 1, 'max': 30},
        'link_interno': True,
    },
    {
        'nome': 'GruposBrasil',
        'base_url': 'https://gruposbrasil.com.br',
        'seeds': ['https://gruposbrasil.com.br'],
        'paginacao': {'tipo': 'query', 'param': 'page', 'inicio': 1, 'max': 50},
        'link_interno': True,
    },
    {
        'nome': 'GruposZapp',
        'base_url': 'https://gruposzapp.com',
        'seeds': ['https://gruposzapp.com'],
        'paginacao': {'tipo': 'query', 'param': 'page', 'inicio': 1, 'max': 30},
        'link_interno': True,
    },
    {
        'nome': 'SuperGrupos',
        'base_url': 'https://supergrupos.com.br',
        'seeds': ['https://supergrupos.com.br', 'https://supergrupos.com.br/grupos'],
        'paginacao': {'tipo': 'query', 'param': 'page', 'inicio': 1, 'max': 30},
        'link_interno': True,
    },
    {
        'nome': 'MaisGrupos',
        'base_url': 'https://maisgrupos.com',
        'seeds': ['https://maisgrupos.com', 'https://maisgrupos.com/grupos'],
        'paginacao': {'tipo': 'query', 'param': 'page', 'inicio': 1, 'max': 30},
        'link_interno': True,
    },
    {
        'nome': 'ChamaNoZap',
        'base_url': 'https://chamanozap.com.br',
        'seeds': ['https://chamanozap.com.br', 'https://chamanozap.com.br/grupos'],
        'paginacao': {'tipo': 'query', 'param': 'page', 'inicio': 1, 'max': 30},
        'link_interno': True,
    },
]

# Queries de busca nos buscadores
DDG_QUERIES = [
    'grupos whatsapp link entrar brasil 2025 chat.whatsapp.com',
    'site:chat.whatsapp.com grupos brasil',
    'whatsapp grupo link {cat} brasil',
    '"chat.whatsapp.com" grupos {cat}',
    'entrar grupo whatsapp {cat} 2025',
]

BING_QUERIES = [
    'chat.whatsapp.com grupos {cat} brasil',
    'whatsapp.com/channel {cat}',
    'grupo whatsapp {cat} link convite',
]


# ──────────────────────────────────────────────────────────────────────────────
# HTTP ROBUSTO (cloudscraper → urllib fallback)
# ──────────────────────────────────────────────────────────────────────────────
class FakeResponse:
    def __init__(self, status=0, text=''):
        self.status_code = status
        self.text = text


class RobustHttp:
    """HTTP client com retry, rotação de UA, e fallback total."""

    def __init__(self):
        self._session = None
        self._init_session()

    def _init_session(self):
        if _HAS_CLOUD:
            try:
                self._session = cloudscraper.create_scraper(
                    browser={'browser': 'chrome', 'platform': 'windows', 'mobile': False}
                )
                self._session.headers.update({'Accept-Language': 'pt-BR,pt;q=0.9,en;q=0.7'})
                return
            except Exception:
                pass
        self._session = None

    def get(self, url: str, timeout: int = 18, retries: int = 3) -> FakeResponse:
        for attempt in range(retries):
            try:
                if self._session:
                    r = self._session.get(url, timeout=timeout, allow_redirects=True)
                    if r.status_code not in (403, 429, 503):
                        return FakeResponse(r.status_code, r.text)
                    # tenta urllib no 403/429
                    if attempt == retries - 1:
                        return FakeResponse(r.status_code, r.text)
                else:
                    return self._urllib_get(url, timeout)
            except Exception:
                pass

            # urllib fallback
            resp = self._urllib_get(url, timeout)
            if resp.status_code == 200:
                return resp

            wait = (2 ** attempt) + random.uniform(0.5, 1.5)
            time.sleep(wait)

        return FakeResponse(0, '')

    def _urllib_get(self, url: str, timeout: int = 18) -> FakeResponse:
        headers = {
            'User-Agent': random.choice(USER_AGENTS),
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language': 'pt-BR,pt;q=0.9,en;q=0.7',
            'Accept-Encoding': 'gzip, deflate',
            'Connection': 'keep-alive',
            'Upgrade-Insecure-Requests': '1',
        }
        try:
            req = urllib.request.Request(url, headers=headers)
            with urllib.request.urlopen(req, timeout=timeout) as r:
                raw = r.read()
                enc = r.headers.get_content_charset() or 'utf-8'
                return FakeResponse(r.status, raw.decode(enc, errors='ignore'))
        except urllib.error.HTTPError as e:
            return FakeResponse(e.code, '')
        except Exception:
            return FakeResponse(0, '')


# ──────────────────────────────────────────────────────────────────────────────
# EXTRATOR DE METADADOS
# ──────────────────────────────────────────────────────────────────────────────
class MetaExtractor:
    """Extrai nome, descrição, imagem de uma página usando Open Graph / JSON-LD / seletores CSS."""

    # Seletores CSS adaptativos (ordem de prioridade)
    NOME_SEL   = ['h1', 'h2.title', 'h2', 'h3.title', 'h3', '.group-name', '.nome-grupo',
                  '.nome', '.title', '.name', '[itemprop="name"]']
    DESC_SEL   = ['.description', '.desc', '.descricao', 'p.desc', 'p.description',
                  '.group-desc', '.about', '[itemprop="description"]', 'p']
    CAT_SEL    = ['.category', '.categoria', '.badge', '.tag', '.cat', '.label',
                  '.cat-label', '[itemprop="keywords"]', '.genre']
    IMG_SEL    = ['img.group-img', 'img.foto', 'img.thumb', 'img.avatar',
                  'img[itemprop="image"]', 'img']

    @staticmethod
    def _og(soup: 'BeautifulSoup', prop: str) -> str:
        tag = soup.find('meta', property=prop) or soup.find('meta', attrs={'name': prop})
        return (tag.get('content') or '').strip() if tag else ''

    @staticmethod
    def _jsonld(soup: 'BeautifulSoup') -> dict:
        for s in soup.find_all('script', type='application/ld+json'):
            try:
                d = json.loads(s.string or '{}')
                if isinstance(d, list):
                    d = d[0]
                return d
            except Exception:
                pass
        return {}

    @staticmethod
    def _txt(tag) -> str:
        if not tag:
            return ''
        return re.sub(r'\s+', ' ', tag.get_text()).strip()

    @classmethod
    def _first(cls, soup, sels: list) -> str:
        for sel in sels:
            try:
                el = soup.select_one(sel)
                if el:
                    return cls._txt(el)
            except Exception:
                pass
        return ''

    @classmethod
    def _first_img(cls, soup, base_url: str) -> str:
        og = cls._og(soup, 'og:image')
        if og and og.startswith('http'):
            return og
        for sel in cls.IMG_SEL:
            try:
                el = soup.select_one(sel)
                if el:
                    src = el.get('src') or el.get('data-src') or el.get('data-lazy') or ''
                    src = src.strip()
                    if src.startswith('//'):
                        src = 'https:' + src
                    elif src.startswith('/'):
                        src = base_url.rstrip('/') + src
                    if src.startswith('http') and not any(x in src for x in ['placeholder', 'blank', 'default', '1x1']):
                        return src
            except Exception:
                pass
        return ''

    @classmethod
    def extract(cls, soup: 'BeautifulSoup', base_url: str) -> dict:
        ld = cls._jsonld(soup)
        nome = (
            cls._og(soup, 'og:title')
            or ld.get('name', '')
            or cls._first(soup, cls.NOME_SEL)
        )
        desc = (
            cls._og(soup, 'og:description')
            or ld.get('description', '')
            or cls._first(soup, cls.DESC_SEL)
        )
        cat  = cls._first(soup, cls.CAT_SEL)
        img  = cls._first_img(soup, base_url)
        return {
            'nome': nome[:200].strip(),
            'desc': desc[:2000].strip(),
            'cat':  cat[:100].strip(),
            'img':  img,
        }


# ──────────────────────────────────────────────────────────────────────────────
# COLETOR PRINCIPAL
# ──────────────────────────────────────────────────────────────────────────────
class GroupCollector:

    def __init__(self, categories_json: list):
        self.http = RobustHttp()

        self.log_path = os.path.normpath(os.path.join(
            os.path.dirname(os.path.abspath(__file__)),
            '..', '..', 'storage', 'logs', 'mineracao.log'
        ))

        # Categorias do sistema: slug → name
        self.categorias = {c.get('slug', 'outros'): c.get('name', 'Outros') for c in categories_json}
        if 'outros' not in self.categorias:
            self.categorias['outros'] = 'Outros'

        # Hashes já vistos nesta sessão (dedup absoluto)
        self._hashes_vistos: set = set()
        self.resultados: list = []

        # ── Limites opcionais por env (default = produção completa) ──
        # Permitem testes rápidos e tuning de ops SEM reduzir o alcance em produção
        # (quando as envs não estão setadas, o comportamento é o varrer-tudo original).
        self._max_dirs   = int(os.environ.get('COLLECTOR_MAX_DIRS', len(DIRETORIOS)))
        self._max_pages  = os.environ.get('COLLECTOR_MAX_PAGES')   # None = usa o max de cada diretório
        self._max_pages  = int(self._max_pages) if self._max_pages else None
        self._max_groups = os.environ.get('COLLECTOR_MAX_GROUPS')  # None = sem teto
        self._max_groups = int(self._max_groups) if self._max_groups else None
        self._skip_search = os.environ.get('COLLECTOR_SKIP_SEARCH', '').lower() in ('1', 'true', 'yes')

    def _cap_atingido(self) -> bool:
        """True quando o teto opcional de grupos (COLLECTOR_MAX_GROUPS) foi alcançado."""
        return self._max_groups is not None and len(self.resultados) >= self._max_groups

    # ──────────────────────────── LOGGING ─────────────────────────────────────
    def log(self, msg: str, tipo: str = 'INFO'):
        ts   = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        linha = f'[{ts}] [PYTHON_{tipo}] {msg}\n'
        try:
            os.makedirs(os.path.dirname(self.log_path), exist_ok=True)
            with open(self.log_path, 'a', encoding='utf-8') as f:
                f.write(linha)
        except Exception:
            pass

    # ──────────────────────── EXTRAÇÃO DE HASHES ──────────────────────────────
    def _extrair_hashes_html(self, html: str) -> list[str]:
        """Extrai todos os links únicos de WhatsApp de um bloco de HTML."""
        links = []
        vistos = set()

        for m in RE_WA_GROUP.finditer(html):
            h = m.group(1)
            url = f'https://chat.whatsapp.com/{h}'
            if h not in vistos:
                vistos.add(h)
                links.append(url)

        for m in RE_WA_CHANNEL.finditer(html):
            h = m.group(1)
            url = f'https://whatsapp.com/channel/{h}'
            key = f'ch_{h}'
            if key not in vistos:
                vistos.add(key)
                links.append(url)

        return links

    def _hash_de_url(self, url: str) -> str | None:
        m = RE_WA_GROUP.search(url)
        if m:
            return m.group(1)
        m = RE_WA_CHANNEL.search(url)
        if m:
            return 'ch_' + m.group(1)
        return None

    def _canonical(self, url: str) -> str | None:
        m = RE_WA_GROUP.search(url)
        if m:
            return f'https://chat.whatsapp.com/{m.group(1)}'
        m = RE_WA_CHANNEL.search(url)
        if m:
            return f'https://whatsapp.com/channel/{m.group(1)}'
        return None

    # ─────────────────────────── CATEGORIZAÇÃO ────────────────────────────────
    def categorizar(self, texto: str) -> str:
        if not texto:
            return 'outros'
        t = texto.lower().strip()

        # 1. Busca direta no mapa (palavra por palavra)
        for termo, slug in TERMO_PARA_SLUG.items():
            if termo in t and slug in self.categorias:
                return slug

        # 2. Fuzzy match nos slugs do sistema
        slugs = list(self.categorias.keys())
        matches = get_close_matches(t, slugs, n=1, cutoff=0.60)
        if matches and matches[0] in self.categorias:
            return matches[0]

        # 3. Fuzzy match nas palavras do mapa
        palavras = list(TERMO_PARA_SLUG.keys())
        matches2 = get_close_matches(t, palavras, n=1, cutoff=0.70)
        if matches2:
            slug = TERMO_PARA_SLUG[matches2[0]]
            if slug in self.categorias:
                return slug

        return 'outros'

    # ────────────────── METADADOS REAIS DO GRUPO (WHATSAPP) ───────────────────
    def _fetch_wa_meta(self, canonical: str) -> dict:
        """
        Busca o NOME e a FOTO REAIS do grupo direto na página de convite do WhatsApp.

        A página chat.whatsapp.com/<hash> expõe:
          • og:title       → nome real do grupo
          • og:image        → foto real do grupo (servida por pps.whatsapp.net)
          • og:description  → texto genérico ("WhatsApp Group Invite"), sem valor

        Isso corrige o bug em que nome/imagem vinham do DIRETÓRIO de origem
        (título do site e print/og:image do diretório), e não do grupo em si.

        Retorna {} quando o convite é inválido/expirado/privado ou não traz dados úteis.
        O BeautifulSoup já decodifica entidades HTML (&#xea; → ê, &amp; → &).
        """
        try:
            resp = self._get(canonical, timeout=12)
            if resp.status_code != 200 or not resp.text:
                return {}
            soup = BeautifulSoup(resp.text, 'html.parser')
            nome = MetaExtractor._og(soup, 'og:title')
            img  = MetaExtractor._og(soup, 'og:image')

            # Títulos genéricos do WhatsApp = convite sem dados reais (expirado/privado).
            genericos = {
                'whatsapp group invite', 'whatsapp', 'invite to group',
                'convite do grupo do whatsapp', 'convite para grupo do whatsapp',
            }
            if nome and nome.strip().lower() in genericos:
                nome = ''

            # Só é foto REAL a que vem do servidor de fotos de perfil (pps.whatsapp.net).
            # O static.whatsapp.net devolve o avatar genérico (grupo sem foto) → tratamos
            # como "sem imagem" para o front exibir o gradiente + inicial (default).
            if img and 'pps.whatsapp.net' not in img:
                img = ''

            return {'nome': (nome or '').strip(), 'img': (img or '').strip()}
        except Exception as e:
            self.log(f'Falha ao buscar metadados WA de {canonical}: {e}', 'DEBUG')
            return {}

    # ──────────────────────────── ADICIONAR ───────────────────────────────────
    def adicionar(self, url: str, cat_slug: str, nome: str = '', desc: str = '', img: str = '') -> bool:
        if self._cap_atingido():
            return False

        canonical = self._canonical(url)
        if not canonical:
            return False

        h = self._hash_de_url(canonical)
        if not h or h in self._hashes_vistos:
            return False  # dedup rigoroso por hash

        self._hashes_vistos.add(h)

        # ── Metadados REAIS do grupo (nome + foto) direto da página de convite ──
        # Fonte da verdade do nome e da foto. O nome/imagem/og passados pelo diretório
        # de origem são apenas pistas e NUNCA viram a identidade do grupo.
        wa = self._fetch_wa_meta(canonical)
        if wa.get('nome'):
            nome = wa['nome']
            # Recategoriza pelo NOME REAL do grupo (melhor que o palpite do diretório).
            slug_real = self.categorizar(nome)
            if slug_real != 'outros':
                cat_slug = slug_real
        # A foto real só pode vir do WhatsApp; descartamos qualquer imagem do diretório.
        img = wa.get('img', '')

        # Garante slug válido com fallback para 'outros'
        if cat_slug not in self.categorias:
            cat_slug = 'outros'

        # Extrai hash puro para enviar ao PHP.
        # Grupos → hash crua; canais → prefixo 'channel_' (idêntico ao WhatsAppLinkValidator::extractHash do PHP),
        # garantindo que o mesmo canal coletado pelo bot e cadastrado no CRUD tenham a MESMA invite_hash.
        m_grp = RE_WA_GROUP.search(canonical)
        m_ch  = RE_WA_CHANNEL.search(canonical)
        if m_grp:
            hash_puro = m_grp.group(1)
        elif m_ch:
            hash_puro = 'channel_' + m_ch.group(1)
        else:
            hash_puro = h

        # Sanitização de nome
        nome = re.sub(r'\s+', ' ', nome or '').strip()[:100]
        if len(nome) < 3:
            nome = f'Grupo WhatsApp {cat_slug.replace("-", " ").title()}'

        # Descrição: o convite do WhatsApp não expõe descrição real do grupo,
        # então geramos uma descrição padrão a partir do NOME REAL + categoria.
        cat_nome = self.categorias.get(cat_slug, 'Outros')
        desc = f'Participe do grupo {nome} da categoria {cat_nome} no WhatsApp!'

        # Imagem: se o grupo não tem foto real, deixamos VAZIO de propósito.
        # O PHP grava image_path = null e o front exibe o gradiente + inicial (default).
        if not img or not img.startswith('http') or any(x in img.lower() for x in ['placeholder', 'blank', '1x1', 'pixel']):
            img = ''

        self.resultados.append({
            'link':           canonical,
            'canonical_link': canonical,
            'hash':           hash_puro,
            'category_slug':  cat_slug,
            'extracted_name': nome,
            'extracted_desc': desc,
            'extracted_img':  img,
            'rules':          REGRAS_FIXAS,
        })

        self.log(f'[+] {canonical} | cat={cat_slug} | {nome[:40]}', 'SUCCESS')
        return True

    # ──────────────────────── HTTP HELPER ─────────────────────────────────────
    def _get(self, url: str, timeout: int = 18) -> FakeResponse:
        try:
            return self.http.get(url, timeout=timeout)
        except Exception as e:
            self.log(f'HTTP erro {url}: {e}', 'WARNING')
            return FakeResponse(0, '')

    # ─────────────────────── PAGINAÇÃO DINÂMICA ───────────────────────────────
    def _gerar_paginas(self, cfg: dict, seed: str) -> list[str]:
        """Gera URLs de paginação até o máximo configurado."""
        pg = cfg.get('paginacao', {})
        if not pg:
            return [seed]

        tipo  = pg.get('tipo', 'query')
        inicio = pg.get('inicio', 1)
        maximo = pg.get('max', 30)
        if self._max_pages is not None:
            maximo = min(maximo, self._max_pages)
        urls = []

        for n in range(inicio, inicio + maximo):
            if tipo == 'query':
                param = pg.get('param', 'page')
                sep = '&' if '?' in seed else '?'
                urls.append(f'{seed}{sep}{param}={n}')
            elif tipo == 'path':
                padrao = pg.get('padrao', '/page/{n}')
                base = seed.rstrip('/')
                urls.append(base + padrao.replace('{n}', str(n)))
            elif tipo == 'custom_allgrupos':
                # AllGrupos usa index.php?page=index&p={n}&search=#groups-section
                # base_url vem do cfg do diretório
                _base = cfg.get('base_url', 'https://www.allgrupos.com.br')
                urls.append(f'{_base}/index.php?page=index&p={n}&search=#groups-section')

        return urls if urls else [seed]

    # ─────────────────────── SCRAPER DE DIRETÓRIO ─────────────────────────────
    def raspar_site_completo(self, cfg: dict):
        """
        Rastreia TODOS os grupos de um site antes de passar pro próximo.
        Usa BFS com deduplicação de URLs já visitadas por domínio.
        """
        nome     = cfg['nome']
        base_url = cfg['base_url']
        seeds    = cfg.get('seeds', [base_url])

        self.log(f'━━━ [{nome}] Iniciando rastreamento completo ━━━', 'INFO')

        # BFS de URLs dentro do mesmo domínio
        fila_urls: deque = deque()
        urls_visitadas: set = set()
        total_grupos = 0

        # Adiciona seeds + todas as páginas paginadas
        for seed in seeds:
            paginas = self._gerar_paginas(cfg, seed)
            for p in paginas:
                if p not in urls_visitadas:
                    fila_urls.append(p)

        paginas_sem_grupo = 0
        MAX_SEM_GRUPO = 5  # para de paginar quando 5 páginas consecutivas não têm grupos

        while fila_urls:
            url_atual = fila_urls.popleft()

            if url_atual in urls_visitadas:
                continue
            urls_visitadas.add(url_atual)

            self.log(f'[{nome}] Raspando: {url_atual}', 'INFO')
            resp = self._get(url_atual)

            if resp.status_code == 0:
                paginas_sem_grupo += 1
                if paginas_sem_grupo >= MAX_SEM_GRUPO:
                    self.log(f'[{nome}] Muitas falhas consecutivas, parando.', 'WARNING')
                    break
                time.sleep(random.uniform(2, 4))
                continue

            if resp.status_code not in (200,):
                paginas_sem_grupo += 1
                if paginas_sem_grupo >= MAX_SEM_GRUPO:
                    break
                time.sleep(random.uniform(1, 2))
                continue

            html = resp.text
            soup = BeautifulSoup(html, 'html.parser')

            # 1. Extrai links diretos do WhatsApp desta página
            links_diretos = self._extrair_hashes_html(html)
            grupos_nesta_pagina = 0

            if links_diretos:
                # O og/título da PÁGINA do diretório serve só como dica de categoria.
                # Nome e foto reais são buscados por link na página de convite do WhatsApp.
                meta = MetaExtractor.extract(soup, base_url)
                cat_hint = self.categorizar(meta['cat'] or meta['nome'])
                for lk in links_diretos:
                    if self.adicionar(lk, cat_hint):
                        grupos_nesta_pagina += 1
                        total_grupos += 1

            # 2. Percorre cards individuais para metadados ricos
            grupos_nesta_pagina += self._processar_cards(soup, html, base_url, nome, fila_urls, urls_visitadas, cfg)
            total_grupos += grupos_nesta_pagina

            if grupos_nesta_pagina == 0:
                paginas_sem_grupo += 1
                if paginas_sem_grupo >= MAX_SEM_GRUPO:
                    self.log(f'[{nome}] {MAX_SEM_GRUPO} páginas sem grupos → parando paginação.', 'INFO')
                    break
            else:
                paginas_sem_grupo = 0  # reseta contador

            # Delay respeitoso entre páginas
            time.sleep(random.uniform(1.2, 3.0))

        self.log(f'[{nome}] ✓ Total coletado do site: {total_grupos}', 'INFO')
        return total_grupos

    def _processar_cards(self, soup, html: str, base_url: str, nome_site: str,
                         fila: deque, visitadas: set, cfg: dict) -> int:
        """Processa cards individuais e enfileira páginas internas de grupo."""
        encontrados = 0

        # Seletores genéricos de card
        CARD_SELS = [
            'div.card', 'div.grupo', 'div.group-card', 'article.group',
            'article', 'div.item', 'li.grupo', 'div.thumbnail',
            'div.group-item', 'div.grupo-card', 'div.entry', 'div.post',
            '.group-box', '.grupo-item',
        ]
        cards = []
        for sel in CARD_SELS:
            try:
                found = soup.select(sel)
                if found and len(found) > 1:
                    cards = found
                    break
            except Exception:
                pass

        for card in cards:
            card_html = str(card)

            # Tenta extrair link direto do card
            links_card = self._extrair_hashes_html(card_html)
            if links_card:
                meta = MetaExtractor.extract(card, base_url)
                cat_hint = self.categorizar(meta['cat'] or meta['nome'])
                for lk in links_card:
                    if self.adicionar(lk, cat_hint):
                        encontrados += 1
                continue

            # Caso contrário, acessa página interna do grupo
            if not cfg.get('link_interno', True):
                continue

            a_tag = card.find('a', href=True)
            if not a_tag:
                continue

            href = a_tag['href'].strip()
            if href.startswith('/'):
                href = base_url.rstrip('/') + href
            if not href.startswith('http'):
                continue

            # Não visita URLs externas ao domínio
            if base_url.replace('www.', '') not in href.replace('www.', ''):
                continue

            if href in visitadas:
                continue

            # Busca página interna
            time.sleep(random.uniform(0.3, 0.9))
            resp2 = self._get(href, timeout=14)
            if resp2.status_code != 200:
                continue

            visitadas.add(href)
            soup2 = BeautifulSoup(resp2.text, 'html.parser')
            links_internos = self._extrair_hashes_html(resp2.text)

            if links_internos:
                meta2 = MetaExtractor.extract(soup2, base_url)
                cat_hint = self.categorizar(meta2['cat'] or meta2['nome'])
                for lk in links_internos:
                    if self.adicionar(lk, cat_hint):
                        encontrados += 1

        return encontrados

    # ─────────────────────────── BUSCADORES ───────────────────────────────────
    def _buscar_ddg(self, query: str, cat_slug: str) -> int:
        url = f'https://html.duckduckgo.com/html/?q={urllib.parse.quote_plus(query)}'
        resp = self._get(url, timeout=15)
        if resp.status_code != 200:
            return 0

        soup = BeautifulSoup(resp.text, 'html.parser')
        total = 0

        # Links nos resultados
        for a in soup.find_all('a', href=True):
            href = a['href']
            # Desempacota redirect DDG
            if 'uddg=' in href:
                try:
                    href = urllib.parse.unquote(re.search(r'uddg=([^&]+)', href).group(1))
                except Exception:
                    pass
            links = self._extrair_hashes_html(href)
            for lk in links:
                if self.adicionar(lk, cat_slug, self._txt(a), '', ''):
                    total += 1

        # Links no HTML bruto
        for lk in self._extrair_hashes_html(resp.text):
            if self.adicionar(lk, cat_slug, '', '', ''):
                total += 1

        return total

    def _buscar_bing(self, query: str, cat_slug: str) -> int:
        url = f'https://www.bing.com/search?q={urllib.parse.quote_plus(query)}&setlang=pt-BR&cc=BR'
        resp = self._get(url, timeout=15)
        if resp.status_code != 200:
            return 0

        soup = BeautifulSoup(resp.text, 'html.parser')
        total = 0

        for a in soup.find_all('a', href=True):
            href = a['href']
            # Desempacota redirect Bing
            if 'r.bing.com' in href and 'u=' in href:
                try:
                    href = urllib.parse.unquote(re.search(r'u=([^&]+)', href).group(1))
                except Exception:
                    pass
            links = self._extrair_hashes_html(href + ' ' + str(a))
            for lk in links:
                if self.adicionar(lk, cat_slug, self._txt(a), '', ''):
                    total += 1

        for lk in self._extrair_hashes_html(resp.text):
            if self.adicionar(lk, cat_slug, '', '', ''):
                total += 1

        return total

    @staticmethod
    def _txt(tag) -> str:
        if not tag:
            return ''
        return re.sub(r'\s+', ' ', tag.get_text()).strip() if hasattr(tag, 'get_text') else str(tag)

    def buscar_por_categoria(self, cat_nome: str, cat_slug: str):
        """Busca grupos via DDG e Bing para uma categoria específica."""
        self.log(f'[BUSCA] Categoria: {cat_nome}', 'INFO')
        total = 0

        for tmpl in DDG_QUERIES:
            query = tmpl.replace('{cat}', cat_nome)
            t = self._buscar_ddg(query, cat_slug)
            total += t
            time.sleep(random.uniform(1.5, 3.5))

        for tmpl in BING_QUERIES:
            query = tmpl.replace('{cat}', cat_nome)
            t = self._buscar_bing(query, cat_slug)
            total += t
            time.sleep(random.uniform(2.0, 4.0))

        self.log(f'[BUSCA] {cat_nome}: {total} grupos via buscadores', 'INFO')

    # ──────────────────────── ORQUESTRADOR PRINCIPAL ───────────────────────────
    def collect_all(self) -> list:
        self.log('=' * 65, 'START')
        self.log('WHATSGRUPOS COLETOR UNIVERSAL v3.0 – INICIANDO', 'START')
        self.log(f'Categorias do sistema: {list(self.categorias.keys())}', 'INFO')

        # ── FASE 1: Diretórios públicos (rastreamento completo por site) ──
        self.log('── FASE 1: Diretórios públicos ──', 'INFO')
        for cfg in DIRETORIOS[:self._max_dirs]:
            if self._cap_atingido():
                self.log(f'Teto de {self._max_groups} grupos atingido — encerrando Fase 1.', 'INFO')
                break
            try:
                self.raspar_site_completo(cfg)
            except Exception as e:
                self.log(f'[{cfg["nome"]}] Erro fatal: {e}', 'ERROR')
            # Pausa entre sites
            time.sleep(random.uniform(3.0, 6.0))

        # ── FASE 2: Buscadores por categoria ──
        if self._skip_search:
            self.log('── FASE 2 pulada (COLLECTOR_SKIP_SEARCH) ──', 'INFO')
        else:
            self.log('── FASE 2: Buscadores por categoria ──', 'INFO')
            for slug, nome in self.categorias.items():
                if self._cap_atingido():
                    self.log(f'Teto de {self._max_groups} grupos atingido — encerrando Fase 2.', 'INFO')
                    break
                try:
                    self.buscar_por_categoria(nome, slug)
                except Exception as e:
                    self.log(f'Busca [{slug}] erro: {e}', 'ERROR')
                time.sleep(random.uniform(1.0, 2.0))

        self.log(f'COLETA CONCLUÍDA: {len(self.resultados)} grupos únicos', 'SUCCESS')
        self.log('=' * 65, 'END')
        return self.resultados


# ──────────────────────────────────────────────────────────────────────────────
# PONTO DE ENTRADA
# ──────────────────────────────────────────────────────────────────────────────
if __name__ == '__main__':
    # Garante stdout UTF-8 sem BOM (evita erro de JSON parse no PHP/read)
    if hasattr(sys.stdout, 'reconfigure'):
        sys.stdout.reconfigure(encoding='utf-8')

    try:
        raw = sys.stdin.read().strip().lstrip('﻿')  # remove BOM se houver
        categories = json.loads(raw) if raw else []
    except Exception:
        categories = []

    if not categories:
        # Categorias padrão caso o PHP não envie nada
        categories = [
            {'id': 1, 'name': 'Outros', 'slug': 'outros'},
        ]

    collector = GroupCollector(categories)
    results   = collector.collect_all()
    print(json.dumps(results, ensure_ascii=False))
