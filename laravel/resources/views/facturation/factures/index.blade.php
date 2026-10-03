@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Factures</h1>
            <div class="section-header-button">
                <a href="{{ route('factures.create') }}" class="btn btn-primary">Nouvelle facture</a>
                <a href="{{ route('factures.export') }}" class="btn btn-success">Exporter Excel</a>
            </div>
        </div>

        <div class="kpi-row">
            <div class="kpi-card" style="--accent:var(--c-factures)">
                <div class="kpi-value">{{ $stats['total'] }}</div>
                <div class="kpi-label">Factures au total</div>
            </div>
            <div class="kpi-card" style="--accent:#16a34a">
                <div class="kpi-value">{{ $stats['payees'] }}</div>
                <div class="kpi-label">Payées</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-recouvrement)">
                <div class="kpi-value">{{ $stats['en_retard'] }}</div>
                <div class="kpi-label">En retard</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-dashboard)">
                <div class="kpi-value">{{ number_format($stats['ca_total'], 0) }}</div>
                <div class="kpi-label">CA facturé (Dhs)</div>
            </div>
        </div>

        <div class="filter-btns">
            <a href="{{ route('factures.index') }}" class="{{ !request('statut') ? 'active' : '' }}">Toutes</a>
            <a href="{{ route('factures.index', ['statut'=>'brouillon']) }}" class="{{ request('statut')==='brouillon' ? 'active' : '' }}">Brouillon</a>
            <a href="{{ route('factures.index', ['statut'=>'envoyee']) }}" class="{{ request('statut')==='envoyee' ? 'active' : '' }}">Envoyée</a>
            <a href="{{ route('factures.index', ['statut'=>'payee']) }}" class="{{ request('statut')==='payee' ? 'active' : '' }}">Payée</a>
            <a href="{{ route('factures.index', ['statut'=>'en_retard']) }}" class="{{ request('statut')==='en_retard' ? 'active' : '' }}">En retard</a>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-striped" id="table-factures">
                    <thead>
                        <tr>
                            <th>Numéro</th><th>Client</th><th>Projet</th><th>Date</th><th>Échéance</th>
                            <th>Statut</th><th>TTC</th><th>Reste à payer</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($factures as $f)
                        <tr class="{{ $f->est_en_retard ? 'table-danger' : '' }}">
                            <td>{{ $f->numero }}</td>
                            <td>{{ $f->client->nom_complet }}</td>
                            <td>{{ $f->projet?->nom }}</td>
                            <td>{{ $f->date_facture->format('d/m/Y') }}</td>
                            <td>{{ $f->date_echeance?->format('d/m/Y') }}</td>
                            <td><span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $f->statut)) }}</span></td>
                            <td>{{ number_format($f->montant_ttc, 2) }}</td>
                            <td>{{ number_format($f->reste_a_payer, 2) }}</td>
                            <td>
                                <a href="{{ route('factures.show', $f) }}" class="btn btn-sm btn-icon" title="Voir"><i data-feather="eye"></i></a>
                                <a href="{{ route('factures.edit', $f) }}" class="btn btn-sm btn-icon" title="Modifier"><i data-feather="edit"></i></a>
                                <a href="{{ route('factures.imprimer', $f) }}" target="_blank" class="btn btn-sm btn-icon" title="Imprimer"><i data-feather="printer"></i></a>
                                <form action="{{ route('factures.destroy', $f) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette facture ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-icon" title="Supprimer"><i data-feather="trash-2"></i></button>
                                </form>
                                @if (! $f->est_avoir)
                                <form action="{{ route('factures.avoir', $f) }}" method="POST" class="d-inline" onsubmit="return confirm('Générer un avoir pour cette facture ?')">
                                    @csrf
                                    <button class="btn btn-sm btn-icon" title="Générer un avoir"><i data-feather="rotate-ccw"></i></button>
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
    $(function () { $('#table-factures').DataTable(); });
</script>
@endpush
