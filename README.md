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

### Passos

1. Clone este repositório
2. Importe o ficheiro `database/schema.sql` para a sua base de dados MySQL
3. Configure as credenciais da base de dados em `backend/config/config.php`
4. Coloque os ficheiros na pasta do seu servidor web (htdocs, www, etc.)
5. Aceda ao projeto através do navegador

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

1. Aceda a `backend/admin/`
2. Login com credenciais (username: admin, password: admin123)
3. Adicione, edite ou remova projetos
4. Aprove ou remova comentários

## Base de Dados

A base de dados inclui as seguintes tabelas:

- **users** - Utilizadores do sistema
- **courses** - Cursos da ESCS
- **scientific_areas** - Áreas científicas
- **curricular_units** - Unidades curriculares
- **projects** - Projetos dos alunos
- **comments** - Comentários nos projetos

## API Endpoints

- `GET /api/projects.php` - Listar projetos
- `GET /api/projects.php?id={id}` - Obter projeto específico
- `POST /api/projects.php` - Criar projeto (requer autenticação)
- `PUT /api/projects.php` - Atualizar projeto (requer autenticação)
- `DELETE /api/projects.php?id={id}` - Apagar projeto (requer autenticação)
- `GET /api/courses.php` - Listar cursos
- `GET /api/scientific_areas.php` - Listar áreas científicas
- `GET /api/curricular_units.php` - Listar unidades curriculares
- `POST /api/comments.php` - Criar comentário
- `POST /api/auth.php` - Login

## Autores

Projeto desenvolvido por estudantes de Licenciatura em Audiovisual e Multimédia - ESCS 2025-2026

## Notas

- Este é um projeto académico desenvolvido para fins educacionais
- A password padrão do administrador deve ser alterada em produção
- Alguns recursos podem estar simplificados para facilitar a aprendizagem
