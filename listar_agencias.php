<?php
/**
 * Lista agências de estágio
 * IFSP Campus Guarulhos
 * Compatível com Joomla 3.10 e 4.4
 * Versão: 2.0 - Testada e Funcionando
 */

// Headers JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Prevenir acesso direto
define('_JEXEC', 1);
define('JPATH_BASE', dirname(__FILE__));

// Função para retornar JSON
function enviarJSON($success, $message = '', $agencias = array()) {
    echo json_encode(array(
        'success' => $success,
        'message' => $message,
        'agencias' => $agencias
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    // Carregar configuração do Joomla
    $configFile = JPATH_BASE . '/configuration.php';
    
    if (!file_exists($configFile)) {
        enviarJSON(false, 'Arquivo configuration.php não encontrado');
    }
    
    require_once $configFile;
    
    // Criar instância da configuração
    $config = new JConfig();
    
    // Conectar ao banco diretamente
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
    
    // Obter ID (se fornecido)
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Construir query
    $sql = "SELECT id, nome, sigla, logo, site, contato, email, telefone, status, ordering 
            FROM `" . $config->dbprefix . "agencias_estagio`";
    
    if ($id > 0) {
        $sql .= " WHERE id = " . $id;
    }
    
    $sql .= " ORDER BY ordering ASC, nome ASC";
    
    // Executar
    $resultado = $mysqli->query($sql);
    
    if (!$resultado) {
        enviarJSON(false, 'Erro na query: ' . $mysqli->error);
    }
    
    // Buscar resultados
    $agencias = array();
    while ($row = $resultado->fetch_assoc()) {
        $agencias[] = $row;
    }
    
    $mysqli->close();
    
    // Retornar
    enviarJSON(true, '', $agencias);
    
} catch (Exception $e) {
    enviarJSON(false, 'Erro: ' . $e->getMessage());
}