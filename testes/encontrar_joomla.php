<?php
/**
 * Script para encontrar a raiz do Joomla
 * Coloque em várias pastas e teste
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Encontrar Joomla - IFSP</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 30px auto; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #006633; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 15px 0; }
        code { background: #f0f0f0; padding: 3px 8px; border-radius: 3px; font-family: monospace; font-size: 14px; }
        .path { background: #006633; color: white; padding: 15px; border-radius: 5px; font-size: 16px; font-weight: bold; margin: 15px 0; word-break: break-all; }
    </style>
</head>
<body>
    <div class="box">
        <h1>🔍 Encontrar Raiz do Joomla - IFSP Guarulhos</h1>
        
        <?php
        echo "<h2>📂 Caminho deste arquivo:</h2>";
        echo "<div class='path'>" . __FILE__ . "</div>";
        
        echo "<h2>📁 Diretório deste arquivo:</h2>";
        echo "<div class='path'>" . __DIR__ . "</div>";
        
        $dirAtual = __DIR__;
        
        // Verificar se é a raiz do Joomla
        $arquivosJoomla = array(
            'configuration.php' => 'Configuração do Joomla',
            'index.php' => 'Arquivo principal',
            'administrator/index.php' => 'Painel administrativo',
            'includes/defines.php' => 'Defines do Joomla',
            'includes/framework.php' => 'Framework do Joomla'
        );
        
        $encontrados = 0;
        
        echo "<h2>✅ Verificando arquivos do Joomla:</h2>";
        echo "<ul style='list-style: none; padding: 0;'>";
        
        foreach ($arquivosJoomla as $arquivo => $descricao) {
            $caminho = $dirAtual . '/' . $arquivo;
            if (file_exists($caminho)) {
                echo "<li style='color: green; padding: 5px;'>✓ <strong>" . $arquivo . "</strong> - " . $descricao . "</li>";
                $encontrados++;
            } else {
                echo "<li style='color: #999; padding: 5px;'>✗ <strong>" . $arquivo . "</strong> - Não encontrado</li>";
            }
        }
        
        echo "</ul>";
        
        if ($encontrados >= 3) {
            echo "<div class='success'>";
            echo "<h3>🎉 SUCESSO! Esta É a Raiz do Joomla!</h3>";
            echo "<p>Faça upload dos arquivos PHP para ESTA PASTA:</p>";
            echo "<div class='path'>" . $dirAtual . "/</div>";
            echo "<p><strong>Arquivos a fazer upload:</strong></p>";
            echo "<ul>";
            echo "<li><code>salvar_agencia.php</code></li>";
            echo "<li><code>listar_agencias.php</code></li>";
            echo "<li><code>toggle_agencia.php</code></li>";
            echo "</ul>";
            echo "</div>";
            
            // Detectar BASE_URL
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'];
            $script = $_SERVER['SCRIPT_NAME'];
            $scriptDir = dirname($script);
            
            $baseURL = $protocol . $host . $scriptDir;
            if (substr($baseURL, -1) !== '/') {
                $baseURL .= '/';
            }
            
            echo "<div class='info'>";
            echo "<h3>🌐 BASE_URL para usar no HTML:</h3>";
            echo "<div class='path'>" . $baseURL . "</div>";
            echo "<p>Use esta URL no seu artigo HTML:</p>";
            echo "<pre style='background: #f0f0f0; padding: 15px; border-radius: 5px; overflow-x: auto;'>";
            echo htmlspecialchars("<script>\nvar BASE_URL = '" . $baseURL . "';\n</script>");
            echo "</pre>";
            echo "</div>";
            
        } elseif ($encontrados > 0) {
            echo "<div class='error'>";
            echo "<h3>⚠️ Parcialmente Correto</h3>";
            echo "<p>Esta pasta tem alguns arquivos do Joomla, mas pode não ser a raiz.</p>";
            echo "<p>Tente colocar este arquivo em outras pastas.</p>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<h3>❌ Esta NÃO é a raiz do Joomla</h3>";
            echo "<p>Não foram encontrados arquivos do Joomla nesta pasta.</p>";
            echo "<p><strong>Tente fazer upload deste arquivo para:</strong></p>";
            echo "<ul>";
            echo "<li>A pasta raiz do servidor</li>";
            echo "<li>Pasta <code>public_html/</code></li>";
            echo "<li>Pasta <code>public_html/cexhom/</code></li>";
            echo "<li>Pasta <code>cexhom/</code></li>";
            echo "<li>Outras subpastas que você suspeitar</li>";
            echo "</ul>";
            echo "</div>";
        }
        
        // Listar pastas no diretório atual
        echo "<h2>📋 Pastas neste diretório:</h2>";
        echo "<ul>";
        $pastas = glob($dirAtual . '/*', GLOB_ONLYDIR);
        if (count($pastas) > 0) {
            foreach ($pastas as $pasta) {
                echo "<li>" . basename($pasta) . "/</li>";
            }
        } else {
            echo "<li>Nenhuma pasta encontrada</li>";
        }
        echo "</ul>";
        
        // Informações do servidor
        echo "<h2>ℹ️ Informações do Servidor:</h2>";
        echo "<ul style='list-style: none; padding: 0;'>";
        echo "<li><strong>DOCUMENT_ROOT:</strong> <code>" . $_SERVER['DOCUMENT_ROOT'] . "</code></li>";
        echo "<li><strong>SCRIPT_FILENAME:</strong> <code>" . $_SERVER['SCRIPT_FILENAME'] . "</code></li>";
        echo "<li><strong>SERVER_NAME:</strong> <code>" . $_SERVER['SERVER_NAME'] . "</code></li>";
        echo "</ul>";
        ?>
        
        <div class="info">
            <h3>📌 Instruções:</h3>
            <ol>
                <li>Faça upload deste arquivo (<code>encontrar_joomla.php</code>) para várias pastas diferentes</li>
                <li>Tente acessar em cada uma delas</li>
                <li>Quando aparecer "SUCESSO", você encontrou o local correto!</li>
                <li>Use aquela pasta para fazer upload dos seus arquivos PHP</li>
            </ol>
        </div>
    </div>
</body>
</html>