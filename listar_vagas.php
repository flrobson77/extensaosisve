<?php
/**
 * Lista vagas de estágio (admin)
 * IFSP Campus Guarulhos
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

define('_JEXEC', 1);
define('JPATH_BASE', dirname(__FILE__));

function enviarJSON($success, $message = '', $vagas = array()) {
    echo json_encode(array(
        'success' => $success,
        'message' => $message,
        'vagas' => $vagas
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
    
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Query com JOIN para trazer nome da agência
    $sql = "SELECT v.*, a.nome as agencia_nome, a.sigla as agencia_sigla 
            FROM `" . $config->dbprefix . "vagas_estagio` v
            LEFT JOIN `" . $config->dbprefix . "agencias_estagio` a ON v.agencia_id = a.id";
    
    if ($id > 0) {
        $sql .= " WHERE v.id = " . $id;
    }
    
    $sql .= " ORDER BY v.destaque DESC, v.created DESC";
    
    $resultado = $mysqli->query($sql);
    
    if (!$resultado) {
        enviarJSON(false, 'Erro na query: ' . $mysqli->error);
    }
    
    $vagas = array();
    while ($row = $resultado->fetch_assoc()) {
        // Calcular dias restantes
        $hoje = new DateTime();
        $data_fim = new DateTime($row['data_fim']);
        $dias_restantes = $hoje->diff($data_fim)->days;
        
        if ($data_fim < $hoje) {
            $dias_restantes = 0;
            $row['expirada'] = true;
        } else {
            $row['expirada'] = false;
        }
        
        $row['dias_restantes'] = $dias_restantes;
        
        $vagas[] = $row;
    }
    
    $mysqli->close();
    
    enviarJSON(true, '', $vagas);
    
} catch (Exception $e) {
    enviarJSON(false, 'Erro: ' . $e->getMessage());
}