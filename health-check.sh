#!/bin/bash
# Health check do CardápioDigital
# Curso "Do Zero à Nuvem" · Aula 4.12
#
# Verifica se o site está respondendo corretamente.
# Executado pelo cron a cada 5 minutos (*/5 * * * *)
# Retorna exit 0 em sucesso e exit 1 em qualquer outro código (para CI/CD)

URL="https://cardapiodigitalaws.com.br"
DATA=$(date '+%Y-%m-%d %H:%M:%S')

echo "[$DATA] Verificando $URL..."

STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$URL")

if [ "$STATUS" -eq 200 ]; then
    echo "[$DATA] OK — site respondendo. Status: $STATUS (deploy via pipeline)"
    exit 0
else
    echo "[$DATA] FALHA — status inesperado: $STATUS"
    exit 1
fi
