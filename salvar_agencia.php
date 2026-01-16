<?php
/**
 * Salva agência de estágio
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
function enviarJSON($success, $message = '', $id = null) {
    $response = array(
        'success' => $success,
        'message' => $message
    );
    if ($id) {
        $response['id'] = $id;
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Criar aplicação
    $container = \Joomla\CMS\Factory::getContainer();
    $app = $container->get(\Joomla\CMS\Application\SiteApplication::class);
    $app->initialise();
    
    // Obter usuário
    $user = \Joomla\CMS\Factory::getUser();
    
    // Verificar autenticação
    if ($user->guest) {
        enviarJSON(false, 'Você precisa estar logado');
    }
    
    if (!$user->authorise('core.admin')) {
        enviarJSON(false, 'Acesso negado. Apenas administradores');
    }
    
    // Obter banco
    $db = \Joomla\CMS\Factory::getDbo();
    
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
    
    if (!empty($site) && !filter_var($site, FILTER_VALIDATE_URL)) {
        enviarJSON(false, 'URL inválida');
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        enviarJSON(false, 'Email inválido');
    }
    
    // Query
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
        
        enviarJSON(true, 'Agência atualizada com sucesso!', $id);
        
    } else {
        // INSERIR
        
        // Verificar duplicidade de sigla
        $queryCheck = $db->getQuery(true);
        $queryCheck->select('COUNT(*)')
            ->from($db->quoteName('tbcex4414_agencias_estagio'))
            ->where($db->quoteName('sigla') . ' = ' . $db->quote($sigla));
        
        $db->setQuery($queryCheck);
        $existe = (int)$db->loadResult();
        
        if ($existe > 0) {
            enviarJSON(false, 'Já existe uma agência com esta sigla');
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
        
        enviarJSON(true, 'Agência cadastrada com sucesso!', $novoId);
    }
    
} catch (Exception $e) {
    enviarJSON(false, 'Erro: ' . $e->getMessage());
}