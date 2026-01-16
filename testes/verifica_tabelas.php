<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('_JEXEC', 1);
define('JPATH_BASE', dirname(__FILE__));
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

$app = JFactory::getApplication('site');
$db = JFactory::getDbo();

echo "<h1>Verificação de Tabelas</h1>";

// Listar todas as tabelas
$tables = $db->getTableList();
$prefix = $db->getPrefix();

echo "<h2>Prefixo do banco: <strong>" . $prefix . "</strong></h2>";

echo "<h3>Procurando tabelas de agências e vagas:</h3>";
echo "<ul>";

$tabelaAgencias = false;
$tabelaVagas = false;

foreach ($tables as $table) {
    if (strpos($table, 'agencia') !== false || strpos($table, 'estagio') !== false) {
        echo "<li style='color: green;'><strong>✓ " . $table . "</strong></li>";
        
        if (strpos($table, 'agencias_estagio') !== false) {
            $tabelaAgencias = $table;
        }
        if (strpos($table, 'vagas_estagio') !== false) {
            $tabelaVagas = $table;
        }
    }
}
echo "</ul>";

if (!$tabelaAgencias) {
    echo "<p style='color: red; font-weight: bold;'>✗ ERRO: Tabela de agências NÃO foi encontrada!</p>";
    echo "<p>Execute o script SQL de instalação no PhpMyAdmin.</p>";
} else {
    echo "<h3>Testando consulta na tabela de agências:</h3>";
    
    $query = $db->getQuery(true);
    $query->select('*')
        ->from($db->quoteName($tabelaAgencias));
    
    $db->setQuery($query);
    
    try {
        $agencias = $db->loadObjectList();
        
        echo "<p style='color: green;'>✓ Query executada com sucesso!</p>";
        echo "<p><strong>Total de agências:</strong> " . count($agencias) . "</p>";
        
        if (count($agencias) > 0) {
            echo "<h4>Agências cadastradas:</h4>";
            echo "<table border='1' cellpadding='10'>";
            echo "<tr><th>ID</th><th>Nome</th><th>Sigla</th><th>Status</th></tr>";
            
            foreach ($agencias as $agencia) {
                echo "<tr>";
                echo "<td>" . $agencia->id . "</td>";
                echo "<td>" . $agencia->nome . "</td>";
                echo "<td>" . $agencia->sigla . "</td>";
                echo "<td>" . ($agencia->status == 1 ? 'Ativo' : 'Inativo') . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ ERRO: " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='listar_agencias.php'>Testar listar_agencias.php</a></p>";