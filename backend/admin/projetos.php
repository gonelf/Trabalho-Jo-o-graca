<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gerir Projetos</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="admin">
        <div class="sidebar">
            <h2>ESCS Admin</h2>
            <a href="projetos.php" class="active">Projetos</a>
            <a href="login.php">Sair</a>
        </div>

        <div class="conteudo">
            <h1>Gerir Projetos</h1>

            <button onclick="mostrarForm()" class="btn">+ Adicionar Projeto</button>

            <!-- Formulario adicionar -->
            <div id="formProjeto" style="display:none; background:#f9f9f9; padding:20px; margin:20px 0;">
                <h2>Novo Projeto</h2>
                <form id="novoProj" enctype="multipart/form-data">
                    <label>Título:</label><br>
                    <input type="text" name="title" required style="width:100%;padding:8px"><br><br>

                    <label>Descrição:</label><br>
                    <textarea name="description" required rows="4" style="width:100%;padding:8px"></textarea><br><br>

                    <label>Nome Aluno:</label><br>
                    <input type="text" name="student_name" style="width:100%;padding:8px"><br><br>

                    <label>Número:</label><br>
                    <input type="text" name="student_number" style="width:100%;padding:8px"><br><br>

                    <label>Curso:</label><br>
                    <select name="course_id" id="cursoSelect" style="width:100%;padding:8px">
                        <option value="">Selecione...</option>
                    </select><br><br>

                    <label>Unidade Curricular:</label><br>
                    <select name="curricular_unit_id" id="unidadeSelect" style="width:100%;padding:8px">
                        <option value="">Selecione...</option>
                    </select><br><br>

                    <label>Área Científica:</label><br>
                    <select name="scientific_area_id" id="areaSelect" style="width:100%;padding:8px">
                        <option value="">Selecione...</option>
                    </select><br><br>

                    <label>Ano Académico:</label><br>
                    <input type="text" name="academic_year" placeholder="2025-2026" style="width:100%;padding:8px"><br><br>

                    <label>Thumbnail:</label><br>
                    <input type="file" name="thumbnail" accept="image/*"><br><br>

                    <label>URL Projeto:</label><br>
                    <input type="url" name="project_url" style="width:100%;padding:8px"><br><br>

                    <label>URL Vídeo:</label><br>
                    <input type="url" name="video_url" style="width:100%;padding:8px"><br><br>

                    <label><input type="checkbox" name="is_featured"> Projeto em Destaque</label><br>
                    <label><input type="checkbox" name="is_published" checked> Publicar</label><br><br>

                    <button type="submit" class="btn">Guardar</button>
                    <button type="button" class="btn" onclick="esconderForm()">Cancelar</button>
                </form>
            </div>

            <!-- Lista de projetos -->
            <div id="listaProjetos"></div>
        </div>
    </div>

    <script>
        var API = '../api';

        // carregar quando abre
        window.onload = function() {
            carregarCursos();
            carregarAreas();
            carregarUnidades();
            carregarProjetos();
        }

        function mostrarForm() {
            document.getElementById('formProjeto').style.display = 'block';
        }

        function esconderForm() {
            document.getElementById('formProjeto').style.display = 'none';
        }

        function carregarCursos() {
            fetch(API + '/get_courses.php')
                .then(r => r.json())
                .then(data => {
                    var select = document.getElementById('cursoSelect');
                    data.data.courses.forEach(function(c) {
                        var opt = new Option(c.name, c.id);
                        select.add(opt);
                    });
                });
        }

        function carregarAreas() {
            fetch(API + '/get_areas.php')
                .then(r => r.json())
                .then(data => {
                    var select = document.getElementById('areaSelect');
                    data.data.scientific_areas.forEach(function(a) {
                        var opt = new Option(a.name, a.id);
                        select.add(opt);
                    });
                });
        }

        function carregarUnidades() {
            fetch(API + '/get_units.php')
                .then(r => r.json())
                .then(data => {
                    var select = document.getElementById('unidadeSelect');
                    data.data.curricular_units.forEach(function(u) {
                        var opt = new Option(u.name, u.id);
                        select.add(opt);
                    });
                });
        }

        function carregarProjetos() {
            fetch(API + '/get_projects.php')
                .then(r => r.json())
                .then(data => {
                    var lista = document.getElementById('listaProjetos');
                    lista.innerHTML = '<h2>Projetos</h2>';

                    if(data.data.projects.length == 0) {
                        lista.innerHTML += '<p>Sem projetos</p>';
                        return;
                    }

                    data.data.projects.forEach(function(p) {
                        var div = document.createElement('div');
                        div.style.border = '1px solid #ddd';
                        div.style.padding = '15px';
                        div.style.margin = '10px 0';

                        div.innerHTML = '<h3>' + p.title + '</h3>' +
                                       '<p>' + p.description.substring(0,150) + '...</p>' +
                                       '<p><strong>Aluno:</strong> ' + (p.student_name || 'N/A') + '</p>' +
                                       '<p><strong>Views:</strong> ' + p.views + '</p>' +
                                       '<button onclick="apagarProjeto(' + p.id + ')" class="btn btn-delete">Apagar</button>';

                        lista.appendChild(div);
                    });
                });
        }

        // submeter novo projeto
        document.getElementById('novoProj').addEventListener('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            fetch(API + '/add_project.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    alert('Projeto adicionado!');
                    esconderForm();
                    carregarProjetos();
                    document.getElementById('novoProj').reset();
                } else {
                    alert('Erro: ' + data.message);
                }
            });
        });

        // apagar projeto
        function apagarProjeto(id) {
            if(confirm('Apagar este projeto?')) {
                fetch(API + '/delete_project.php?id=' + id, {method: 'GET'})
                    .then(r => r.json())
                    .then(data => {
                        if(data.success) {
                            alert('Apagado!');
                            carregarProjetos();
                        } else {
                            alert('Erro ao apagar');
                        }
                    });
            }
        }
    </script>
</body>
</html>
