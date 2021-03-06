Exemplo API REST E CRUD com Silex e Doctrine
=====================================

Este é um exemplo de uma api REST E CRUD utilizando o micro-framework Silex e o ORM Doctrine.

-------------

Utilização
-------------

> **Instalação:**
> - git clone https://github.com/AlexGutler/doctrine.git
> - cd doctrine
> - Baixe o composer e faça a instalação das dependências
> - Edite as configurações para conectar em seu MySql em: "src\AG\config\config.php"
> - Ainda na raiz do projeto, para criar o schema e as tabelas execute: "php fixtures.php"
> - Acesse a pasta public e execute o built-in server com o comando "php -S localhost:80"

### <i class="icon-refresh"></i> ROTAS

> **Utilzando o método HTTP GET:**
> - /api/tags - seleciona todas as tags.
> - /api/tags/{id} - seleciona uma única tag de acordo com o id informado.

> - /api/tags - seleciona todas as categorias.
> - /api/categorias/{id} - seleciona uma única categoria de acordo com o id informado

> - /api/produtos - Selecionar todos os produtos.
> - /api/produtos/{id} - Selecionar um único produto de acordo com o id informado.

> **Utilizando o método HTTP POST:**
> - /api/tags/ - {Parâmetro: nome} - insere uma tag no banco.

> - /api/categorias/ - {Parâmetro: nome} - insere uma categoria no banco.

> - /api/produtos/ - {Parâmetros: nome, descricao, valor, categoria(o id de uma categoria), tags[](passar o id da tag)} - insere um produto no banco.

> **Utilizando o método HTTP PUT**
> - /api/tags/{id} - {Parâmetro: nome} - altera a tag com o id informado.

> - /api/categorias/{id} - {Parâmetro: nome} - altera a categoria com o id informado.

> - /api/produtos/{id} - {Parâmetros: nome, descricao, valor, categoria(o id de uma categoria), tags[](passar o id da tag)} - altera o produto com o id informado.

> **Utilizando o método HTTP DELETE**
> - /api/tags/{id} - deleta a tag de acordo com o id informado.

> - /api/categorias/{id} - deleta a categoria de acordo com o id informado.

> - /api/produtos/{id} - deleta o produto de acordo com o id informado.
