@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"><h1>Modifier {{ $projet->nom }}</h1></div>
        <div class="card"><div class="card-body">
            <form action="{{ route('projets.update', $projet) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Client *</label>
                        <select name="client_id" class="form-control" required>
                            @foreach ($clients as $c)
                            <option value="{{ $c->id }}" @selected($projet->client_id==$c->id)>{{ $c->nom_complet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Nom du projet *</label>
                        <input type="text" name="nom" class="form-control" value="{{ $projet->nom }}" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Norme</label>
                        <input type="text" name="norme" class="form-control" value="{{ $projet->norme }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Statut *</label>
                        <select name="statut" class="form-control" required>
                            @foreach (['planifie'=>'Planifié','en_cours'=>'En cours','termine'=>'Terminé','suspendu'=>'Suspendu'] as $val => $label)
                            <option value="{{ $val }}" @selected($projet->statut==$val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Date de début</label>
                        <input type="date" name="date_debut" class="form-control" value="{{ $projet->date_debut?->format('Y-m-d') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Fin prévue</label>
                        <input type="date" name="date_fin_prevue" class="form-control" value="{{ $projet->date_fin_prevue?->format('Y-m-d') }}">
                    </div>
                </div>
                <button class="btn btn-primary">Enregistrer</button>
                <a href="{{ route('projets.show', $projet) }}" class="btn btn-light">Annuler</a>
            </form>
        </div></div>
    </section>
</div>
@endsection
