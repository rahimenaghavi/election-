# FUM-Election with Docker swarm
# 1.configure Docker swarm
first step is installing docker and virtualbox in ubuntu and use them in project.
after that we configure swarm cluster for election portals which consist of one manager node as LoadBalancer and two wrkers as election portals.
```
docker-machine create --driver virtualbox manager
docker-machine create --driver virtualbox worker1
docker-machine create --driver virtualbox worker3

```
after run the above commands, with docker-machine ls we can observe manager and workers IP's on the network.
by the below command we can go to manager node and recieve tokens of joined workers to the manager.
```
docker-machine ssh manager
swarm init --advertise-addr [manager node IP]

```
by the help of below commands we can go to both workers and attach them to manager by swarm join command.
```
docker-machine ssh worker1
docker swarm join --token SWMTKN-1-46kvmprfpbo03dofelt1foqnvks9keymweludtx8mej0ijhu77-1ahzn5au230kdmep8qmo79pwr 192.168.99.124:2377
docker-machine ssh worker2
docker swarm join --token SWMTKN-1-46kvmprfpbo03dofelt1foqnvks9keymweludtx8mej0ijhu77-1ahzn5au230kdmep8qmo79pwr 192.168.99.124:2377

```
add below command to create 2 replicas from php:7.2-apache service which is runing on the worker nodes.
attention! the difference between replicated and global is on --mode: 
in global mode, each service run in any active workers and its impossible to run more than one service on the worker(x) while in replica mode it may happen.
```
docker service create --name swarm-nodes --publish 8087:80 --mode global 

```
for access to woker's bash or manager's bash, we must run following command:
```
docker exec -ti "container id of nodes" bash

```
index.php should be created on var/www/html directory. afterward php codes must develope on it.
since, the responsibility of manager node is load balancing, we must run following command on manager node,:
```
docker node update --availaibility drain manager
###################################################################
sudo docker-machine scp -r docker-compose1.yml manager:/home
[sudo] password for rahime: 
docker-compose1.yml                           100% 2070     1.9MB/s   00:00
rahime@rahime-K45VD:~/Desktop/project-f$ sudo docker-machine ssh manager
   ( '>')
  /) TC (\   Core is distributed with ABSOLUTELY NO WARRANTY.
 (/-_--_-\)           www.tinycorelinux.net

docker@manager:~$ cd /home/
docker@manager:/home$ docker stack deploy --compose-file docker-compose1.yml Rnet
Creating service Rnet_auth
Creating service Rnet_election_ui
Creating service Rnet_mongo
Creating service Rnet_visualizer
Creating service Rnet_electionmanager
Creating service Rnet_electionportal
Creating service Rnet_electionManagerDb
Creating service Rnet_electionPortalDb
docker@manager:/home$ docker service ls
ID                  NAME                     MODE                REPLICAS            IMAGE                                           PORTS
fq4p7d238kd6        Rnet_auth                replicated          1/1                 sayid/auth:latest                               *:8023->2000/tcp
1pvldjf5pyf8        Rnet_electionManagerDb   replicated          1/1                 ardalanfp/election_manager_db:latest
ldqx3k0lysdw        Rnet_electionPortalDb    replicated          0/1                 ardalanfp/election_portal_db:latest
ja4c96nyq5mf        Rnet_election_ui         replicated          1/1                 sayid/election_ui:latest                        *:8024->9090/tcp
03kzx8e8t2pl        Rnet_electionmanager     replicated          1/1                 ardalanfp/fum_election_electionmanager:latest   *:8021->8080/tcp
72c5gxk7bj7h        Rnet_electionportal      replicated          1/1                 ardalanfp/fum_election_electionportal:latest    *:8022->8090/tcp
ywv1ee7gpysc        Rnet_mongo               replicated          1/1                 mongo:latest
pdgmpbcyxg9t        Rnet_visualizer          replicated          0/1                 dockersamples/visualizer:latest                 *:8020->8080/tcp
->8080/tcp

`
