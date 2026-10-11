-- Dump mínimo no formato do phpMyAdmin, com os casos que a importação precisa tratar.

INSERT INTO `autenticacao_user` (`id_autenticacao_user`, `email`, `senha`, `d_e_l_e_t_e`, `id_usuario`) VALUES
(1, 'Ana@Example.com', 'x', NULL, 1),
(2, 'bia@example.com', 'x', NULL, 2),
(3, 'saiu@example.com', 'x', 'S', 3),
(4, 'vazio@example.com', 'x', NULL, 4);

INSERT INTO `mes_referencia` (`id_mes_referencia`, `chave`, `descricao`, `salario_extra`, `d_e_l_e_t_e`) VALUES
(10, 'AGO/2026', 'AGOSTO/2026', NULL, NULL),
(11, 'SET/2026', 'SETEMBRO/2026', NULL, NULL);

INSERT INTO `status_pagamento` (`id_status_pagamento`, `chave`, `descricao`, `d_e_l_e_t_e`) VALUES
(3, 'PG', 'Pagamento Efetuado', NULL),
(4, 'NPG', 'Pagamento não Efetuado', NULL);

INSERT INTO `usuario` (`id_usuario`, `nome`, `sobrenome`, `salario`, `id_orgao_pagador`, `id_status_usuario`, `d_e_l_e_t_e`) VALUES
(1, 'ana', 'silva', '5000.00', 1, 3, NULL),
(2, 'bia', NULL, '4000.00', 1, 3, NULL),
(3, 'saiu', 'x', '1.00', 1, 3, 'S'),
(4, 'vazio', 'x', '1.00', 1, 3, NULL);

INSERT INTO `tipo_despesa` (`id_tipo_despesa`, `id_usuario`, `chave`, `descricao`, `d_e_l_e_t_e`) VALUES
(1, 1, 'CASA', 'CASA', NULL),
(2, 2, 'casa', 'casa', NULL),
(3, 2, 'FIL', 'Filho d\'água; (teste)', NULL),
(4, 2, 'CASA2', 'Cása', NULL);

INSERT INTO `tipo_pagamento` (`id_tipo_pagamento`, `descricao`, `id_tipo_despesa`, `d_e_l_e_t_e`) VALUES
(1, 'Aluguel', 1, NULL),
(2, ' Escola', 3, NULL),
(3, 'Antiga', NULL, 'S'),
(4, 'Nunca usada', 2, NULL);

INSERT INTO `usuario_tipo_pagamento` (`id_usuario_tipo_pagamento`, `id_usuario`, `id_tipo_pagamento`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 2, 4);

INSERT INTO `pagamentos` (`id_pagamento`, `id_tipo_pagamento`, `valor_pagamento`, `data_lancamento`, `id_usuario`, `id_status_pagamento`, `data_processamento`, `id_mes_referencia`, `ch_clone`, `d_e_l_e_t_e`) VALUES
(1, 1, '1500.00', '2026-08-01', 1, 3, '2026-08-05', 10, 'S', NULL),
(2, 1, '1550.00', '2026-09-01', 1, 4, NULL, 11, 'S', NULL),
(3, 3, '80.00', '2026-08-02', 1, 4, '2026-08-03', 10, 'S', NULL),
(4, 1, '9999.00', '2026-09-01', 1, 3, '2026-09-02', 11, 'S', 'S'),
(5, 2, '700.50', '2026-08-10', 2, 3, '2026-08-11', 10, 'S', NULL),
(6, 1, '1.00', '2026-08-10', 3, 3, '2026-08-11', 10, 'S', NULL);

INSERT INTO `pagamento_extra` (`id_pag_extra`, `id_usuario`, `id_mes_referencia`, `valor_extra`) VALUES
(1, 1, 10, '100.00'),
(2, 1, 10, '0.00'),
(3, 1, 10, '300.00'),
(4, 2, 10, '50.00'),
(5, 2, 10, '0.00');

INSERT INTO `imposto_renda` (`id_ir`, `descricao`, `imagem`, `valor`) VALUES
(1, 'teste', 0x6f692e6a7067, NULL);
