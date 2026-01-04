<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ESCS Portfolio Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>ESCS Portfolio</h2>
                <p id="userInfo"></p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="manage-projects.php">Gerir Projetos</a></li>
                <li><a href="manage-comments.php">Gerir Comentários</a></li>
                <li><a href="#" id="logoutBtn">Sair</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <header class="content-header">
                <h1>Dashboard</h1>
            </header>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Total de Projetos</h3>
                    <p class="stat-number" id="totalProjects">-</p>
                </div>
                <div class="stat-card">
                    <h3>Projetos Publicados</h3>
                    <p class="stat-number" id="publishedProjects">-</p>
                </div>
                <div class="stat-card">
                    <h3>Comentários Pendentes</h3>
                    <p class="stat-number" id="pendingComments">-</p>
                </div>
                <div class="stat-card">
                    <h3>Visualizações Totais</h3>
                    <p class="stat-number" id="totalViews">-</p>
                </div>
            </div>

            <div class="dashboard-content">
                <h2>Bem-vindo ao Painel de Administração</h2>
                <p>Utilize o menu lateral para gerir os projetos e comentários do ESCS Portfolio.</p>
            </div>
        </main>
    </div>

    <script src="admin.js"></script>
    <script>
        // Carregar estatísticas do dashboard
        async function loadDashboardStats() {
            // Esta é uma implementação simplificada
            // Em produção, criar um endpoint específico para estatísticas
            document.getElementById('totalProjects').textContent = '-';
            document.getElementById('publishedProjects').textContent = '-';
            document.getElementById('pendingComments').textContent = '-';
            document.getElementById('totalViews').textContent = '-';
        }

        checkAuth();
        loadDashboardStats();
    </script>
</body>
</html>
