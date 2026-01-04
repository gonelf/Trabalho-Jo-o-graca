// JavaScript básico para o Portfolio ESCS

// URL da API
const API_URL = '../backend/api';

// Variáveis globais
let allProjects = [];
let currentPage = 1;
let totalPages = 1;

// Carregar tudo quando a página carrega
window.onload = function() {
    loadCourses();
    loadAreas();
    loadUnits();
    loadProjects();
};

// Função para carregar cursos
async function loadCourses() {
    try {
        const response = await fetch(`${API_URL}/courses.php`);
        const data = await response.json();

        const select = document.getElementById('courseFilter');

        if (data.success && data.data.courses) {
            data.data.courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course.id;
                option.textContent = course.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erro ao carregar cursos:', error);
    }
}

// Função para carregar áreas científicas
async function loadAreas() {
    try {
        const response = await fetch(`${API_URL}/scientific_areas.php`);
        const data = await response.json();

        const select = document.getElementById('areaFilter');

        if (data.success && data.data.scientific_areas) {
            data.data.scientific_areas.forEach(area => {
                const option = document.createElement('option');
                option.value = area.id;
                option.textContent = area.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erro ao carregar áreas:', error);
    }
}

// Função para carregar unidades curriculares
async function loadUnits() {
    try {
        const response = await fetch(`${API_URL}/curricular_units.php`);
        const data = await response.json();

        const select = document.getElementById('unitFilter');

        if (data.success && data.data.curricular_units) {
            data.data.curricular_units.forEach(unit => {
                const option = document.createElement('option');
                option.value = unit.id;
                option.textContent = unit.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erro ao carregar unidades:', error);
    }
}

// Função para carregar projetos
async function loadProjects(filters = {}) {
    try {
        // Construir URL com filtros
        let url = `${API_URL}/projects.php?page=${currentPage}`;

        if (filters.search) {
            url += `&search=${encodeURIComponent(filters.search)}`;
        }
        if (filters.course_id) {
            url += `&course_id=${filters.course_id}`;
        }
        if (filters.scientific_area_id) {
            url += `&scientific_area_id=${filters.scientific_area_id}`;
        }
        if (filters.curricular_unit_id) {
            url += `&curricular_unit_id=${filters.curricular_unit_id}`;
        }
        if (filters.featured) {
            url += `&featured=1`;
        }

        const response = await fetch(url);
        const data = await response.json();

        if (data.success && data.data.projects) {
            allProjects = data.data.projects;
            totalPages = data.data.pagination.pages;
            displayProjects(allProjects);
            createPagination();
        } else {
            document.getElementById('projectsGrid').innerHTML = '<p>Nenhum projeto encontrado.</p>';
        }
    } catch (error) {
        console.error('Erro ao carregar projetos:', error);
        document.getElementById('projectsGrid').innerHTML = '<p>Erro ao carregar projetos.</p>';
    }
}

// Função para mostrar projetos na grade
function displayProjects(projects) {
    const grid = document.getElementById('projectsGrid');

    if (projects.length === 0) {
        grid.innerHTML = '<p>Nenhum projeto encontrado.</p>';
        return;
    }

    grid.innerHTML = '';

    projects.forEach(project => {
        const card = document.createElement('div');
        card.className = 'project-card';
        card.onclick = function() {
            openProjectModal(project.id);
        };

        // Thumbnail
        let thumbnailHtml = '';
        if (project.thumbnail) {
            thumbnailHtml = `<div class="project-thumbnail" style="background-image: url('../backend/uploads/thumbnails/${project.thumbnail}')"></div>`;
        } else {
            thumbnailHtml = '<div class="project-thumbnail no-image">📁</div>';
        }

        // Descrição curta
        let shortDesc = project.description.substring(0, 100);
        if (project.description.length > 100) {
            shortDesc += '...';
        }

        card.innerHTML = `
            ${thumbnailHtml}
            <div class="project-info">
                ${project.is_featured ? '<span class="badge featured">★ Destaque</span>' : ''}
                <h3>${project.title}</h3>
                <p>${shortDesc}</p>
                <div class="project-meta">
                    <p><strong>Aluno:</strong> ${project.student_name || 'N/A'}</p>
                    <p><strong>Curso:</strong> ${project.course_name || 'N/A'}</p>
                    ${project.scientific_area_name ? `<span class="badge">${project.scientific_area_name}</span>` : ''}
                </div>
            </div>
        `;

        grid.appendChild(card);
    });
}

// Função para criar paginação
function createPagination() {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    if (totalPages <= 1) {
        return;
    }

    // Botão anterior
    if (currentPage > 1) {
        const prevBtn = document.createElement('button');
        prevBtn.textContent = '← Anterior';
        prevBtn.onclick = function() {
            currentPage--;
            applyFilters();
        };
        pagination.appendChild(prevBtn);
    }

    // Números das páginas
    for (let i = 1; i <= totalPages; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.textContent = i;
        pageBtn.className = i === currentPage ? 'active' : '';
        pageBtn.onclick = function() {
            currentPage = i;
            applyFilters();
        };
        pagination.appendChild(pageBtn);
    }

    // Botão próximo
    if (currentPage < totalPages) {
        const nextBtn = document.createElement('button');
        nextBtn.textContent = 'Próximo →';
        nextBtn.onclick = function() {
            currentPage++;
            applyFilters();
        };
        pagination.appendChild(nextBtn);
    }
}

// Função para aplicar filtros
function applyFilters() {
    const filters = {
        search: document.getElementById('searchInput').value,
        course_id: document.getElementById('courseFilter').value,
        scientific_area_id: document.getElementById('areaFilter').value,
        curricular_unit_id: document.getElementById('unitFilter').value
    };

    loadProjects(filters);
}

// Função para limpar filtros
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('courseFilter').value = '';
    document.getElementById('areaFilter').value = '';
    document.getElementById('unitFilter').value = '';
    currentPage = 1;
    loadProjects();
}

// Função para mostrar todos os projetos
function showAllProjects() {
    document.getElementById('sectionTitle').textContent = 'Todos os Projetos';

    // Atualizar navegação
    const links = document.querySelectorAll('.nav-link');
    links.forEach(link => link.classList.remove('active'));
    links[0].classList.add('active');

    currentPage = 1;
    clearFilters();
}

// Função para mostrar projetos em destaque
function showFeatured() {
    document.getElementById('sectionTitle').textContent = 'Projetos em Destaque';

    // Atualizar navegação
    const links = document.querySelectorAll('.nav-link');
    links.forEach(link => link.classList.remove('active'));
    links[1].classList.add('active');

    currentPage = 1;
    loadProjects({ featured: true });
}

// Função para abrir modal do projeto
async function openProjectModal(projectId) {
    try {
        const response = await fetch(`${API_URL}/projects.php?id=${projectId}`);
        const data = await response.json();

        if (data.success && data.data.project) {
            const project = data.data.project;

            let modalContent = `
                <h2>${project.title}</h2>
                <p><strong>Aluno:</strong> ${project.student_name || 'N/A'}</p>
                <p><strong>Número:</strong> ${project.student_number || 'N/A'}</p>
                <p><strong>Curso:</strong> ${project.course_name || 'N/A'}</p>
                <p><strong>Unidade Curricular:</strong> ${project.curricular_unit_name || 'N/A'}</p>
                <p><strong>Área Científica:</strong> ${project.scientific_area_name || 'N/A'}</p>
                <hr style="margin: 20px 0;">
                <h3>Descrição</h3>
                <p>${project.description}</p>
            `;

            if (project.project_url) {
                modalContent += `<p><strong>URL:</strong> <a href="${project.project_url}" target="_blank">${project.project_url}</a></p>`;
            }

            if (project.video_url) {
                modalContent += `<p><strong>Vídeo:</strong> <a href="${project.video_url}" target="_blank">${project.video_url}</a></p>`;
            }

            modalContent += `
                <hr style="margin: 20px 0;">
                <h3>Deixar Comentário</h3>
                <form id="commentForm" onsubmit="submitComment(event, ${projectId})">
                    <div style="margin-bottom: 10px;">
                        <input type="text" id="commentName" placeholder="Seu nome" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <input type="email" id="commentEmail" placeholder="Seu email (opcional)" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <textarea id="commentText" placeholder="Seu comentário" required rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar Comentário</button>
                </form>
            `;

            document.getElementById('modalBody').innerHTML = modalContent;
            document.getElementById('projectModal').style.display = 'block';
        }
    } catch (error) {
        console.error('Erro ao carregar projeto:', error);
        alert('Erro ao carregar projeto');
    }
}

// Função para fechar modal
function closeModal() {
    document.getElementById('projectModal').style.display = 'none';
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    const modal = document.getElementById('projectModal');
    if (event.target == modal) {
        closeModal();
    }
};

// Função para submeter comentário
async function submitComment(event, projectId) {
    event.preventDefault();

    const name = document.getElementById('commentName').value;
    const email = document.getElementById('commentEmail').value;
    const text = document.getElementById('commentText').value;

    try {
        const response = await fetch(`${API_URL}/comments.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                project_id: projectId,
                author_name: name,
                author_email: email,
                comment_text: text
            })
        });

        const data = await response.json();

        if (data.success) {
            alert('Comentário enviado! Será publicado após aprovação.');
            document.getElementById('commentForm').reset();
        } else {
            alert('Erro ao enviar comentário: ' + data.message);
        }
    } catch (error) {
        console.error('Erro ao enviar comentário:', error);
        alert('Erro ao enviar comentário');
    }
}
