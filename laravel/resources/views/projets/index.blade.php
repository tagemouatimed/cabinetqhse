@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Projets d'accompagnement</h1>
            <div class="section-header-button">
                <a href="{{ route('projets.create') }}" class="btn btn-primary">Nouveau projet</a>
                <a href="{{ route('projets.export') }}" class="btn btn-success">Exporter Excel</a>
            </div>
        </div>

        <div class="kpi-row">
            <div class="kpi-card" style="--accent:var(--c-projets)">
                <div class="kpi-value">{{ $stats['total'] }}</div>
                <div class="kpi-label">Projets au total</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-dashboard)">
                <div class="kpi-value">{{ $stats['en_cours'] }}</div>
                <div class="kpi-label">En cours</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-recouvrement)">
                <div class="kpi-value">{{ $stats['en_retard'] }}</div>
                <div class="kpi-label">En retard</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-taches)">
                <div class="kpi-value">{{ $stats['termine'] }}</div>
                <div class="kpi-label">Terminés</div>
            </div>
        </div>

        <div class="filter-btns">
            <a href="{{ route('projets.index') }}" class="{{ !request('statut') ? 'active' : '' }}">Tous</a>
            <a href="{{ route('projets.index', ['statut' => 'planifie']) }}" class="{{ request('statut')==='planifie' ? 'active' : '' }}">Planifiés</a>
            <a href="{{ route('projets.index', ['statut' => 'en_cours']) }}" class="{{ request('statut')==='en_cours' ? 'active' : '' }}">En cours</a>
            <a href="{{ route('projets.index', ['statut' => 'en_retard']) }}" class="{{ request('statut')==='en_retard' ? 'active' : '' }}">En retard</a>
            <a href="{{ route('projets.index', ['statut' => 'termine']) }}" class="{{ request('statut')==='termine' ? 'active' : '' }}">Terminés</a>
        </div>

        <div class="card"><div class="card-body">
            <table class="table table-striped" id="table-projets">
                <thead><tr><th>Projet</th><th>Client</th><th>Norme</th><th>Statut</th><th>Avancement</th><th>Fin prévue</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach ($projets as $p)
                    <tr class="{{ $p->est_en_retard ? 'table-danger' : '' }}">
                        <td>{{ $p->nom }}</td>
                        <td>{{ $p->client->nom_complet }}</td>
                        <td>{{ $p->norme }}</td>
                        <td><span class="badge badge-info">{{ ucfirst(str_replace('_',' ',$p->statut)) }}</span></td>
                        <td>{{ $p->avancement }}%</td>
                        <td>{{ $p->date_fin_prevue?->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('projets.show', $p) }}" class="btn btn-sm btn-icon" title="Voir"><i data-feather="eye"></i></a>
                            <a href="{{ route('projets.edit', $p) }}" class="btn btn-sm btn-icon" title="Modifier"><i data-feather="edit"></i></a>
                            <form action="{{ route('projets.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce projet ? Tous ses plans d\'action, notes et rapports seront aussi supprimés.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-icon" title="Supprimer"><i data-feather="trash-2"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div></div>
    </section>
</div>
@endsection
@push('scripts')<script>$(function(){$('#table-projets').DataTable();});</script>@endpush
