// Máscara de reais para campos com data-reais: os dígitos entram pelos centavos,
// como em maquininha de cartão ("1" → 0,01, "12345" → 123,45, "123456" → 1.234,56).
(() => {
    const MAXIMO_DE_DIGITOS = 12; // limite da coluna decimal(12,2)

    const formatar = (texto) => {
        const digitos = texto.replace(/\D/g, '').replace(/^0+(?=\d)/, '').slice(0, MAXIMO_DE_DIGITOS);

        if (digitos === '') {
            return '';
        }

        const preenchido = digitos.padStart(3, '0');
        const inteiros = preenchido.slice(0, -2).replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        return inteiros + ',' + preenchido.slice(-2);
    };

    // O valor que volta do servidor após um erro vem como "1234.5"; completa os centavos antes de mascarar.
    const prepararValorInicial = (texto) => /^\d+(\.\d{1,2})?$/.test(texto) ? Number(texto).toFixed(2) : texto;

    document.querySelectorAll('[data-reais]').forEach((campo) => {
        campo.value = formatar(prepararValorInicial(campo.value));
    });

    document.addEventListener('input', (evento) => {
        const campo = evento.target;

        if (!campo.matches || !campo.matches('[data-reais]')) {
            return;
        }

        const mascarado = formatar(campo.value);
        // Apagar até zerar limpa o campo; senão um valor opcional nunca voltaria a ficar vazio.
        const apagouTudo = (evento.inputType || '').startsWith('delete') && /^0,00$/.test(mascarado);

        campo.value = apagouTudo ? '' : mascarado;
    });
})();
