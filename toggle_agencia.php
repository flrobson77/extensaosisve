<?php
/**
 * Arquivo: toggle_agencia.php
 * Descrição: Ativa/Desativa agência
 * IFSP Campus Guarulhos
 */

// Desativar avisos
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

// Headers
header('Content-Type: application/json; charset=utf-8');

// Função para retornar JSON
function retornarJSON($success, $message = '') {
    echo json_encode(array(
        'success' => $success,
        'message' => $message
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    retornarJSON(false, 'Método não permitido.');
}

try {
    // Definir constantes
    if (!defined('_JEXEC')) define('_JEXEC', 1);
    if (!defined('JPATH_BASE')) define('JPATH_BASE', dirname(__FILE__));
    
    // Carregar Joomla
    require_once JPATH_BASE . '/includes/defines.php';
    require_once JPATH_BASE . '/includes/framework.php';
    
    jimport('joomla.application.application');
    jimport('joomla.factory');
    
    // Obter objetos
    if (class_exists('Joomla\CMS\Factory')) {
        $app = Joomla\CMS\Factory::getApplication('site');
        $user = Joomla\CMS\Factory::getUser();
        $db = Joomla\CMS\Factory::getDbo();
    } else {
        $app = JFactory::getApplication('site');
        $user = JFactory::getUser();
        $db = JFactory::getDbo();
    }
    
    // Verificar permissões
    if (!$user->authorise('core.admin')) {
        retornarJSON(false, 'Acesso negado');
    }
    
    // Obter dados
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
    
    if ($id <= 0) {
        retornarJSON(false, 'ID inválido');
    }
    
    // Atualizar
    $query = $db->getQuery(true);
    $query->update($db->quoteName('tbcex4414_agencias_estagio'))
        ->set($db->quoteName('status') . ' = ' . (int)$status)
        ->where($db->quoteName('id') . ' = ' . (int)$id);
    
    $db->setQuery($query);
    $db->execute();
    
    retornarJSON(true, 'Status atualizado com sucesso');
    
} catch (Exception $e) {
    retornarJSON(false, 'Erro: ' . $e->getMessage());
}