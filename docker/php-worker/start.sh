#!/bin/bash

# Inicia o cron
service cron start

# Carrega variáveis de ambiente (caso necessário)
# export $(grep -v '^#' /var/www/.env | xargs)

# Mantém o Supervisor em primeiro plano (para gerenciar outros processos)
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]