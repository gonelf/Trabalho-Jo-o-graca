<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Projetos - ESCS Portfolio</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>ESCS Portfolio</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage-projects.php" class="active">Gerir Projetos</a></li>
                <li><a href="manage-comments.php">Gerir Comentários</a></li>
                <li><a href="#" id="logoutBtn">Sair</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <header class="content-header">
                <h1>Gerir Projetos</h1>
                <button class="btn btn-primary" onclick="showAddProjectForm()">+ Adicionar Projeto</button>
            </header>

            <!-- Formulário para adicionar projeto -->
            <div id="addProjectForm" style="display:none;" class="form-container">
                <h2>Novo Projeto</h2>
                <form id="projectForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Título:</label>
                        <input type="text" name="title" required>
                    </div>

                    <div class="form-group">
                        <label>Descrição:</label>
                        <textarea name="description" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Nome do Aluno:</label>
                        <input type="text" name="student_name">
                    </div>

                    <div class="form-group">
                        <label>Número do Aluno:</label>
                        <input type="text" name="student_number">
                    </div>

                    <div class="form-group">
                        <label>Curso:</label>
                        <select name="course_id" id="courseSelect">
                            <option value="">Selecione...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Unidade Curricular:</label>
                        <select name="curricular_unit_id" id="unitSelect">
                            <option value="">Selecione...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Área Científica:</label>
                        <select name="scientific_area_id" id="areaSelect">
                            <option value="">Selecione...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Ano Académico:</label>
                        <input type="text" name="academic_year" placeholder="2025-2026">
                    </div>

                    <div class="form-group">
                        <label>Thumbnail:</label>
                        <input type="file" name="thumbnail" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label>URL do Projeto:</label>
                        <input type="url" name="project_url">
                    </div>

                    <div class="form-group">
                        <label>URL do Vídeo:</label>
                        <input type="url" name="video_url">
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_featured"> Projeto em Destaque
                        </label>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_published" checked> Publicar
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar Projeto</button>
                    <button type="button" class="btn btn-secondary" onclick="hideAddProjectForm()">Cancelar</button>
                </form>
            </div>

            <!-- Lista de projetos -->
            <div id="projectsList" class="projects-list"></div>
        </main>
    </div>

    <script>
        const API_URL = '../api';

        // Carregar dados iniciais
        window.onload = function() {
            loadCourses();
            loadAreas();
            loadUnits();
            loadProjects();
        };

        function showAddProjectForm() {
            document.getElementById('addProjectForm').style.display = 'block';
        }

        function hideAddProjectForm() {
            document.getElementById('addProjectForm').style.display = 'none';
            document.getElementById('projectForm').reset();
        }

        // Carregar cursos
        async function loadCourses() {
            try {
                const response = await fetch(`${API_URL}/courses.php`);
                const data = await response.json();

                const select = document.getElementById('courseSelect');
                data.data.courses.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.id;
                    option.textContent = course.name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Erro ao carregar cursos:', error);
            }
        }

        // Carregar áreas científicas
        async function loadAreas() {
            try {
                const response = await fetch(`${API_URL}/scientific_areas.php`);
                const data = await response.json();

                const select = document.getElementById('areaSelect');
                data.data.scientific_areas.forEach(area => {
                    const option = document.createElement('option');
                    option.value = area.id;
                    option.textContent = area.name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Erro ao carregar áreas:', error);
            }
        }

        // Carregar unidades curriculares
        async function loadUnits() {
            try {
                const response = await fetch(`${API_URL}/curricular_units.php`);
                const data = await response.json();

                const select = document.getElementById('unitSelect');
                data.data.curricular_units.forEach(unit => {
                    const option = document.createElement('option');
                    option.value = unit.id;
                    option.textContent = unit.name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Erro ao carregar unidades:', error);
            }
        }

        // Carregar projetos
        async function loadProjects() {
            try {
                const response = await fetch(`${API_URL}/projects.php`);
                const data = await response.json();

                const list = document.getElementById('projectsList');
                list.innerHTML = '<h2>Projetos Existentes</h2>';

                if (data.data.projects.length === 0) {
                    list.innerHTML += '<p>Nenhum projeto encontrado.</p>';
                    return;
                }

                data.data.projects.forEach(project => {
                    const div = document.createElement('div');
                    div.className = 'project-item';
                    div.innerHTML = `
                        <h3>${project.title}</h3>
                        <p>${project.description.substring(0, 100)}...</p>
                        <p><strong>Aluno:</strong> ${project.student_name || 'N/A'}</p>
                        <p><strong>Curso:</strong> ${project.course_name || 'N/A'}</p>
                        <p><strong>Visualizações:</strong> ${project.views}</p>
                        <button class="btn btn-danger" onclick="deleteProject(${project.id})">Apagar</button>
                    `;
                    list.appendChild(div);
                });
            } catch (error) {
                console.error('Erro ao carregar projetos:', error);
            }
        }

        // Submeter formulário
        document.getElementById('projectForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch(`${API_URL}/projects.php`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    alert('Projeto criado com sucesso!');
                    hideAddProjectForm();
                    loadProjects();
                } else {
                    alert('Erro: ' + data.message);
                }
            } catch (error) {
                alert('Erro ao criar projeto');
                console.error(error);
            }
        });

        // Apagar projeto
        async function deleteProject(id) {
            if (!confirm('Tem a certeza que deseja apagar este projeto?')) {
                return;
            }

            try {
                const response = await fetch(`${API_URL}/projects.php?id=${id}`, {
                    method: 'DELETE'
                });

                const data = await response.json();

                if (data.success) {
                    alert('Projeto apagado!');
                    loadProjects();
                } else {
                    alert('Erro ao apagar: ' + data.message);
                }
            } catch (error) {
                alert('Erro ao apagar projeto');
                console.error(error);
            }
        }

        // Logout
        document.getElementById('logoutBtn').addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Deseja sair?')) {
                fetch(`${API_URL}/auth.php`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({action: 'logout'})
                }).then(() => {
                    window.location.href = 'index.php';
                });
            }
        });
    </script>
</body>
</html>
