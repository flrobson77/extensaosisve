/**
 * JavaScript para página pública de Agências Conveniadas
 * IFSP Campus Guarulhos
 * Versão: 1.0
 */

(function() {
    'use strict';
    
    // Configuração
    const BASE_URL = window.BASE_URL || (window.location.origin + '/');
    
    console.log('🏢 Sistema de Agências Públicas - v1.0');
    console.log('🌐 Base URL:', BASE_URL);
    
    // Elementos do DOM
    let tabelaBody = null;
    let loadingDiv = null;
    let contadorDiv = null;
    
    /**
     * Inicializar quando DOM estiver pronto
     */
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📋 Iniciando carregamento de agências...');
        
        tabelaBody = document.getElementById('agencias-tbody');
        loadingDiv = document.getElementById('agencias-loading');
        contadorDiv = document.getElementById('agencias-contador');
        
        if (!tabelaBody) {
            console.error('❌ Elemento agencias-tbody não encontrado!');
            return;
        }
        
        carregarAgencias();
    });
    
    /**
     * Carregar agências do servidor
     */
    function carregarAgencias() {
        const url = BASE_URL + 'listar_agencias_publico.php';
        
        console.log('🔄 Buscando agências em:', url);
        
        // Mostrar loading
        mostrarLoading(true);
        
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro HTTP: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ Resposta recebida:', data);
                
                if (data.success) {
                    renderizarAgencias(data.agencias);
                } else {
                    mostrarErro(data.message || 'Erro ao carregar agências');
                }
            })
            .catch(error => {
                console.error('❌ Erro ao carregar agências:', error);
                mostrarErro('Não foi possível carregar as agências. Tente novamente mais tarde.');
            })
            .finally(() => {
                mostrarLoading(false);
            });
    }
    
    /**
     * Renderizar agências na tabela
     */
    function renderizarAgencias(agencias) {
        if (!agencias || agencias.length === 0) {
            mostrarVazio();
            return;
        }
        
        console.log('📊 Renderizando', agencias.length, 'agências');
        
        let html = '';
        
        agencias.forEach(agencia => {
            html += `
                <tr>
                    <!-- Logo -->
                    <td class="agencias-logo-col">
                        ${renderizarLogo(agencia)}
                    </td>
                    
                    <!-- Nome e Sigla -->
                    <td class="agencias-nome-col">
                        <div class="agencias-nome">${escapeHtml(agencia.nome)}</div>
                        <span class="agencias-sigla">${escapeHtml(agencia.sigla)}</span>
                    </td>
                    
                    <!-- Email -->
                    <td class="agencias-email-col">
                        ${renderizarEmail(agencia.email)}
                    </td>
                    
                    <!-- Telefone -->
                    <td class="agencias-telefone-col">
                        ${renderizarTelefone(agencia.telefone)}
                    </td>
                </tr>
            `;
        });
        
        tabelaBody.innerHTML = html;
        
        // Atualizar contador
        if (contadorDiv) {
            contadorDiv.innerHTML = `Total: <strong>${agencias.length}</strong> ${agencias.length === 1 ? 'agência' : 'agências'} conveniada${agencias.length === 1 ? '' : 's'}`;
        }
    }
    
    /**
     * Renderizar logo
     */
    function renderizarLogo(agencia) {
        if (agencia.site) {
            if (agencia.logo && agencia.logo.trim() !== '') {
                return `
                    <a href="${escapeHtml(agencia.site)}" target="_blank" rel="noopener noreferrer" class="agencias-logo-link" title="Visitar site de ${escapeHtml(agencia.nome)}">
                        <img src="${escapeHtml(agencia.logo)}" alt="Logo ${escapeHtml(agencia.nome)}" class="agencias-logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="agencias-logo-placeholder" style="display:none;">${escapeHtml(agencia.sigla.substring(0, 2))}</div>
                    </a>
                `;
            } else {
                return `
                    <a href="${escapeHtml(agencia.site)}" target="_blank" rel="noopener noreferrer" class="agencias-logo-link" title="Visitar site de ${escapeHtml(agencia.nome)}">
                        <div class="agencias-logo-placeholder">${escapeHtml(agencia.sigla.substring(0, 2))}</div>
                    </a>
                `;
            }
        } else {
            if (agencia.logo && agencia.logo.trim() !== '') {
                return `
                    <img src="${escapeHtml(agencia.logo)}" alt="Logo ${escapeHtml(agencia.nome)}" class="agencias-logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="agencias-logo-placeholder" style="display:none;">${escapeHtml(agencia.sigla.substring(0, 2))}</div>
                `;
            } else {
                return `<div class="agencias-logo-placeholder">${escapeHtml(agencia.sigla.substring(0, 2))}</div>`;
            }
        }
    }
    
    /**
     * Renderizar email
     */
    function renderizarEmail(email) {
        if (email && email.trim() !== '') {
            return `<a href="mailto:${escapeHtml(email)}" class="agencias-email" title="Enviar email">${escapeHtml(email)}</a>`;
        }
        return '<span style="color: #999;">-</span>';
    }
    
    /**
     * Renderizar telefone
     */
    function renderizarTelefone(telefone) {
        if (telefone && telefone.trim() !== '') {
            // Remover caracteres não numéricos para o link
            const telLimpo = telefone.replace(/\D/g, '');
            return `<a href="tel:+55${telLimpo}" class="agencias-telefone" title="Ligar">${escapeHtml(telefone)}</a>`;
        }
        return '<span style="color: #999;">-</span>';
    }
    
    /**
     * Mostrar loading
     */
    function mostrarLoading(mostrar) {
        if (loadingDiv) {
            loadingDiv.style.display = mostrar ? 'block' : 'none';
        }
    }
    
    /**
     * Mostrar mensagem de erro
     */
    function mostrarErro(mensagem) {
        tabelaBody.innerHTML = `
            <tr>
                <td colspan="4" class="agencias-empty">
                    <div class="agencias-empty-icon">⚠️</div>
                    <p><strong>Erro ao carregar agências</strong></p>
                    <p>${escapeHtml(mensagem)}</p>
                </td>
            </tr>
        `;
    }
    
    /**
     * Mostrar mensagem vazia
     */
    function mostrarVazio() {
        tabelaBody.innerHTML = `
            <tr>
                <td colspan="4" class="agencias-empty">
                    <div class="agencias-empty-icon">📋</div>
                    <p><strong>Nenhuma agência cadastrada</strong></p>
                    <p>Em breve teremos agências conveniadas disponíveis.</p>
                </td>
            </tr>
        `;
    }
    
    /**
     * Escape HTML para segurança
     */
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
})();