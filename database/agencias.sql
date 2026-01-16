-- ==================================================
-- SCRIPT DE INSTALAÇÃO - SISTEMA DE VAGAS DE ESTÁGIO
-- IFSP Campus Guarulhos
-- Compatível com Joomla 3.10 e 4.4
-- ==================================================

-- IMPORTANTE: Substitua #__ pelo prefixo do seu banco
-- Exemplo: se seu prefixo é tbcex4414_ , substitua #__ por tbcex4414_

-- Tabela de Agências de Estágio
CREATE TABLE IF NOT EXISTS `tbcex4414_agencias_estagio` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `sigla` VARCHAR(20) NOT NULL,
  `logo` VARCHAR(255) DEFAULT NULL,
  `site` VARCHAR(255) DEFAULT NULL,
  `contato` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `telefone` VARCHAR(20) DEFAULT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=ativo, 0=inativo',
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_sigla` (`sigla`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Vagas de Estágio
CREATE TABLE IF NOT EXISTS `tbcex4414_vagas_estagio` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `agencia_id` INT(11) UNSIGNED NOT NULL,
  `titulo` VARCHAR(200) NOT NULL,
  `empresa` VARCHAR(150) NOT NULL,
  `descricao` TEXT,
  `imagem` VARCHAR(255) NOT NULL,
  `url_vaga` VARCHAR(500) NOT NULL,
  `local` VARCHAR(100) DEFAULT NULL,
  `bolsa` DECIMAL(10,2) DEFAULT NULL,
  `data_inicio` DATE NOT NULL,
  `data_fim` DATE NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=ativo, 0=inativo, 2=expirado',
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `hits` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_agencia` (`agencia_id`),
  KEY `idx_data_fim` (`data_fim`),
  KEY `idx_status` (`status`),
  KEY `idx_composite` (`agencia_id`, `status`, `data_fim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir agências de exemplo
INSERT INTO `#__agencias_estagio` (`nome`, `sigla`, `site`, `status`, `ordering`, `created`) VALUES
('Centro de Integração Empresa-Escola', 'CIEE', 'https://www.ciee.org.br', 1, 1, NOW()),
('Núcleo Brasileiro de Estágios', 'NUBE', 'https://www.nube.com.br', 1, 2, NOW()),
('Companhia de Estágios', 'CIA', 'https://www.ciadeestagio.com.br', 1, 3, NOW()),
('Super Estágios', 'SUPER', 'https://www.superestagio.com.br', 1, 4, NOW()),
('IEL - Instituto Euvaldo Lodi', 'IEL', 'https://www.portaldaindustria.com.br/iel', 1, 5, NOW());

-- Mensagem de sucesso
SELECT 'Instalação concluída com sucesso!' AS Resultado;
SELECT COUNT(*) AS 'Total de Agências Cadastradas' FROM `#__agencias_estagio`;