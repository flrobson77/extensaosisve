<?php
/**
 * Arquivo: salvar_agencia.php
 * Descrição: Processa cadastro de agências de estágio
 * Local: Raiz do Joomla
 */

// Não permitir acesso direto
define('_JEXEC', 1);

// Carregar o framework do Joomla
define('JPATH_BASE', dirname(__FILE__));
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

// Criar aplicação
$app = JFactory::getApplication('site');

// Verificar se usuário está logado e é administrador
$user = JFactory::getUser();

if (!$user->id) {
    die(json_encode(['success' => false, 'message' => 'Você precisa estar logado']));
}

// Verificar se é administrador
if (!$user->authorise('core.admin')) {
    die(json_encode(['success' => false, 'message' => 'Acesso negado. Apenas administradores.']));
}

// Processar apenas requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Método não permitido']));
}

// Obter dados do POST
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$sigla = isset($_POST['sigla']) ? strtoupper(trim($_POST['sigla'])) : '';
$site = isset($_POST['site']) ? trim($_POST['site']) : '';
$contato = isset($_POST['contato']) ? trim($_POST['contato']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// Validar campos obrigatórios
if (empty($nome)) {
    die(json_encode(['success' => false, 'message' => 'Nome da agência é obrigatório']));
}

if (empty($sigla)) {
    die(json_encode(['success' => false, 'message' => 'Sigla é obrigatória']));
}

if (!empty($site) && !filter_var($site, FILTER_VALIDATE_URL)) {
    die(json_encode(['success' => false, 'message' => 'URL inválida']));
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(['success' => false, 'message' => 'Email inválido']));
}

// Obter banco de dados
$db = JFactory::getDbo();
$query = $db->getQuery(true);

try {
    if ($id > 0) {
        // ATUALIZAR agência existente
        $fields = array(
            $db->quoteName('nome') . ' = ' . $db->quote($nome),
            $db->quoteName('sigla') . ' = ' . $db->quote($sigla),
            $db->quoteName('site') . ' = ' . $db->quote($site),
            $db->quoteName('contato') . ' = ' . $db->quote($contato),
            $db->quoteName('email') . ' = ' . $db->quote($email),
            $db->quoteName('telefone') . ' = ' . $db->quote($telefone),
            $db->quoteName('modified') . ' = NOW()',
            $db->quoteName('modified_by') . ' = ' . $user->id
        );

        $query->update($db->quoteName('#__agencias_estagio'))
            ->set($fields)
            ->where($db->quoteName('id') . ' = ' . $id);

        $db->setQuery($query);
        $db->execute();

        echo json_encode([
            'success' => true, 
            'message' => 'Agência atualizada com sucesso!',
            'id' => $id
        ]);

    } else {
        // INSERIR nova agência
        
        // Verificar se sigla já existe
        $queryCheck = $db->getQuery(true);
        $queryCheck->select('COUNT(*)')
            ->from($db->quoteName('#__agencias_estagio'))
            ->where($db->quoteName('sigla') . ' = ' . $db->quote($sigla));
        
        $db->setQuery($queryCheck);
        $existe = $db->loadResult();

        if ($existe > 0) {
            die(json_encode(['success' => false, 'message' => 'Já existe uma agência com esta sigla']));
        }

        // Obter próximo ordering
        $queryOrdering = $db->getQuery(true);
        $queryOrdering->select('MAX(ordering)')
            ->from($db->quoteName('#__agencias_estagio'));
        
        $db->setQuery($queryOrdering);
        $maxOrdering = (int)$db->loadResult();
        $novoOrdering = $maxOrdering + 1;

        // Inserir
        $columns = array('nome', 'sigla', 'site', 'contato', 'email', 'telefone', 'status', 'ordering', 'created', 'created_by');
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
            $user->id
        );

        $query->insert($db->quoteName('#__agencias_estagio'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));

        $db->setQuery($query);
        $db->execute();

        $novoId = $db->insertid();

        echo json_encode([
            'success' => true, 
            'message' => 'Agência cadastrada com sucesso!',
            'id' => $novoId
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao salvar: ' . $e->getMessage()
    ]);
}