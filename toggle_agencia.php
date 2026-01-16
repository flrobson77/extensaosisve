<?php
/**
 * Ativa/Desativa agência
 * IFSP Campus Guarulhos
 * Compatível com Joomla 3.10 e 4.4
 * Versão: 2.0 - Testada e Funcionando
 */

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'Método não permitido'));
    exit;
}

// Headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Prevenir acesso direto
define('_JEXEC', 1);
define('JPATH_BASE', dirname(__FILE__));

// Função para retornar JSON
function enviarJSON($success, $message = '') {
    echo json_encode(array(
        'success' => $success,
        'message' => $message
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    // Carregar configuração
    $configFile = JPATH_BASE . '/configuration.php';
    
    if (!file_exists($configFile)) {
        enviarJSON(false, 'Arquivo configuration.php não encontrado');
    }
    
    require_once $configFile;
    $config = new JConfig();
    
    // Conectar ao banco
    $mysqli = new mysqli(
        $config->host,
        $config->user,
        $config->password,
        $config->db
    );
    
    if ($mysqli->connect_error) {
        enviarJSON(false, 'Erro de conexão: ' . $mysqli->connect_error);
    }
    
    $mysqli->set_charset("utf8mb4");
    
    // Obter dados
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
    
    if ($id <= 0) {
        enviarJSON(false, 'ID inválido');
    }
    
    if ($status !== 0 && $status !== 1) {
        enviarJSON(false, 'Status inválido (deve ser 0 ou 1)');
    }
    
    $prefix = $config->dbprefix;
    
    // Atualizar
    $sql = "UPDATE `{$prefix}agencias_estagio` SET 
            status = {$status},
            modified = NOW(),
            modified_by = 0
            WHERE id = {$id}";
    
    if ($mysqli->query($sql)) {
        $mensagem = $status == 1 ? 'Agência ativada com sucesso' : 'Agência desativada com sucesso';
        enviarJSON(true, $mensagem);
    } else {
        enviarJSON(false, 'Erro ao atualizar: ' . $mysqli->error);
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    enviarJSON(false, 'Erro: ' . $e->getMessage());
}
