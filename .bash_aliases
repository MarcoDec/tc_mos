# Aliases Docker
alias docker:build='GROUP_ID=$(id -g) USER_ID=$(id -u) USER_NAME=$USER docker-compose build'
alias docker:build:nocache='docker:build --no-cache'
alias docker:clean:containers='docker rm $(docker ps -a -q)'
alias docker:clean:images='docker rmi $(docker images -q)'
alias docker:cleanandrecreate='docker:stop && docker:clean:containers && docker:clean:images && docker:recreate'
alias docker:stop='GROUP_ID=$(id -g) USER_ID=$(id -u) USER_NAME=$USER docker-compose stop'
alias docker:rerun='docker:stop && docker:run'
alias docker:run='GROUP_ID=$(id -g) USER_ID=$(id -u) USER_NAME=$USER docker-compose up -d'
docker_recreate() {
    docker:stop && docker:build:nocache && docker:run
}
alias docker:recreate='time docker_recreate'

# Aliases Projet
alias docker:php='docker exec -ti tconcept_gpao_php zsh'
