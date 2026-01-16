/**
 * JavaScript para Administração de Agências
 * IFSP Campus Guarulhos - Sistema de Vagas de Estágio
 */

// ============================================
// VARIÁVEIS GLOBAIS
// ============================================
let agenciasData = [];

// ============================================
// INICIALIZAÇÃO
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sistema de Agências carregado');
    
    // Carregar agências ao iniciar
    carregarAgencias();
    
    // Configurar envio do formulário
    setupFormSubmit();
    
    // Configurar máscara de telefone
    setupTelefoneMask();
});

// ============================================
// CONFIGURAR ENVIO DO FORMULÁRIO
// ============================================
function setupFormSubmit() {
    const form = document.getElementById('formAgencia');
    
    if (!form) {
        console.error('Formulário não encontrado');
        return;
    }
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        salvarAgencia();
    });
}

// ============================================
// SALVAR AGÊNCIA
// ============================================
function salvarAgencia() {
    const form = document.getElementById('formAgencia');
    const formData = new FormData(form);
    
    // Validar formulário
    if (!validarFormulario()) {
        return;
    }
    
    // Mostrar loading
    mostrarLoading(true);
    ocultarMensagem();
    
    // Enviar via AJAX
    fetch(BASE_URL + 'salvar_agencia.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro na resposta do servidor');
        }
        return response.json();
    })
    .then(data => {
        mostrarLoading(false);
        
        if (data.success) {
            mostrarMensagem(data.message, 'success');
            limparFormulario();
            carregarAgencias();
            
            // Scroll para o topo
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            // Esconder mensagem após 5 segundos
            setTimeout(ocultarMensagem, 5000);
        } else {
            mostrarMensagem(data.message, 'error');
        }
    })
    .catch(error => {
        mostrarLoading(false);
        console.error('Erro:', error);
        mostrarMensagem('Erro ao salvar agência: ' + error.message, 'error');
    });
}

// ============================================
// VALIDAR FORMULÁRIO
// ============================================
function validarFormulario() {
    const nome = document.getElementById('nome').value.trim();
    const sigla = document.getElementById('sigla').value.trim();
    const site = document.getElementById('site').value.trim();
    const email = document.getElementById('email').value.trim();
    
    // Validar nome
    if (nome === '') {
        mostrarMensagem('Nome da agência é obrigatório', 'error');
        document.getElementById('nome').focus();
        return false;
    }
    
    // Validar sigla
    if (sigla === '') {
        mostrarMensagem('Sigla é obrigatória', 'error');
        document.getElementById('sigla').focus();
        return false;
    }
    
    // Validar URL (se preenchida)
    if (site !== '' && !validarURL(site)) {
        mostrarMensagem('URL do site inválida', 'error');
        document.getElementById('site').focus();
        return false;
    }
    
    // Validar email (se preenchido)
    if (email !== '' && !validarEmail(email)) {
        mostrarMensagem('Email inválido', 'error');
        document.getElementById('email').focus();
        return false;
    }
    
    return true;
}

// ============================================
// VALIDAÇÕES AUXILIARES
// ============================================
function validarURL(url) {
    try {
        new URL(url);
        return true;
    } catch (e) {
        return false;
    }
}

