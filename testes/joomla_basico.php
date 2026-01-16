<?php
define('_JEXEC', 1);
define('JPATH_BASE', dirname(__FILE__));

echo "<h1>Teste Básico Joomla</h1>";

// Verificar arquivos
if (file_exists(JPATH_BASE . '/includes/defines.php')) {
    echo "<p>✓ defines.php encontrado</p>";
    require_once JPATH_BASE . '/includes/defines.php';
} else {
    die("<p>✗ defines.php NÃO encontrado</p>");
}

if (file_exists(JPATH_BASE . '/includes/framework.php')) {
    echo "<p>✓ framework.php encontrado</p>";
    require_once JPATH_BASE . '/includes/framework.php';
} else {
    die("<p>✗ framework.php NÃO encontrado</p>");
}

jimport('joomla.application.application');

// Verificar versão
if (class_exists('\Joomla\CMS\Factory')) {
    echo "<p>✓ Detectado Joomla 4.x</p>";
    
    try {
        $config = \Joomla\CMS\Factory::getConfig();
        echo "<p>✓ Config carregada</p>";
        
        $user = \Joomla\CMS\Factory::getUser();
        echo "<p>User ID: " . $user->id . "</p>";
        echo "<p>Username: " . ($user->username ?: 'Guest') . "</p>";
        echo "<p>É Admin? " . ($user->authorise('core.admin') ? 'SIM' : 'NÃO') . "</p>";
        
    } catch (Exception $e) {
        echo "<p style='color:red'>✗ Erro: " . $e->getMessage() . "</p>";
    }
    
} elseif (class_exists('JFactory')) {
    echo "<p>✓ Detectado Joomla 3.x</p>";
    
    try {
        $config = JFactory::getConfig();
        echo "<p>✓ Config carregada</p>";
        
        $user = JFactory::getUser();
        echo "<p>User ID: " . $user->id . "</p>";
        echo "<p>Username: " . ($user->username ?: 'Guest') . "</p>";
        echo "<p>É Admin? " . ($user->authorise('core.admin') ? 'SIM' : 'NÃO') . "</p>";
        
    } catch (Exception $e) {
        echo "<p style='color:red'>✗ Erro: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>✗ Nenhuma versão do Joomla detectada</p>";
}
?>