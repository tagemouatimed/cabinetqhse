@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Suivi du recouvrement</h1>
            <div class="section-header-button">
                <a href="{{ route('recouvrement.export') }}" class="btn btn-success">Exporter Excel</a>
            </div>
        </div>

        <div class="kpi-row">
            <div class="kpi-card" style="--accent:var(--c-factures)">
                <div class="kpi-value">{{ number_format($caTotal, 2) }}</div>
                <div class="kpi-label">Chiffre d'affaires facturé</div>
            </div>
            <div class="kpi-card" style="--accent:#16a34a">
                <div class="kpi-value">{{ number_format($totalEncaisse, 2) }}</div>
                <div class="kpi-label">Total encaissé</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-devis)">
                <div class="kpi-value">{{ number_format($resteAVenir, 2) }}</div>
                <div class="kpi-label">Reste à venir</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-recouvrement)">
                <div class="kpi-value">{{ $nbEcheancesEnRetard }}</div>
                <div class="kpi-label">Échéances en retard</div>
            </div>
        </div>

        <div class="filter-btns">
            <a href="{{ route('recouvrement.index') }}" class="{{ !request('statut') ? 'active' : '' }}">Toutes</a>
            <a href="{{ route('recouvrement.index', ['statut' => 'en_attente']) }}" class="{{ request('statut')==='en_attente' ? 'active' : '' }}">En attente</a>
            <a href="{{ route('recouvrement.index', ['statut' => 'en_retard']) }}" class="{{ request('statut')==='en_retard' ? 'active' : '' }}">En retard</a>
            <a href="{{ route('recouvrement.index', ['statut' => 'regle']) }}" class="{{ request('statut')==='regle' ? 'active' : '' }}">Réglées</a>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-striped" id="table-recouvrement">
                    <thead>
                        <tr>
                            <th>Client</th><th>Projet</th><th>Partie</th><th>Échéance</th>
                            <th>Montant prévu</th><th>Réglé</th><th>Date règlement</th><th>Moyen</th><th>Reste</th><th>Statut</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($echeances as $e)
                        <tr class="{{ $e->est_en_retard ? 'table-danger' : '' }}">
                            <td>{{ $e->client->nom_complet }}</td>
                            <td>{{ $e->projet_id ?? '-' }}</td>
                            <td>{{ $e->partie }}</td>
                            <td>{{ $e->date_echeance->format('d/m/Y') }}</td>
                            <td>{{ number_format($e->montant_prevu, 2) }}</td>
                            <td>{{ number_format($e->montant_regle, 2) }}</td>
                            <td>{{ $e->date_reglement?->format('d/m/Y') }}</td>
                            <td>{{ $e->moyen_paiement }}</td>
                            <td>{{ number_format($e->reste, 2) }}</td>
                            <td>{{ $e->reglement_recu ? 'Réglé' : ($e->est_en_retard ? 'En retard' : 'En attente') }}</td>
                            <td>
                                @unless ($e->reglement_recu)
                                <button class="btn btn-sm btn-icon" data-toggle="modal" data-target="#modal-reglement-{{ $e->id }}"><i data-feather="check"></i></button>
                                @endunless
                                <form action="{{ route('recouvrement.destroy', $e) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette échéance ?')">
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
    </section>
</div>

@foreach ($echeances as $e)
<div class="modal fade" id="modal-reglement-{{ $e->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('recouvrement.reglement', $e) }}" method="POST">
                @csrf
                <div class="modal-header"><h5>Enregistrer un règlement — {{ $e->partie }}</h5></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Date du règlement</label>
                        <input type="date" name="date_reglement" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Moyen de paiement</label>
                        <select name="moyen_paiement" class="form-control" required>
                            <option value="virement">Virement</option>
                            <option value="cheque">Chèque</option>
                            <option value="espece">Espèce</option>
                            <option value="traite">Traite</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Montant réglé</label>
                        <input type="number" step="0.01" name="montant_regle" class="form-control" value="{{ $e->montant_prevu }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Annuler</button>
                    <button class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
    $(function () { $('#table-recouvrement').DataTable(); });
</script>
@endpush
