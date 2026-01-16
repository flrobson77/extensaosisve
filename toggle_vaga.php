<?php
/**
 * Ativa/Desativa vaga
 * IFSP Campus Guarulhos
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'Método não permitido'));
    exit;
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

define('_JEXEC', 1);
define('JPATH_BASE', dirname(__FILE__));

function enviarJSON($success, $message = '') {
    echo json_encode(array('success' => $success, 'message' => $message), JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $configFile = JPATH_BASE . '/configuration.php';
    if (!file_exists($configFile)) {
        enviarJSON(false, 'Arquivo configuration.php não encontrado');
    }
    
    require_once $configFile;
    $config = new JConfig();
    
    $mysqli = new mysqli($config->host, $config->user, $config->password, $config->db);
    if ($mysqli->connect_error) {
        enviarJSON(false, 'Erro de conexão: ' . $mysqli->connect_error);
    }
    
    $mysqli->set_charset("utf8mb4");
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
    
    if ($id <= 0) {
        enviarJSON(false, 'ID inválido');
    }
    
    if ($status !== 0 && $status !== 1) {
        enviarJSON(false, 'Status inválido');
    }
    
    $prefix = $config->dbprefix;
    
    $sql = "UPDATE `{$prefix}vagas_estagio` SET 
            status = {$status},
            modified = NOW(),
            modified_by = 0
            WHERE id = {$id}";
    
    if ($mysqli->query($sql)) {
        $mensagem = $status == 1 ? 'Vaga ativada com sucesso' : 'Vaga desativada com sucesso';
        enviarJSON(true, $mensagem);
    } else {
        enviarJSON(false, 'Erro ao atualizar: ' . $mysqli->error);
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    enviarJSON(false, 'Erro: ' . $e->getMessage());
}