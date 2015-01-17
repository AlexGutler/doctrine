Exemplo API REST E CRUD com Silex e Doctrine
=====================================

Este é um exemplo de uma api REST e CRUD utilizando o micro-framework Silex e o Doctrine.

-------------

Utilização
-------------

> **Instalação:**
> - git clone https://github.com/AlexGutler/doctrine.git
> - cd doctrine
> - Baixe o composer e faça a instalação das dependências
> - Edite as configurações para conectar em seu MySql em: "src\AG\config\config.php"
> - Ainda na raiz do projeto, para criar o banco e inserir registros de teste execute: "php fixtures.php"
> - Acesse a pasta public e execute o built-in server com o comando "php -S localhost:80"

### <i class="icon-refresh"></i> ROTAS

> **Utilzando o método HTTP GET:**
> - /api/produtos - Selecionar todos os registros.
> - /api/produtos/{id} - Selecionar um registro único de acordo com o id informado.

> **Utilizando o método HTTP POST:**
> - /api/produtos/ - {Parâmetros: nome, descricao, valor} - insere um registro no banco.

> **Utilizando o método HTTP PUT**
> - /api/produtos/{id} - {Parâmetros: nome, descricao, valor} - altera o registro com o id informado.

> **Utilizando o método HTTP DELETE**
> - /api/produtos/{id} - deleta o registro de acordo com o id informado.

### <i class="icon-refresh"></i> CRUD
> **Para utilizar o CRUD acessar no browser o Menu PRODUTOS**