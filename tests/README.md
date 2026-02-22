# Gestão de Propostas - API REST

Projeto desenvolvido com **CodeIgniter 4** para gestão de clientes e propostas, incluindo controle de status, auditoria, filtros, paginação e documentação via Swagger.

---

## 📂 Estrutura do Projeto

- `app/Controllers/Api/V1` - Controllers da API
- `app/Models` - Models (Cliente, Proposta, Auditoria)
- `app/Services` - Serviços (PropostaService, PropostaStatusService, AuditoriaService)
- `app/Database/Migrations` - Migrations para criação de tabelas
- `app/Docs` - Arquivo `openapi.yaml` para Swagger
- `public/` - Front controller e arquivos públicos

---
📖 Documentação Swagger

A documentação completa da API está disponível em:

http://localhost/gestao-propostas/public/index.php/docs

---
## ⚙️ Requisitos

- PHP >= 8.0
- XAMPP / Apache / MySQL
- Composer
- Extensão `intl` do PHP habilitada

---

## 🏁 Instalação

1. Clone o projeto:

```bash
git clone <seu-repositorio> gestao-propostas
cd gestao-propostas

Instale dependências via Composer:

composer install

Configure o ambiente:

copy env .env

Edite o .env para ajustar a conexão com o banco de dados:

database.default.hostname = localhost
database.default.database = gestao_proposta
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
🗄️ Criar banco de dados e migrations

Crie o banco de dados MySQL:

CREATE DATABASE gestao_proposta CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

Execute as migrations:

php spark migrate

(Opcional) Adicione seeds iniciais, se houver:

php spark db:seed <NomeDoSeeder>
🚀 Rodando o projeto

No terminal, execute o servidor interno do CodeIgniter:

php spark serve

Acesse a API:

http://localhost:8080/api/v1

Caso esteja usando XAMPP com Apache, acesse:

http://localhost/gestao-propostas/public
📄 Endpoints da API
Clientes
Método	Endpoint	Descrição
POST	/api/v1/clientes	Criar cliente
GET	/api/v1/clientes/{id}	Buscar cliente
PATCH	/api/v1/clientes/{id}	Atualizar cliente
DELETE	/api/v1/clientes/{id}	Remover cliente (soft delete)
Propostas
Método	Endpoint	Descrição
POST	/api/v1/propostas	Criar proposta
PATCH	/api/v1/propostas/{id}	Atualizar proposta
POST	/api/v1/propostas/{id}/submit	Submeter proposta
POST	/api/v1/propostas/{id}/approve	Aprovar proposta
POST	/api/v1/propostas/{id}/reject	Rejeitar proposta
POST	/api/v1/propostas/{id}/cancel	Cancelar proposta
GET	/api/v1/propostas/{id}	Buscar proposta
GET	/api/v1/propostas	Listar propostas com filtros e paginação
GET	/api/v1/propostas/{id}/auditoria	Histórico de auditoria
🔍 Filtros e Paginação em GET /api/v1/propostas

?status=SUBMITTED - Filtrar por status

?date_from=2026-02-01&date_to=2026-02-22 - Filtrar por período

?sort=created_at&order=desc - Ordenação (asc ou desc)

?page=1&per_page=10 - Paginação (máximo 100 itens por página)

Exemplo:

GET /api/v1/propostas?status=SUBMITTED&date_from=2026-02-01&date_to=2026-02-22&sort=valor_mensal&order=desc&page=1&per_page=20
💾 Exemplos de requisições cURL
Criar cliente
curl -X POST http://localhost:8080/api/v1/clientes \
-H "Content-Type: application/json" \
-d '{
  "nome": "João Silva",
  "email": "joao@email.com"
}'
Criar proposta
curl -X POST http://localhost:8080/api/v1/propostas \
-H "Content-Type: application/json" \
-H "X-Actor: user:1" \
-d '{
  "cliente_id": 1,
  "produto": "Produto A",
  "valor_mensal": 250.00,
  "origem": "APP"
}'
Aprovar proposta
curl -X POST http://localhost:8080/api/v1/propostas/1/approve \
-H "X-Actor: user:1"
Buscar propostas paginadas com filtros
curl -X GET "http://localhost:8080/api/v1/propostas?status=SUBMITTED&sort=created_at&order=desc&page=1&per_page=10"
