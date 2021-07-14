FROM httpd:2.4

ARG RGPDCVC_ENDPOINT

WORKDIR /usr/local/apache2/htdocs/

COPY . .

RUN sed -i "s|RGPDCVC_ENDPOINT|$RGPDCVC_ENDPOINT|g" index.html