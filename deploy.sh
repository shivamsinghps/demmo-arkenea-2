
git pull

cd ./environment

docker-compose stop

docker rm -vf $(docker ps -aq)

docker rmi -f $(docker images -aq)

docker-compose up -d --build