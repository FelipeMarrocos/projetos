<?php
if (isset($_POST['submit'])) {
    include_once("dados.php");
    $usuario = $_POST["usuario"];
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];


    $result = mysqli_query($conecta, "INSERT INTO tb_usuario (usuario,nome,email,senha)
                  VALUES('$usuario','$nome','$email','$senha')");

    if ($result) {
        echo '<script>alert("Usuário cadastrado com sucesso!");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lugares</title>
    <link rel="stylesheet" href="./css/cores.css">
</head>

<body>
    <div class="wrapper">
        <div class="form-box login">
            <h2>Login</h2>
            <form action="testlogin.php" method="POST">
            
                     
            <div class="input-box">
                    <span class="icon"><ion-icon name="mail"></ion-icon></span>
                    <input type="email" name="email" id="email" required> <label>Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                    <input type="password" name="senha" id="senha" required>
                    <label>Senha</label>
                </div>
                <button type="submit" name="submit" id="submit" class="btn"> Login</button>
                <div class="login-register">
                    <p>Não tem uma conta?<a href="#" class="register-link"> Registrar-se</a></p>
                </div>
            </form>
        </div>

        <div class="form-box register">
            <h2>Cadastro</h2>
            <form action="index.php" method="POST">
            <div class="input-box">
            <span class="icon"><ion-icon name="person"></ion-icon></span>
                
            <input type="text" name="usuario" id="usuario" required>
                    <label>Usuário</label>
                    </div>  
                <div class="input-box">
                    <span class="icon"><ion-icon name="person"></ion-icon></span>
                    <input type="nome" name="nome" id="nome" required>
                    <label>Nome</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail"></ion-icon></span>
                    <input type="email" name="email" id="email" required>
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                    <input type="password" name="senha" id="senha" required>
                    <label>Senha</label>
                </div>
                <button type="submit" name="submit" id="submit" class="btn"> Registrar-se</button>
                <div class="login-register">

                    <p>já tem uma conta?<a href="#" class="login-link"> Entrar</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="./js/script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>