# ESCS Portfolio - Laboratório de Aplicações Interativas

Projeto de grupo desenvolvido para a disciplina de Laboratório de Aplicações Interativas.

## Descrição do Projeto

Plataforma web para divulgação de trabalhos desenvolvidos pelos alunos no âmbito dos vários cursos da ESCS. A plataforma permite a navegação interativa dos conteúdos apresentados bem como a atualização da informação através de um gestor de conteúdos.

## Funcionalidades

- ✅ Navegação interativa dos projetos
- ✅ Sistema de filtros (curso, área científica, unidade curricular)
- ✅ Pesquisa de projetos
- ✅ Sistema de comentários
- ✅ Painel de administração (CMS)
- ✅ Design responsivo

## Tecnologias Utilizadas

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP
- **Base de Dados**: MySQL
- **Servidor**: Apache (XAMPP/WAMP)

## Instalação

### Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor Apache (XAMPP, WAMP ou similar)

### Passo a Passo Detalhado (XAMPP + phpMyAdmin)

#### 1. Instalar o XAMPP

- Descarregue o XAMPP em: https://www.apachefriends.org/
- Instale normalmente (deixar tudo por defeito)
- Abra o XAMPP Control Panel
- Inicie o **Apache** e o **MySQL** (clicar em "Start")

#### 2. Copiar os Ficheiros do Projeto

- Clone ou descarregue este repositório
- Copie a pasta `Trabalho-Jo-o-graca` para `C:\xampp\htdocs\`
- O caminho final deve ser: `C:\xampp\htdocs\Trabalho-Jo-o-graca\`

#### 3. Criar a Base de Dados no phpMyAdmin

- Abra o navegador e vá a: `http://localhost/phpmyadmin`
- Clique em **"Novo"** no menu lateral (ou "New")
- Nome da base de dados: `escs_portfolio`
- Collation: `utf8mb4_unicode_ci`
- Clique em **"Criar"** (ou "Create")

#### 4. Importar o Schema SQL

- Com a base de dados `escs_portfolio` selecionada
- Clique no separador **"Importar"** (ou "Import")
- Clique em **"Escolher ficheiro"** (ou "Choose file")
- Navegue até `C:\xampp\htdocs\Trabalho-Jo-o-graca\database\schema.sql`
- Clique em **"Executar"** (ou "Go") no fundo da página
- Deve aparecer uma mensagem de sucesso
- Verifique se as tabelas foram criadas (deve ver: users, courses, projects, etc.)

#### 5. Configurar a Ligação à Base de Dados

- Abra o ficheiro `backend/config/db.php` num editor de texto
- Verifique se está assim (normalmente já está correto):
  ```php
  $host = "localhost";
  $dbname = "escs_portfolio";
  $username = "root";
  $password = "";
  ```
- Se a sua instalação MySQL tiver password, altere a linha `$password = "";`

#### 6. Testar o Projeto

- Abra o navegador
- **Frontend**: `http://localhost/Trabalho-Jo-o-graca/frontend/index.html`
- **Admin**: `http://localhost/Trabalho-Jo-o-graca/backend/admin/login.php`
  - Username: `admin`
  - Password: `admin123`

#### 7. Problemas Comuns

**Erro "Access denied for user"**
- Verifique se o MySQL está a correr no XAMPP
- Verifique a password em `backend/config/db.php`

**Erro "Table doesn't exist"**
- Importe novamente o ficheiro `schema.sql` no phpMyAdmin

**Página em branco**
- Ative os erros PHP: abra `php.ini` do XAMPP e mude `display_errors = On`
- Reinicie o Apache no XAMPP

**Imagens não aparecem**
- Verifique se a pasta `backend/uploads/` tem permissões de escrita

## Estrutura do Projeto

```
├── backend/
│   ├── api/              # Endpoints da API REST
│   ├── config/           # Configurações e conexão BD
│   ├── admin/            # Painel de administração
│   └── uploads/          # Ficheiros enviados
├── frontend/
│   ├── css/              # Estilos
│   ├── js/               # Scripts JavaScript
│   └── index.html        # Página principal
└── database/
    └── schema.sql        # Schema da base de dados
```

## Como Usar

### Utilizador Frontend

1. Aceda a `frontend/index.html`
2. Navegue pelos projetos
3. Use os filtros para encontrar projetos específicos
4. Clique num projeto para ver detalhes
5. Deixe comentários (sujeito a aprovação)

### Administração

1. Aceda a `backend/admin/login.php`
2. Login com credenciais (username: admin, password: admin123)
3. Adicione ou remova projetos

## Base de Dados

A base de dados inclui as seguintes tabelas:

- **users** - Utilizadores do sistema
- **courses** - Cursos da ESCS
- **scientific_areas** - Áreas científicas
- **curricular_units** - Unidades curriculares
- **projects** - Projetos dos alunos
- **comments** - Comentários nos projetos

## API Endpoints

- `GET /api/get_projects.php` - Listar projetos
- `GET /api/get_project.php?id={id}` - Obter projeto específico
- `POST /api/add_project.php` - Criar projeto (requer login)
- `GET /api/delete_project.php?id={id}` - Apagar projeto (requer login)
- `GET /api/get_courses.php` - Listar cursos
- `GET /api/get_areas.php` - Listar áreas científicas
- `GET /api/get_units.php` - Listar unidades curriculares
- `POST /api/add_comment.php` - Criar comentário
- `POST /api/login.php` - Fazer login

## Autores

Projeto desenvolvido por estudantes de Licenciatura em Audiovisual e Multimédia - ESCS 2025-2026

## Notas

- Este é um projeto académico desenvolvido para fins educacionais
- A password padrão do administrador deve ser alterada em produção
- Alguns recursos podem estar simplificados para facilitar a aprendizagem
