#!/bin/sh
/bin/cp /home/usoft/bin/imagick/imagick.so /usr/lib/php/20131226/
/bin/cp /home/usoft/bin/tclink/tclink.so /usr/lib/php/20131226/
/bin/cp /home/usoft/bin/imagick/imagick.ini /etc/php/5.6/mods-available/
/bin/cp /home/usoft/bin/tclink/tclink.ini /etc/php/5.6/mods-available/
/bin/ln -s /etc/php/5.6/mods-available/tclink.ini /etc/php/5.6/fpm/conf.d/20-tclink.ini
/bin/ln -s /etc/php/5.6/mods-available/imagick.ini /etc/php/5.6/cli/conf.d/20-imagick.ini
/bin/ln -s /etc/php/5.6/mods-available/imagick.ini /etc/php/5.6/fpm/conf.d/20-imagick.ini
/bin/ln -s /etc/php/5.6/mods-available/tclink.ini /etc/php/5.6/cli/conf.d/20-tclink.ini
/bin/cp /home/usoft/bin/apache/apache2.conf /etc/apache2/
/bin/cp /home/usoft/bin/apache/envvars /etc/apache2/
/bin/cp /home/usoft/bin/apache/ports.conf /etc/apache2/
/bin/cp -ar /home/usoft/bin/jobboardssl /etc/ssl/
/bin/cp /home/usoft/bin/apache/000-default.conf /etc/apache2/sites-available/
/bin/cp /home/usoft/bin/apache/jobboarddev-nonssl.conf /etc/apache2/sites-available/
/bin/cp /home/usoft/bin/apache/jobboarddev-ssl.conf /etc/apache2/sites-available/
/bin/cp /home/usoft/bin/ssh/id_rsa /root/.ssh/
/bin/chmod 600 /root/.ssh/id_rsa
/bin/chown -R usoft:usoft /var/lib/apache2/fastcgi
/bin/cp /home/usoft/bin/ssh/id_rsa.pub /root/.ssh/
/bin/ln -s /etc/apache2/sites-available/jobboarddev-nonssl.conf /etc/apache2/sites-enabled/
/bin/ln -s /etc/apache2/sites-available/jobboarddev-ssl.conf /etc/apache2/sites-enabled/
/bin/cp -ar /home/usoft/bin/fpm/pool.d /etc/php/5.6/fpm/
/bin/cp -ar /home/usoft/bin/fpm/php.ini /etc/php/5.6/fpm/
/bin/cp -ar /home/usoft/bin/fpm/php-fpm.conf /etc/php/5.6/fpm/
/bin/cp /home/usoft/bin/postfix/main.cf /etc/postfix/
/bin/mkdir -p /var/www/html
/etc/init.d/php5.6-fpm start
/etc/init.d/postfix restart
/bin/sh /home/usoft/bin/apache.sh
