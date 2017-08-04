sudo apt-get update
sudo apt-get -y install \
 apt-transport-https \
 ca-certificates \
 curl \
 software-properties-common
curl -fsSL https://download.docker.com/linux/debian/gpg | sudo apt-key add -
sudo add-apt-repository \
 "deb [arch=amd64] https://download.docker.com/linux/debian \
 $(lsb_release -cs) \
 stable"
sudo groupadd docker
sudo usermod -aG docker $USER
sudo apt-get update
sudo apt-get -y install docker-ce
sudo systemctl start docker

