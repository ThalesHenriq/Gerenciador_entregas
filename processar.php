<?php
// Função para calcular a distância euclidiana entre dois pontos
function euclidean_distance($lat1, $lon1, $lat2, $lon2) {
    // Diferença das coordenadas
    $dlat = $lat2 - $lat1;
    $dlon = $lon2 - $lon1;

    // Fórmula da distância euclidiana
    return sqrt(pow($dlat, 2) + pow($dlon, 2)) * 111.32; // Aproximadamente em km
}

// Função para obter as coordenadas de um endereço usando a OpenCageData API
function get_coordinates($address) {
    $api_key = '5de75b09f5d94c44a78a1ea552f1e271'; // Substitua pela sua chave da OpenCageData
    $url = "https://api.opencagedata.com/geocode/v1/json?q=" . urlencode($address) . "&key=" . $api_key;

    // Realizar a requisição GET
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data['total_results'] > 0) {
        // Retornar as coordenadas (latitude e longitude) do primeiro resultado
        return [
            'lat' => $data['results'][0]['geometry']['lat'],
            'lng' => $data['results'][0]['geometry']['lng']
        ];
    } else {
        return null; // Caso não encontre resultados
    }
}

$file = 'entregas.json';
$entregas = file_exists($file) ? json_decode(file_get_contents($file), true) : array();

// Verifica se o botão "Zerar Lista" foi acionado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'zerar') {
    // Zerar a lista de entregas
    file_put_contents($file, json_encode(array()));
    echo "Lista de entregas zerada com sucesso!";
    echo "<script>
            setTimeout(function(){
                window.location.href = 'index.php';
            }, 2000);
          </script>";
    exit();
}

$file = 'entregas.json';
$entregas = file_exists($file) ? json_decode(file_get_contents($file), true) : array();

// Processar o formulário de adição de entrega
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
    $partida = $_POST['partida'];
    $nome = $_POST['nome'];
    $quantidade = $_POST['quantidade'];
    $status = $_POST['status'];
    $endereco = $_POST['endereco'];

    // Checa duplicação apenas para status "ok"
    $isDuplicate = false;
    foreach ($entregas as $entrega) {
        if ($entrega['endereco'] == $endereco && $entrega['status'] == "ok") {
            $isDuplicate = true;
            break;
        }
    }

    // Só adiciona se não for duplicado com status "ok"
    if (!$isDuplicate || $status == "aguardando") {
        $dados = array(
            'nome' => $nome,
            'quantidade' => $quantidade,
            'status' => $status,
            'endereco' => $endereco,
            'lat' => null, // Inicializar para coordenadas
            'lng' => null
        );

        // Obter coordenadas
        $coords = get_coordinates($endereco);
        if ($coords) {
            $dados['lat'] = $coords['lat'];
            $dados['lng'] = $coords['lng'];
        }

        // Adiciona a nova entrega e grava no arquivo JSON
        $entregas[] = $dados;
        file_put_contents($file, json_encode($entregas, JSON_PRETTY_PRINT));
    }

    header("Location: ver_lista.php");
    exit();
}


// Coordenadas de São Paulo
$partida_lat = -23.5505;
$partida_lng = -46.6333;

// Verificar se há entregas e calcular a distância
if (!empty($entregas)) {
    foreach ($entregas as &$entrega) {
        if (isset($entrega['lat']) && isset($entrega['lng'])) {
            $distancia = euclidean_distance($partida_lat, $partida_lng, $entrega['lat'], $entrega['lng']);
            $entrega['distancia'] = $distancia;
        } else {
            $entrega['distancia'] = null;
        }
    }

    // Ordenar por distância (mais próxima para mais distante)
    usort($entregas, function($a, $b) {
        if ($a['distancia'] === null) return 1;
        if ($b['distancia'] === null) return -1;
        return $a['distancia'] <=> $b['distancia'];
    });
    
    // Exibir entregas
    echo "<table>";
    echo "<tr><th>Nome</th><th>Quantidade</th><th>Status</th><th>Endereço</th></tr>";
    foreach ($entregas as $entrega) {
        echo "<tr>";
        echo "<td>{$entrega['nome']}</td>";
        echo "<td>{$entrega['quantidade']}</td>";
        echo "<td>{$entrega['status']}</td>";
        echo "<td>{$entrega['endereco']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Nenhuma entrega cadastrada.";
}
?>
