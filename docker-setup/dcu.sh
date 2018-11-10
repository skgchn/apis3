#!/bin/bash

# Step 1
# cd docker-setup

# Step 2
#docker build  -t node8php7.1-web:1.0 .

# Step 3
# Run this bash script from which you are reading the instructions.

create_file() {
    if ! [[ -f $1 ]]; then
        touch $1
    fi
}

if !docker network inspect devproj > /dev/null 2>&1; then
     docker network create --subnet=202.22.2.0/24 devproj
fi

create_file ./config/s6web/web/supervise/status
create_file ./history/usoft.history
create_file ./history/mysql.root.history
create_file ./history/redis.root.history
create_file ./history/redis-cli.history

docker-compose up -d
docker-compose ps

docker exec -ti apis3_web /bin/bash -c "cd /var/www/htdocs/movies; exec ${SHELL:-sh}"

# Step 4
#composer install

# Step 5
#bin/console doctrine:migrations:migrate

#Step 6
#bin/console doctrine:fixtures:load

# Step 7
# Make hosts file entry
# 202.22.2.55     apis3.test www.apis3.test

# Note
# You may have to generate your own SSL certificates for the domain and place in docker-setup/config/webapache/devssl.
# Also you may have to import your private CA certificate as trusted CA in your browser.

# To run mysql in web container
#mysql -u usoft -h 202.22.2.33 -p1235 movies

# In another terminal - for monitoring redis (access log)
#docker exec -ti apis3_redis /bin/sh -c "redis-cli MONITOR"
