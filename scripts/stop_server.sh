#!/bin/bash
# scripts/stop_server.sh
# CardápioDigital - Aula 5.3
# Para o Nginx antes do deploy do CodeDeploy
# Executado pelo CodeDeploy Agent na hook BeforeInstall

sudo systemctl stop nginx

