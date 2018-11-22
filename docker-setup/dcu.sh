#!/bin/bash

# Step 1
# cd docker-setup

# Step 2
# docker build  -t node8php7.1-web:1.0 .

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
# composer install

# Step 5
# bin/console doctrine:migrations:migrate

# Step 6
# bin/console doctrine:fixtures:load

# Step 7
# Make hosts file entry
# 202.22.2.55     apis3.test www.apis3.test

# Step 8
# You may have to generate your own SSL certificates for the domain and place in docker-setup/config/webapache/devssl.
# Update SSLCertificateFile, SSLCertificateKeyFile in docker-setup/config/webapache/apache/25-app-ssl.conf file.
# Also you may have to import your private CA certificate as trusted CA in your browser.

# Step 9
# Install any REST client browser extention, I am using Tabbed Postman (https://github.com/oznu/postman-chrome-extension-legacy)

# Step 10
# An unauthenticated API endpoint is https://apis3.test/messages
# Use GET as the HTTP method, set Accept header as application/json

# For exchanging username and password with a JWT,
# 1. Click on Basic auth tab.
# 2. Use API endpoint, https://apis3.test/tokens
# 3. Use POST as the HTTP method
# 4. Enter username user1 and password Secure123!
# 5. Click on Refresh headers button.
# 6. Set both Content-Type and Accept headers as application/json
# 7. Click Send

# For changing user's password
# 1. Use API endpoint https://apis3.test/users/20
# 2. Use PATCH as the HTTP method
# 3. Set both Content-Type and Accept headers as application/json
# 4. Add Header Authorization, set it's value to Bearer replace-this-with-a-JWT
# 5. In the body add the new password and the retyped_password.
# {
#   "password": "Secure1234!",
#   "retyped_password":"Secure1234!"  
# }
# 6. Send

# To run mysql in web container
# mysql -u usoft -h 202.22.2.33 -p1235 movies

# In another terminal - for monitoring redis (access log)
# docker exec -ti apis3_redis /bin/sh -c "redis-cli MONITOR"
