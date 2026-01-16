# 📦 Guia de Instalação

## Passo a Passo Completo

### 1. Banco de Dados

Execute o script `/database/install.sql` no PhpMyAdmin.

**Importante:** Substitua `#__` pelo prefixo do seu Joomla.

### 2. Upload de Arquivos

**Arquivos PHP (raiz do Joomla):**
- salvar_agencia.php
- listar_agencias.php
- toggle_agencia.php

**Pasta Media:**
- Faça upload da pasta `media/com_vagasestagio/` para `/media/`

### 3. Criar Artigos

Cole o conteúdo dos arquivos da pasta `artigos/` nos artigos do Joomla.

### 4. Criar Menus

Siga as instruções do README.md para criar a estrutura de menus.

---

## Problemas Comuns

**CSS/JS não carrega:**
- Verifique se a pasta está em `/media/com_vagasestagio/`
- Limpe o cache do Joomla

**Formulário não salva:**
- Verifique permissões dos arquivos PHP
- Confira configuração de Filtros de Texto

**Tabelas não aparecem:**
- Verifique se o script SQL foi executado
- Confirme o prefixo correto das tabelas