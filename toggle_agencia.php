<?php
/**
 * Ativa/Desativa agência
 * IFSP Campus Guarulhos
 */

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'Método não permitido'));
    exit;
}

// Prevenir acesso direto sem Joomla
define('_JEXEC', 1);

// Definir o caminho base
define('JPATH_BASE', dirname(__FILE__));

// Carregar sistema do Joomla
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

// Importar bibliotecas
jimport('joomla.application.application');

// Headers
header('Content-Type: application/json; charset=utf-8');

// Função para retornar JSON
function enviarJSON($success, $message = '') {
    echo json_encode(array(
        'success' => $success,
        'message' => $message
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Criar aplicação
    $container = \Joomla\CMS\Factory::getContainer();
    $app = $container->get(\Joomla\CMS\Application\SiteApplication::class);
    $app->initialise();
    
    // Obter usuário
    $user = \Joomla\CMS\Factory::getUser();
    
    // Verificar permissões
    if (!$user->authorise('core.admin')) {
        enviarJSON(false, 'Acesso negado');
    }
    
    // Obter dados
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
    
    if ($id <= 0) {
        enviarJSON(false, 'ID inválido');
    }
    
    // Obter banco
    $db = \Joomla\CMS\Factory::getDbo();
    
    // Atualizar
    $query = $db->getQuery(true);
    
    $query->update($db->quoteName('tbcex4414_agencias_estagio'))
        ->set($db->quoteName('status') . ' = ' . (int)$status)
        ->set($db->quoteName('modified') . ' = NOW()')
        ->set($db->quoteName('modified_by') . ' = ' . (int)$user->id)
        ->where($db->quoteName('id') . ' = ' . (int)$id);
    
    $db->setQuery($query);
    $db->execute();
    
    enviarJSON(true, 'Status atualizado com sucesso');
    
} catch (Exception $e) {
    enviarJSON(false, 'Erro: ' . $e->getMessage());
}