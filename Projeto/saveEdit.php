<?php
    include_once('dados.php');
    
    if(isset($_POST['update'])) {
       
    $id = $_POST["id"];
    $usuario = $_POST["usuario"];
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $sqlUpdate = "UPDATE tb_usuario SET usuario='$usuario',nome='$nome',email='$email',senha='$senha' 
    WHERE id='$id'";
   
   $result = $conecta ->query($sqlUpdate);
}

   header('Location: crud.php')

?>