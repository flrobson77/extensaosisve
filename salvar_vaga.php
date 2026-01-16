<?php
/**
 * Salvar Vaga de Estágio (com upload de imagem e PDF)
 * IFSP Campus Guarulhos
 * Versão: 1.0
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'Método não permitido'));
    exit;
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

define('_JEXEC', 1);
define('JPATH_BASE', dirname(__FILE__));

function enviarJSON($success, $message = '', $id = null) {
    $response = array('success' => $success, 'message' => $message);
    if ($id !== null) $response['id'] = $id;
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    $configFile = JPATH_BASE . '/configuration.php';
    if (!file_exists($configFile)) {
        enviarJSON(false, 'Arquivo configuration.php não encontrado');
    }
    
    require_once $configFile;
    $config = new JConfig();
    
    $mysqli = new mysqli($config->host, $config->user, $config->password, $config->db);
    
    if ($mysqli->connect_error) {
        enviarJSON(false, 'Erro de conexão: ' . $mysqli->connect_error);
    }
    
    $mysqli->set_charset("utf8mb4");
    
    // Obter dados POST
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $agencia_id = isset($_POST['agencia_id']) ? (int)$_POST['agencia_id'] : 0;
    $codigo_vaga = isset($_POST['codigo_vaga']) ? trim($_POST['codigo_vaga']) : '';
    $empresa = isset($_POST['empresa']) ? trim($_POST['empresa']) : '';
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
    $requisitos = isset($_POST['requisitos']) ? trim($_POST['requisitos']) : '';
    $atividades = isset($_POST['atividades']) ? trim($_POST['atividades']) : '';
    $curso = isset($_POST['curso']) ? trim($_POST['curso']) : '';
    $nivel_escolar = isset($_POST['nivel_escolar']) ? trim($_POST['nivel_escolar']) : '';
    $area_profissional = isset($_POST['area_profissional']) ? trim($_POST['area_profissional']) : '';
    $localidade = isset($_POST['localidade']) ? trim($_POST['localidade']) : '';
    $horario = isset($_POST['horario']) ? trim($_POST['horario']) : '';
    $bolsa_valor = isset($_POST['bolsa_valor']) ? trim($_POST['bolsa_valor']) : null;
    $bolsa_beneficios = isset($_POST['bolsa_beneficios']) ? trim($_POST['bolsa_beneficios']) : '';
    $url_vaga = isset($_POST['url_vaga']) ? trim($_POST['url_vaga']) : '';
    $email_contato = isset($_POST['email_contato']) ? trim($_POST['email_contato']) : '';
    $telefone_contato = isset($_POST['telefone_contato']) ? trim($_POST['telefone_contato']) : '';
    $data_inicio = isset($_POST['data_inicio']) ? trim($_POST['data_inicio']) : '';
    $data_fim = isset($_POST['data_fim']) ? trim($_POST['data_fim']) : '';
    $destaque = isset($_POST['destaque']) ? (int)$_POST['destaque'] : 0;
    
    // Validações
    if ($agencia_id <= 0) enviarJSON(false, 'Agência é obrigatória');
    if (empty($empresa)) enviarJSON(false, 'Nome da empresa é obrigatório');
    if (empty($titulo)) enviarJSON(false, 'Título da vaga é obrigatório');
    if (empty($data_inicio)) enviarJSON(false, 'Data de início é obrigatória');
    if (empty($data_fim)) enviarJSON(false, 'Data de término é obrigatória');
    
    // Validar datas
    $data_inicio_obj = DateTime::createFromFormat('Y-m-d', $data_inicio);
    $data_fim_obj = DateTime::createFromFormat('Y-m-d', $data_fim);
    
    if (!$data_inicio_obj || !$data_fim_obj) {
        enviarJSON(false, 'Formato de data inválido (use AAAA-MM-DD)');
    }
    
    if ($data_fim_obj < $data_inicio_obj) {
        enviarJSON(false, 'Data de término deve ser posterior à data de início');
    }
    
    if (!empty($email_contato) && !filter_var($email_contato, FILTER_VALIDATE_EMAIL)) {
        enviarJSON(false, 'Email inválido');
    }
    
    if (!empty($url_vaga) && !filter_var($url_vaga, FILTER_VALIDATE_URL)) {
        enviarJSON(false, 'URL inválida');
    }
    
    // Converter bolsa_valor
    if ($bolsa_valor !== null && $bolsa_valor !== '') {
        $bolsa_valor = str_replace(',', '.', str_replace('.', '', $bolsa_valor));
        $bolsa_valor = floatval($bolsa_valor);
    } else {
        $bolsa_valor = null;
    }
    
    // UPLOAD DE IMAGEM
    $imagem_path = '';
    $imagem_antiga = '';
    
    if ($id > 0) {
        // Buscar imagem antiga para edição
        $sql_img = "SELECT imagem FROM `" . $config->dbprefix . "vagas_estagio` WHERE id = " . $id;
        $result_img = $mysqli->query($sql_img);
        if ($result_img && $row_img = $result_img->fetch_assoc()) {
            $imagem_antiga = $row_img['imagem'];
        }
    }
    
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        // Validar tipo de arquivo
        $extensoes_permitidas = array('jpg', 'jpeg', 'png', 'gif');
        $nome_arquivo = $_FILES['imagem']['name'];
        $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
        
        if (!in_array($extensao, $extensoes_permitidas)) {
            enviarJSON(false, 'Tipo de arquivo não permitido. Use: JPG, PNG ou GIF');
        }
        
        // Validar tamanho (max 5MB)
        if ($_FILES['imagem']['size'] > 5 * 1024 * 1024) {
            enviarJSON(false, 'Arquivo muito grande. Máximo: 5MB');
        }
        
        // Criar pasta se não existir
        $pasta_upload = JPATH_BASE . '/images/vagas/';
        if (!is_dir($pasta_upload)) {
            mkdir($pasta_upload, 0755, true);
        }
        
        // Nome único para o arquivo
        $nome_unico = 'vaga_' . time() . '_' . uniqid() . '.' . $extensao;
        $caminho_completo = $pasta_upload . $nome_unico;
        
        // Fazer upload
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_completo)) {
            $imagem_path = 'images/vagas/' . $nome_unico;
            
            // Se está editando, deletar imagem antiga
            if ($id > 0 && !empty($imagem_antiga) && file_exists(JPATH_BASE . '/' . $imagem_antiga)) {
                unlink(JPATH_BASE . '/' . $imagem_antiga);
            }
        } else {
            enviarJSON(false, 'Erro ao fazer upload da imagem');
        }
    } else {
        // Se está editando e não enviou nova imagem, manter a antiga
        if ($id > 0 && !empty($imagem_antiga)) {
            $imagem_path = $imagem_antiga;
        } else {
            // Vaga nova sem imagem
            enviarJSON(false, 'Imagem é obrigatória');
        }
    }
    
    // UPLOAD DE PDF (OPCIONAL)
    $pdf_path = '';
    $pdf_antigo = '';
    
    if ($id > 0) {
        $sql_pdf = "SELECT arquivo_pdf FROM `" . $config->dbprefix . "vagas_estagio` WHERE id = " . $id;
        $result_pdf = $mysqli->query($sql_pdf);
        if ($result_pdf && $row_pdf = $result_pdf->fetch_assoc()) {
            $pdf_antigo = $row_pdf['arquivo_pdf'];
        }
    }
    
    if (isset($_FILES['arquivo_pdf']) && $_FILES['arquivo_pdf']['error'] === UPLOAD_ERR_OK) {
        $extensao_pdf = strtolower(pathinfo($_FILES['arquivo_pdf']['name'], PATHINFO_EXTENSION));
        
        if ($extensao_pdf !== 'pdf') {
            enviarJSON(false, 'Apenas arquivos PDF são permitidos');
        }
        
        if ($_FILES['arquivo_pdf']['size'] > 10 * 1024 * 1024) {
            enviarJSON(false, 'PDF muito grande. Máximo: 10MB');
        }
        
        $pasta_pdf = JPATH_BASE . '/images/vagas/pdf/';
        if (!is_dir($pasta_pdf)) {
            mkdir($pasta_pdf, 0755, true);
        }
        
        $nome_pdf = 'vaga_' . time() . '_' . uniqid() . '.pdf';
        $caminho_pdf = $pasta_pdf . $nome_pdf;
        
        if (move_uploaded_file($_FILES['arquivo_pdf']['tmp_name'], $caminho_pdf)) {
            $pdf_path = 'images/vagas/pdf/' . $nome_pdf;
            
            if ($id > 0 && !empty($pdf_antigo) && file_exists(JPATH_BASE . '/' . $pdf_antigo)) {
                unlink(JPATH_BASE . '/' . $pdf_antigo);
            }
        }
    } else {
        if ($id > 0 && !empty($pdf_antigo)) {
            $pdf_path = $pdf_antigo;
        }
    }
    
    // Escapar dados
    $prefix = $config->dbprefix;
    
    if ($id > 0) {
        // ATUALIZAR
        $sql = "UPDATE `{$prefix}vagas_estagio` SET 
                agencia_id = " . $agencia_id . ",
                codigo_vaga = " . ($codigo_vaga ? "'" . $mysqli->real_escape_string($codigo_vaga) . "'" : "NULL") . ",
                empresa = '" . $mysqli->real_escape_string($empresa) . "',
                titulo = '" . $mysqli->real_escape_string($titulo) . "',
                descricao = " . ($descricao ? "'" . $mysqli->real_escape_string($descricao) . "'" : "NULL") . ",
                requisitos = " . ($requisitos ? "'" . $mysqli->real_escape_string($requisitos) . "'" : "NULL") . ",
                atividades = " . ($atividades ? "'" . $mysqli->real_escape_string($atividades) . "'" : "NULL") . ",
                curso = " . ($curso ? "'" . $mysqli->real_escape_string($curso) . "'" : "NULL") . ",
                nivel_escolar = " . ($nivel_escolar ? "'" . $mysqli->real_escape_string($nivel_escolar) . "'" : "NULL") . ",
                area_profissional = " . ($area_profissional ? "'" . $mysqli->real_escape_string($area_profissional) . "'" : "NULL") . ",
                localidade = " . ($localidade ? "'" . $mysqli->real_escape_string($localidade) . "'" : "NULL") . ",
                horario = " . ($horario ? "'" . $mysqli->real_escape_string($horario) . "'" : "NULL") . ",
                bolsa_valor = " . ($bolsa_valor !== null ? $bolsa_valor : "NULL") . ",
                bolsa_beneficios = " . ($bolsa_beneficios ? "'" . $mysqli->real_escape_string($bolsa_beneficios) . "'" : "NULL") . ",
                imagem = '" . $mysqli->real_escape_string($imagem_path) . "',
                arquivo_pdf = " . ($pdf_path ? "'" . $mysqli->real_escape_string($pdf_path) . "'" : "NULL") . ",
                url_vaga = '" . $mysqli->real_escape_string($url_vaga) . "',
                email_contato = " . ($email_contato ? "'" . $mysqli->real_escape_string($email_contato) . "'" : "NULL") . ",
                telefone_contato = " . ($telefone_contato ? "'" . $mysqli->real_escape_string($telefone_contato) . "'" : "NULL") . ",
                data_inicio = '" . $data_inicio . "',
                data_fim = '" . $data_fim . "',
                destaque = " . $destaque . ",
                modified = NOW(),
                modified_by = 0
                WHERE id = " . $id;
        
        if ($mysqli->query($sql)) {
            enviarJSON(true, 'Vaga atualizada com sucesso!', $id);
        } else {
            enviarJSON(false, 'Erro ao atualizar: ' . $mysqli->error);
        }
        
    } else {
        // INSERIR
        $sql_ordering = "SELECT MAX(ordering) as max_ordering FROM `{$prefix}vagas_estagio`";
        $result = $mysqli->query($sql_ordering);
        $maxOrdering = 0;
        if ($result && $row = $result->fetch_assoc()) {
            $maxOrdering = (int)$row['max_ordering'];
        }
        $novoOrdering = $maxOrdering + 1;
        
        $sql = "INSERT INTO `{$prefix}vagas_estagio` 
                (agencia_id, codigo_vaga, empresa, titulo, descricao, requisitos, atividades, curso, nivel_escolar, 
                area_profissional, localidade, horario, bolsa_valor, bolsa_beneficios, imagem, arquivo_pdf, url_vaga, 
                email_contato, telefone_contato, data_inicio, data_fim, status, destaque, ordering, created, created_by) 
                VALUES 
                (" . $agencia_id . ", " . 
                ($codigo_vaga ? "'" . $mysqli->real_escape_string($codigo_vaga) . "'" : "NULL") . ", '" . 
                $mysqli->real_escape_string($empresa) . "', '" . 
                $mysqli->real_escape_string($titulo) . "', " . 
                ($descricao ? "'" . $mysqli->real_escape_string($descricao) . "'" : "NULL") . ", " . 
                ($requisitos ? "'" . $mysqli->real_escape_string($requisitos) . "'" : "NULL") . ", " . 
                ($atividades ? "'" . $mysqli->real_escape_string($atividades) . "'" : "NULL") . ", " . 
                ($curso ? "'" . $mysqli->real_escape_string($curso) . "'" : "NULL") . ", " . 
                ($nivel_escolar ? "'" . $mysqli->real_escape_string($nivel_escolar) . "'" : "NULL") . ", " . 
                ($area_profissional ? "'" . $mysqli->real_escape_string($area_profissional) . "'" : "NULL") . ", " . 
                ($localidade ? "'" . $mysqli->real_escape_string($localidade) . "'" : "NULL") . ", " . 
                ($horario ? "'" . $mysqli->real_escape_string($horario) . "'" : "NULL") . ", " . 
                ($bolsa_valor !== null ? $bolsa_valor : "NULL") . ", " . 
                ($bolsa_beneficios ? "'" . $mysqli->real_escape_string($bolsa_beneficios) . "'" : "NULL") . ", '" . 
                $mysqli->real_escape_string($imagem_path) . "', " . 
                ($pdf_path ? "'" . $mysqli->real_escape_string($pdf_path) . "'" : "NULL") . ", '" . 
                $mysqli->real_escape_string($url_vaga) . "', " . 
                ($email_contato ? "'" . $mysqli->real_escape_string($email_contato) . "'" : "NULL") . ", " . 
                ($telefone_contato ? "'" . $mysqli->real_escape_string($telefone_contato) . "'" : "NULL") . ", '" . 
                $data_inicio . "', '" . 
                $data_fim . "', 1, " . 
                $destaque . ", " . 
                $novoOrdering . ", NOW(), 0)";
        
        if ($mysqli->query($sql)) {
            $novoId = $mysqli->insert_id;
            enviarJSON(true, 'Vaga cadastrada com sucesso!', $novoId);
        } else {
            enviarJSON(false, 'Erro ao inserir: ' . $mysqli->error);
        }
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    enviarJSON(false, 'Erro: ' . $e->getMessage());
}