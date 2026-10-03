@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"><h1>Modifier client</h1></div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('clients.update', $client) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Nom *</label>
                            <input type="text" name="nom" class="form-control" value="{{ old('nom', $client->nom) }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="{{ old('prenom', $client->prenom) }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Raison sociale (si personne morale)</label>
                            <input type="text" name="raison_sociale" class="form-control" value="{{ old('raison_sociale', $client->raison_sociale) }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>ICE</label>
                            <input type="text" name="ice" class="form-control" value="{{ old('ice', $client->ice) }}">
                        </div>
                        <div class="form-group col-md-12">
                            <label>Adresse</label>
                            <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $client->adresse) }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Secteur d'activité</label>
                            <input type="text" name="secteur_activite" class="form-control" value="{{ old('secteur_activite', $client->secteur_activite) }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Téléphone</label>
                            <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $client->telephone) }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}">
                        </div>
                        <div class="form-group col-md-12">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $client->notes) }}</textarea>
                        </div>
                    </div>
                    <button class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('clients.index') }}" class="btn btn-light">Annuler</a>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
