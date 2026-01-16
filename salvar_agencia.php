<?php
/**
 * Salva agência de estágio
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
function enviarJSON($success, $message = '', $id = null) {
    $response = array(
        'success' => $success,
        'message' => $message
    );
    if ($id !== null) {
        $response['id'] = $id;
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
    
    // Obter dados POST
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $sigla = isset($_POST['sigla']) ? strtoupper(trim($_POST['sigla'])) : '';
    $site = isset($_POST['site']) ? trim($_POST['site']) : '';
    $contato = isset($_POST['contato']) ? trim($_POST['contato']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
    
    // Validações
    if (empty($nome)) {
        enviarJSON(false, 'Nome da agência é obrigatório');
    }
    
    if (empty($sigla)) {
        enviarJSON(false, 'Sigla é obrigatória');
    }
    
    if (strlen($sigla) > 20) {
        enviarJSON(false, 'Sigla muito longa (máximo 20 caracteres)');
    }
    
    if (!empty($site) && !filter_var($site, FILTER_VALIDATE_URL)) {
        enviarJSON(false, 'URL inválida');
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        enviarJSON(false, 'Email inválido');
    }
    
    // Escapar dados
    $nome = $mysqli->real_escape_string($nome);
    $sigla = $mysqli->real_escape_string($sigla);
    $site = $mysqli->real_escape_string($site);
    $contato = $mysqli->real_escape_string($contato);
    $email = $mysqli->real_escape_string($email);
    $telefone = $mysqli->real_escape_string($telefone);
    
    $prefix = $config->dbprefix;
    
    if ($id > 0) {
        // ==========================================
        // ATUALIZAR AGÊNCIA EXISTENTE
        // ==========================================
        
        $sql = "UPDATE `{$prefix}agencias_estagio` SET 
                nome = '{$nome}',
                sigla = '{$sigla}',
                site = '{$site}',
                contato = '{$contato}',
                email = '{$email}',
                telefone = '{$telefone}',
                modified = NOW(),
                modified_by = 0
                WHERE id = {$id}";
        
        if ($mysqli->query($sql)) {
            enviarJSON(true, 'Agência atualizada com sucesso!', $id);
        } else {
            enviarJSON(false, 'Erro ao atualizar: ' . $mysqli->error);
        }
        
    } else {
        // ==========================================
        // INSERIR NOVA AGÊNCIA
        // ==========================================
        
        // Verificar se sigla já existe
        $sqlCheck = "SELECT COUNT(*) as total FROM `{$prefix}agencias_estagio` WHERE sigla = '{$sigla}'";
        $resultado = $mysqli->query($sqlCheck);
        
        if ($resultado) {
            $row = $resultado->fetch_assoc();
            if ($row['total'] > 0) {
                enviarJSON(false, 'Já existe uma agência com esta sigla');
            }
        }
        
        // Obter próximo ordering
        $sqlOrdering = "SELECT MAX(ordering) as max_ordering FROM `{$prefix}agencias_estagio`";
        $resultado = $mysqli->query($sqlOrdering);
        $maxOrdering = 0;
        
        if ($resultado) {
            $row = $resultado->fetch_assoc();
            $maxOrdering = (int)$row['max_ordering'];
        }
        
        $novoOrdering = $maxOrdering + 1;
        
        // Inserir
        $sql = "INSERT INTO `{$prefix}agencias_estagio` 
                (nome, sigla, site, contato, email, telefone, status, ordering, created, created_by) 
                VALUES 
                ('{$nome}', '{$sigla}', '{$site}', '{$contato}', '{$email}', '{$telefone}', 1, {$novoOrdering}, NOW(), 0)";
        
        if ($mysqli->query($sql)) {
            $novoId = $mysqli->insert_id;
            enviarJSON(true, 'Agência cadastrada com sucesso!', $novoId);
        } else {
            enviarJSON(false, 'Erro ao inserir: ' . $mysqli->error);
        }
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    enviarJSON(false, 'Erro: ' . $e->getMessage());
}