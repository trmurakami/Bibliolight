# Bibliolight

Software livre para gestão de acervos bibliográficos

Você pode experimentar uma [versão demo online](http://tecbib.com/bibliolight/)

## Dependencias

- [Apache 2](https://httpd.apache.org/)
- [PHP > 7](https://www.php.net/)
- [Elasticsearch > 7.2](https://www.elastic.co/pt/products/elasticsearch)

## Instalação no Linux

```
curl -s http://getcomposer.org/installer | php
php composer.phar install --no-dev
```

Copiar o arquivo inc/config.demo.php para inc/config.php

```
cd inc/
cp config.demo.php config.php
```

Editar o arquivo config.php

## Instalação no Windows

Baixar a [versão compilada](https://github.com/trmurakami/Bibliolight/releases/download/v1.1/bibliolight_v1.1.zip) e extrair do zip

Entrar no diretório executar o elasticsearch.ink

Aguardar o Elasticsearch carregar (A primeira execução demora mais do que as demais)

Executar o arquivo bibliolight.exe

Obs: A versão Windows só é possível graças ao [PHP Desktop](https://github.com/cztomczak/phpdesktop)


## Execução via docker

Baixar e instalar o [docker compose](https://docs.docker.com/compose/install/)

...
cp inc/config.docker.php inc/config.php
docker-compose up
...

## Doação

Você pode ajudar o projeto doando qualquer quantia: [Pagseguro](https://pag.ae/7VbJhhRHP)
