ARG PROJECT_NAME
ARG PHP_FPM_IMAGE=${PROJECT_NAME}-php-fpm:latest

FROM ${PHP_FPM_IMAGE}

# Instala cron e Supervisor
RUN apt-get update && \
    apt-get install -y supervisor cron && \
    rm -rf /var/lib/apt/lists/*

COPY supervisord.d/*.conf /etc/supervisor/conf.d/

#RUN (crontab -l -u root 2>/dev/null; echo "* * * * * /usr/local/bin/php /var/www/artisan schedule:run >> /var/www/storage/logs/cron.log 2>&1") | crontab -u root -

# Copia o script de inicialização
#COPY start.sh /usr/local/bin/start.sh
#RUN chmod +x /usr/local/bin/start.sh

# Define o ponto de entrada
#CMD ["/usr/local/bin/start.sh"]

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]