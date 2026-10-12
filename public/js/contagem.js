// Contagem animada para valores com data-contagem: o número sobe de zero até o valor
// que o servidor escreveu. Sem JavaScript, ou com "reduzir movimento" ligado, o valor aparece direto.
(() => {
    const DURACAO_MS = 800;
    const elementos = document.querySelectorAll('[data-contagem]');

    if (elementos.length === 0 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const formato = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    // Rápido no começo e suave na chegada.
    const suavizar = (t) => 1 - Math.pow(1 - t, 3);

    elementos.forEach((elemento) => {
        const final = Number(elemento.dataset.contagem);
        const textoFinal = elemento.textContent;

        if (!Number.isFinite(final) || final === 0) {
            return;
        }

        // Leitores de tela recebem o valor final, não cada passo da contagem.
        elemento.setAttribute('aria-label', textoFinal.trim());
        let inicio = null;

        const passo = (agora) => {
            inicio ??= agora;
            const progresso = Math.min((agora - inicio) / DURACAO_MS, 1);

            if (progresso === 1) {
                elemento.textContent = textoFinal;

                return;
            }

            elemento.textContent = 'R$ ' + formato.format(final * suavizar(progresso));
            requestAnimationFrame(passo);
        };

        elemento.textContent = 'R$ ' + formato.format(0);
        requestAnimationFrame(passo);
    });
})();