function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// ============================================
// CARREGAR AGÊNCIAS
// ============================================
function carregarAgencias() {
    console.log('Carregando agências...');
    
    // Mostrar loading na tabela
    const tbody = document.getElementById('listaAgencias');
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="7" class="empty-state"><div class="spinner"></div><p>Carregando...</p></td></tr>';
    }
    
    fetch(BASE_URL + 'listar_agencias.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao buscar agências');
            }
            return response.json();
        })
        .then(data => {
            console.log('Agências carregadas:', data);
            
            if (data.success) {
                agenciasData = data.agencias;
                renderizarTabela(data.agencias);
                atualizarContador(data.agencias.length);
            } else {
                throw new Error(data.message || 'Erro desconhecido');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar agências:', error);
            
            if (tbody) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="empty-state">
                            <div class="empty-state-icon">⚠️</div>
                            <p class="empty-state-text">Erro ao carregar agências</p>
                            <p class="empty-state-subtext">${error.message}</p>
                        </td>
                    </tr>
                `;
            }
        });
}

// ============================================
// RENDERIZAR TABELA
// ============================================
function renderizarTabela(agencias) {
    const tbody = document.getElementById('listaAgencias');
    
    if (!tbody) {
        console.error('Elemento listaAgencias não encontrado');
        return;
    }
    
    // Se não há agências
    if (!agencias || agencias.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="empty-state">
                    <div class="empty-state-icon">📋</div>
                    <p class="empty-state-text">Nenhuma agência cadastrada</p>
                    <p class="empty-state-subtext">Comece cadastrando uma agência usando o formulário acima</p>
                </td>
            </tr>
        `;
        return;
    }
    
    // Renderizar linhas
    let html = '';
    
    agencias.forEach(function(agencia) {
        const statusClass = agencia.status == 1 ? 'status-ativo' : 'status-inativo';
        const statusText = agencia.status == 1 ? 'Ativo' : 'Inativo';
        const toggleText = agencia.status == 1 ? '🚫 Desativar' : '✅ Ativar';
        
        html += `
            <tr class="fade-in">
                <td class="col-id">${agencia.id}</td>
                <td class="col-nome">
                    <span class="agency-name">${escapeHtml(agencia.nome)}</span>
                    ${agencia.site ? `<span class="agency-site"><a href="${escapeHtml(agencia.site)}" target="_blank">🔗 ${formatarURL(agencia.site)}</a></span>` : ''}
                </td>
                <td class="col-sigla"><strong>${escapeHtml(agencia.sigla)}</strong></td>
                <td class="col-contato">${agencia.contato ? escapeHtml(agencia.contato) : '<span class="no-data">-</span>'}</td>
                <td class="col-telefone">${agencia.telefone ? formatarTelefone(agencia.telefone) : '<span class="no-data">-</span>'}</td>
                <td class="col-status">
                    <span class="status-badge ${statusClass}">${statusText}</span>
                </td>
                <td class="col-acoes">
                    <div class="action-buttons">
                        <button class="btn-action btn-edit tooltip-btn" 
                                onclick="editarAgencia(${agencia.id})"
                                data-tooltip="Editar agência">
                            ✏️ Editar
                        </button>
                        <button class="btn-action btn-toggle tooltip-btn" 
                                onclick="toggleStatus(${agencia.id}, ${agencia.status})"
                                data-tooltip="${statusText == 'Ativo' ? 'Desativar' : 'Ativar'} agência">
                            ${toggleText}
                        </button>
                        ${agencia.site ? `
                        <a href="${escapeHtml(agencia.site)}" 
                           target="_blank" 
                           class="btn-action btn-view tooltip-btn"
                           data-tooltip="Visitar site">
                            🌐 Site
                        </a>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// ============================================
// EDITAR AGÊNCIA
// ============================================
function editarAgencia(id) {
    console.log('Editando agência:', id);
    
    mostrarLoading(true);
    
    fetch(BASE_URL + 'listar_agencias.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            mostrarLoading(false);
            
            if (data.success && data.agencias.length > 0) {
                const agencia = data.agencias[0];
                
                // Preencher formulário
                document.getElementById('agencia_id').value = agencia.id;
                document.getElementById('nome').value = agencia.nome;
                document.getElementById('sigla').value = agencia.sigla;
                document.getElementById('site').value = agencia.site || '';
                document.getElementById('contato').value = agencia.contato || '';
                document.getElementById('email').value = agencia.email || '';
                document.getElementById('telefone').value = agencia.telefone || '';
                
                // Scroll suave para o formulário
                document.querySelector('.form-card-agencias').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Focar no primeiro campo
                setTimeout(() => {
                    document.getElementById('nome').focus();
                }, 500);
                
                mostrarMensagem('Editando agência: ' + agencia.nome, 'success');
                setTimeout(ocultarMensagem, 3000);
            }
        })
        .catch(error => {
            mostrarLoading(false);
            console.error('Erro:', error);
            mostrarMensagem('Erro ao carregar dados da agência', 'error');
        });
}

// ============================================
// TOGGLE STATUS
// ============================================
function toggleStatus(id, statusAtual) {
    const acao = statusAtual == 1 ? 'desativar' : 'ativar';
    
    if (!confirm(`Deseja realmente ${acao} esta agência?`)) {
        return;
    }
    
    mostrarLoading(true);
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('status', statusAtual == 1 ? 0 : 1);
    
    fetch(BASE_URL + 'toggle_agencia.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        mostrarLoading(false);
        
        if (data.success) {
            mostrarMensagem('Status atualizado com sucesso!', 'success');
            carregarAgencias();
            setTimeout(ocultarMensagem, 3000);
        } else {
            mostrarMensagem(data.message, 'error');
        }
    })
    .catch(error => {
        mostrarLoading(false);
        console.error('Erro:', error);
        mostrarMensagem('Erro ao atualizar status', 'error');
    });
}

// ============================================
// LIMPAR FORMULÁRIO
// ============================================
function limparFormulario() {
    const form = document.getElementById('formAgencia');
    if (form) {
        form.reset();
        document.getElementById('agencia_id').value = '';
        document.getElementById('nome').focus();
    }
}

// ============================================
// MOSTRAR/OCULTAR MENSAGEM
// ============================================
function mostrarMensagem(texto, tipo) {
    const mensagem = document.getElementById('mensagem');
    
    if (!mensagem) {
        console.error('Elemento mensagem não encontrado');
        return;
    }
    
    mensagem.textContent = texto;
    mensagem.className = 'alert-message alert-' + tipo + ' show';
}

function ocultarMensagem() {
    const mensagem = document.getElementById('mensagem');
    if (mensagem) {
        mensagem.classList.remove('show');
    }
}

// ============================================
// MOSTRAR/OCULTAR LOADING
// ============================================
function mostrarLoading(mostrar) {
    const loading = document.getElementById('loading');
    
    if (!loading) {
        console.error('Elemento loading não encontrado');
        return;
    }
    
    if (mostrar) {
        loading.classList.add('active');
    } else {
        loading.classList.remove('active');
    }
}

// ============================================
// ATUALIZAR CONTADOR
// ============================================
function atualizarContador(total) {
    const contador = document.getElementById('totalAgencias');
    if (contador) {
        contador.textContent = total;
    }
}

// ============================================
// FORMATAÇÃO DE DADOS
// ============================================
function formatarTelefone(telefone) {
    if (!telefone) return '';
    
    // Remove tudo que não é número
    const numeros = telefone.replace(/\D/g, '');
    
    // Formata: (11) 99999-9999 ou (11) 9999-9999
    if (numeros.length === 11) {
        return `(${numeros.substr(0,2)}) ${numeros.substr(2,5)}-${numeros.substr(7)}`;
    } else if (numeros.length === 10) {
        return `(${numeros.substr(0,2)}) ${numeros.substr(2,4)}-${numeros.substr(6)}`;
    }
    
    return telefone;
}

function formatarURL(url) {
    if (!url) return '';
    return url.replace(/^https?:\/\/(www\.)?/, '').replace(/\/$/, '');
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// ============================================
// MÁSCARA DE TELEFONE
// ============================================
function setupTelefoneMask() {
    const telefoneInput = document.getElementById('telefone');
    
    if (!telefoneInput) return;
    
    telefoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        if (value.length > 11) {
            value = value.substr(0, 11);
        }
        
        if (value.length >= 11) {
            value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        } else if (value.length >= 7) {
            value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
        } else if (value.length >= 3) {
            value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        } else if (value.length >= 1) {
            value = value.replace(/^(\d*)/, '($1');
        }
        
        e.target.value = value;
    });
}

// ============================================
// BUSCA E FILTRO (FUTURO)
// ============================================
function filtrarAgencias(termo) {
    if (!termo) {
        renderizarTabela(agenciasData);
        return;
    }
    
    termo = termo.toLowerCase();
    
    const filtradas = agenciasData.filter(agencia => {
        return agencia.nome.toLowerCase().includes(termo) ||
               agencia.sigla.toLowerCase().includes(termo) ||
               (agencia.contato && agencia.contato.toLowerCase().includes(termo));
    });
    
    renderizarTabela(filtradas);
}

// ============================================
// EXPORTAR DADOS (FUTURO)
// ============================================
function exportarCSV() {
    console.log('Exportar CSV - Em desenvolvimento');
    // Implementar depois se necessário
}

function exportarPDF() {
    console.log('Exportar PDF - Em desenvolvimento');
    // Implementar depois se necessário
}

// ============================================
// LOG DE DEBUG
// ============================================
console.log('📋 Sistema de Gerenciamento de Agências - Versão 1.0');
console.log('🎓 IFSP Campus Guarulhos - 2026');