@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"><h1>Planifier un audit</h1></div>
        <div class="card"><div class="card-body">
            <form action="{{ route('audits.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Client *</label>
                        <select name="client_id" class="form-control" required>
                            <option value="">-- Choisir --</option>
                            @foreach ($clients as $c)<option value="{{ $c->id }}">{{ $c->nom_complet }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Norme *</label>
                        <select name="norme_id" class="form-control" required>
                            <option value="">-- Choisir --</option>
                            @foreach ($normes as $n)<option value="{{ $n->id }}">{{ $n->code }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Type *</label>
                        <select name="type" class="form-control" id="select-type" required>
                            <option value="ponctuel">Nouvelle demande (ponctuel)</option>
                            <option value="recurrent">Récurrent</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Date planifiée *</label>
                        <input type="date" name="date_planifiee" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4" id="champ-recurrence" style="display:none">
                        <label>Récurrence (mois)</label>
                        <input type="number" name="recurrence_mois" class="form-control" placeholder="ex: 12 pour annuel">
                    </div>
                </div>
                <button class="btn btn-primary">Planifier</button>
                <a href="{{ route('audits.index') }}" class="btn btn-light">Annuler</a>
            </form>
        </div></div>
    </section>
</div>
@endsection
@push('scripts')
<script>
    document.getElementById('select-type').addEventListener('change', function () {
        document.getElementById('champ-recurrence').style.display = this.value === 'recurrent' ? 'block' : 'none';
    });
</script>
@endpush
