#!/bin/bash

# ==============================================================================
# SCRIPT DE DEPLOY E AUTOMAÇÃO PARA O SERVIDOR - WHATSGRUPOS
# ==============================================================================
# Este script automatiza todas as tarefas necessárias após você fazer o push do 
# código para o servidor (como limpar cache, rodar migrations, reiniciar filas).
# 
# Modo de uso no servidor:
# 1. Dê permissão de execução: chmod +x deploy.sh
# 2. Execute: ./deploy.sh
# ==============================================================================

set -e

echo "🚀 Iniciando processo de Deploy e Atualização do Servidor..."

# 1. Ativar modo de manutenção para evitar erros para os usuários enquanto atualiza
echo "🚧 Colocando o aplicativo em modo de manutenção..."
php artisan down || true

# 2. Puxar as últimas atualizações do Git (Descomente a linha abaixo se quiser que o script puxe do git automaticamente)
# echo "📥 Baixando código do repositório..."
# git pull origin main

# 3. Instalar dependências do Composer otimizadas para produção
echo "📦 Instalando dependências do Composer..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 4. Limpar e recriar o cache de toda a aplicação (Config, Rotas, Views e Eventos)
echo "🧹 Limpando e otimizando cache da aplicação..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 5. Executar as Migrations do Banco de Dados
echo "🗄️ Executando Migrations do Banco de Dados..."
php artisan migrate --force

# 6. Reiniciar os Workers das Filas/Jobs (Para que eles leiam o novo código recém baixado)
echo "⚙️ Reiniciando Workers e Filas (Jobs)..."
php artisan queue:restart

# 7. (Opcional) Executar comandos customizados que você tenha criado para o sistema
# Descomente e adicione abaixo caso tenha comandos que devem rodar a cada deploy
# echo "🤖 Executando comandos personalizados..."
# php artisan seu_comando:customizado

# 8. (Opcional) Rodar Seeders (Descomente se precisar popular o banco num primeiro deploy)
# echo "🌱 Populando Banco com Seeders..."
# php artisan db:seed --force

# 9. Link do Storage
echo "🔗 Verificando link simbólico do storage..."
php artisan storage:link || true

# 10. Desativar modo de manutenção
echo "✅ Voltando o site para o ar..."
php artisan up

echo "🎉 Deploy finalizado com sucesso! Tudo está rodando e atualizado no servidor."
