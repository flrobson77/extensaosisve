# 🎓 ExtensãoSISVE - Sistema de Vagas de Estágio

> Sistema Web para publicação de vagas de estágio para os estudantes do IFSP Campus Guarulhos

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Joomla](https://img.shields.io/badge/Joomla-3.10%20%7C%204.4-blue)](https://www.joomla.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)](https://www.mysql.com/)

## 📋 Sobre o Projeto

O ExtensãoSISVE é um sistema desenvolvido para facilitar a divulgação de vagas de estágio oferecidas por agências parceiras (CIEE, NUBE, IEL, etc.) para os estudantes do Instituto Federal de São Paulo - Campus Guarulhos.

### ✨ Funcionalidades

- 📝 **Cadastro de Agências** - Gerenciamento completo de agências parceiras
- 💼 **Cadastro de Vagas** - Publicação de vagas com imagens e informações detalhadas
- 🎠 **Carrossel Dinâmico** - Visualização organizada por agência
- ⏰ **Expiração Automática** - Vagas expiram automaticamente após prazo
- 🔐 **Área Administrativa** - Acesso restrito para gestão
- 📱 **Responsivo** - Funciona em desktop, tablet e mobile

## 🚀 Tecnologias Utilizadas

- **CMS:** Joomla 3.10.12 / 4.4.14
- **Backend:** PHP 7.4+
- **Banco de Dados:** MySQL 5.7+ / MariaDB 10.3+
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Design:** CSS Grid, Flexbox, Animações CSS

## 📦 Instalação

### Pré-requisitos

- Joomla 3.10+ ou 4.4+ instalado
- PHP 7.4 ou superior
- MySQL 5.7+ ou MariaDB 10.3+
- Acesso administrativo ao Joomla
- Cliente FTP ou acesso ao Gerenciador de Arquivos

### Passo 1: Banco de Dados

1. Acesse o PhpMyAdmin
2. Selecione o banco de dados do Joomla
3. Execute o script `database/install.sql`
4. Verifique se as tabelas foram criadas:
   - `[prefixo]_agencias_estagio`
   - `[prefixo]_vagas_estagio`

### Passo 2: Upload dos Arquivos PHP

1. Faça upload dos arquivos da pasta `backend/` para a raiz do Joomla
2. Verifique as permissões (644 para arquivos)

### Passo 3: Criar Pasta de Imagens

1. Acesse: **Conteúdo → Mídia** no Joomla
2. Crie a pasta `vagas` dentro de `/images/`
3. Defina permissões 755

### Passo 4: Criar Artigos

1. Copie o conteúdo de `frontend/admin/cadastro-agencias.html`
2. Crie um artigo no Joomla
3. Cole o código (modo código-fonte)
4. Configure acesso como "Special" (apenas administradores)
5. Repita para os demais artigos

## 📖 Documentação Completa

Para instruções detalhadas, consulte:
- [Guia de Instalação](docs/instalacao.md)
- [Manual de Configuração](docs/configuracao.md)
- [Manual do Usuário](docs/manual-usuario.md)

## 🎯 Como Usar

### Para Administradores

1. Acesse o painel administrativo do Joomla
2. Navegue até o menu "Gerenciar Agências"
3. Cadastre as agências parceiras
4. Acesse "Gerenciar Vagas"
5. Cadastre novas vagas selecionando a agência

### Para Estudantes

1. Acesse a página de vagas de estágio
2. Navegue pelos carrosseis de cada agência
3. Clique em "Ver Vaga Completa" para ser redirecionado

## 🔧 Configuração Avançada

### CRON para Expiração Automática

Execute diariamente via crontab:
```bash
0 0 * * * php /caminho/para/joomla/cron/expirar_vagas.php
```

## 🤝 Contribuindo

Contribuições são bem-vindas! Este projeto foi desenvolvido para a comunidade educacional.

1. Faça um Fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👨‍🏫 Autor

**Professor do IFSP Campus Guarulhos**
- Especialista em Arquitetura de Computadores, Sistemas Operacionais, Redes, DevOps e Segurança da Informação
- GitHub: [@seu-usuario](https://github.com/seu-usuario)

## 🏫 Sobre o IFSP Guarulhos

O Instituto Federal de São Paulo - Campus Guarulhos oferece cursos técnicos e superiores nas áreas de Tecnologia da Informação e áreas correlatas.

## 📧 Contato

Para dúvidas ou sugestões:
- Email: extensao@ifsp.edu.br
- Site: [IFSP Guarulhos](https://gru.ifsp.edu.br)

## 🙏 Agradecimentos

- Equipe de extensão do IFSP Guarulhos
- Agências parceiras: CIEE, NUBE, IEL, CIA Estágios
- Comunidade Joomla Brasil
- Alunos que contribuíram com feedback

---

**Desenvolvido com ❤️ para os estudantes do IFSP Guarulhos**
