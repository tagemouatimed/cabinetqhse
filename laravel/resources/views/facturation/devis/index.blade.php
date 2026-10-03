@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Devis</h1>
            <div class="section-header-button">
                <a href="{{ route('devis.create') }}" class="btn btn-primary">Nouveau devis</a>
                <a href="{{ route('devis.export') }}" class="btn btn-success">Exporter Excel</a>
            </div>
        </div>

        <div class="kpi-row">
            <div class="kpi-card" style="--accent:var(--c-devis)">
                <div class="kpi-value">{{ $stats['total'] }}</div>
                <div class="kpi-label">Devis au total</div>
            </div>
            <div class="kpi-card" style="--accent:#9ca3af">
                <div class="kpi-value">{{ $stats['brouillon'] }}</div>
                <div class="kpi-label">Brouillons</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-taches)">
                <div class="kpi-value">{{ $stats['converti'] }}</div>
                <div class="kpi-label">Convertis en facture</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-factures)">
                <div class="kpi-value">{{ number_format($stats['montant_en_cours'], 0) }}</div>
                <div class="kpi-label">Montant en cours (Dhs)</div>
            </div>
        </div>

        <div class="filter-btns">
            <a href="{{ route('devis.index') }}" class="{{ !request('statut') ? 'active' : '' }}">Tous</a>
            <a href="{{ route('devis.index', ['statut'=>'brouillon']) }}" class="{{ request('statut')==='brouillon' ? 'active' : '' }}">Brouillon</a>
            <a href="{{ route('devis.index', ['statut'=>'envoye']) }}" class="{{ request('statut')==='envoye' ? 'active' : '' }}">Envoyé</a>
            <a href="{{ route('devis.index', ['statut'=>'accepte']) }}" class="{{ request('statut')==='accepte' ? 'active' : '' }}">Accepté</a>
            <a href="{{ route('devis.index', ['statut'=>'converti']) }}" class="{{ request('statut')==='converti' ? 'active' : '' }}">Converti</a>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-striped" id="table-devis">
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Montant TTC</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($devis as $d)
                        <tr>
                            <td>{{ $d->numero }}</td>
                            <td>{{ $d->client->nom_complet }}</td>
                            <td>{{ $d->date_devis->format('d/m/Y') }}</td>
                            <td><span class="badge badge-info">{{ ucfirst($d->statut) }}</span></td>
                            <td>{{ number_format($d->montant_ttc, 2) }}</td>
                            <td>
                                <a href="{{ route('devis.show', $d) }}" class="btn btn-sm btn-icon" title="Voir"><i data-feather="eye"></i></a>
                                <a href="{{ route('devis.edit', $d) }}" class="btn btn-sm btn-icon" title="Modifier"><i data-feather="edit"></i></a>
                                <a href="{{ route('devis.imprimer', $d) }}" target="_blank" class="btn btn-sm btn-icon" title="Imprimer"><i data-feather="printer"></i></a>
                                <form action="{{ route('devis.destroy', $d) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce devis ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-icon" title="Supprimer"><i data-feather="trash-2"></i></button>
                                </form>
                                @if ($d->statut !== 'converti')
                                <form action="{{ route('devis.convertir', $d) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-icon" title="Convertir en facture"><i data-feather="file-plus"></i></button>
                                </form>
                                @endif
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
    $(function () { $('#table-devis').DataTable(); });
</script>
@endpush
