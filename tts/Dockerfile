FROM debian:10

RUN apt-get update \
    && apt-get install -y \
        # Python
        python3 \
        python3-pip

COPY src/ /usr/src/app
WORKDIR /usr/src/app

RUN pip3 install --no-cache-dir -r requirements.txt

CMD [ "python3", "waitress_server.py" ]