<?php
/**
 * Arquivo: listar_agencias.php
 * Descrição: Lista agências cadastradas
 * Local: Raiz do Joomla
 */

define('_JEXEC', 1);
define('JPATH_BASE', dirname(__FILE__));
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

$app = JFactory::getApplication('site');
$user = JFactory::getUser();

// Verificar se é administrador
if (!$user->authorise('core.admin')) {
    die(json_encode(['success' => false, 'message' => 'Acesso negado']));
}

$db = JFactory::getDbo();
$query = $db->getQuery(true);

// Se passou ID, buscar apenas uma agência
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query->select('*')
    ->from($db->quoteName('tbcex4414_agencias_estagio'));

if ($id > 0) {
    $query->where($db->quoteName('id') . ' = ' . $id);
}

$query->order('ordering ASC, nome ASC');

$db->setQuery($query);
$agencias = $db->loadObjectList();

echo json_encode([
    'success' => true,
    'agencias' => $agencias
]);