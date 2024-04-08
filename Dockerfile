FROM ghcr.io/dnj/php-alpine:8.2-mysql-nginx

ARG TSCONFIG_PATH="packages/ghafiye/frontend-userpanel/tsconfig.json"

RUN apk --no-cache add --virtual .dev pcre-dev yaml-dev ${PHPIZE_DEPS} \
  && pecl install timezonedb yaml \
  && docker-php-ext-install calendar \
  && docker-php-ext-enable timezonedb yaml calendar \
  && apk del .dev \
  && apk add yaml bind-tools \
  && echo 'php_admin_value[upload_max_filesize] = 100M' >> /usr/local/etc/php-fpm.d/www.conf \
  && echo 'php_admin_value[post_max_size] = 100M' >> /usr/local/etc/php-fpm.d/www.conf \
  && echo 'date.timezone = Asia/Tehran' > /usr/local/etc/php/conf.d/tz.ini \
  && echo 'max_input_vars = 65535' > /usr/local/etc/php/conf.d/zz-araduser.ini

COPY --chown=www-data:www-data . /var/www/html
COPY packages/dockerize/nginx/jalno.conf /etc/nginx/conf.d/default.conf.d/

RUN rm -fr packages/dockerize; \
	find /var/www/html -type d -name ".docker" -prune -exec rm -fr {} \;; \
	if [[ -d /var/www/html/packages/node_webpack ]]; then \
		chown -R root:root /var/www/html && \
		mkdir -p /tmp/nodejs && \
		cd /tmp/nodejs && \
		wget -O nodejs.tar.gz https://unofficial-builds.nodejs.org/download/release/v14.9.0/node-v14.9.0-linux-x64-musl.tar.gz && \
		tar --strip-components=1 -xf nodejs.tar.gz && \
		ln -s /tmp/nodejs/bin/node /usr/bin/node && \
		ln -s /tmp/nodejs/bin/npm /usr/bin/npm && \
		ln -s /tmp/nodejs/bin/npx /usr/bin/npx && \
		cd /var/www/html/packages/node_webpack/nodejs && \
		mkdir -p storage/public/frontend/dist && \
		if [[ -n "$TSCONFIG_PATH" ]]; then \
			NODE_ENV=production npm start -- --production --tsconfig=$TSCONFIG_PATH; \
		else \
			NODE_ENV=production npm start -- --production; \
		fi; \
		find /var/www/html -type d -name "node_modules" -prune -exec rm -fr {} \;; \
		rm -fr ~/.npm ~/.config /tmp/nodejs /usr/bin/node /usr/bin/npm /usr/bin/npx && \
		chown -R www-data:www-data /var/www/html; \
	fi; \
	(crontab -l 2>/dev/null; echo -e -n \
		'* * * * * php /var/www/html/index.php --process=packages/ghafiye/processes/crawler@run' '\n' \
	) | crontab -

WORKDIR /var/www/html
