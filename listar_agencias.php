<?php
/**
 * Lista agências de estágio
 * IFSP Campus Guarulhos
 */

// Prevenir acesso direto sem Joomla
define('_JEXEC', 1);

// Definir o caminho base
define('JPATH_BASE', dirname(__FILE__));

// Carregar sistema do Joomla
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

// Importar bibliotecas necessárias
jimport('joomla.application.application');

// Headers JSON
header('Content-Type: application/json; charset=utf-8');

// Função para retornar JSON
function enviarJSON($success, $message = '', $agencias = array()) {
    echo json_encode(array(
        'success' => $success,
        'message' => $message,
        'agencias' => $agencias
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Criar instância da aplicação manualmente
    // Isso evita o erro "Failed to start application"
    
    // Obter o container
    $container = \Joomla\CMS\Factory::getContainer();
    
    // Criar aplicação
    $app = $container->get(\Joomla\CMS\Application\SiteApplication::class);
    
    // Inicializar a aplicação
    $app->initialise();
    
    // Obter usuário
    $user = \Joomla\CMS\Factory::getUser();
    
    // Verificar se está logado
    if ($user->guest) {
        enviarJSON(false, 'Você precisa estar logado como administrador');
    }
    
    // Verificar se é admin
    if (!$user->authorise('core.admin')) {
        enviarJSON(false, 'Acesso negado. Apenas administradores');
    }
    
    // Obter banco de dados
    $db = \Joomla\CMS\Factory::getDbo();
    
    // Obter ID (se fornecido)
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Construir query
    $query = $db->getQuery(true);
    
    $query->select('*')
        ->from($db->quoteName('tbcex4414_agencias_estagio'));
    
    if ($id > 0) {
        $query->where($db->quoteName('id') . ' = ' . $db->quote($id));
    }
    
    $query->order($db->quoteName('ordering') . ' ASC');
    $query->order($db->quoteName('nome') . ' ASC');
    
    // Executar
    $db->setQuery($query);
    $agencias = $db->loadObjectList();
    
    // Retornar
    enviarJSON(true, '', $agencias ? $agencias : array());
    
} catch (Exception $e) {
    enviarJSON(false, 'Erro: ' . $e->getMessage());
}