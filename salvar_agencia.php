<?php
/**
 * Arquivo: salvar_agencia.php
 * Descrição: Processa cadastro de agências de estágio
 * IFSP Campus Guarulhos
 */

// Desativar avisos
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

// Headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Função para retornar JSON
function retornarJSON($success, $message = '', $id = null) {
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

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    retornarJSON(false, 'Método não permitido. Use POST.');
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
    
    if (!file_exists($definesPath) || !file_exists($frameworkPath)) {
        retornarJSON(false, 'Erro: Script não está na raiz do Joomla.');
    }
    
    // Carregar framework do Joomla
    require_once $definesPath;
    require_once $frameworkPath;
    
    // Importar classes necessárias
    jimport('joomla.application.application');
    jimport('joomla.factory');
    
    // Criar aplicação - Compatível com Joomla 3 e 4
    try {
        if (class_exists('Joomla\CMS\Factory')) {
            // Joomla 4
            $app = Joomla\CMS\Factory::getApplication('site');
            $user = Joomla\CMS\Factory::getUser();
            $db = Joomla\CMS\Factory::getDbo();
        } else {
            // Joomla 3
            $app = JFactory::getApplication('site');
            $user = JFactory::getUser();
            $db = JFactory::getDbo();
        }
    } catch (Exception $e) {
        retornarJSON(false, 'Erro ao iniciar Joomla: ' . $e->getMessage());
    }
    
    // Verificar autenticação
    if (!$user->id) {
        retornarJSON(false, 'Você precisa estar logado.');
    }
    
    if (!$user->authorise('core.admin')) {
        retornarJSON(false, 'Acesso negado. Apenas administradores.');
    }
    
    // Obter dados do POST
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $sigla = isset($_POST['sigla']) ? strtoupper(trim($_POST['sigla'])) : '';
    $site = isset($_POST['site']) ? trim($_POST['site']) : '';
    $contato = isset($_POST['contato']) ? trim($_POST['contato']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    // Validações
    if (empty($nome)) {
        retornarJSON(false, 'Nome da agência é obrigatório');
    }
    
    if (empty($sigla)) {
        retornarJSON(false, 'Sigla é obrigatória');
    }
    
    if (!empty($site) && !filter_var($site, FILTER_VALIDATE_URL)) {
        retornarJSON(false, 'URL inválida');
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        retornarJSON(false, 'Email inválido');
    }
    
    // Obter query
    $query = $db->getQuery(true);
    
    if ($id > 0) {
        // ATUALIZAR
        $fields = array(
            $db->quoteName('nome') . ' = ' . $db->quote($nome),
            $db->quoteName('sigla') . ' = ' . $db->quote($sigla),
            $db->quoteName('site') . ' = ' . $db->quote($site),
            $db->quoteName('contato') . ' = ' . $db->quote($contato),
            $db->quoteName('email') . ' = ' . $db->quote($email),
            $db->quoteName('telefone') . ' = ' . $db->quote($telefone),
            $db->quoteName('modified') . ' = NOW()',
            $db->quoteName('modified_by') . ' = ' . (int)$user->id
        );
        
        $query->update($db->quoteName('tbcex4414_agencias_estagio'))
            ->set($fields)
            ->where($db->quoteName('id') . ' = ' . (int)$id);
        
        $db->setQuery($query);
        $db->execute();
        
        retornarJSON(true, 'Agência atualizada com sucesso!', $id);
        
    } else {
        // INSERIR
        
        // Verificar se sigla já existe
        $queryCheck = $db->getQuery(true);
        $queryCheck->select('COUNT(*)')
            ->from($db->quoteName('tbcex4414_agencias_estagio'))
            ->where($db->quoteName('sigla') . ' = ' . $db->quote($sigla));
        
        $db->setQuery($queryCheck);
        $existe = (int)$db->loadResult();
        
        if ($existe > 0) {
            retornarJSON(false, 'Já existe uma agência com esta sigla');
        }
        
        // Obter próximo ordering
        $queryOrdering = $db->getQuery(true);
        $queryOrdering->select('MAX(ordering)')
            ->from($db->quoteName('tbcex4414_agencias_estagio'));
        
        $db->setQuery($queryOrdering);
        $maxOrdering = (int)$db->loadResult();
        $novoOrdering = $maxOrdering + 1;
        
        // Inserir
        $columns = array(
            'nome', 'sigla', 'site', 'contato', 
            'email', 'telefone', 'status', 'ordering', 
            'created', 'created_by'
        );
        
        $values = array(
            $db->quote($nome),
            $db->quote($sigla),
            $db->quote($site),
            $db->quote($contato),
            $db->quote($email),
            $db->quote($telefone),
            1,
            $novoOrdering,
            'NOW()',
            (int)$user->id
        );
        
        $query->insert($db->quoteName('tbcex4414_agencias_estagio'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));
        
        $db->setQuery($query);
        $db->execute();
        
        $novoId = $db->insertid();
        
        retornarJSON(true, 'Agência cadastrada com sucesso!', $novoId);
    }
    
} catch (Exception $e) {
    retornarJSON(false, 'Erro: ' . $e->getMessage());
}