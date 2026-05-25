#!/bin/bash
# scripts/start_server.sh
# CardápioDigital - Aula 5.3
# Inicia o Nginx após o deploy do CodeDeploy
# Executado pelo CodeDeploy Agent na hook AfterInstall

sudo systemctl start nginx
