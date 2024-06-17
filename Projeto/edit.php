<?php
 
if (!empty($_GET['id'])) {
    include_once("dados.php");

    $id = $_GET['id'];
    
    $sqlSelect = "SELECT * FROM tb_usuario WHERE id=$id";

    $result = $conecta->query($sqlSelect);

    if($result->num_rows > 0) {

        while($user_data = mysqli_fetch_assoc($result)){
       
        $nome = $user_data["nome"]; 
        $email = $user_data["email"];
        $senha = $user_data["senha"];
        $usuario = $user_data["usuario"];
     }    
    }
    else {
        header('location: crud.php');
    } 
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar</title>
    <link rel="stylesheet" href="./css/cores.css">
    <style>
        body {
            background-image: url('./img/p.jpg');
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
        }
    </style>

</head>
<body>
    <div class="wrapper">
        <div class="form-box login">
            <h2></h2>
            <form id="registerForm" action="saveEdit.php" method="POST">
            <div class="input-box">
                    <span class="icon"><ion-icon name="person"></ion-icon></span>
                    <input type="text" name="usuario" id="usuario" value="<?php echo $usuario ?>" required>
                    <label>Usuário</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="person"></ion-icon></span>
                    <input type="text" name="nome" id="nome" value="<?php echo $nome ?>" required>
                    <label>Nome Completo</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail"></ion-icon></span>
                    <input type="email" name="email" id="email" value="<?php echo $email ?>" required >
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                    <input type="password" name="senha" id="senha" value="<?php echo $senha ?>" required >
                    <label>Senha</label>
                    <input type="hidden" name="id" value="<?php echo $id ?>">
                
                </div>
                <button type="submit" name="update" id="update" class="btn">Editar</button>
                <div class="login-register">
                    <p><a href="crud.php" class="register-link">Voltar</a></p>
                </div>
                </div>
            </form>
        </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
