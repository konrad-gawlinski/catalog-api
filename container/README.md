# Steps to setup the machine.

## Install Docker
1) run `./install.sh` and restart 
 * it will install docker
 * it will create docker user group and add current user to that group, so that sudo is not needed to execute 'docker' command
2) run `./setup-docker.sh`
 * it will create a network for virutal machines. User-defined networks automaticly link all machines that belong to the network.

## Install Nginx and Php-Fpm
1) go to `webapp` directory
2) run `./build-webapp.sh`
 * it will create a docker image called 'private/webapp'
3) run `./run-container.sh`
 * it will start the http and php-fpm services
 * the services are started through superviser automatically on the container startup
4) login to the machine `docker exec -it webapp-01 /bin/bash`
5) go to `/var/webapp`
6) run `php /usr/local/composer.phar install`

## Install Postgres
1) go to `postgres` directory
2) run `./build-webapp.sh`
 * it will create a docker image called 'private/postgres'
3) run `./run-container.sh`
 * it will start the postgres service
 * the services are started through superviser automatically on the container startup
4) login to the machine `docker exec -it postgres-01 /bin/bash`
5) run `su -m postgres -c "/usr/local/pgsql/bin/psql -c \"ALTER ROLE postgres WITH PASSWORD 'postgres'\""`
 * it will set the 'postgres' password for the 'postgres' user
