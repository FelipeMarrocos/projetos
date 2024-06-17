<?php
session_start();
$logado = $_SESSION["email"];


$nome_servidor = "localhost";
$nome_usuario = "root";
$senha = "";
$nome_banco = "agenda";

$google_api_key = "AIzaSyDA-kKzIVc3RidhTlNYfPKy_jSonyJRtvc";

$conn = new mysqli($nome_servidor, $nome_usuario, $senha, $nome_banco,);
$conn->set_charset("utf8");

if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);

function removerAcentos($string)
{
    return preg_replace(
        array(
            '/(á|à|ã|â|ä)/i',
            '/(é|è|ê|ë)/i',
            '/(í|ì|î|ï)/i',
            '/(ó|ò|õ|ô|ö)/i',
            '/(ú|ù|û|ü)/i',
            '/(ñ)/i',
            '/(ç)/i'
        ),
        array(
            'a',
            'e',
            'i',
            'o',
            'u',
            'n',
            'c'
        ),
        $string
    );
}

function buscarEstados($query, $conn)
{
    $query = removerAcentos($conn->real_escape_string($query));

    // Busca pela sigla exata
    $sqlSigla = "SELECT estado_nome, sigla FROM estados WHERE sigla = '$query'";
    $resultSigla = $conn->query($sqlSigla);

    $estados = array();
    if ($resultSigla->num_rows > 0) {
        while ($row = $resultSigla->fetch_assoc()) {
            $estados[] = $row['sigla'];
        }
    } else {
        // Se não encontrou pela sigla, busca pelo nome
        $sqlNome = "SELECT estado_nome, sigla FROM estados";
        $resultNome = $conn->query($sqlNome);
        if ($resultNome->num_rows > 0) {
            while ($row = $resultNome->fetch_assoc()) {
                $estadoNome = removerAcentos($row['estado_nome']);
                if (stripos($estadoNome, $query) !== false) {
                    $estados[] = $row['sigla'];
                }
            }
        }
    }
    return $estados;
}

function buscarCidades($estado, $conn)
{
    $estado = $conn->real_escape_string($estado);
    $sql = "SELECT cidade FROM cidades WHERE sigla = '$estado'";
    $result = $conn->query($sql);

    $cidades = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cidades[] = $row['cidade'];
        }
    } else {
        $cidades[] = "Nenhuma cidade encontrada para este estado";
    }
    return $cidades;
}
     
function buscarPontosTuristicos($cidade, $google_api_key, $email, $conn)
{
    $cidade = urlencode($cidade);
    $url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=points+of+interest+in+$cidade&language=pt-BR&key=$google_api_key";
    $response = file_get_contents($url);
    $json = json_decode($response, true);

    $pontos_turisticos = array();
    $contador = 0; // Contador para limitar a quantidade de resultados
    if (isset($json['results'])) {
        foreach ($json['results'] as $result) {
            $foto = isset($result['photos']) ? "https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference={$result['photos'][0]['photo_reference']}&key=$google_api_key" : null;
            if ($foto) {
                $place_id = $result['place_id'];
                $detalhes_url = "https://maps.googleapis.com/maps/api/place/details/json?place_id=$place_id&fields=name,formatted_address,photo,rating,reviews,url&language=pt-BR&key=$google_api_key";
                $detalhes_response = file_get_contents($detalhes_url);
                $detalhes_json = json_decode($detalhes_response, true);

                if (isset($detalhes_json['result'])) {
                    $descricao = isset($detalhes_json['result']['reviews'][0]['text']) ? $detalhes_json['result']['reviews'][0]['text'] : "Descrição não disponível";
                    $map_url = isset($detalhes_json['result']['url']) ? $detalhes_json['result']['url'] : null;
                    $is_favorito = isFavorito($email, $place_id, $conn);
                    $ponto_turistico = array(
                        'nome' => $result['name'],
                        'endereco' => $result['formatted_address'],
                        'foto' => $foto,
                        'descricao' => $descricao,
                        'map_url' => $map_url,
                        'place_id' => $place_id,
                        'is_favorito' => $is_favorito
                    );
                    $pontos_turisticos[] = $ponto_turistico;
                    $contador++;
                    if ($contador >= 5) break; // Limitar a quantidade de resultados
                }
            }
        }
    }
    return $pontos_turisticos;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['estado'])) {
    $estado = $_POST['estado'];
    $siglas = buscarEstados($estado, $conn);
    if (empty($siglas)) {
        $siglas[] = "Nenhum estado encontrado";
    }
    $cidades = buscarCidades($siglas[0], $conn);
    echo json_encode($cidades, JSON_UNESCAPED_UNICODE);
    exit;
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cidade'])) {
    $cidade = $_POST['cidade'];
    $email = $_SESSION['email']; // Pegando o email do usuário logado
    $pontos_turisticos = buscarPontosTuristicos($cidade, $google_api_key, $email, $conn);
    echo json_encode($pontos_turisticos, JSON_UNESCAPED_UNICODE);
    exit;
}

