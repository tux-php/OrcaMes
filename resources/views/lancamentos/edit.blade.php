@extends('layouts.app')

@php use App\Support\Dinheiro; @endphp

@section('titulo', 'Editar lançamento — OrcaMes')

@section('conteudo')
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <h1 class="h4 mb-3">Editar lançamento</h1>
            <form method="POST" action="{{ route('lancamentos.update', $lancamento) }}" class="card card-body shadow-sm">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label" for="conta_id">Conta</label>
                    <select class="form-select" id="conta_id" name="conta_id" required>
                        @foreach ($contas as $conta)
                            <option value="{{ $conta->id }}" @selected((int) old('conta_id', $lancamento->conta_id) === $conta->id)>{{ $conta->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="mes">Mês de referência</label>
                        <input class="form-control" type="month" id="mes" name="mes" value="{{ old('mes', $lancamento->competencia->format('Y-m')) }}" required>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="valor">Valor (R$)</label>
                        <input class="form-control" id="valor" name="valor" inputmode="numeric" data-reais autocomplete="off" value="{{ old('valor', Dinheiro::br($lancamento->valor)) }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="pago_em">Pago em (deixe vazio se está em aberto)</label>
                    <input class="form-control" type="date" id="pago_em" name="pago_em" value="{{ old('pago_em', $lancamento->pago_em?->format('Y-m-d')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="observacao">Observação</label>
                    <input class="form-control" id="observacao" name="observacao" maxlength="255" value="{{ old('observacao', $lancamento->observacao) }}">
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary">Salvar</button>
                    <a class="btn btn-outline-secondary" href="{{ route('painel', ['mes' => $lancamento->competencia->format('Y-m')]) }}">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
