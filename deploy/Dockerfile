# GH Çalıştay — Global Hukukçular Derneği
#
# The upstream Kanboard release already contains PHP 8.5, nginx and the
# production entrypoint. We only add the branding/membership plugin so the
# image stays close to the audited upstream release.

FROM kanboard/kanboard:v1.2.58

LABEL org.opencontainers.image.title="GH Çalıştay" \
      org.opencontainers.image.description="Global Hukukçular Derneği üyelerine özel Kanban panosu" \
      org.opencontainers.image.vendor="Global Hukukçular Derneği" \
      org.opencontainers.image.licenses="MIT" \
      org.opencontainers.image.url="https://calistay.globalhukukcular.com" \
      org.opencontainers.image.source="https://github.com/kadircicek34/ghd-calistay"

COPY app/Core/Http/Route.php /var/www/app/app/Core/Http/Route.php
COPY plugins/GhdMembership /var/www/app/plugins/GhdMembership
