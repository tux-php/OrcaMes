// Busca em listas longas para selects com data-busca: troca a caixa por um campo de texto
// que filtra as opções enquanto se digita ("agua fer" acha "Água (Fernando)"). O select
// original continua no formulário, escondido, e é ele que vai para o servidor.
(() => {
    // Ignora maiúsculas e acentos na comparação.
    const normalizar = (texto) => texto.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim();

    let contador = 0;

    const montar = (select) => {
        const opcoes = [...select.options].filter((opcao) => opcao.value !== '')
            .map((opcao) => ({ opcao, chave: normalizar(opcao.text) }));
        const idLista = 'busca-lista-' + (++contador);

        const caixa = document.createElement('div');
        caixa.className = 'busca-select';

        const campo = document.createElement('input');
        campo.type = 'text';
        campo.className = select.className;
        campo.id = select.id;
        campo.placeholder = 'Digite para buscar…';
        campo.autocomplete = 'off';
        campo.spellcheck = false;
        campo.required = select.required;
        campo.setAttribute('role', 'combobox');
        campo.setAttribute('aria-autocomplete', 'list');
        campo.setAttribute('aria-expanded', 'false');
        campo.setAttribute('aria-controls', idLista);

        const lista = document.createElement('ul');
        lista.className = 'dropdown-menu';
        lista.id = idLista;
        lista.setAttribute('role', 'listbox');

        // O rótulo passa a apontar para o campo de texto.
        select.removeAttribute('id');
        select.hidden = true;
        select.tabIndex = -1;
        select.before(caixa);
        caixa.append(campo, lista, select);

        let visiveis = [];
        let ativo = -1;
        let anterior = select.value;

        const textoEscolhido = () => select.value === '' ? '' : select.selectedOptions[0].text;

        const validar = () => campo.setCustomValidity(select.required && select.value === '' ? 'Escolha uma conta da lista.' : '');

        const marcar = (indice) => {
            ativo = indice;
            [...lista.children].forEach((item, i) => {
                const marcado = i === ativo;
                item.firstChild?.classList?.toggle('active', marcado);
                item.setAttribute('aria-selected', marcado ? 'true' : 'false');

                if (marcado) {
                    item.scrollIntoView({ block: 'nearest' });
                    campo.setAttribute('aria-activedescendant', item.id);
                }
            });

            if (ativo < 0) {
                campo.removeAttribute('aria-activedescendant');
            }
        };

        // A lista fica presa à janela, não ao cartão (que corta o que passa da borda), e abre
        // para o lado com mais espaço. Na folha do celular ela segue o fluxo normal (ver app.css).
        const NA_FOLHA = caixa.closest('.offcanvas') !== null;
        const MARGEM = 8;
        const ALTURA_MAXIMA = 320;

        const posicionar = () => {
            if (NA_FOLHA || !lista.classList.contains('show')) {
                return;
            }

            const area = campo.getBoundingClientRect();
            const abaixo = window.innerHeight - area.bottom - MARGEM;
            const acima = area.top - MARGEM;
            const paraCima = abaixo < 200 && acima > abaixo;

            lista.style.left = area.left + 'px';
            lista.style.width = area.width + 'px';
            lista.style.maxHeight = Math.min(ALTURA_MAXIMA, paraCima ? acima : abaixo) + 'px';
            lista.style.top = paraCima ? '' : area.bottom + 2 + 'px';
            lista.style.bottom = paraCima ? window.innerHeight - area.top + 2 + 'px' : '';
        };

        window.addEventListener('scroll', posicionar, true);
        window.addEventListener('resize', posicionar);

        const fechar = () => {
            lista.classList.remove('show');
            campo.setAttribute('aria-expanded', 'false');
            marcar(-1);
        };

        const desenhar = () => {
            const termos = normalizar(campo.value).split(/\s+/).filter(Boolean);
            visiveis = opcoes.filter(({ chave }) => termos.every((termo) => chave.includes(termo)));
            lista.replaceChildren();

            if (visiveis.length === 0) {
                const vazio = document.createElement('li');
                vazio.innerHTML = '<span class="dropdown-item-text text-muted">Nenhuma conta encontrada</span>';
                lista.append(vazio);
            }

            visiveis.forEach(({ opcao }, i) => {
                const item = document.createElement('li');
                item.id = idLista + '-' + i;
                item.setAttribute('role', 'option');
                const botao = document.createElement('button');
                botao.type = 'button';
                botao.tabIndex = -1;
                botao.className = 'dropdown-item';
                botao.textContent = opcao.text;
                item.append(botao);
                lista.append(item);
            });

            lista.classList.add('show');
            posicionar();
            campo.setAttribute('aria-expanded', 'true');
            marcar(visiveis.length > 0 && termos.length > 0 ? 0 : -1);
        };

        const escolher = (i) => {
            select.value = anterior = visiveis[i].opcao.value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            campo.value = visiveis[i].opcao.text;
            validar();
            fechar();
        };

        const restaurar = () => {
            select.value = anterior;
            campo.value = textoEscolhido();
            validar();
            fechar();
        };

        campo.value = textoEscolhido();
        validar();

        campo.addEventListener('focus', () => {
            anterior = select.value;
            campo.select();
            desenhar();
        });

        campo.addEventListener('input', () => {
            select.value = '';
            validar();
            desenhar();
        });

        campo.addEventListener('keydown', (evento) => {
            const aberta = lista.classList.contains('show');

            if (evento.key === 'ArrowDown' || evento.key === 'ArrowUp') {
                evento.preventDefault();

                if (!aberta) {
                    desenhar();
                }

                if (visiveis.length > 0) {
                    const passo = evento.key === 'ArrowDown' ? 1 : -1;
                    marcar((ativo + passo + visiveis.length) % visiveis.length);
                }
            } else if (evento.key === 'Enter' && aberta && ativo >= 0) {
                // Enter escolhe a conta em vez de enviar o formulário.
                evento.preventDefault();
                escolher(ativo);
            } else if (evento.key === 'Escape' && aberta) {
                // Não deixa o Esc fechar também a folha de lançamento no celular.
                evento.stopPropagation();
                restaurar();
            }
        });

        // Mantém o foco no campo ao clicar na lista, senão ela some antes do clique valer.
        lista.addEventListener('mousedown', (evento) => evento.preventDefault());

        lista.addEventListener('click', (evento) => {
            const item = evento.target.closest('[role="option"]');

            if (item) {
                escolher([...lista.children].indexOf(item));
            }
        });

        campo.addEventListener('blur', () => {
            // Se o texto deixou uma só conta, escolhe ela; se não bate com nenhuma, volta à escolha anterior.
            if (select.value === '' && visiveis.length === 1 && campo.value.trim() !== '') {
                escolher(0);
            } else if (select.value === '') {
                restaurar();
            } else {
                fechar();
            }
        });
    };

    document.querySelectorAll('select[data-busca]').forEach(montar);
})();
