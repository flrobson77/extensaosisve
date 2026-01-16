<?php
/**
 * Arquivo: toggle_agencia.php
 * Descrição: Ativa/Desativa agência
 * Local: Raiz do Joomla
 */

define('_JEXEC', 1);
define('JPATH_BASE', dirname(__FILE__));
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

$app = JFactory::getApplication('site');
$user = JFactory::getUser();

if (!$user->authorise('core.admin')) {
    die(json_encode(['success' => false, 'message' => 'Acesso negado']));
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$status = isset($_POST['status']) ? (int)$_POST['status'] : 0;

if ($id <= 0) {
    die(json_encode(['success' => false, 'message' => 'ID inválido']));
}

$db = JFactory::getDbo();
$query = $db->getQuery(true);

$query->update($db->quoteName('#__agencias_estagio'))
    ->set($db->quoteName('status') . ' = ' . $status)
    ->where($db->quoteName('id') . ' = ' . $id);

$db->setQuery($query);

try {
    $db->execute();
    echo json_encode(['success' => true, 'message' => 'Status atualizado']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}