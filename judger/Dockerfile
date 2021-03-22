FROM ubuntu:18.04

RUN apt update && apt install -y g++ gcc python3.7 python2.7 libcap-dev make wget unzip python3-pip openjdk-11-jdk-headless default-jre openjdk-11-jre-headless
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
