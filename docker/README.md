# Установка `docker`&`docker-compose` окгружения

## Установка на чистом `CentOS 7`

```bash
# Добавление нового пользователя с правами sudo
adduser fastuser
passwd fastuser
usermod -aG wheel fastuser

# Далее работа от нового пользователя
sudo su fastuser

# Установка docker
cd ~ && wget -qO- https://get.docker.com/ | sudo sh
sudo usermod -aG docker $(whoami)
sudo systemctl enable docker.service
sudo systemctl start docker.service

# Установка docker-compose
cd ~ && sudo curl -L https://github.com/docker/compose/releases/download/1.29.2/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
sudo ln -s /usr/local/bin/docker-compose /usr/bin/docker-compose
docker-compose --version
```
