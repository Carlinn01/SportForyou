// JavaScript para funcionalidades da página de eventos

document.addEventListener('DOMContentLoaded', function() {
    // Botão "Tenho interesse"
    const botoesInteresse = document.querySelectorAll('.btn-interesse');
    
    botoesInteresse.forEach(btn => {
        btn.addEventListener('click', function() {
            const eventoId = this.dataset.evento;
            
            if (!eventoId) return;
            
            // Desabilita botão temporariamente
            this.disabled = true;
            const textoOriginal = this.innerHTML;
            
            fetch('../api/interessado_evento.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'evento_id=' + eventoId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.interesse) {
                        // Adicionou interesse
                        this.classList.add('ativo');
                        this.innerHTML = '<i class="fa-solid fa-check"></i> Tem interesse';
                    } else {
                        // Removeu interesse
                        this.classList.remove('ativo');
                        this.innerHTML = 'Tenho interesse';
                    }
                    
                    // Atualiza contador de interessados (opcional - pode ser implementado)
                    atualizarContadorInteressados(eventoId);
                } else {
                    alert('Erro: ' + (data.message || 'Erro desconhecido'));
                    this.innerHTML = textoOriginal;
                }
            })
            .catch(error => {
                console.error('Erro ao processar interesse:', error);
                alert('Erro ao processar interesse. Tente novamente.');
                this.innerHTML = textoOriginal;
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });
});

// Função para atualizar contador de interessados (opcional)
function atualizarContadorInteressados(eventoId) {
    // Pode implementar atualização dinâmica do contador se necessário
    // Por enquanto, apenas recarrega a página após um delay
    setTimeout(() => {
        // location.reload();
    }, 1000);
}

