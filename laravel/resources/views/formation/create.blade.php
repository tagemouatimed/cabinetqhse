@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"><h1>Planifier une formation</h1></div>
        <div class="card"><div class="card-body">
            <form action="{{ route('formations.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Thème *</label>
                        <input type="text" name="theme" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Client (laisser vide si formation interne)</label>
                        <select name="client_id" class="form-control">
                            <option value="">-- Interne --</option>
                            @foreach ($clients as $c)<option value="{{ $c->id }}">{{ $c->nom_complet }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Formateur</label>
                        <input type="text" name="formateur" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Date *</label>
                        <input type="date" name="date_planifiee" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Durée (heures)</label>
                        <input type="number" name="duree_heures" class="form-control">
                    </div>
                </div>
                <button class="btn btn-primary">Planifier</button>
                <a href="{{ route('formations.index') }}" class="btn btn-light">Annuler</a>
            </form>
        </div></div>
    </section>
</div>
@endsection
