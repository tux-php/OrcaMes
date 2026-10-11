<?php

// Só as regras usadas pelo sistema; as demais caem no texto padrão em inglês.
return [
    'between' => [
        'numeric' => 'O campo :attribute deve ficar entre :min e :max.',
    ],
    'confirmed' => 'A confirmação de :attribute não confere.',
    'current_password' => 'A senha atual está incorreta.',
    'date' => 'O campo :attribute não é uma data válida.',
    'date_format' => 'O campo :attribute não está no formato esperado.',
    'decimal' => 'O campo :attribute deve ser um valor em reais, como 1.234,56.',
    'different' => 'A nova senha deve ser diferente da atual.',
    'email' => 'Informe um e-mail válido.',
    'enum' => 'A opção escolhida em :attribute é inválida.',
    'exists' => 'A opção escolhida em :attribute é inválida.',
    'file' => 'O campo :attribute deve ser um arquivo.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'max' => [
        'file' => 'O arquivo não pode passar de :max KB.',
        'string' => 'O campo :attribute aceita no máximo :max caracteres.',
    ],
    'mimes' => 'O arquivo deve ser de um destes tipos: :values.',
    'min' => [
        'numeric' => 'O campo :attribute não pode ser menor que :min.',
        'string' => 'O campo :attribute precisa de pelo menos :min caracteres.',
    ],
    'required' => 'O campo :attribute é obrigatório.',
    'string' => 'O campo :attribute deve ser um texto.',
    'uploaded' => 'Falha ao enviar o arquivo. Confira o tamanho (até 5 MB).',
    'unique' => 'Já existe um registro com este :attribute.',

    'attributes' => [
        'ano_base' => 'ano-base',
        'arquivo' => 'arquivo',
        'categoria_id' => 'categoria',
        'conta_id' => 'conta',
        'descricao' => 'descrição',
        'email' => 'e-mail',
        'mes' => 'mês de referência',
        'observacao' => 'observação',
        'pago_em' => 'pago em',
        'password' => 'senha',
        'senha_atual' => 'senha atual',
        'usuario_id' => 'responsável',
    ],
];
