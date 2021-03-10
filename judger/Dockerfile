FROM ubuntu:18.04

RUN apt update
RUN apt install -y systemd g++ gcc default-jre openjdk-11-jre-headless python3.7 python2.7 libcap-dev make git wget unzip python3-pip
RUN wget https://github.com/ioi/isolate/archive/master.zip
RUN unzip master.zip
WORKDIR isolate-master
RUN make install
RUN pip3 install pymysql

WORKDIR /var
RUN mkdir ./judge
WORKDIR /var/judge
COPY ./judger.py ./
RUN chmod +u+x ./judger.py

ENTRYPOINT ["/var/judge/judger.py"]
