#!/bin/bash


create_file() {
    if ! [[ -f $1 ]]; then
        touch $1
    fi
}

if !docker network inspect devproj > /dev/null 2>&1; then
     docker network create --subnet=202.22.2.0/24 devproj
fi

create_file ./history/usoft.history
create_file ./history/mysql.root.history
create_file ./history/redis.root.history
create_file ./history/redis-cli.history

docker-compose up -d
docker-compose ps

docker exec -ti apis3_web /bin/bash -c "cd /var/www/htdocs/movies; exec ${SHELL:-sh}"
#composer install
#bin/console doctrine:migrations:migrate
#bin/console doctrine:fixtures:load
#mysql -u usoft -h 202.22.2.33 -p1235 movies

#docker exec -ti apis3_redis /bin/sh -c "redis-cli MONITOR"