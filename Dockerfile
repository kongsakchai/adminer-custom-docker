FROM adminer:latest

WORKDIR /var/www/html

USER root

RUN rm -rf /var/www/html/designs

COPY ./assets /var/www/html/assets
COPY ./styles.css /var/www/html/adminer.css
COPY ./plugins/cutomizeUI.php /var/www/html/plugins-enabled/customizeUI.php

EXPOSE 8080

CMD	[ "php", "-S", "[::]:8080", "-t", "/var/www/html" ]

HEALTHCHECK CMD [ "curl", "-f", "http://localhost:8080" ]
