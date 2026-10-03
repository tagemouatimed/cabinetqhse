@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Audits</h1>
            <div class="section-header-button">
                <a href="{{ route('audits.create') }}" class="btn btn-primary">Planifier un audit</a>
                <a href="{{ route('normes.index') }}" class="btn btn-light">Normes</a>
                <a href="{{ route('audits.export') }}" class="btn btn-success">Exporter Excel</a>
            </div>
        </div>

        <div class="kpi-row">
            <div class="kpi-card" style="--accent:var(--c-audits)">
                <div class="kpi-value">{{ $stats['total'] }}</div>
                <div class="kpi-label">Audits au total</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-dashboard)">
                <div class="kpi-value">{{ $stats['planifie'] }}</div>
                <div class="kpi-label">Planifiés</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-taches)">
                <div class="kpi-value">{{ $stats['realise'] }}</div>
                <div class="kpi-label">Réalisés</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-recouvrement)">
                <div class="kpi-value">{{ $stats['en_retard'] }}</div>
                <div class="kpi-label">En retard</div>
            </div>
        </div>

        <div class="filter-btns">
            <a href="{{ route('audits.index') }}" class="{{ !request('statut') ? 'active' : '' }}">Tous</a>
            <a href="{{ route('audits.index', ['statut'=>'planifie']) }}" class="{{ request('statut')==='planifie' ? 'active' : '' }}">Planifiés</a>
            <a href="{{ route('audits.index', ['statut'=>'realise']) }}" class="{{ request('statut')==='realise' ? 'active' : '' }}">Réalisés</a>
            <a href="{{ route('audits.index', ['statut'=>'en_retard']) }}" class="{{ request('statut')==='en_retard' ? 'active' : '' }}">En retard</a>
        </div>

        <div class="card"><div class="card-body">
            <table class="table table-striped" id="table-audits">
                <thead><tr><th>Client</th><th>Norme</th><th>Type</th><th>Date planifiée</th><th>Statut</th><th></th></tr></thead>
                <tbody>
                    @foreach ($audits as $a)
                    <tr class="{{ $a->date_planifiee->isPast() && $a->statut==='planifie' ? 'table-warning' : '' }}">
                        <td>{{ $a->client->nom_complet }}</td>
                        <td>{{ $a->norme->code }}</td>
                        <td>{{ $a->type }}</td>
                        <td>{{ $a->date_planifiee->format('d/m/Y') }}</td>
                        <td><span class="badge badge-info">{{ $a->statut }}</span></td>
                        <td><a href="{{ route('audits.show', $a) }}" class="btn btn-sm btn-icon"><i data-feather="eye"></i></a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div></div>
    </section>
</div>
@endsection
@push('scripts')<script>$(function(){$('#table-audits').DataTable();});</script>@endpush
