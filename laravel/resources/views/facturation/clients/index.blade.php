@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Clients</h1>
            <div class="section-header-button">
                <a href="{{ route('clients.create') }}" class="btn btn-primary">Nouveau client</a>
                <a href="{{ route('clients.export') }}" class="btn btn-success">Exporter Excel</a>
            </div>
        </div>

        <div class="kpi-row">
            <div class="kpi-card" style="--accent:var(--c-clients)">
                <div class="kpi-value">{{ $stats['total'] }}</div>
                <div class="kpi-label">Clients au total</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-taches)">
                <div class="kpi-value">{{ $stats['nouveaux_mois'] }}</div>
                <div class="kpi-label">Nouveaux ce mois</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-recouvrement)">
                <div class="kpi-value">{{ $stats['avec_impaye'] }}</div>
                <div class="kpi-label">Avec facture impayée</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-dashboard)">
                <div class="kpi-value">{{ $stats['secteurs'] }}</div>
                <div class="kpi-label">Secteurs d'activité</div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-striped" id="table-clients">
                    <thead>
                        <tr>
                            <th>Nom / Raison sociale</th>
                            <th>ICE</th>
                            <th>Secteur d'activité</th>
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clients as $client)
                        <tr>
                            <td>{{ $client->nom_complet }}</td>
                            <td>{{ $client->ice }}</td>
                            <td>{{ $client->secteur_activite }}</td>
                            <td>{{ $client->telephone }}</td>
                            <td>{{ $client->email }}</td>
                            <td>
                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-icon" title="Modifier"><i data-feather="edit"></i></a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce client ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-icon" title="Supprimer"><i data-feather="trash-2"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    $(function () { $('#table-clients').DataTable(); });
</script>
@endpush
