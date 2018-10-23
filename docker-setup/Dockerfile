FROM  ubuntu:14.04
MAINTAINER gopala.krishna@poweredbyu.com
ENV DEBIAN_FRONTEND noninteractive
ENV user  usoft
ENV suid 1000
ENV sgid 1000
RUN export LANG=en_US.UTF-8 && apt-get clean && apt-get update && apt-get install -y  --no-install-recommends apt-utils locales && locale-gen en_US.UTF-8 && LC_ALL=en_US.UTF-8 && \
    echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections && \
    apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C && \
    apt-get update && \
    apt-get install -y software-properties-common && \
    apt-add-repository -y ppa:ondrej/php && \
    apt-add-repository -y ppa:ondrej/apache2 && \
    apt-add-repository ppa:git-core/ppa && \
    apt-get update && \
    apt-get install -y php7.1 php7.1-cli php7.1-dev php7.1-mysql php-gettext php7.1-mbstring php-xdebug libapache2-mod-fastcgi php7.1-curl php7.1-gd php7.1-mcrypt php7.1-intl php7.1-xml php7.1-xmlrpc php7.1-xsl php7.1-zip php7.1-bcmath php7.1-bz2 php7.1-fpm apache2 php7.1-imagick libmagickwand-dev apt-transport-https cron git mysql-client-5.6 postfix mailutils curl lsof wget unzip telnet openssh-client vim nano && \
   apt-get install -y  python-software-properties && \
   curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash - && \
   apt-get update && \
   apt-get install -y  nodejs && \
   npm install -g @vue/cli && \
   npm install -g @vue/cli-init && \
   npm install -g create-nuxt-app && \
   useradd -ms /bin/bash ${user} && \
   usermod -u ${suid} ${user} && groupmod -g ${sgid} ${user} && \
   mkdir -p /home/${user}/bin && \
   echo "${user} ALL=(root) NOPASSWD:ALL" > /etc/sudoers.d/${user} && chmod 0440 /etc/sudoers.d/${user} && \
   chown -R ${user}:${user} /var/lib/apache2/fastcgi && \
   mkdir  /root/.ssh && \
   apt-get clean && \
   apt-get autoremove && \
   rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
   curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer && \
   rm -rf /root/.composer/cache/* && \
   cd /etc/apache2/mods-available && \
   a2enmod authz_groupfile.load cache.load cgi.load expires.load http2.load headers.load reqtimeout.load rewrite.load socache_shmcb.load proxy_fcgi.load ssl.load
ADD https://github.com/just-containers/s6-overlay/releases/download/v1.11.0.1/s6-overlay-amd64.tar.gz /tmp/
RUN gunzip -c /tmp/s6-overlay-amd64.tar.gz | tar -xf - -C /
EXPOSE 80 443 3000 8080 9100 9101
WORKDIR /home/usoft
ENTRYPOINT ["/usr/bin/s6-svscan", "/etc/s6"]
