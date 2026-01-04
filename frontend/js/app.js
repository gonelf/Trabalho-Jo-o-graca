// JavaScript do portfolio ESCS
// Feito pelo grupo de LAI

var API = '../backend/api';
var projetos = [];

// quando a pagina carrega
window.onload = function() {
    console.log('Pagina carregada!');
    carregarCursos();
    carregarAreas();
    carregarUnidades();
    carregarProjetos();
}

// carregar cursos para o filtro
function carregarCursos() {
    fetch(API + '/get_courses.php')
        .then(response => response.json())
        .then(data => {
            var select = document.getElementById('courseFilter');

            data.data.courses.forEach(function(curso) {
                var option = document.createElement('option');
                option.value = curso.id;
                option.text = curso.name;
                select.add(option);
            });
        })
        .catch(error => console.log('Erro:', error));
}

// carregar areas
function carregarAreas() {
    fetch(API + '/get_areas.php')
        .then(response => response.json())
        .then(data => {
            var select = document.getElementById('areaFilter');

            data.data.scientific_areas.forEach(function(area) {
                var option = document.createElement('option');
                option.value = area.id;
                option.text = area.name;
                select.add(option);
            });
        });
}

// carregar unidades
function carregarUnidades() {
    fetch(API + '/get_units.php')
        .then(response => response.json())
        .then(data => {
            var select = document.getElementById('unitFilter');

            data.data.curricular_units.forEach(function(unit) {
                var option = document.createElement('option');
                option.value = unit.id;
                option.text = unit.name;
                select.add(option);
            });
        });
}

// carregar projetos
function carregarProjetos(filtros) {
    var url = API + '/get_projects.php?';

    if(filtros) {
        if(filtros.search) url += 'search=' + filtros.search + '&';
        if(filtros.course_id) url += 'course_id=' + filtros.course_id + '&';
        if(filtros.scientific_area_id) url += 'scientific_area_id=' + filtros.scientific_area_id + '&';
        if(filtros.curricular_unit_id) url += 'curricular_unit_id=' + filtros.curricular_unit_id + '&';
        if(filtros.featured) url += 'featured=1&';
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                projetos = data.data.projects;
                mostrarProjetos(projetos);
            }
        })
        .catch(error => {
            console.log('Erro ao carregar projetos:', error);
            document.getElementById('projectsGrid').innerHTML = '<p>Erro ao carregar projetos</p>';
        });
}

// mostrar projetos na tela
function mostrarProjetos(lista) {
    var grid = document.getElementById('projectsGrid');
    grid.innerHTML = '';

    if(lista.length == 0) {
        grid.innerHTML = '<p>Nenhum projeto encontrado</p>';
        return;
    }

    // criar cards
    lista.forEach(function(projeto) {
        var card = document.createElement('div');
        card.className = 'project-card';
        card.onclick = function() {
            abrirModal(projeto.id);
        }

        // thumbnail
        var thumb = '';
        if(projeto.thumbnail) {
            thumb = '<div class="project-thumbnail" style="background-image: url(../backend/uploads/thumbnails/' + projeto.thumbnail + ')"></div>';
        } else {
            thumb = '<div class="project-thumbnail no-image">📁</div>';
        }

        // descrição curta
        var desc = projeto.description.substring(0, 100);
        if(projeto.description.length > 100) desc += '...';

        // badge destaque
        var badge = '';
        if(projeto.is_featured == 1) {
            badge = '<span class="badge featured">★ Destaque</span>';
        }

        card.innerHTML = thumb + '<div class="project-info">' + badge +
                         '<h3>' + projeto.title + '</h3>' +
                         '<p>' + desc + '</p>' +
                         '<div class="project-meta">' +
                         '<p><strong>Aluno:</strong> ' + (projeto.student_name || 'N/A') + '</p>' +
                         '<p><strong>Curso:</strong> ' + (projeto.course_name || 'N/A') + '</p>' +
                         '</div></div>';

        grid.appendChild(card);
    });
}

// aplicar filtros
function applyFilters() {
    var filtros = {
        search: document.getElementById('searchInput').value,
        course_id: document.getElementById('courseFilter').value,
        scientific_area_id: document.getElementById('areaFilter').value,
        curricular_unit_id: document.getElementById('unitFilter').value
    };

    console.log('Aplicando filtros:', filtros);
    carregarProjetos(filtros);
}

// limpar filtros
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('courseFilter').value = '';
    document.getElementById('areaFilter').value = '';
    document.getElementById('unitFilter').value = '';
    carregarProjetos();
}

// mostrar todos
function showAllProjects() {
    document.getElementById('sectionTitle').textContent = 'Todos os Projetos';
    clearFilters();
}

// mostrar destaques
function showFeatured() {
    document.getElementById('sectionTitle').textContent = 'Projetos em Destaque';
    carregarProjetos({featured: true});
}

// abrir modal do projeto
function abrirModal(id) {
    fetch(API + '/get_project.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                var p = data.data.project;

                var html = '<h2>' + p.title + '</h2>' +
                          '<p><strong>Aluno:</strong> ' + (p.student_name || 'N/A') + '</p>' +
                          '<p><strong>Curso:</strong> ' + (p.course_name || 'N/A') + '</p>' +
                          '<p><strong>UC:</strong> ' + (p.curricular_unit_name || 'N/A') + '</p>' +
                          '<hr style="margin:20px 0">' +
                          '<h3>Descrição</h3><p>' + p.description + '</p>';

                if(p.project_url) {
                    html += '<p><strong>URL:</strong> <a href="' + p.project_url + '" target="_blank">' + p.project_url + '</a></p>';
                }

                if(p.video_url) {
                    html += '<p><strong>Vídeo:</strong> <a href="' + p.video_url + '" target="_blank">' + p.video_url + '</a></p>';
                }

                // form comentarios
                html += '<hr style="margin:20px 0"><h3>Deixar Comentário</h3>' +
                        '<form onsubmit="enviarComentario(event, ' + id + ')">' +
                        '<input type="text" id="nome" placeholder="Nome" required style="width:100%;padding:10px;margin:5px 0"><br>' +
                        '<input type="email" id="email" placeholder="Email (opcional)" style="width:100%;padding:10px;margin:5px 0"><br>' +
                        '<textarea id="texto" placeholder="Comentário" required rows="4" style="width:100%;padding:10px;margin:5px 0"></textarea><br>' +
                        '<button type="submit" class="btn btn-primary">Enviar</button></form>';

                document.getElementById('modalBody').innerHTML = html;
                document.getElementById('projectModal').style.display = 'block';
            }
        })
        .catch(error => {
            alert('Erro ao carregar projeto');
            console.log(error);
        });
}

// fechar modal
function closeModal() {
    document.getElementById('projectModal').style.display = 'none';
}

// clicar fora fecha
window.onclick = function(event) {
    var modal = document.getElementById('projectModal');
    if(event.target == modal) {
        closeModal();
    }
}

// enviar comentario
function enviarComentario(event, projeto_id) {
    event.preventDefault();

    var nome = document.getElementById('nome').value;
    var email = document.getElementById('email').value;
    var texto = document.getElementById('texto').value;

    fetch(API + '/add_comment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            project_id: projeto_id,
            author_name: nome,
            author_email: email,
            comment_text: texto
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Comentário enviado! Será aprovado em breve.');
            document.getElementById('nome').value = '';
            document.getElementById('email').value = '';
            document.getElementById('texto').value = '';
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        alert('Erro ao enviar');
        console.log(error);
    });
}
