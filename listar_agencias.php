<?php
/**
 * Arquivo: listar_agencias.php
 * Descrição: Lista agências cadastradas
 * IFSP Campus Guarulhos
 */

// Desativar avisos
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

// Headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Função para retornar JSON
function retornarJSON($success, $message = '', $data = array()) {
    echo json_encode(array(
        'success' => $success,
        'message' => $message,
        'agencias' => $data
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    // Definir constantes do Joomla
    if (!defined('_JEXEC')) {
        define('_JEXEC', 1);
    }
    
    if (!defined('JPATH_BASE')) {
        define('JPATH_BASE', dirname(__FILE__));
    }
    
    // Verificar se estamos na raiz do Joomla
    $definesPath = JPATH_BASE . '/includes/defines.php';
    $frameworkPath = JPATH_BASE . '/includes/framework.php';
    
    if (!file_exists($definesPath)) {
        retornarJSON(false, 'Erro: Script não está na raiz do Joomla. Defines.php não encontrado.');
    }
    
    if (!file_exists($frameworkPath)) {
        retornarJSON(false, 'Erro: Framework.php não encontrado.');
    }
    
    // Carregar framework do Joomla
    require_once $definesPath;
    require_once $frameworkPath;
    
    // Importar a aplicação
    jimport('joomla.application.application');
    jimport('joomla.factory');
    
    // Criar aplicação - Método para Joomla 3 e 4
    try {
        // Joomla 4
        if (class_exists('Joomla\CMS\Factory')) {
            $app = Joomla\CMS\Factory::getApplication('site');
            $user = Joomla\CMS\Factory::getUser();
            $db = Joomla\CMS\Factory::getDbo();
        } 
        // Joomla 3
        else {
            $app = JFactory::getApplication('site');
            $user = JFactory::getUser();
            $db = JFactory::getDbo();
        }
    } catch (Exception $e) {
        retornarJSON(false, 'Erro ao iniciar Joomla: ' . $e->getMessage());
    }
    
    // Verificar se usuário está logado
    if (!$user->id) {
        retornarJSON(false, 'Você precisa estar logado como administrador.');
    }
    
    // Verificar se é administrador
    if (!$user->authorise('core.admin')) {
        retornarJSON(false, 'Acesso negado. Apenas administradores.');
    }
    
    // Construir query
    $query = $db->getQuery(true);
    
    // Se passou ID específico
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    $query->select($db->quoteName(array(
        'id', 'nome', 'sigla', 'logo', 'site', 
        'contato', 'email', 'telefone', 'status', 'ordering'
    )))
    ->from($db->quoteName('tbcex4414_agencias_estagio'));
    
    if ($id > 0) {
        $query->where($db->quoteName('id') . ' = ' . (int)$id);
    }
    
    $query->order($db->quoteName('ordering') . ' ASC, ' . $db->quoteName('nome') . ' ASC');
    
    // Executar query
    $db->setQuery($query);
    $agencias = $db->loadObjectList();
    
    // Verificar erros
    if ($db->getErrorNum()) {
        retornarJSON(false, 'Erro no banco: ' . $db->getErrorMsg());
    }
    
    // Retornar dados
    retornarJSON(true, '', $agencias ? $agencias : array());
    
} catch (Exception $e) {
    retornarJSON(false, 'Exceção: ' . $e->getMessage());
}