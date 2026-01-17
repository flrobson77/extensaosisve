<?php
/**
 * Verificação Completa do Sistema de Vagas
 * IFSP Campus Guarulhos
 * Versão: 1.0
 */

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação Sistema de Vagas - IFSP</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            max-width: 1200px; 
            margin: 30px auto; 
            padding: 20px; 
            background: #f5f7f9;
        }
        .box { 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            border-top: 5px solid #006633; 
            margin-bottom: 2rem;
        }
        h1 { 
            color: #006633; 
            margin: 0 0 10px 0;
            font-size: 2rem;
        }
        h2 { 
            color: #006633; 
            margin: 40px 0 15px 0; 
            padding-bottom: 10px; 
            border-bottom: 2px solid #e0e0e0;
            font-size: 1.5rem;
        }
        h3 {
            color: #004d26;
            margin: 25px 0 10px 0;
            font-size: 1.25rem;
        }
        .success { 
            background: #d4edda; 
            color: #155724; 
            padding: 15px; 
            border-radius: 6px; 
            margin: 10px 0; 
            border-left: 4px solid #28a745;
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 15px; 
            border-radius: 6px; 
            margin: 10px 0; 
            border-left: 4px solid #dc3545;
        }
        .warning { 
            background: #fff3cd; 
            color: #856404; 
            padding: 15px; 
            border-radius: 6px; 
            margin: 10px 0; 
            border-left: 4px solid #ffc107;
        }
        .info { 
            background: #d1ecf1; 
            color: #0c5460; 
            padding: 15px; 
            border-radius: 6px; 
            margin: 10px 0; 
            border-left: 4px solid #17a2b8;
        }
        code { 
            background: #f8f9fa; 
            padding: 3px 8px; 
            border-radius: 4px; 
            font-family: 'Courier New', monospace; 
            font-size: 0.9em;
            color: #d63384;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 15px 0;
            background: white;
        }
        th { 
            background: #006633; 
            color: white; 
            padding: 12px; 
            text-align: left; 
            font-weight: 600;
        }
        td { 
            padding: 12px; 
            border-bottom: 1px solid #e0e0e0;
        }
        tr:hover { 
            background: #f8f9fa;
        }
        pre { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 6px; 
            overflow-x: auto; 
            border: 1px solid #dee2e6;
            font-size: 0.875rem;
        }
        .badge { 
            display: inline-block; 
            padding: 4px 10px; 
            border-radius: 12px; 
            font-size: 0.75rem; 
            font-weight: 600;
        }
        .badge-success { 
            background: #d4edda; 
            color: #155724;
        }
        .badge-error { 
            background: #f8d7da; 
            color: #721c24;
        }
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        .section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .check-item {
            display: flex;
            align-items: center;
            padding: 10px;
            margin: 5px 0;
            background: white;
            border-radius: 6px;
            border-left: 3px solid #e0e0e0;
        }
        .check-item.ok {
            border-left-color: #28a745;
        }
        .check-item.fail {
            border-left-color: #dc3545;
        }
        .check-icon {
            font-size: 1.5rem;
            margin-right: 10px;
            min-width: 30px;
        }
        .progress-bar {
            width: 100%;
            height: 30px;
            background: #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            margin: 20px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #006633, #00864d);
            transition: width 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        .thumbnail {
            max-width: 120px;
            max-height: 80px;
            border-radius: 4px;
            border: 2px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>🔍 Verificação Completa - Sistema de Vagas</h1>
        <p style="color: #666; font-size: 1.125rem;">IFSP Campus Guarulhos - Diagnóstico Técnico</p>
        
        <?php
        $totalTestes = 0;
        $testesOk = 0;
        $errosCriticos = array();
        $avisos = array();
        
        try {
            // ==========================================
            // ETAPA 1: LOCALIZAÇÃO
            // ==========================================
            
            echo "<h2>📁 Etapa 1: Verificando Localização dos Arquivos</h2>";
            
            $caminhoAtual = __DIR__;
            echo "<div class='info'><strong>Caminho deste script:</strong><br><code>" . $caminhoAtual . "</code></div>";
            
            $configFile = $caminhoAtual . '/configuration.php';
            
            $totalTestes++;
            if (!file_exists($configFile)) {
                $errosCriticos[] = "Arquivo configuration.php não encontrado! Este script deve estar na RAIZ do Joomla.";
                throw new Exception("❌ Arquivo configuration.php não encontrado!");
            }
            $testesOk++;
            
            echo "<div class='success'>✓ Arquivo configuration.php encontrado! Local correto.</div>";
            
            // ==========================================
            // ETAPA 2: CONFIGURAÇÃO DO BANCO
            // ==========================================
            
            echo "<h2>🗄️ Etapa 2: Lendo Configuração do Banco de Dados</h2>";
            
            require_once $configFile;
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
            // ETAPA 3: CONEXÃO COM BANCO
            // ==========================================
            
            echo "<h2>🔌 Etapa 3: Conectando ao Banco de Dados</h2>";
            
            $totalTestes++;
            $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
            
            if ($mysqli->connect_error) {
                $errosCriticos[] = "Erro ao conectar: " . $mysqli->connect_error;
                throw new Exception("❌ Erro ao conectar: " . $mysqli->connect_error);
            }
            $testesOk++;
            
            $mysqli->set_charset("utf8mb4");
            
            echo "<div class='success'>✓ Conexão com banco de dados estabelecida com sucesso!</div>";
            
            $versaoMySQL = $mysqli->server_info;
            echo "<div class='info'><strong>Versão MySQL/MariaDB:</strong> " . $versaoMySQL . "</div>";
            
            // ==========================================
            // ETAPA 4: VERIFICAR TABELAS
            // ==========================================
            
            echo "<h2>📊 Etapa 4: Verificando Tabelas do Sistema</h2>";
            
            $tabelasVerificar = array(
                $dbPrefix . 'agencias_estagio' => array(
                    'descricao' => 'Tabela de Agências',
                    'critica' => true
                ),
                $dbPrefix . 'vagas_estagio' => array(
                    'descricao' => 'Tabela de Vagas',
                    'critica' => true
                )
            );
            
            echo "<table>";
            echo "<tr><th>Tabela</th><th>Status</th><th>Registros</th><th>Estrutura</th></tr>";
            
            foreach ($tabelasVerificar as $nomeTabela => $info) {
                $totalTestes++;
                
                // Verificar se existe
                $query = "SELECT COUNT(*) as total FROM `" . $mysqli->real_escape_string($nomeTabela) . "`";
                $resultado = $mysqli->query($query);
                
                if ($resultado) {
                    $testesOk++;
                    $row = $resultado->fetch_assoc();
                    $total = $row['total'];
                    
                    // Verificar estrutura
                    $queryColunas = "SHOW COLUMNS FROM `" . $mysqli->real_escape_string($nomeTabela) . "`";
                    $resultColunas = $mysqli->query($queryColunas);
                    $numColunas = $resultColunas->num_rows;
                    
                    echo "<tr>";
                    echo "<td><strong>" . $info['descricao'] . "</strong><br><small><code>" . $nomeTabela . "</code></small></td>";
                    echo "<td><span class='badge badge-success'>✓ Existe</span></td>";
                    echo "<td><strong>" . $total . "</strong> registro(s)</td>";
                    echo "<td>" . $numColunas . " colunas</td>";
                    echo "</tr>";
                    
                    if ($total == 0 && strpos($nomeTabela, 'agencias') !== false) {
                        $avisos[] = "Tabela de agências está vazia. Cadastre ao menos uma agência.";
                    }
                    
                } else {
                    if ($info['critica']) {
                        $errosCriticos[] = "Tabela crítica não existe: " . $nomeTabela;
                    }
                    
                    echo "<tr>";
                    echo "<td><strong>" . $info['descricao'] . "</strong><br><small><code>" . $nomeTabela . "</code></small></td>";
                    echo "<td><span class='badge badge-error'>✗ NÃO existe</span></td>";
                    echo "<td>-</td>";
                    echo "<td>-</td>";
                    echo "</tr>";
                }
            }
            
            echo "</table>";
            
            // ==========================================
            // ETAPA 5: VERIFICAR ESTRUTURA DA TABELA VAGAS
            // ==========================================
            
            echo "<h2>🔍 Etapa 5: Verificando Estrutura da Tabela de Vagas</h2>";
            
            $colunasEsperadas = array(
                'id', 'agencia_id', 'codigo_vaga', 'empresa', 'titulo', 'descricao',
                'requisitos', 'atividades', 'curso', 'nivel_escolar', 'area_profissional',
                'localidade', 'horario', 'bolsa_valor', 'bolsa_beneficios', 'imagem',
                'arquivo_pdf', 'url_vaga', 'email_contato', 'telefone_contato',
                'data_inicio', 'data_fim', 'status', 'destaque', 'visualizacoes',
                'ordering', 'created', 'created_by', 'modified', 'modified_by'
            );
            
            $queryColunas = "SHOW COLUMNS FROM `" . $dbPrefix . "vagas_estagio`";
            $resultColunas = $mysqli->query($queryColunas);
            
            $colunasExistentes = array();
            if ($resultColunas) {
                while ($coluna = $resultColunas->fetch_assoc()) {
                    $colunasExistentes[] = $coluna['Field'];
                }
            }
            
            echo "<div class='section'>";
            echo "<h3>Colunas Encontradas vs Esperadas:</h3>";
            
            $colunasFaltando = array_diff($colunasEsperadas, $colunasExistentes);
            $colunasExtras = array_diff($colunasExistentes, $colunasEsperadas);
            
            echo "<table>";
            echo "<tr><th>Coluna</th><th>Status</th></tr>";
            
            foreach ($colunasEsperadas as $coluna) {
                $totalTestes++;
                $existe = in_array($coluna, $colunasExistentes);
                
                if ($existe) {
                    $testesOk++;
                    echo "<tr>";
                    echo "<td><code>" . $coluna . "</code></td>";
                    echo "<td><span class='badge badge-success'>✓ OK</span></td>";
                    echo "</tr>";
                } else {
                    echo "<tr>";
                    echo "<td><code>" . $coluna . "</code></td>";
                    echo "<td><span class='badge badge-error'>✗ FALTA</span></td>";
                    echo "</tr>";
                    
                    $avisos[] = "Coluna faltando: " . $coluna;
                }
            }
            
            echo "</table>";
            
            if (count($colunasFaltando) > 0) {
                echo "<div class='warning'>";
                echo "<strong>⚠️ Colunas faltando:</strong> " . implode(', ', $colunasFaltando);
                echo "<p>Execute o SQL completo de criação da tabela!</p>";
                echo "</div>";
            }
            
            echo "</div>";
            
            // ==========================================
            // ETAPA 6: VERIFICAR ARQUIVOS PHP
            // ==========================================
            
            echo "<h2>📄 Etapa 6: Verificando Arquivos PHP do Sistema</h2>";
            
            $arquivosPHP = array(
                'salvar_vaga.php' => array('desc' => 'Salvar Vaga', 'critico' => true),
                'listar_vagas.php' => array('desc' => 'Listar Vagas (Admin)', 'critico' => true),
                'listar_vagas_publico.php' => array('desc' => 'Listar Vagas (Público)', 'critico' => true),
                'toggle_vaga.php' => array('desc' => 'Ativar/Desativar Vaga', 'critico' => true),
                'salvar_agencia.php' => array('desc' => 'Salvar Agência', 'critico' => false),
                'listar_agencias.php' => array('desc' => 'Listar Agências', 'critico' => true),
                'toggle_agencia.php' => array('desc' => 'Toggle Agência', 'critico' => false)
            );
            
            echo "<table>";
            echo "<tr><th>Arquivo</th><th>Status</th><th>Tamanho</th><th>Permissões</th></tr>";
            
            foreach ($arquivosPHP as $arquivo => $info) {
                $totalTestes++;
                $caminho = $caminhoAtual . '/' . $arquivo;
                $existe = file_exists($caminho);
                
                echo "<tr>";
                echo "<td><strong>" . $info['desc'] . "</strong><br><small><code>" . $arquivo . "</code></small></td>";
                
                if ($existe) {
                    $testesOk++;
                    $tamanho = filesize($caminho);
                    $perms = substr(sprintf('%o', fileperms($caminho)), -4);
                    
                    echo "<td><span class='badge badge-success'>✓ Encontrado</span></td>";
                    echo "<td>" . number_format($tamanho / 1024, 2) . " KB</td>";
                    echo "<td><code>" . $perms . "</code></td>";
                } else {
                    if ($info['critico']) {
                        $errosCriticos[] = "Arquivo crítico faltando: " . $arquivo;
                    }
                    
                    echo "<td><span class='badge badge-error'>✗ FALTANDO</span></td>";
                    echo "<td>-</td>";
                    echo "<td>-</td>";
                }
                
                echo "</tr>";
            }
            
            echo "</table>";
            
            // ==========================================
            // ETAPA 7: VERIFICAR PASTA MEDIA
            // ==========================================
            
            echo "<h2>📁 Etapa 7: Verificando Pasta Media e Assets</h2>";
            
            $caminhoMedia = $caminhoAtual . '/media/com_vagasestagio';
            
            echo "<div class='section'>";
            
            $totalTestes++;
            if (is_dir($caminhoMedia)) {
                $testesOk++;
                echo "<div class='success'>✓ Pasta <code>/media/com_vagasestagio/</code> existe!</div>";
                
                $arquivosMedia = array(
                    'css/ifsp-theme.css' => 'CSS Tema IFSP',
                    'css/ifsp-oficial.css' => 'CSS Oficial IFSP',
                    'css/admin-agencias.css' => 'CSS Admin Agências',
                    'css/admin-vagas.css' => 'CSS Admin Vagas',
                    'js/admin-agencias.js' => 'JavaScript Admin Agências',
                    'js/admin-vagas.js' => 'JavaScript Admin Vagas'
                );
                
                echo "<table>";
                echo "<tr><th>Arquivo</th><th>Status</th><th>Tamanho</th></tr>";
                
                foreach ($arquivosMedia as $arquivo => $descricao) {
                    $totalTestes++;
                    $caminhoCompleto = $caminhoMedia . '/' . $arquivo;
                    $existe = file_exists($caminhoCompleto);
                    
                    echo "<tr>";
                    echo "<td>" . $descricao . "<br><small><code>" . $arquivo . "</code></small></td>";
                    
                    if ($existe) {
                        $testesOk++;
                        $tamanho = filesize($caminhoCompleto);
                        echo "<td><span class='badge badge-success'>✓ Existe</span></td>";
                        echo "<td>" . number_format($tamanho / 1024, 2) . " KB</td>";
                    } else {
                        echo "<td><span class='badge badge-warning'>⚠️ Faltando</span></td>";
                        echo "<td>-</td>";
                        
                        if (strpos($arquivo, 'vagas') !== false) {
                            $avisos[] = "Arquivo faltando: /media/com_vagasestagio/" . $arquivo;
                        }
                    }
                    
                    echo "</tr>";
                }
                
                echo "</table>";
                
            } else {
                $errosCriticos[] = "Pasta /media/com_vagasestagio/ não existe!";
                echo "<div class='error'>❌ Pasta <code>/media/com_vagasestagio/</code> NÃO existe!</div>";
            }
            
            echo "</div>";
            
            // ==========================================
            // ETAPA 8: VERIFICAR PASTA DE UPLOADS
            // ==========================================
            
            echo "<h2>📤 Etapa 8: Verificando Pastas de Upload</h2>";
            
            $pastasUpload = array(
                'images/vagas' => 'Pasta de imagens das vagas',
                'images/vagas/pdf' => 'Pasta de PDFs das vagas'
            );
            
            echo "<table>";
            echo "<tr><th>Pasta</th><th>Status</th><th>Permissões</th><th>Arquivos</th></tr>";
            
            foreach ($pastasUpload as $pasta => $descricao) {
                $totalTestes++;
                $caminhoPasta = $caminhoAtual . '/' . $pasta;
                $existe = is_dir($caminhoPasta);
                
                echo "<tr>";
                echo "<td><strong>" . $descricao . "</strong><br><small><code>/" . $pasta . "/</code></small></td>";
                
                if ($existe) {
                    $testesOk++;
                    $perms = substr(sprintf('%o', fileperms($caminhoPasta)), -4);
                    $arquivos = count(glob($caminhoPasta . '/*'));
                    
                    $permOk = ($perms == '0755' || $perms == '0775' || $perms == '0777');
                    
                    echo "<td><span class='badge badge-success'>✓ Existe</span></td>";
                    echo "<td><code>" . $perms . "</code> " . ($permOk ? '✓' : '⚠️') . "</td>";
                    echo "<td>" . $arquivos . " arquivo(s)</td>";
                    
                    if (!$permOk) {
                        $avisos[] = "Permissões da pasta " . $pasta . " podem impedir uploads (atual: " . $perms . ", recomendado: 755)";
                    }
                } else {
                    echo "<td><span class='badge badge-error'>✗ NÃO existe</span></td>";
                    echo "<td>-</td>";
                    echo "<td>-</td>";
                    
                    $errosCriticos[] = "Pasta de upload não existe: " . $pasta;
                }
                
                echo "</tr>";
            }
            
            echo "</table>";
            
            // ==========================================
            // ETAPA 9: TESTAR CONEXÕES (SIMULAÇÃO)
            // ==========================================
            
            echo "<h2>🔗 Etapa 9: Testando Endpoints (Simulação)</h2>";
            
            $baseUrl = 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/';
            
            echo "<div class='info'><strong>Base URL detectada:</strong><br><code>" . $baseUrl . "</code></div>";
            
            $endpoints = array(
                'listar_agencias.php' => 'Listar Agências',
                'listar_vagas.php' => 'Listar Vagas (Admin)',
                'listar_vagas_publico.php' => 'Listar Vagas (Público)'
            );
            
            echo "<table>";
            echo "<tr><th>Endpoint</th><th>URL</th><th>Status</th></tr>";
            
            foreach ($endpoints as $arquivo => $descricao) {
                $url = $baseUrl . $arquivo;
                
                echo "<tr>";
                echo "<td><strong>" . $descricao . "</strong></td>";
                echo "<td><a href='" . $url . "' target='_blank'><code>" . $arquivo . "</code></a></td>";
                
                if (file_exists($caminhoAtual . '/' . $arquivo)) {
                    echo "<td><span class='badge badge-success'>✓ Disponível</span></td>";
                } else {
                    echo "<td><span class='badge badge-error'>✗ Arquivo não existe</span></td>";
                }
                
                echo "</tr>";
            }
            
            echo "</table>";
            
            // ==========================================
            // ETAPA 10: LISTAR VAGAS EXISTENTES
            // ==========================================
            
            echo "<h2>📋 Etapa 10: Vagas Cadastradas</h2>";
            
            $sqlVagas = "SELECT v.*, a.nome as agencia_nome, a.sigla as agencia_sigla 
                        FROM `" . $dbPrefix . "vagas_estagio` v
                        LEFT JOIN `" . $dbPrefix . "agencias_estagio` a ON v.agencia_id = a.id
                        ORDER BY v.created DESC
                        LIMIT 10";
            
            $resultVagas = $mysqli->query($sqlVagas);
            
            if ($resultVagas && $resultVagas->num_rows > 0) {
                echo "<div class='success'>✓ Encontradas <strong>" . $resultVagas->num_rows . "</strong> vaga(s) cadastrada(s)</div>";
                
                echo "<table>";
                echo "<tr><th>ID</th><th>Imagem</th><th>Título</th><th>Empresa</th><th>Agência</th><th>Período</th><th>Status</th></tr>";
                
                while ($vaga = $resultVagas->fetch_assoc()) {
                    $statusTexto = $vaga['status'] == 1 ? 'Ativo' : 'Inativo';
                    $statusCor = $vaga['status'] == 1 ? 'success' : 'error';
                    
                    $hoje = new DateTime();
                    $dataFim = new DateTime($vaga['data_fim']);
                    $expirada = $dataFim < $hoje;
                    
                    echo "<tr>";
                    echo "<td>" . $vaga['id'] . "</td>";
                    echo "<td>";
                    if (file_exists($caminhoAtual . '/' . $vaga['imagem'])) {
                        echo "<img src='" . $baseUrl . $vaga['imagem'] . "' alt='Vaga' class='thumbnail'>";
                    } else {
                        echo "❌ Arquivo não encontrado";
                    }
                    echo "</td>";
                    echo "<td><strong>" . htmlspecialchars($vaga['titulo']) . "</strong></td>";
                    echo "<td>" . htmlspecialchars($vaga['empresa']) . "</td>";
                    echo "<td>" . htmlspecialchars($vaga['agencia_sigla']) . "</td>";
                    echo "<td><small>" . date('d/m/Y', strtotime($vaga['data_inicio'])) . "<br>até<br>" . date('d/m/Y', strtotime($vaga['data_fim'])) . "</small></td>";
                    echo "<td>";
                    echo "<span class='badge badge-" . $statusCor . "'>" . $statusTexto . "</span>";
                    if ($expirada) {
                        echo "<br><span class='badge badge-warning'>⏰ Expirada</span>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
                
            } else {
                echo "<div class='warning'>⚠️ Nenhuma vaga cadastrada ainda. Use o formulário de cadastro para adicionar a primeira vaga!</div>";
            }
            
            // Fechar conexão
            $mysqli->close();
            
            // ==========================================
            // RESUMO FINAL
            // ==========================================
            
            echo "<h2>✅ Resumo Final da Verificação</h2>";
            
            $percentual = ($totalTestes > 0) ? round(($testesOk / $totalTestes) * 100) : 0;
            
            echo "<div class='progress-bar'>";
            echo "<div class='progress-fill' style='width: " . $percentual . "%;'>" . $percentual . "%</div>";
            echo "</div>";
            
            echo "<div class='section'>";
            echo "<h3>📊 Estatísticas:</h3>";
            echo "<ul style='font-size: 1.125rem; line-height: 2;'>";
            echo "<li><strong>Total de Testes:</strong> " . $totalTestes . "</li>";
            echo "<li><strong>Testes OK:</strong> <span style='color: #28a745;'>" . $testesOk . " ✓</span></li>";
            echo "<li><strong>Testes Falharam:</strong> <span style='color: #dc3545;'>" . ($totalTestes - $testesOk) . " ✗</span></li>";
            echo "<li><strong>Percentual de Sucesso:</strong> <strong style='color: #006633;'>" . $percentual . "%</strong></li>";
            echo "</ul>";
            echo "</div>";
            
            // Erros Críticos
            if (count($errosCriticos) > 0) {
                echo "<div class='error'>";
                echo "<h3>❌ Erros Críticos Encontrados:</h3>";
                echo "<ol>";
                foreach ($errosCriticos as $erro) {
                    echo "<li>" . htmlspecialchars($erro) . "</li>";
                }
                echo "</ol>";
                echo "</div>";
            }
            
            // Avisos
            if (count($avisos) > 0) {
                echo "<div class='warning'>";
                echo "<h3>⚠️ Avisos:</h3>";
                echo "<ol>";
                foreach ($avisos as $aviso) {
                    echo "<li>" . htmlspecialchars($aviso) . "</li>";
                }
                echo "</ol>";
                echo "</div>";
            }
            
            // Veredicto Final
            if ($percentual >= 90) {
                echo "<div class='success'>";
                echo "<h3>🎉 SISTEMA PRONTO PARA USO!</h3>";
                echo "<p style='font-size: 1.125rem;'>Seu sistema de vagas está corretamente instalado e configurado!</p>";
                echo "<p><strong>Próximos passos:</strong></p>";
                echo "<ol>";
                echo "<li>Acesse a página de gerenciamento de vagas</li>";
                echo "<li>Cadastre a primeira vaga de teste</li>";
                echo "<li>Verifique se tudo funciona corretamente</li>";
                echo "<li>Depois crie o carrossel público</li>";
                echo "</ol>";
                echo "</div>";
            } elseif ($percentual >= 70) {
                echo "<div class='warning'>";
                echo "<h3>⚠️ Sistema Parcialmente Instalado</h3>";
                echo "<p>Alguns itens precisam ser corrigidos antes do uso completo.</p>";
                echo "<p>Verifique os erros e avisos acima.</p>";
                echo "</div>";
            } else {
                echo "<div class='error'>";
                echo "<h3>❌ Sistema Incompleto</h3>";
                echo "<p>Muitos itens precisam ser corrigidos.</p>";
                echo "<p>Siga as instruções de instalação e corrija os erros críticos listados acima.</p>";
                echo "</div>";
            }
            
            // Info Técnica
            echo "<div class='info' style='margin-top: 2rem;'>";
            echo "<h3>ℹ️ Informações Técnicas</h3>";
            echo "<table>";
            echo "<tr><th>Item</th><th>Valor</th></tr>";
            echo "<tr><td>Versão PHP</td><td>" . PHP_VERSION . "</td></tr>";
            echo "<tr><td>Versão MySQL</td><td>" . $versaoMySQL . "</td></tr>";
            echo "<tr><td>Caminho Joomla</td><td><code>" . $caminhoAtual . "</code></td></tr>";
            echo "<tr><td>Prefixo Tabelas</td><td><code>" . $dbPrefix . "</code></td></tr>";
            echo "<tr><td>Base URL</td><td><code>" . $baseUrl . "</code></td></tr>";
            echo "<tr><td>Data/Hora</td><td>" . date('d/m/Y H:i:s') . "</td></tr>";
            echo "</table>";
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "<h3>❌ ERRO CRÍTICO</h3>";
            echo "<p><strong>Mensagem:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
        }
        ?>
        
    </div>
</body>
</html>
