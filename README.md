# CardápioDigital 🍽️

Sistema de cardápio digital para restaurante construído do zero na AWS.

Projeto desenvolvido no curso **Do Zero à Nuvem** (Missão AWS) como portfólio de transição de carreira para cloud.

---

## 🏗️ Arquitetura

```
Usuário → Route 53 → ACM (HTTPS) → WAF → ALB
         → ECS Fargate (Container Docker — Nginx + PHP-FPM 8.4)
              → RDS MySQL (subnet privada)
              → S3 (imagens dos pratos)
              → CloudWatch (monitoramento)
              → SNS (notificações de alarmes)
         → CodePipeline (deploy automatizado)
         → CloudFormation (infraestrutura como código)
```

---

## ☁️ Serviços AWS utilizados

| Serviço | Função |
|---------|--------|
| EC2 | Servidor web com Nginx + PHP (ambiente de build/dev) |
| VPC + Subnets | Rede privada isolada — pública para ALB, privada para RDS |
| Security Groups | Controle de acesso por porta e IP — SG do ALB separado do SG dos containers |
| Route 53 | DNS — `cardapiodigitalaws.com.br` aponta para o ALB |
| ACM | Certificado SSL gratuito (HTTPS) |
| WAF | Firewall — bloqueia requisições maliciosas antes do ALB |
| S3 | Armazenamento de imagens dos pratos |
| RDS MySQL | Banco de dados gerenciado em subnet privada |
| ECS Fargate | Orquestração de containers sem servidor (com Container Insights) |
| ECR | Repositório de imagens Docker |
| ALB | Load balancer na frente dos containers ECS |
| CloudWatch | Métricas, logs, alarmes e dashboard com auto-refresh |
| SNS | Notificações por e-mail dos alarmes do CloudWatch |
| CodePipeline | Orquestração da pipeline CI/CD |
| CodeBuild | Build da imagem Docker e push para ECR |
| CodeDeploy | Deploy automatizado |
| CloudFormation | Infraestrutura como código |
| IAM | Controle de acesso e permissões |

---

## 🐳 Stack técnica

- **Linguagem:** PHP 8.4 (container) / PHP 8.5 (EC2 dev)
- **Servidor web:** Nginx + PHP-FPM (imagem base `php:8.4-fpm-bookworm`)
- **Banco de dados:** MySQL 8
- **Containers:** Docker + ECS Fargate
- **CI/CD:** CodePipeline + CodeBuild + CodeDeploy
- **IaC:** CloudFormation
- **Versionamento:** Git + GitHub
- **Região AWS:** sa-east-1 (São Paulo)

---

## 📁 Estrutura do repositório

```
cardapio-digital/
├── index.php               # Cardápio dinâmico consultando RDS via getenv()
├── Dockerfile              # Imagem Docker (php:8.4-fpm-bookworm + Nginx)
├── nginx.conf              # Config do Nginx (logs em stdout/stderr)
├── www.conf                # Config do PHP-FPM (clear_env = no)
├── health-check.sh         # Script bash de monitoramento (cron a cada 5 minutos)
├── README.md               # Esta documentação
└── .gitignore              # Padrões ignorados pelo Git
```

> Os artefatos da pipeline CI/CD (`appspec.yml`, `buildspec.yml`, `scripts/`) são adicionados ao repositório nas aulas seguintes do curso.

---

## 🚀 Pipeline CI/CD

Qualquer `git push` na branch `main` dispara automaticamente:

1. **Source** — CodePipeline detecta a mudança no GitHub
2. **Build** — CodeBuild constrói a imagem Docker e faz push para o ECR (tag = hash do commit)
3. **Deploy** — CodePipeline atualiza o serviço ECS com a nova imagem (rolling update)

Em 2-3 minutos, o site em produção (`cardapiodigitalaws.com.br`) reflete a mudança — sem SSH, sem comandos manuais.

---

## 🔐 Segurança

- Credenciais do banco de dados **nunca** estão no código — passadas via variáveis de ambiente (Task Definition do ECS) e lidas no PHP com `getenv()`
- Banco de dados em **subnet privada** — sem acesso direto da internet
- HTTPS obrigatório via ACM (listener 80 do ALB faz redirect 301 → 443)
- WAF ativo na frente do ALB (Anti-DDoS L7, Common Rule Set, SQL Injection, Known Bad Inputs)
- Security Group dos containers só aceita tráfego vindo do Security Group do ALB — na prática, acesso à aplicação somente via ALB (a task tem IP público apenas para o pull da imagem no ECR)
- `.gitignore` cobre `.pem`, `.key`, `.env`, `*.log` e outros padrões sensíveis

---

## 📊 Observabilidade

- **2 alarmes ativos:** CPU > 80% por 10 minutos / Latência ALB > 2s por 5 minutos
- **Notificações via SNS** para e-mail quando um alarme dispara
- **Container Insights** habilitado no cluster — métricas detalhadas por task
- **Logs do Nginx** em CloudWatch Logs (via stdout/stderr do container)
- **Logs Insights** para queries customizadas nos logs
- **Dashboard** com auto-refresh de 1 minuto e widget de status dos alarmes

---

## ▶️ Como rodar localmente (ou em outro EC2)

Pré-requisitos: Docker instalado, conta AWS com RDS MySQL acessível.

```bash
# Build da imagem
docker build -t cardapio-digital:v1 .

# Run com env vars do banco
docker run -d -p 8080:80 \
  -e DB_HOST=seu-rds-endpoint.amazonaws.com \
  -e DB_USER=admin \
  -e DB_PASS=senha-segura \
  -e DB_NAME=cardapio \
  --name cardapio-app cardapio-digital:v1
```

Acesse em `http://localhost:8080`.

---

## 👤 Autor

Desenvolvido por **Vinicius Siqueira** como projeto de portfólio do curso Do Zero à Nuvem.

[![LinkedIn](https://img.shields.io/badge/LinkedIn-0077B5?style=flat&logo=linkedin&logoColor=white)](https://linkedin.com/in/vcsiqueira)
[![GitHub](https://img.shields.io/badge/GitHub-181717?style=flat&logo=github&logoColor=white)](https://github.com/viniciussiqueiraaws)
