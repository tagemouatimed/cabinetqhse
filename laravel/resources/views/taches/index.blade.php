@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Tâches &amp; rappels</h1>
            <div class="section-header-button">
                <a href="{{ route('taches.export') }}" class="btn btn-success">Exporter Excel</a>
            </div>
        </div>

        <div class="kpi-row">
            <div class="kpi-card" style="--accent:var(--c-taches)">
                <div class="kpi-value">{{ $stats['total'] }}</div>
                <div class="kpi-label">Tâches libres</div>
            </div>
            <div class="kpi-card" style="--accent:#9ca3af">
                <div class="kpi-value">{{ $stats['a_faire'] }}</div>
                <div class="kpi-label">À faire</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-dashboard)">
                <div class="kpi-value">{{ $stats['en_cours'] }}</div>
                <div class="kpi-label">En cours</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-recouvrement)">
                <div class="kpi-value">{{ $stats['en_retard'] }}</div>
                <div class="kpi-label">En retard</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header card-header-green"><h5 class="mb-0">Nouvelle tâche libre</h5></div>
            <div class="card-body">
                <form action="{{ route('taches.store') }}" method="POST" class="form-inline">
                    @csrf
                    <input type="text" name="titre" class="form-control mr-2" placeholder="Titre" required>
                    <input type="date" name="date_echeance" class="form-control mr-2">
                    <input type="text" name="responsable" class="form-control mr-2" placeholder="Responsable">
                    <button class="btn btn-primary btn-sm">Ajouter</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header card-header-green"><h5 class="mb-0">Tâches libres</h5></div>
            <div class="card-body">
                <div class="filter-btns">
                    <a href="{{ route('taches.index') }}" class="{{ !request('statut') ? 'active' : '' }}">Toutes</a>
                    <a href="{{ route('taches.index', ['statut'=>'a_faire']) }}" class="{{ request('statut')==='a_faire' ? 'active' : '' }}">À faire</a>
                    <a href="{{ route('taches.index', ['statut'=>'en_cours']) }}" class="{{ request('statut')==='en_cours' ? 'active' : '' }}">En cours</a>
                    <a href="{{ route('taches.index', ['statut'=>'faite']) }}" class="{{ request('statut')==='faite' ? 'active' : '' }}">Faites</a>
                    <a href="{{ route('taches.index', ['statut'=>'en_retard']) }}" class="{{ request('statut')==='en_retard' ? 'active' : '' }}">En retard</a>
                </div>
                <table class="table table-striped" id="table-taches">
                    <thead><tr><th>Titre</th><th>Responsable</th><th>Échéance</th><th>Statut</th><th>Actions</th></tr></thead>
                    <tbody>
                        @foreach ($taches as $t)
                        <tr class="{{ $t->est_en_retard ? 'table-danger' : '' }}">
                            <td>{{ $t->titre }}</td>
                            <td>{{ $t->responsable }}</td>
                            <td>{{ $t->date_echeance?->format('d/m/Y') }}</td>
                            <td>
                                <form action="{{ route('taches.statut', $t) }}" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <select name="statut" class="form-control form-control-sm d-inline w-auto" onchange="this.form.submit()">
                                        @foreach (['a_faire','en_cours','faite'] as $s)
                                        <option value="{{ $s }}" @selected($t->statut===$s)>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('taches.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette tâche ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-icon"><i data-feather="trash-2"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header card-header-indigo"><h5 class="mb-0">Actions en cours (plans d'action, tous projets)</h5></div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead><tr><th>Action</th><th>Projet</th><th>Responsable</th><th>Échéance</th><th>Statut</th></tr></thead>
                    <tbody>
                        @foreach ($actions as $a)
                        <tr class="{{ $a->est_en_retard ? 'table-danger' : '' }}">
                            <td>{{ $a->titre }}</td>
                            <td><a href="{{ route('projets.show', $a->planAction->projet) }}">{{ $a->planAction->projet->nom }}</a></td>
                            <td>{{ $a->responsable }}</td>
                            <td>{{ $a->date_echeance?->format('d/m/Y') }}</td>
                            <td>{{ $a->statut }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header card-header-violet"><h5 class="mb-0">Audits planifiés à venir</h5></div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead><tr><th>Client</th><th>Norme</th><th>Date</th></tr></thead>
                    <tbody>
                        @foreach ($audits as $a)
                        <tr class="{{ $a->date_planifiee->isPast() ? 'table-danger' : '' }}">
                            <td>{{ $a->client->nom_complet }}</td>
                            <td>{{ $a->norme->code }}</td>
                            <td><a href="{{ route('audits.show', $a) }}">{{ $a->date_planifiee->format('d/m/Y') }}</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header card-header-pink"><h5 class="mb-0">Formations planifiées à venir</h5></div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead><tr><th>Thème</th><th>Client</th><th>Date</th></tr></thead>
                    <tbody>
                        @foreach ($formations as $f)
                        <tr class="{{ $f->date_planifiee->isPast() ? 'table-danger' : '' }}">
                            <td>{{ $f->theme }}</td>
                            <td>{{ $f->client?->nom_complet ?? 'Interne' }}</td>
                            <td><a href="{{ route('formations.show', $f) }}">{{ $f->date_planifiee->format('d/m/Y') }}</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
@push('scripts')<script>$(function(){$('#table-taches').DataTable();});</script>@endpush
