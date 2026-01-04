<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Comentários - ESCS Portfolio</title>
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
                <li><a href="manage-projects.php">Gerir Projetos</a></li>
                <li><a href="manage-comments.php" class="active">Gerir Comentários</a></li>
                <li><a href="#" id="logoutBtn">Sair</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <header class="content-header">
                <h1>Gerir Comentários</h1>
            </header>

            <div id="commentsList" class="comments-list">
                <p>A carregar comentários...</p>
            </div>
        </main>
    </div>

    <script>
        const API_URL = '../api';

        // Carregar comentários pendentes
        window.onload = function() {
            // Para simplicidade, vamos apenas mostrar uma mensagem
            document.getElementById('commentsList').innerHTML = `
                <h2>Comentários Pendentes de Aprovação</h2>
                <p>Esta funcionalidade permite gerir comentários dos visitantes.</p>
                <p>Os comentários precisam de ser aprovados antes de aparecerem no site.</p>
            `;
        };

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
