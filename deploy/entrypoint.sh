#!/bin/bash

# Generate a new self-signed SSL certificate when none is provided in the volume.
if [ ! -f /etc/nginx/ssl/kanboard.key ] || [ ! -f /etc/nginx/ssl/kanboard.crt ]
then
    openssl req -x509 -nodes -newkey rsa:2048 -keyout /etc/nginx/ssl/kanboard.key -out /etc/nginx/ssl/kanboard.crt -subj "/C=GB/ST=London/L=London/O=Self Signed/OU=IT Department/CN=kanboard.org"
fi

chown -R nginx:nginx /var/www/app/data

# GH Çalıştay mounts plugins/ read-only from the repository. Avoid noisy
# chown failures in that case; a writable plugins volume still works.
if [ -w /var/www/app/plugins ]
then
    chown -R nginx:nginx /var/www/app/plugins
fi

exec /usr/bin/s6-svscan /etc/services.d
