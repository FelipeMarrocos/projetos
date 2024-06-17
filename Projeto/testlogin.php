<?php
session_start();
//print_r($_REQUEST);
if (isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
   //acessa
   include_once("dados.php");
   $email = $_POST["email"];
   $senha = $_POST["senha"];

   //print_r("email: " . $email);
   //print_r("<br>");
   //print_r("senha: " . $senha);

   $sql = "SELECT * FROM tb_usuario WHERE email = '$email' and senha = '$senha'";

   $result = $conecta->query($sql);

   //print_r($result);
   //print_r($sql);
   if (mysqli_num_rows($result) < 1) {
      unset($_SESSION["email"]);
      unset($_SESSION["senha"]);
      echo "<script>
               alert('Usuário ou senha incorretos');
               window.location.href = 'index.php';
            </script>";
   } else {
      $_SESSION["email"] = $email;
      $_SESSION["senha"] = $senha;
      header("location: WebSite.php");
   }
} else {
   //não acessa
   header("location: index.php");
}
