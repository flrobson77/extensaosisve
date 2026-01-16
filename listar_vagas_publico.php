<?php
/**
 * Lista vagas ATIVAS para página pública (com limite)
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
    
    // Parâmetros
    $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 0;
    $carrossel = isset($_GET['carrossel']) && $_GET['carrossel'] == '1';
    
    // Query - APENAS vagas ATIVAS e não expiradas
    $sql = "SELECT v.*, a.nome as agencia_nome, a.sigla as agencia_sigla, a.site as agencia_site, a.logo as agencia_logo
            FROM `" . $config->dbprefix . "vagas_estagio` v
            LEFT JOIN `" . $config->dbprefix . "agencias_estagio` a ON v.agencia_id = a.id
            WHERE v.status = 1 
            AND v.data_fim >= CURDATE()
            AND v.data_inicio <= CURDATE()
            ORDER BY v.destaque DESC, v.created DESC";
    
    if ($limite > 0) {
        $sql .= " LIMIT " . $limite;
    }
    
    $resultado = $mysqli->query($sql);
    
    if (!$resultado) {
        enviarJSON(false, 'Erro na query: ' . $mysqli->error);
    }
    
    $vagas = array();
    while ($row = $resultado->fetch_assoc()) {
        // Calcular dias restantes
        $hoje = new DateTime();
        $data_fim = new DateTime($row['data_fim']);
        $diff = $hoje->diff($data_fim);
        $dias_restantes = ($data_fim >= $hoje) ? $diff->days : 0;
        
        $row['dias_restantes'] = $dias_restantes;
        
        // Se for para carrossel, retornar apenas dados essenciais
        if ($carrossel) {
            $vagas[] = array(
                'id' => $row['id'],
                'titulo' => $row['titulo'],
                'empresa' => $row['empresa'],
                'imagem' => $row['imagem'],
                'agencia_nome' => $row['agencia_nome'],
                'agencia_sigla' => $row['agencia_sigla']
            );
        } else {
            $vagas[] = $row;
        }
    }
    
    $mysqli->close();
    
    enviarJSON(true, '', $vagas);
    
} catch (Exception $e) {
    enviarJSON(false, 'Erro: ' . $e->getMessage());
}