<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Lista de Palavras Proibidas (Filtro de Profanidade / Anti-Spam)
    |--------------------------------------------------------------------------
    |
    | Palavras que impedirão o envio de frases ou grupos.
    | O sistema verificará se alguma dessas strings existe no texto enviado.
    |
    */
    'palavroes' => [
        'porno',
        'pornô',
        'xvideos',
        'sexo',
        'puta',
        'caralho',
        'buceta',
        'piroca',
        'pica',
        'viado',
        'bicha',
        'arrombado',
        'foder',
        'foda',
        'putari',
        'putaria',
        'corno',
        'cuzão',
        'cuzao',
        'macaco',
        'preto de',
        'nazista',
        'nazi',
        'fascista',
        'suicídio',
        'suicidio',
        'morte a',
        'matar',
        'vadia',
        'vagabunda',
        'onlyfans',
        'privacy',
        'merda',
        'bosta',
        'idiota',
        'otário',
        'otario',
        'imbecil',
        'retardado',
        'babaca',
        'fdp',
        'cacete',
        'safado',
        'safada',
        'vagabundo',
    ],

    /*
    |--------------------------------------------------------------------------
    | Palavras-chave de Apostas / Gambling
    |--------------------------------------------------------------------------
    |
    | Grupos que contiverem QUALQUER uma destas palavras no nome ou descrição
    | recebem automaticamente a tag `is_gambling = true`.
    |
    | A tag:
    |   - É invisível no site público (só aparece no dashboard admin)
    |   - IMPEDE o grupo de ser impulsionado (boost/VIP)
    |   - Pode ser inserida manualmente pelo admin na curadoria
    |   - Pode ser removida manualmente pelo admin caso seja falso-positivo
    |
    | Adicione variações criativas que tentam burlar o sistema.
    |
    */
    'gambling' => [
        // ─── Termos genéricos de apostas ───────────────────────────────────
        'aposta',
        'apostas',
        'apostando',
        'apostador',
        'apostadores',
        'apostar',

        // ─── Esportes + apostas ────────────────────────────────────────────
        'esportiva',
        'esportivas',
        'futebol bet',
        'futebol aposta',
        'palpite',
        'palpites',
        'dica de aposta',
        'dicas de apostas',
        'dica esportiva',
        'dicas esportivas',
        'tips esportivos',
        'tips de futebol',
        'tips grátis',
        'tips gratuitas',
        'tips vip',
        'tips free',
        'bet tips',
        'análise de jogo',
        'análises de jogos',
        'análise esportiva',
        'análises esportivas',
        'escanteios',
        'handicap',
        'over under',
        'placar exato',
        'odds',
        'odd',
        'cartões amarelos',
        'ambas marcam',
        'gols esperados',
        'tenista',   // contexto bet
        'basquete bet',

        // ─── Plataformas e marcas de bet ───────────────────────────────────
        'bet',       // cobre: bet365, betano, pixbet, etc — substring match
        'bets',
        'betano',
        'bet365',
        'betfair',
        'pixbet',
        'sportingbet',
        'betsson',
        'betnacional',
        'betcris',
        'betmotion',
        'bet7k',
        'esporte da sorte',
        'estrelabet',
        'estrela bet',
        'f12 bet',
        'f12bet',
        'lance bet',
        'lancebet',
        'superbet',
        'super bet',
        'novibet',
        'vaidebet',
        'vai de bet',
        'galera bet',
        'galeraet',  // typo intencional
        'bwin',
        'pinnacle',
        'betway',
        'unibet',
        'mr jack',
        'mrjack',
        'bodog',
        'stake',     // plataforma gambling
        'rivalbet',
        'segurobet',
        'realsbet',
        'velobet',
        'bolsadeaposta',
        'bolsa de aposta',
        'apostaganha',
        'aposta ganha',
        'brazino',
        'cassino',
        'casino',

        // ─── Jogos de cassino ──────────────────────────────────────────────
        'tigrinho',
        'fortune tiger',
        'fortune ox',
        'fortune rabbit',
        'fortune mouse',
        'fortune dragon',
        'fortune horse',
        'fortune gems',
        'dragon hatch',
        'dragon tiger',
        'panda master',
        'roleta',
        'roleta brasileira',
        'slots',
        'slot',
        'caça niquel',
        'caça-níquel',
        'caça níquel',
        'jackpot',
        'baccarat',
        'bacará',
        'blackjack',
        'poker',
        'pôquer',
        'aviator',
        'spaceman',
        'double',
        'crash',
        'mines',
        'fivers',
        'multibet',
        'rocketman',
        'penalty shootout',
        'jogo do bicho',

        // ─── Estratégias e termos técnicos de bet ─────────────────────────
        'valor esperado',
        'bankroll',
        'gestão de banca',
        'banca',         // contexto apostas
        'lay',           // aposta lay no Betfair
        'back',          // aposta back
        'trading esportivo',
        'trading bet',
        'surebet',
        'sure bet',
        'arbitragem esportiva',
        'greening',
        'dutching',
        'rebuy',
        'all in',        // contexto poker/slots

        // ─── Termos disfarçados / evasão ──────────────────────────────────
        'b3t',           // b3t (letra substituída)
        'b€t',
        'ap0sta',
        'ap0stas',
        'ganhando na',   // "ganhando na blaze"
        'ganhe na',
        'ganhe no',
        'lucro nas apostas',
        'renda com apostas',
        'renda extra slots',
        'lucro com jogos',
        'ganhar dinheiro jogando',
        'multiplicar dinheiro',
        'dobrar dinheiro',
        'sinais vip',
        'sinais grátis',
        'sinais gratuitos',
        'sinais esportivos',
        'sinal vip',
        'sinal esportivo',
        'entrada confirmada',
        'entrada vip',
        'entrada grátis',
    ],
];
