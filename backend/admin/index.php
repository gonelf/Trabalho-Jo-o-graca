<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ESCS Portfolio Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <h1>ESCS Portfolio</h1>
                <p>Painel de Administração</p>
            </div>

            <form id="loginForm">
                <div class="form-group">
                    <label for="username">Utilizador</label>
                    <input type="text" id="username" name="username" required autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>

                <div id="errorMessage" class="error-message" style="display: none;"></div>

                <button type="submit" class="btn btn-primary">Entrar</button>
            </form>
        </div>
    </div>

    <script>
        const API_URL = '../../backend/api';

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const errorMessage = document.getElementById('errorMessage');

            try {
                const response = await fetch(`${API_URL}/auth.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username, password })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = 'dashboard.php';
                } else {
                    errorMessage.textContent = data.message || 'Erro ao fazer login';
                    errorMessage.style.display = 'block';
                }
            } catch (error) {
                errorMessage.textContent = 'Erro de conexão. Tente novamente.';
                errorMessage.style.display = 'block';
            }
        });
    </script>
</body>
</html>
