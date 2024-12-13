<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Conexão com o banco de dados
$mysqli = new mysqli('localhost', 'user', 'password', 'database');
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro na conexão: " . $mysqli->connect_error]);
    exit;
}

// Função para responder JSON
function responder($dados, $status = 200) {
    http_response_code($status);
    echo json_encode($dados);
    exit;
}

// API para cadastrar usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['endpoint'] === 'cadastrarUsuario') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $nome = $dados['nome'] ?? '';
    $email = $dados['email'] ?? '';
    $senha = $dados['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        responder(["erro" => "Dados incompletos."], 400);
    }

    global $mysqli;
    $senhaCriptografada = md5($senha);
    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senhaCriptografada')";
    if ($mysqli->query($sql)) {
        responder(["mensagem" => "Usuário cadastrado com sucesso!"]);
    } else {
        responder(["erro" => "Erro ao cadastrar usuário: " . $mysqli->error], 500);
    }
}

// API para cadastrar motorista
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['endpoint'] === 'cadastrarMotorista') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $nome = $dados['nome'] ?? '';
    $email = $dados['email'] ?? '';
    $senha = $dados['senha'] ?? '';
    $placa = $dados['placa'] ?? '';
    $modelo = $dados['modelo'] ?? '';

    if (empty($nome) || empty($email) || empty($senha) || empty($placa) || empty($modelo)) {
        responder(["erro" => "Dados incompletos."], 400);
    }

    global $mysqli;
    $senhaCriptografada = md5($senha);
    $sql = "INSERT INTO motoristas (nome, email, senha, placa_veiculo, modelo_veiculo, status) 
            VALUES ('$nome', '$email', '$senhaCriptografada', '$placa', '$modelo', 'disponível')";
    if ($mysqli->query($sql)) {
        responder(["mensagem" => "Motorista cadastrado com sucesso!"]);
    } else {
        responder(["erro" => "Erro ao cadastrar motorista: " . $mysqli->error], 500);
    }
}

// API para solicitar uma corrida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['endpoint'] === 'solicitarCorrida') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $usuarioId = $dados['usuario_id'] ?? '';
    $origem = $dados['origem'] ?? '';
    $destino = $dados['destino'] ?? '';

    if (empty($usuarioId) || empty($origem) || empty($destino)) {
        responder(["erro" => "Dados incompletos."], 400);
    }

    global $mysqli;
    $sql = "INSERT INTO corridas (usuario_id, origem, destino, status) 
            VALUES ('$usuarioId', '$origem', '$destino', 'pendente')";
    if ($mysqli->query($sql)) {
        responder(["mensagem" => "Corrida solicitada com sucesso!"]);
    } else {
        responder(["erro" => "Erro ao solicitar corrida: " . $mysqli->error], 500);
    }
}

// API para atribuir um motorista a uma corrida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['endpoint'] === 'atribuirMotorista') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $corridaId = $dados['corrida_id'] ?? '';

    if (empty($corridaId)) {
        responder(["erro" => "Dados incompletos."], 400);
    }

    global $mysqli;

    $cacheFile = 'cache_motoristas_disponiveis.json';
    if (file_exists($cacheFile)) {
        $motoristasDisponiveis = json_decode(file_get_contents($cacheFile), true);
    } else {
        $result = $mysqli->query("SELECT id FROM motoristas WHERE status = 'disponível' LIMIT 1");
        $motoristasDisponiveis = $result->fetch_all(MYSQLI_ASSOC);
        file_put_contents($cacheFile, json_encode($motoristasDisponiveis));
    }

    if (empty($motoristasDisponiveis)) {
        responder(["erro" => "Nenhum motorista disponível."], 400);
    }

    $motoristaId = $motoristasDisponiveis[0]['id'];

    $sql = "UPDATE corridas SET motorista_id = $motoristaId, status = 'em andamento' WHERE id = $corridaId";
    if ($mysqli->query($sql)) {
        $mysqli->query("UPDATE motoristas SET status = 'ocupado' WHERE id = $motoristaId");
        unlink($cacheFile);
        responder(["mensagem" => "Motorista atribuído à corrida!"]);
    } else {
        responder(["erro" => "Erro ao atribuir motorista: " . $mysqli->error], 500);
    }
}

// API para finalizar uma corrida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['endpoint'] === 'finalizaCorrida') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $corridaId = $dados['corrida_id'] ?? '';

    if (empty($corridaId)) {
        responder(["erro" => "Dados incompletos."], 400);
    }

    global $mysqli;
    $sql = "UPDATE corridas SET status = 'finalizada' WHERE id = $corridaId";
    if ($mysqli->query($sql)) {
        responder(["mensagem" => "Corrida finalozada com sucesso!"]);
    } else {
        responder(["erro" => "Erro ao finalizar corrida: " . $mysqli->error], 500);
    }
}

// API para buscar detalhes da corrida
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['endpoint'] === 'buscarDetalhesCorrida') {
    $corridaId = $_GET['corrida_id'] ?? '';

    if (empty($corridaId)) {
        responder(["erro" => "ID da corrida não fornecido."], 400);
    }

    global $mysqli;

    $cacheFile = "cache_corrida_$corridaId.json";
    if (file_exists($cacheFile)) {
        $dadosCorrida = json_decode(file_get_contents($cacheFile), true);
    } else {
        $result = $mysqli->query("SELECT * FROM corridas WHERE id = $corridaId");
        if ($result->num_rows > 0) {
            $dadosCorrida = $result->fetch_assoc();
            file_put_contents($cacheFile, json_encode($dadosCorrida));
        } else {
            responder(["erro" => "Corrida não encontrada."], 404);
        }
    }

    responder($dadosCorrida);
}