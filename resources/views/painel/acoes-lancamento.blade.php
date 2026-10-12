{{-- Ações de um lançamento, usadas na tabela e na lista do celular ($celular = botão de pagar maior). --}}
@php($celular ??= false)
<div class="acoes">
    <form method="POST" action="{{ route($lancamento->estaPago() ? 'lancamentos.estornar' : 'lancamentos.pagar', $lancamento) }}" class="d-inline">
        @csrf
        @method('PATCH')
        <button @class(['btn', 'btn-sm' => ! $celular || $lancamento->estaPago(), $lancamento->estaPago() ? 'btn-outline-secondary' : 'btn-success'])>{{ $lancamento->estaPago() ? 'Estornar' : 'Pagar' }}</button>
    </form>
    <a class="btn btn-sm btn-outline-primary" href="{{ route('lancamentos.edit', $lancamento) }}">Editar</a>
    <form method="POST" action="{{ route('lancamentos.destroy', $lancamento) }}" class="d-inline" onsubmit="return confirm('Excluir este lançamento?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-outline-danger">Excluir</button>
    </form>
</div>
