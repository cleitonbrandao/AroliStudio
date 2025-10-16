FROM ubuntu:22.04

RUN apt-get update && apt-get install -y tinyproxy && \
    rm -rf /var/lib/apt/lists/*

COPY tinyproxy.conf /etc/tinyproxy/tinyproxy.conf

EXPOSE 8888

CMD ["tinyproxy", "-d"]
