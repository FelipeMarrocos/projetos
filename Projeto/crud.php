<?php
session_start();
include_once('dados.php');

// Verifica se a sessão está iniciada e se o email está definido
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit(); // Encerra o script para evitar que o código continue sendo executado
}

// Obtém o email da sessão
$logado = $_SESSION['email'];

// Consulta SQL ajustada para selecionar apenas o usuário logado
$sql = "SELECT * FROM tb_usuario WHERE email = '$logado'";

$result = mysqli_query($conecta, $sql);

// Verifica se há resultados na consulta
if (mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result); // Obtém os dados do usuário
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lugares</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

        <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
                background-image: url('./img/s.jpg');
                background-size: cover;  
                background-repeat: no-repeat; 
                color: blue;
                text-align: center;
            }

            .table-bg {
                background: rgba(0, 0, 0, 0.3);
                border-radius: 15px 15px 0 0;
            }
        </style>
    </head>

    <body>
        
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Perfil</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                </li>
                </ul>
            </div>
            <div class="d-flex">
                <a href="Website.php" class="btn btn-danger me-5">Voltar</a>
            </div>
        </nav>
        <?php
        echo "<h1>Bem vindo <u>$logado</u></h1>";
        ?>
        <div class="m-5">
            <table class="table text-white table-bg">
                <thead>
                    <tr>
                        <th scope="col">Usuário</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Email</th>
                        <th scope="col">Senha</th>
                        <th scope="col">...</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $user_data['usuario']; ?></td>
                        <td><?php echo $user_data['nome']; ?></td>
                        <td><?php echo $user_data['email']; ?></td>
                        <td><?php echo $user_data['senha']; ?></td>
                        <td>
                            <a class='btn btn-sm btn-primary' href='edit.php?id=<?php echo $user_data['id']; ?>'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                                    <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325' />
                                </svg>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>

    </html>
<?php
} else {
    echo "<p>Nenhum usuário encontrado.</p>";
}
?>
