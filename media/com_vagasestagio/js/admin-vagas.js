/**
 * JavaScript para gerenciamento de vagas (admin)
 * IFSP Campus Guarulhos
 * Versão: 1.0
 */

(function() {
    'use strict';
    
    // Configuração
    const BASE_URL = (typeof BASE_URL !== 'undefined') ? BASE_URL : (window.location.origin + '/');
    
    console.log('📋 Sistema de Gerenciamento de Vagas - v1.0');
    console.log('🎓 IFSP Campus Guarulhos - 2026');
    console.log('🌐 Base URL:', BASE_URL);
    
    // Variáveis globais
    let vagasData = [];
    let agenciasData = [];
    let editandoId = 0;
    
    /**
     * Inicializar quando DOM estiver pronto
     */
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Sistema de Vagas carregado');
        
        // Carregar agências para o select
        carregarAgencias();
        
        // Carregar vagas
        carregarVagas();
        
        // Setup do formulário
        setupFormSubmit();
        
        // Setup de uploads
        setupFileUploads();
        
        // Setup de máscaras
        setupMascaras();
        
        // Setup de preview de imagem
        setupImagePreview();
    });
    
    /**
     * Carregar agências para o select
     */
    function carregarAgencias() {
        const url = BASE_URL + 'listar_agencias.php';
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    agenciasData = data.agencias;
                    popularSelectAgencias();
                } else {
                    console.error('Erro ao carregar agências:', data.message);
                }
            })
            .catch(error => {
                console.error('Erro ao carregar agências:', error);
            });
    }
    
    /**
     * Popular select de agências
     */
    function popularSelectAgencias() {
        const select = document.getElementById('agencia_id');
        if (!select) return;
        
        // Limpar options
        select.innerHTML = '<option value="">Selecione uma agência</option>';
        
        // Adicionar agências ativas
        agenciasData.forEach(agencia => {
            if (agencia.status == 1) {
                const option = document.createElement('option');
                option.value = agencia.id;
                option.textContent = agencia.nome + ' (' + agencia.sigla + ')';
                select.appendChild(option);
            }
        });
    }
    
    /**
     * Setup do formulário
     */
    function setupFormSubmit() {
        const form = document.getElementById('form-vaga');
        if (!form) return;
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            salvarVaga();
        });
        
        // Botão cancelar
        const btnCancelar = document.getElementById('btn-cancelar');
        if (btnCancelar) {
            btnCancelar.addEventListener('click', function() {
                limparFormulario();
            });
        }
    }
    
    /**
     * Setup de uploads
     */
    function setupFileUploads() {
        // Upload de imagem
        const inputImagem = document.getElementById('imagem');
        const labelImagem = document.querySelector('label[for="imagem"]');
        const nomeImagem = document.getElementById('nome-imagem');
        
        if (inputImagem) {
            inputImagem.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const fileName = this.files[0].name;
                    if (nomeImagem) {
                        nomeImagem.textContent = fileName;
                    }
                }
            });
        }
        
        // Upload de PDF
        const inputPdf = document.getElementById('arquivo_pdf');
        const nomePdf = document.getElementById('nome-pdf');
        
        if (inputPdf) {
            inputPdf.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const fileName = this.files[0].name;
                    if (nomePdf) {
                        nomePdf.textContent = fileName;
                    }
                }
            });
        }
    }
    
    /**
     * Setup de preview de imagem
     */
    function setupImagePreview() {
        const inputImagem = document.getElementById('imagem');
        const previewDiv = document.getElementById('preview-imagem');
        
        if (inputImagem && previewDiv) {
            inputImagem.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        previewDiv.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                        previewDiv.style.display = 'block';
                    };
                    
                    reader.readAsDataURL(this.files[0]);
                } else {
                    previewDiv.style.display = 'none';
                }
            });
        }
    }
    
    /**
     * Setup de máscaras
     */
    function setupMascaras() {
        // Máscara de telefone
        const inputTelefone = document.getElementById('telefone_contato');
        if (inputTelefone) {
            inputTelefone.addEventListener('input', function(e) {
                let valor = e.target.value.replace(/\D/g, '');
                
                if (valor.length <= 11) {
                    valor = valor.replace(/^(\d{2})(\d)/g, '($1) $2');
                    valor = valor.replace(/(\d)(\d{4})$/, '$1-$2');
                }
                
                e.target.value = valor;
            });
        }
        
        // Máscara de valor
        const inputBolsa = document.getElementById('bolsa_valor');
        if (inputBolsa) {
            inputBolsa.addEventListener('input', function(e) {
                let valor = e.target.value.replace(/\D/g, '');
                valor = (valor / 100).toFixed(2);
                valor = valor.replace('.', ',');
                valor = valor.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                e.target.value = 'R$ ' + valor;
            });
        }
    }
    
    /**
     * Salvar vaga
     */
    function salvarVaga() {
        console.log('Salvando vaga...');
        
        // Validar formulário
        if (!validarFormulario()) {
            return;
        }
        
        // Obter dados do formulário
        const formData = new FormData(document.getElementById('form-vaga'));
        
        // Adicionar ID se estiver editando
        if (editandoId > 0) {
            formData.append('id', editandoId);
        }
        
        // Mostrar loading
        mostrarLoading(true);
        
        // Enviar para o servidor
        const url = BASE_URL + 'salvar_vaga.php';
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Resposta do servidor:', data);
            
            if (data.success) {
                mostrarMensagem('success', data.message);
                limparFormulario();
                carregarVagas();
            } else {
                mostrarMensagem('error', data.message);
            }
        })
        .catch(error => {
            console.error('Erro ao salvar vaga:', error);
            mostrarMensagem('error', 'Erro ao salvar vaga. Tente novamente.');
        })
        .finally(() => {
            mostrarLoading(false);
        });
    }
    
    /**
     * Validar formulário
     */
    function validarFormulario() {
        const agencia_id = document.getElementById('agencia_id').value;
        const empresa = document.getElementById('empresa').value.trim();
        const titulo = document.getElementById('titulo').value.trim();
        const data_inicio = document.getElementById('data_inicio').value;
        const data_fim = document.getElementById('data_fim').value;
        const imagem = document.getElementById('imagem').files[0];
        
        // Validações
        if (!agencia_id) {
            mostrarMensagem('error', 'Selecione uma agência');
            document.getElementById('agencia_id').focus();
            return false;
        }
        
        if (!empresa) {
            mostrarMensagem('error', 'Nome da empresa é obrigatório');
            document.getElementById('empresa').focus();
            return false;
        }
        
        if (!titulo) {
            mostrarMensagem('error', 'Título da vaga é obrigatório');
            document.getElementById('titulo').focus();
            return false;
        }
        
        if (!data_inicio) {
            mostrarMensagem('error', 'Data de início é obrigatória');
            document.getElementById('data_inicio').focus();
            return false;
        }
        
        if (!data_fim) {
            mostrarMensagem('error', 'Data de término é obrigatória');
            document.getElementById('data_fim').focus();
            return false;
        }
        
        // Validar datas
        const inicio = new Date(data_inicio);
        const fim = new Date(data_fim);
        
        if (fim < inicio) {
            mostrarMensagem('error', 'Data de término deve ser posterior à data de início');
            document.getElementById('data_fim').focus();
            return false;
        }
        
        // Validar imagem (apenas se for nova vaga)
        if (editandoId === 0 && !imagem) {
            mostrarMensagem('error', 'Imagem da vaga é obrigatória');
            document.getElementById('imagem').focus();
            return false;
        }
        
        return true;
    }
    
    /**
     * Carregar vagas
     */
    function carregarVagas() {
        console.log('Carregando vagas...');
        
        const url = BASE_URL + 'listar_vagas.php';
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                console.log('Vagas carregadas:', data);
                
                if (data.success) {
                    vagasData = data.vagas;
                    renderizarTabela();
                    atualizarContador();
                } else {
                    mostrarMensagem('error', data.message);
                }
            })
            .catch(error => {
                console.error('Erro ao carregar vagas:', error);
                mostrarMensagem('error', 'Erro ao carregar vagas');
            });
    }
    
    /**
     * Renderizar tabela de vagas
     */
    function renderizarTabela() {
        const tbody = document.getElementById('vagas-tbody');
        if (!tbody) return;
        
        if (vagasData.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" style="text-align: center; padding: 3rem; color: #999;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📋</div>
                        <p><strong>Nenhuma vaga cadastrada</strong></p>
                        <p>Cadastre a primeira vaga usando o formulário acima.</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        let html = '';
        
        vagasData.forEach(vaga => {
            const statusClass = vaga.status == 1 ? 'ativo' : 'inativo';
            const statusTexto = vaga.status == 1 ? 'Ativo' : 'Inativo';
            const statusBtnTexto = vaga.status == 1 ? 'Desativar' : 'Ativar';
            const statusBtnNovoValor = vaga.status == 1 ? 0 : 1;
            
            const expirada = vaga.expirada;
            const diasRestantes = vaga.dias_restantes;
            
            let badgeStatus = `<span class="status-badge-vaga ${statusClass}">${statusTexto}</span>`;
            
            if (expirada) {
                badgeStatus += ` <span class="status-badge-vaga expirado">Expirada</span>`;
            }
            
            if (vaga.destaque == 1) {
                badgeStatus += ` <span class="status-badge-vaga destaque">⭐ Destaque</span>`;
            }
            
            const diasClass = diasRestantes <= 7 ? 'urgente' : '';
            
            html += `
                <tr>
                    <td><img src="${BASE_URL}${escapeHtml(vaga.imagem)}" alt="${escapeHtml(vaga.titulo)}" class="vaga-thumbnail"></td>
                    <td><strong>${escapeHtml(vaga.titulo)}</strong><br><small>${escapeHtml(vaga.empresa)}</small></td>
                    <td>${escapeHtml(vaga.agencia_sigla || vaga.agencia_nome)}</td>
                    <td>${formatarData(vaga.data_inicio)}<br><small>até ${formatarData(vaga.data_fim)}</small></td>
                    <td><span class="dias-restantes ${diasClass}">${diasRestantes} dia${diasRestantes != 1 ? 's' : ''}</span></td>
                    <td>${badgeStatus}</td>
                    <td>${vaga.visualizacoes || 0}</td>
                    <td>
                        <div class="action-buttons-vagas">
                            <button class="action-btn-vagas edit" onclick="window.editarVaga(${vaga.id})">✏️ Editar</button>
                            <button class="action-btn-vagas toggle" onclick="window.toggleStatusVaga(${vaga.id}, ${statusBtnNovoValor})">${statusBtnTexto}</button>
                            <a href="${BASE_URL}${escapeHtml(vaga.imagem)}" target="_blank" class="action-btn-vagas view">👁️ Ver Imagem</a>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }
    
    /**
     * Editar vaga
     */
    window.editarVaga = function(id) {
        console.log('Editando vaga:', id);
        
        const vaga = vagasData.find(v => v.id == id);
        if (!vaga) {
            mostrarMensagem('error', 'Vaga não encontrada');
            return;
        }
        
        editandoId = id;
        
        // Preencher formulário
        document.getElementById('agencia_id').value = vaga.agencia_id;
        document.getElementById('codigo_vaga').value = vaga.codigo_vaga || '';
        document.getElementById('empresa').value = vaga.empresa;
        document.getElementById('titulo').value = vaga.titulo;
        document.getElementById('descricao').value = vaga.descricao || '';
        document.getElementById('requisitos').value = vaga.requisitos || '';
        document.getElementById('atividades').value = vaga.atividades || '';
        document.getElementById('curso').value = vaga.curso || '';
        document.getElementById('nivel_escolar').value = vaga.nivel_escolar || '';
        document.getElementById('area_profissional').value = vaga.area_profissional || '';
        document.getElementById('localidade').value = vaga.localidade || '';
        document.getElementById('horario').value = vaga.horario || '';
        
        if (vaga.bolsa_valor) {
            document.getElementById('bolsa_valor').value = formatarMoeda(vaga.bolsa_valor);
        }
        
        document.getElementById('bolsa_beneficios').value = vaga.bolsa_beneficios || '';
        document.getElementById('url_vaga').value = vaga.url_vaga || '';
        document.getElementById('email_contato').value = vaga.email_contato || '';
        document.getElementById('telefone_contato').value = vaga.telefone_contato || '';
        document.getElementById('data_inicio').value = vaga.data_inicio;
        document.getElementById('data_fim').value = vaga.data_fim;
        document.getElementById('destaque').checked = vaga.destaque == 1;
        
        // Mostrar preview da imagem atual
        const previewDiv = document.getElementById('preview-imagem');
        if (previewDiv && vaga.imagem) {
            previewDiv.innerHTML = '<img src="' + BASE_URL + escapeHtml(vaga.imagem) + '" alt="Imagem atual">';
            previewDiv.style.display = 'block';
        }
        
        // Atualizar texto do botão
        const btnSalvar = document.querySelector('.ifsp-btn-primary-vagas');
        if (btnSalvar) {
            btnSalvar.textContent = '💾 Atualizar Vaga';
        }
        
        // Scroll para o formulário
        document.getElementById('form-vaga').scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        mostrarMensagem('success', 'Editando vaga. Altere os campos e clique em Atualizar.');
    };
    
    /**
     * Toggle status da vaga
     */
    window.toggleStatusVaga = function(id, novoStatus) {
        const statusTexto = novoStatus == 1 ? 'ativar' : 'desativar';
        
        if (!confirm(`Deseja realmente ${statusTexto} esta vaga?`)) {
            return;
        }
        
        console.log('Alternando status da vaga:', id, 'para', novoStatus);
        
        mostrarLoading(true);
        
        const url = BASE_URL + 'toggle_vaga.php';
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id + '&status=' + novoStatus
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarMensagem('success', data.message);
                carregarVagas();
            } else {
                mostrarMensagem('error', data.message);
            }
        })
        .catch(error => {
            console.error('Erro ao alterar status:', error);
            mostrarMensagem('error', 'Erro ao alterar status da vaga');
        })
        .finally(() => {
            mostrarLoading(false);
        });
    };
    
    /**
     * Limpar formulário
     */
    function limparFormulario() {
        editandoId = 0;
        document.getElementById('form-vaga').reset();
        
        // Limpar nomes de arquivo
        const nomeImagem = document.getElementById('nome-imagem');
        const nomePdf = document.getElementById('nome-pdf');
        if (nomeImagem) nomeImagem.textContent = 'Nenhum arquivo selecionado';
        if (nomePdf) nomePdf.textContent = 'Nenhum arquivo selecionado';
        
        // Limpar preview
        const previewDiv = document.getElementById('preview-imagem');
        if (previewDiv) {
            previewDiv.style.display = 'none';
            previewDiv.innerHTML = '';
        }
        
        // Restaurar texto do botão
        const btnSalvar = document.querySelector('.ifsp-btn-primary-vagas');
        if (btnSalvar) {
            btnSalvar.textContent = '💾 Cadastrar Vaga';
        }
    }
    
    /**
     * Atualizar contador
     */
    function atualizarContador() {
        const contador = document.getElementById('contador-vagas');
        if (!contador) return;
        
        const total = vagasData.length;
        const ativas = vagasData.filter(v => v.status == 1 && !v.expirada).length;
        
        contador.textContent = `${total} vaga${total != 1 ? 's' : ''} cadastrada${total != 1 ? 's' : ''} | ${ativas} ativa${ativas != 1 ? 's' : ''}`;
    }
    
    /**
     * Mostrar mensagem
     */
    function mostrarMensagem(tipo, mensagem) {
        const container = document.getElementById('mensagem-container');
        if (!container) return;
        
        const div = document.createElement('div');
        div.className = `alert-message-vagas ${tipo}`;
        div.textContent = mensagem;
        
        container.appendChild(div);
        
        // Auto-hide após 5 segundos
        setTimeout(() => {
            div.style.opacity = '0';
            setTimeout(() => {
                container.removeChild(div);
            }, 300);
        }, 5000);
    }
    
    /**
     * Mostrar/ocultar loading
     */
    function mostrarLoading(mostrar) {
        let overlay = document.getElementById('loading-overlay-vagas');
        
        if (mostrar) {
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'loading-overlay-vagas';
                overlay.className = 'loading-overlay-vagas';
                overlay.innerHTML = '<div class="loading-spinner-vagas"></div>';
                document.body.appendChild(overlay);
            }
            overlay.style.display = 'flex';
        } else {
            if (overlay) {
                overlay.style.display = 'none';
            }
        }
    }
    
    /**
     * Formatações
     */
    function formatarData(data) {
        if (!data) return '-';
        const partes = data.split('-');
        return `${partes[2]}/${partes[1]}/${partes[0]}`;
    }
    
    function formatarMoeda(valor) {
        return 'R$ ' + parseFloat(valor).toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
})();