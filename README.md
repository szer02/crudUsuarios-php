# CRUD de Usuários

Este é um projeto de sistema de gerenciamento de usuários (CRUD - Create, Read, Update, Delete) desenvolvido com PHP e o framework Symfony. A aplicação segue boas práticas de desenvolvimento, separando a lógica em camadas (Application, Domain, Infrastructure e Presentation) para facilitar a manutenção e escalabilidade.

## 🚀 Tecnologias Utilizadas

* **PHP** (Linguagem principal)
* **Symfony Framework** (Estrutura do backend, rotas e injeção de dependências)
* **Doctrine ORM** (Mapeamento Objeto-Relacional para o banco de dados)
* **MySQL** (Banco de dados gerenciado via Laragon e phpMyAdmin)
* **Twig** (Motor de templates para a renderização do HTML)
* **Bootstrap** (Framework CSS para o layout e responsividade)

## 📋 Pré-requisitos

Para rodar este projeto na sua máquina, você precisará ter instalado:

* [PHP](https://www.php.net/) (Versão 8.1 ou superior recomendada)
* [Composer](https://getcomposer.org/) (Gerenciador de dependências do PHP)
* [Laragon](https://laragon.org/) (Para rodar o ambiente com o servidor MySQL)
* [Symfony CLI](https://symfony.com/download) (Opcional, mas recomendado para rodar o servidor local)

## ⚙️ Instalação e Configuração

**1. Clone o repositório ou extraia os arquivos do projeto:**

```bash
git clone <url-do-seu-repositorio>
cd crudUsuarios
```

**2. Instale as dependências do PHP via Composer:**

```bash
composer install
```

**3. Configure o Banco de Dados:**

No diretório raiz do projeto, crie um arquivo chamado `.env.local` (este arquivo é ignorado pelo Git para segurança) e configure a string de conexão com o banco de dados do seu Laragon. 

Exemplo de configuração para o MySQL do Laragon (usuário root e sem senha):

```ini
DATABASE_URL="mysql://root:@127.0.0.1:3306/crud_usuarios?serverVersion=8.0&charset=utf8mb4"
```
*(Ajuste o nome do banco `crud_usuarios`, usuário e senha conforme o seu ambiente)*

## 🗄️ Criação do Banco de Dados e Tabelas (Doctrine ORM)

As tabelas do banco de dados não precisam ser criadas manualmente no phpMyAdmin. O projeto utiliza o Doctrine ORM, que permite gerar a estrutura do banco automaticamente a partir das entidades do código e das Migrations.

Para replicar o banco de dados e as tabelas, execute os comandos abaixo no terminal do seu projeto:

**1. Crie o banco de dados (caso ainda não exista):**

```bash
php bin/console doctrine:database:create
```

**2. Execute as Migrations para criar as tabelas:**

Como a estrutura já foi gerada e está salva na pasta `migrations/`, basta rodar o comando abaixo para aplicar as tabelas ao banco de dados:

```bash
php bin/console doctrine:migrations:migrate
```
*Digite `yes` quando o terminal pedir confirmação para executar a migração.*


## ▶️ Executando a Aplicação

Com o banco de dados pronto, inicie o servidor embutido do Symfony:

```bash
symfony server:start
```

Se você não tiver o Symfony CLI instalado, pode usar o servidor nativo do PHP:

```bash
php -S localhost:8000 -t public
```

Acesse a aplicação no seu navegador através do endereço: [http://localhost:8000](http://localhost:8000)

