<?php
$nome_servidor = "Localhost";
$nome_usuario = "root";
$senha = "";
$nome_banco = "agenda";

//criar conexão

$conecta =  new mysqli($nome_servidor, $nome_usuario, $senha, $nome_banco);


if ($conecta->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}
