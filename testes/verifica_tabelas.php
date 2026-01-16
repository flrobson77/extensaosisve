<?php
/**
 * Verificação de Tabelas - Versão Standalone
 * Não inicializa a aplicação Joomla (evita erro "Failed to start application")
 * IFSP Campus Guarulhos
 */

// Desabilitar warnings
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação de Tabelas - IFSP</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 30px auto; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-top: 5px solid #006633; }
        h1 { color: #006633; margin: 0 0 20px 0; }
        h2 { color: #006633; margin: 30px 0 15px 0; padding-bottom: 10px; border-bottom: 2px solid #e0e0e0; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc3545; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ffc107; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #17a2b8; }
        code { background: #f0f0f0; padding: 3px 8px; border-radius: 3px; font-family: 'Courier New', monospace; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background: #006633; color: white; padding: 12px; text-align: left; font-weight: bold; }
        td { padding: 12px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f8f9fa; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; border: 1px solid #dee2e6; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="box">
        <h1>🔍 Verificação de Tabelas - Sistema de Vagas IFSP</h1>
        
        <?php
        try {
            // ==========================================
            // ETAPA 1: VERIFICAR LOCALIZAÇÃO
            // ==========================================
            
            echo "<h2>📁 Etapa 1: Verificando Localização dos Arquivos</h2>";
            
            $caminhoAtual = __DIR__;
            echo "<div class='info'><strong>Caminho deste script:</strong><br><code>" . $caminhoAtual . "</code></div>";
            
            // Verificar se está na raiz do Joomla
            $configFile = $caminhoAtual . '/configuration.php';
            
            if (!file_exists($configFile)) {
                throw new Exception("❌ Arquivo configuration.php não encontrado! Este script deve estar na RAIZ do Joomla (mesma pasta do index.php)");
            }
            
            echo "<div class='success'>✓ Arquivo configuration.php encontrado! Local correto.</div>";
            
            // ==========================================
            // ETAPA 2: LER CONFIGURAÇÃO DO BANCO
            // ==========================================
            
            echo "<h2>🗄️ Etapa 2: Lendo Configuração do Banco de Dados</h2>";
            
            // Carregar configuração do Joomla
            require_once $configFile;
            
            // Criar instância da configuração
            $config = new JConfig();
            
            $dbHost = $config->host;
            $dbUser = $config->user;
            $dbPass = $config->password;
            $dbName = $config->db;
            $dbPrefix = $config->dbprefix;
            $dbType = $config->dbtype;
            
            echo "<table>";
            echo "<tr><th>Configuração</th><th>Valor</th></tr>";
            echo "<tr><td>Tipo de Banco</td><td><code>" . $dbType . "</code></td></tr>";
            echo "<tr><td>Host</td><td><code>" . $dbHost . "</code></td></tr>";
            echo "<tr><td>Nome do Banco</td><td><code>" . $dbName . "</code></td></tr>";
            echo "<tr><td>Usuário</td><td><code>" . $dbUser . "</code></td></tr>";
            echo "<tr><td>Prefixo das Tabelas</td><td><code>" . $dbPrefix . "</code></td></tr>";
            echo "</table>";
            
            // ==========================================
            // ETAPA 3: CONECTAR AO BANCO
            // ==========================================
            
            echo "<h2>🔌 Etapa 3: Conectando ao Banco de Dados</h2>";
            
            // Conectar ao MySQL
            $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
            
            if ($mysqli->connect_error) {
                throw new Exception("❌ Erro ao conectar: " . $mysqli->connect_error);
            }
            
            $mysqli->set_charset("utf8mb4");
            
            echo "<div class='success'>✓ Conexão com banco de dados estabelecida com sucesso!</div>";
            
            // Obter versão do MySQL
            $versaoMySQL = $mysqli->server_info;
            echo "<div class='info'><strong>Versão MySQL/MariaDB:</strong> " . $versaoMySQL . "</div>";
            
            // ==========================================
            // ETAPA 4: VERIFICAR TABELAS DO SISTEMA
            // ==========================================
            
            echo "<h2>📊 Etapa 4: Verificando Tabelas do Sistema</h2>";
            
            $tabelasVerificar = array(
                $dbPrefix . 'agencias_estagio' => 'Tabela de Agências de Estágio',
                $dbPrefix . 'vagas_estagio' => 'Tabela de Vagas de Estágio'
            );
            
            $tabelasOk = 0;
            
            echo "<table>";
            echo "<tr><th>Tabela</th><th>Status</th><th>Registros</th><th>Detalhes</th></tr>";
            
            foreach ($tabelasVerificar as $nomeTabela => $descricao) {
                $query = "SELECT COUNT(*) as total FROM `" . $mysqli->real_escape_string($nomeTabela) . "`";
                $resultado = $mysqli->query($query);
                
                if ($resultado) {
                    $row = $resultado->fetch_assoc();
                    $total = $row['total'];
                    
                    echo "<tr>";
                    echo "<td><strong>" . $descricao . "</strong><br><small><code>" . $nomeTabela . "</code></small></td>";
                    echo "<td><span class='badge badge-success'>✓ Existe</span></td>";
                    echo "<td><strong>" . $total . "</strong> registro(s)</td>";
                    echo "<td>";
                    
                    if ($total == 0) {
                        echo "<span style='color: #856404;'>⚠️ Vazia</span>";
                    } else {
                        echo "<span style='color: #155724;'>✓ Com dados</span>";
                    }
                    
                    echo "</td>";
                    echo "</tr>";
                    
                    $tabelasOk++;
                    
                } else {
                    echo "<tr>";
                    echo "<td><strong>" . $descricao . "</strong><br><small><code>" . $nomeTabela . "</code></small></td>";
                    echo "<td><span class='badge badge-error'>✗ NÃO existe</span></td>";
                    echo "<td>-</td>";
                    echo "<td><span style='color: #721c24;'>Precisa criar</span></td>";
                    echo "</tr>";
                }
            }
            
            echo "</table>";
            
            // ==========================================
            // ETAPA 5: SE TABELAS NÃO EXISTEM
            // ==========================================
            
            if ($tabelasOk < 2) {
                echo "<div class='error'>";
                echo "<h3>❌ Tabelas Não Encontradas!</h3>";
                echo "<p>Execute o script SQL de instalação:</p>";
                echo "<ol>";
                echo "<li>Acesse o <strong>PhpMyAdmin</strong></li>";
                echo "<li>Selecione o banco: <code>" . $dbName . "</code></li>";
                echo "<li>Vá em <strong>SQL</strong></li>";
                echo "<li>Cole o script do arquivo <code>database/install.sql</code></li>";
                echo "<li><strong>IMPORTANTE:</strong> Substitua <code>#__</code> por <code>" . $dbPrefix . "</code></li>";
                echo "<li>Clique em <strong>Executar</strong></li>";
                echo "</ol>";
                echo "</div>";
                
            } else {
                echo "<div class='success'>";
                echo "<h3>✓ Tabelas Criadas Corretamente!</h3>";
                
                // Mostrar dados das agências
                $query = "SELECT * FROM `" . $dbPrefix . "agencias_estagio` ORDER BY ordering ASC";
                $resultado = $mysqli->query($query);
                
                if ($resultado && $resultado->num_rows > 0) {
                    echo "<h4>📋 Agências Cadastradas:</h4>";
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Nome</th><th>Sigla</th><th>Status</th></tr>";
                    
                    while ($agencia = $resultado->fetch_assoc()) {
                        $statusTexto = $agencia['status'] == 1 ? 'Ativo' : 'Inativo';
                        $statusCor = $agencia['status'] == 1 ? '#28a745' : '#6c757d';
                        
                        echo "<tr>";
                        echo "<td>" . $agencia['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($agencia['nome']) . "</td>";
                        echo "<td><strong>" . htmlspecialchars($agencia['sigla']) . "</strong></td>";
                        echo "<td><span style='color: " . $statusCor . ";'>●</span> " . $statusTexto . "</td>";
                        echo "</tr>";
                    }
                    
                    echo "</table>";
                }
                echo "</div>";
            }
            
            // ==========================================
            // ETAPA 6: VERIFICAR ARQUIVOS PHP
            // ==========================================
            
            echo "<h2>📄 Etapa 6: Verificando Arquivos PHP</h2>";
            
            $arquivosPHP = array(
                'salvar_agencia.php' => 'Salvar Agência',
                'listar_agencias.php' => 'Listar Agências',
                'toggle_agencia.php' => 'Toggle Status'
            );
            
            $phpOk = 0;
            
            echo "<table>";
            echo "<tr><th>Arquivo</th><th>Status</th><th>Tamanho</th></tr>";
            
            foreach ($arquivosPHP as $arquivo => $descricao) {
                $caminho = $caminhoAtual . '/' . $arquivo;
                $existe = file_exists($caminho);
                
                echo "<tr>";
                echo "<td><strong>" . $descricao . "</strong><br><small><code>" . $arquivo . "</code></small></td>";
                
                if ($existe) {
                    $tamanho = filesize($caminho);
                    echo "<td><span class='badge badge-success'>✓ Encontrado</span></td>";
                    echo "<td>" . number_format($tamanho / 1024, 2) . " KB</td>";
                    $phpOk++;
                } else {
                    echo "<td><span class='badge badge-error'>✗ Faltando</span></td>";
                    echo "<td>-</td>";
                }
                
                echo "</tr>";
            }
            
            echo "</table>";
            
            if ($phpOk < 3) {
                echo "<div class='warning'>";
                echo "<p>⚠️ Faça upload dos arquivos PHP que estão faltando para:</p>";
                echo "<p><code>" . $caminhoAtual . "/</code></p>";
                echo "</div>";
            }
            
            // ==========================================
            // ETAPA 7: VERIFICAR PASTA MEDIA
            // ==========================================
            
            echo "<h2>📁 Etapa 7: Verificando Pasta Media</h2>";
            
            $pastaMedia = $caminhoAtual . '/media/com_vagasestagio';
            
            if (is_dir($pastaMedia)) {
                echo "<div class='success'>✓ Pasta <code>/media/com_vagasestagio/</code> existe!</div>";
                
                $arquivosMedia = array(
                    'css/ifsp-theme.css',
                    'css/ifsp-oficial.css',
                    'css/admin-agencias.css',
                    'js/admin-agencias.js'
                );
                
                echo "<ul>";
                foreach ($arquivosMedia as $arquivo) {
                    $existe = file_exists($pastaMedia . '/' . $arquivo);
                    $icone = $existe ? '✓' : '✗';
                    $cor = $existe ? '#28a745' : '#dc3545';
                    echo "<li style='color: " . $cor . ";'>" . $icone . " " . $arquivo . "</li>";
                }
                echo "</ul>";
                
            } else {
                echo "<div class='error'>❌ Pasta <code>/media/com_vagasestagio/</code> não existe!</div>";
            }
            
            // ==========================================
            // RESUMO FINAL
            // ==========================================
            
            echo "<h2>✅ Resumo Final</h2>";
            
            $tudoOk = ($tabelasOk >= 2 && $phpOk >= 3);
            
            if ($tudoOk) {
                echo "<div class='success'>";
                echo "<h3>🎉 SISTEMA PRONTO!</h3>";
                echo "<p><strong>Próximos passos:</strong></p>";
                echo "<ol>";
                echo "<li>Acesse: <code>" . $dbPrefix . "</code> (Extensão → Estágio → Gerenciar Agências)</li>";
                echo "<li>Teste o cadastro de agências</li>";
                echo "<li>Se funcionar, prossiga com cadastro de vagas</li>";
                echo "</ol>";
                echo "<p><strong>URLs para testar:</strong></p>";
                echo "<ul>";
                echo "<li><a href='listar_agencias.php' target='_blank'>listar_agencias.php</a> (deve mostrar JSON)</li>";
                echo "</ul>";
                echo "</div>";
            } else {
                echo "<div class='warning'>";
                echo "<h3>⚠️ Instalação Incompleta</h3>";
                echo "<p>Corrija os itens marcados acima antes de prosseguir.</p>";
                echo "</div>";
            }
            
            // ==========================================
            // INFORMAÇÕES TÉCNICAS
            // ==========================================
            
            echo "<h2>ℹ️ Informações Técnicas</h2>";
            
            echo "<table>";
            echo "<tr><th>Item</th><th>Valor</th></tr>";
            echo "<tr><td>Versão PHP</td><td>" . PHP_VERSION . "</td></tr>";
            echo "<tr><td>Versão MySQL</td><td>" . $versaoMySQL . "</td></tr>";
            echo "<tr><td>Caminho Joomla</td><td><code>" . $caminhoAtual . "</code></td></tr>";
            echo "<tr><td>Prefixo Tabelas</td><td><code>" . $dbPrefix . "</code></td></tr>";
            echo "</table>";
            
            // Fechar conexão
            $mysqli->close();
            
        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "<h3>❌ ERRO</h3>";
            echo "<p><strong>Mensagem:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
        }
        ?>
        
    </div>
</body>
</html>