// Função para adicionar um favorito
function adicionarFavorito($email, $place_id, $conn)
{
    $stmt = $conn->prepare("INSERT INTO favoritos (user_email, place_id) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $place_id);
    $stmt->execute();
    $stmt->close();
}

// Função para remover um favorito
function removerFavorito($email, $place_id, $conn)
{
    $stmt = $conn->prepare("DELETE FROM favoritos WHERE user_email = ? AND place_id = ?");
    $stmt->bind_param("ss", $email, $place_id);
    $stmt->execute();
    $stmt->close();
}

// Função para verificar se um local é favorito
function isFavorito($email, $place_id, $conn)
{
    $stmt = $conn->prepare("SELECT * FROM favoritos WHERE user_email = ? AND place_id = ?");
    $stmt->bind_param("ss", $email, $place_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $isFavorito = $result->num_rows > 0;
    $stmt->close();
    return $isFavorito;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['acao'])) {
    $email = $_SESSION['email'];
    $place_id = $_POST['place_id'];
    $acao = $_POST['acao'];
    

    if ($acao == 'adicionar') {
        adicionarFavorito($email, $place_id, $conn);
    } else if ($acao == 'remover') {
        removerFavorito($email, $place_id, $conn);
    }

    exit;
}
 $conn->close();
?>

   




<!DOCTYPE html>
<html lang="PT-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
    
</head>

 
<body>
<style>
    body {
    background-image: url('./img/b.png');
    color: white;
    background-size: cover; 
   
    background-repeat: no-repeat;  
    }
    
</style>
<nav class="navbar navbar-expand-lg bg-body-tertary" style="background-color: #f0f0f0">
  <div class="container-fluid">
    <div class="navbar-brand">
      <a class="navbar-brand" href="WebSite.php">Lugares</a>
    </div>

    <div class="navbar-collapse collapse w-auto order-3 ml-auto">
      <label class="d-none d-md-inline-block mr-sm-2"><h6><?php echo $logado; ?></h6></label>
      <a href="crud.php" class="btn btn-success2">Editar Perfil</a>
      <a href="index.php" class="btn btn-success btn-outline-light">Logout</a>
    </div>
  </div>
</nav>


<main>
  <div class="container">
        <h2>Escolher Estado e Cidade</h2>
       <form id="form_estado" method="POST" action="">
            <div class="mb-3">
                <input type="text" class="form-control" id="estado" name="estado" placeholder="Digite o Estado (nome ou sigla)...">
            </div>
            <div class="mb-3" id="cidade_div" style="display: none;">
                <select class="form-control" id="cidade" name="cidade" disabled>
                    <option>Escolha a cidade...</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" id="confirmar_estado_btn">Confirmar Estado</button>
            <button type="button" class="btn btn-primary" id="confirmar_cidade_btn" style="display: none;">Confirmar Cidade</button>
       </form>
    <div id="info_cidade" style="display: none;">
        <h3>Informações sobre a cidade</h3>
        <div class="imagem" id="imagem_local"></div>
        <div class="pontos_turisticos" id="pontos_turisticos"></div>
    </div>
</div>
</main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#form_estado').submit(function(event) {
                event.preventDefault();
                var estado = $('#estado').val();
                $.ajax({
                    url: 'WebSite.php',
                    method: 'POST',
                    data: {
                        estado: estado
                    },
                    success: function(data) {
                        var cidades = JSON.parse(data);
                        var select = $('#cidade');
                        select.empty();
                        if (cidades[0] !== "Nenhum estado encontrado") {
                            select.prop('disabled', false);
                            $.each(cidades, function(index, cidade) {
                                select.append($('<option></option>').text(cidade));
                            });
                            $('#cidade_div').show();
                            $('#confirmar_estado_btn').hide();
                            $('#confirmar_cidade_btn').show();
                        } else {
                            select.prop('disabled', true);
                            select.append($('<option></option>').text('Nenhuma cidade encontrada'));
                            $('#cidade_div').hide();
                            $('#confirmar_cidade_btn').hide();
                            $('#confirmar_estado_btn').show();
                        }
                    }
                });
            });

            $('#confirmar_cidade_btn').click(function() {
                var cidade = $('#cidade').val();
                $.ajax({
                    url: 'WebSite.php',
                    method: 'POST',
                    data: {
                        cidade: cidade
                    },
                    success: function(data) {
                        var pontosTuristicos = JSON.parse(data);
                        $('#info_cidade').show();
                        if (pontosTuristicos.length > 0) {
                            $('#pontos_turisticos').empty();
                            $.each(pontosTuristicos, function(index, pontoTuristico) {
                                var estrelaClass = pontoTuristico.is_favorito ? 'estrela-favorita' : 'estrela-nao-favorita';
                                $('#pontos_turisticos').append(
                                    '<div><h3>' + pontoTuristico.nome + '</h3>' +
                                    '<p>' + pontoTuristico.endereco + '</p>' +
                                    (pontoTuristico.foto ? '<img src="' + pontoTuristico.foto + '" alt="' + pontoTuristico.nome + '">' : '') +
                                    '<p>' + pontoTuristico.descricao + '</p>' +
                                    (pontoTuristico.map_url ? '<a href="' + pontoTuristico.map_url + '" target="_blank">Ver no Google Maps</a>' : '') +
                                    '<span class="estrela ' + estrelaClass + '" data-place-id="' + pontoTuristico.place_id + '">&#9733;</span>' +
                                    '</div>'
                                );
                            });

                            $('.estrela').click(function() {
                                var place_id = $(this).data('place-id');
                                var isFavorito = $(this).hasClass('estrela-favorita');
                                var acao = isFavorito ? 'remover' : 'adicionar';
                                var estrelaElement = $(this);

                                $.ajax({
                                    url: 'WebSite.php',
                                    method: 'POST',
                                    data: {
                                        acao: acao,
                                        place_id: place_id
                                    },
                                    success: function(response) {
                                        if (acao == 'adicionar') {
                                            estrelaElement.removeClass('estrela-nao-favorita').addClass('estrela-favorita');
                                        } else {
                                            estrelaElement.removeClass('estrela-favorita').addClass('estrela-nao-favorita');
                                        }
                                    }
                                });
                            });
                        } else {
                            $('#pontos_turisticos').html('Nenhum ponto turístico encontrado para esta cidade.');
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>