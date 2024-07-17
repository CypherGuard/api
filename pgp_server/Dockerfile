FROM python:3.12

WORKDIR /pgp_server
COPY . .

RUN apt-get update  \
    && apt-get install -y gnupg2

RUN pip install --upgrade pip

COPY requirements.txt ./
RUN pip install --no-cache-dir -r requirements.txt

CMD [ "python", "-u", "./main.py" ]
