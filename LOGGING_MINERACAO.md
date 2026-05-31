# 📊 Sistema de Logging de Mineração de Grupos

## 📝 Sobre

Sistema detalhado de logging para rastrear toda a coleta automática de grupos de WhatsApp. Os logs são salvos em `storage/logs/mineracao.log` com timestamps e informações específicas de cada etapa do processo.

## 🚀 Usando a Coleta com Logging

### Executar coleta com limite de 99999 grupos:

```bash
php artisan whatsgrupos:povoar --limit=99999
```

### Ver os logs em tempo real:

```bash
# Mostrar as últimas 50 linhas (padrão)
php artisan mineracao:log

# Mostrar as últimas 100 linhas
php artisan mineracao:log --lines=100

# Acompanhar o log em tempo real (como tail -f)
php artisan mineracao:log --follow
```

## 📋 Formato do Log

O arquivo `storage/logs/mineracao.log` contém registros estruturados:

```
[2026-05-29 19:00:00] [START] ========== INICIANDO COLETA DE GRUPOS ==========
[2026-05-29 19:00:00] [INFO] Total de categorias para mineração: 10
[2026-05-29 19:00:00] [INFO]   - Amizade (slug: amizade)
[2026-05-29 19:00:00] [INFO]   - Games (slug: games)
[2026-05-29 19:00:00] [INFO] Python Binary: python
[2026-05-29 19:00:05] [PYTHON_START] ========== INICIANDO COLETA PYTHON ==========
[2026-05-29 19:00:05] [PYTHON_INFO] Total de categorias para processar: 10
[2026-05-29 19:00:06] [PYTHON_INFO] [1/10] Processando categoria: Amizade (slug: amizade)
[2026-05-29 19:00:06] [PYTHON_INFO]   → Tentando Bing com 2 queries
[2026-05-29 19:00:08] [PYTHON_DEBUG] Tentando acessar: https://www.bing.com/search?q=...
[2026-05-29 19:00:10] [PYTHON_DEBUG] URL acessada com sucesso: https://www.bing.com/search (15234 bytes)
[2026-05-29 19:00:10] [PYTHON_DEBUG] Extraídos 3 links únicos do HTML
[2026-05-29 19:00:10] [PYTHON_INFO]   → Bing retornou 3 links
[2026-05-29 19:00:15] [PYTHON_INFO]   → Total de links únicos para Amizade: 3
[2026-05-29 19:00:15] [PYTHON_INFO] [2/10] Processando categoria: Games...
...
[2026-05-29 19:00:45] [PYTHON_END] ========== COLETA PYTHON CONCLUÍDA ==========
[2026-05-29 19:00:45] [PYTHON_SUMMARY] Total de links coletados (com duplicatas): 45
[2026-05-29 19:00:45] [PYTHON_SUMMARY] Total de links únicos: 38
[2026-05-29 19:00:50] [INFO] Tamanho do stdout: 2345 bytes
[2026-05-29 19:00:50] [INFO] Total de links coletados pelo Python: 38
[2026-05-29 19:00:51] [DEBUG] Link #0: DUPLICADO (já existe no banco) - https://chat.whatsapp.com/abc123...
[2026-05-29 19:00:52] [SUCCESS] Link #1: ✓ IMPORTADO com sucesso - https://chat.whatsapp.com/xyz789... (Categoria: Amizade)
[2026-05-29 19:01:00] [SUMMARY] ========== RESUMO DA COLETA ==========
[2026-05-29 19:01:00] [SUMMARY] Novos grupos importados: 25
[2026-05-29 19:01:00] [SUMMARY] Duplicados encontrados: 10
[2026-05-29 19:01:00] [SUMMARY] Links inválidos: 1
[2026-05-29 19:01:00] [SUMMARY] Categorias não encontradas: 2
[2026-05-29 19:01:00] [SUMMARY] Tempo total de execução: 15.23s
[2026-05-29 19:01:00] [END] ========== FIM DA COLETA ==========
```

## 🔍 Tipos de Log

- `[START]` / `[END]` - Início e fim da coleta
- `[INFO]` - Informações gerais sobre o processo
- `[DEBUG]` - Detalhes específicos (acessos a URLs, extração de links)
- `[SUCCESS]` - Grupos importados com sucesso
- `[WARNING]` - Avisos (links inválidos, categorias não encontradas)
- `[ERROR]` - Erros críticos
- `[SUMMARY]` - Resumo final com estatísticas
- `[PYTHON_*]` - Logs específicos do script Python

## 📊 Interpretando os Resultados

### Importações Bem-Sucedidas
```
[SUCCESS] Link #1: ✓ IMPORTADO com sucesso - https://chat.whatsapp.com/xyz... (Categoria: Games)
```

### Duplicados (Já Existem no Banco)
```
[DEBUG] Link #0: DUPLICADO (já existe no banco) - https://chat.whatsapp.com/abc...
```

### Erros de Processamento
```
[WARNING] Link #5: Categoria 'categoria-invalida' não encontrada - https://chat.whatsapp.com/...
[WARNING] Link #3: Inválido (link vazio ou categoria vazia)
```

### Falhas na Coleta Python
```
[ERROR] Erro ao executar script Python. Código: 1. Erro: ModuleNotFoundError...
```

## 🛠️ Troubleshooting

### Arquivo de log muito grande
Se o arquivo crescer muito, você pode limpá-lo:

```bash
# Limpar o log (início fresco)
rm storage/logs/mineracao.log
```

### Verifica se o log está sendo criado
```bash
# Linux/Mac
tail -f storage/logs/mineracao.log

# Windows PowerShell
Get-Content storage/logs/mineracao.log -Wait
```

### Nenhum grupo está sendo importado
Verifique o log para:
1. Se o Python está sendo executado (`[PYTHON_START]`)
2. Se links estão sendo coletados (`Total de links coletados...`)
3. Se há duplicados ou erros de categoria

## 📈 Monitoramento em Produção

Para acompanhar a coleta automatizada:

```bash
# Terminal 1: Executar coleta
php artisan whatsgrupos:povoar --limit=99999

# Terminal 2: Acompanhar logs em tempo real
php artisan mineracao:log --follow
```

O comando `--follow` mostrará as atualizações em tempo real, similar a `tail -f` no Linux.
