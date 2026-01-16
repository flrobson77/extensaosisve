<?php
/**
 * Lista agências ATIVAS para página pública
 * IFSP Campus Guarulhos
 * Versão: 1.0
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

define('_JEXEC', 1);
define('JPATH_BASE', dirname(__FILE__));

function enviarJSON($success, $message = '', $agencias = array()) {
    echo json_encode(array(
        'success' => $success,
        'message' => $message,
        'agencias' => $agencias
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
    
    // Buscar APENAS agências ATIVAS
    $sql = "SELECT id, nome, sigla, logo, site, email, telefone, ordering 
            FROM `" . $config->dbprefix . "agencias_estagio`
            WHERE status = 1
            ORDER BY ordering ASC, nome ASC";
    
    $resultado = $mysqli->query($sql);
    
    if (!$resultado) {
        enviarJSON(false, 'Erro na query: ' . $mysqli->error);
    }
    
    $agencias = array();
    while ($row = $resultado->fetch_assoc()) {
        $agencias[] = $row;
    }
    
    $mysqli->close();
    
    enviarJSON(true, '', $agencias);
    
} catch (Exception $e) {
    enviarJSON(false, 'Erro: ' . $e->getMessage());
}