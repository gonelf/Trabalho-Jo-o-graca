<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin ESCS</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="login-page">
    <div class="login-box">
        <h1>ESCS Portfolio</h1>
        <p>Painel Admin</p>

        <form id="formLogin">
            <input type="text" id="username" placeholder="Utilizador" required><br>
            <input type="password" id="password" placeholder="Password" required><br>

            <div id="erro" style="color:red;display:none;"></div>

            <button type="submit">Entrar</button>
        </form>
    </div>

    <script>
        // login simples
        document.getElementById('formLogin').addEventListener('submit', function(e) {
            e.preventDefault();

            var user = document.getElementById('username').value;
            var pass = document.getElementById('password').value;

            fetch('../api/login.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({username: user, password: pass})
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.href = 'projetos.php';
                } else {
                    document.getElementById('erro').textContent = data.message;
                    document.getElementById('erro').style.display = 'block';
                }
            })
            .catch(error => {
                document.getElementById('erro').textContent = 'Erro de conexão';
                document.getElementById('erro').style.display = 'block';
            });
        });
    </script>
</body>
</html>
