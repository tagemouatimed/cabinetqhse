@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"><h1>Nouveau projet</h1></div>
        <div class="card"><div class="card-body">
            <form action="{{ route('projets.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Client *</label>
                        <select name="client_id" class="form-control" required>
                            <option value="">-- Choisir --</option>
                            @foreach ($clients as $c)<option value="{{ $c->id }}">{{ $c->nom_complet }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Nom du projet *</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Norme</label>
                        <input type="text" name="norme" class="form-control" placeholder="ISO 9001...">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Date de début</label>
                        <input type="date" name="date_debut" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Fin prévue</label>
                        <input type="date" name="date_fin_prevue" class="form-control">
                    </div>
                </div>
                <button class="btn btn-primary">Créer</button>
                <a href="{{ route('projets.index') }}" class="btn btn-light">Annuler</a>
            </form>
        </div></div>
    </section>
</div>
@endsection
