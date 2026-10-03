@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Formations</h1>
            <div class="section-header-button">
                <a href="{{ route('formations.create') }}" class="btn btn-primary">Planifier une formation</a>
                <a href="{{ route('formations.export') }}" class="btn btn-success">Exporter Excel</a>
            </div>
        </div>

        <div class="kpi-row">
            <div class="kpi-card" style="--accent:var(--c-formation)">
                <div class="kpi-value">{{ $stats['total'] }}</div>
                <div class="kpi-label">Formations au total</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-dashboard)">
                <div class="kpi-value">{{ $stats['planifiee'] }}</div>
                <div class="kpi-label">Planifiées</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-taches)">
                <div class="kpi-value">{{ $stats['realisee'] }}</div>
                <div class="kpi-label">Réalisées</div>
            </div>
        </div>

        <div class="filter-btns">
            <a href="{{ route('formations.index') }}" class="{{ !request('statut') ? 'active' : '' }}">Toutes</a>
            <a href="{{ route('formations.index', ['statut'=>'planifiee']) }}" class="{{ request('statut')==='planifiee' ? 'active' : '' }}">Planifiées</a>
            <a href="{{ route('formations.index', ['statut'=>'realisee']) }}" class="{{ request('statut')==='realisee' ? 'active' : '' }}">Réalisées</a>
        </div>

        <div class="card"><div class="card-body">
            <table class="table table-striped" id="table-formations">
                <thead><tr><th>Thème</th><th>Client</th><th>Formateur</th><th>Date</th><th>Participants</th><th>Statut</th><th></th></tr></thead>
                <tbody>
                    @foreach ($formations as $f)
                    <tr>
                        <td>{{ $f->theme }}</td>
                        <td>{{ $f->client?->nom_complet ?? 'Interne' }}</td>
                        <td>{{ $f->formateur }}</td>
                        <td>{{ $f->date_planifiee->format('d/m/Y') }}</td>
                        <td>{{ $f->participants_count }}</td>
                        <td><span class="badge badge-info">{{ $f->statut }}</span></td>
                        <td><a href="{{ route('formations.show', $f) }}" class="btn btn-sm btn-icon"><i data-feather="eye"></i></a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div></div>
    </section>
</div>
@endsection
@push('scripts')<script>$(function(){$('#table-formations').DataTable();});</script>@endpush
