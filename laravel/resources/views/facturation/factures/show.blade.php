@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Facture {{ $facture->numero }}</h1>
            <div class="section-header-button">
                <a href="{{ route('factures.imprimer', $facture) }}" target="_blank" class="btn btn-light">Imprimer</a>
                <a href="{{ route('factures.edit', $facture) }}" class="btn btn-primary">Modifier</a>
                <form action="{{ route('factures.destroy', $facture) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette facture ?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <p><strong>Client :</strong> {{ $facture->client->nom_complet }}</p>
                <p><strong>Adresse :</strong> {{ $facture->client->adresse ?: '—' }}</p>
                <p><strong>ICE :</strong> {{ $facture->client->ice ?: '—' }}</p>
                <p><strong>Date :</strong> {{ $facture->date_facture->format('d/m/Y') }}</p>
                <p><strong>Statut :</strong> {{ ucfirst(str_replace('_', ' ', $facture->statut)) }}</p>

                <table class="table">
                    <thead><tr><th>Désignation</th><th>Qté</th><th>PU</th><th>Montant</th></tr></thead>
                    <tbody>
                        @foreach ($facture->lignes as $l)
                        <tr>
                            <td>{{ $l->designation }}</td>
                            <td>{{ $l->quantite }}</td>
                            <td>{{ number_format($l->prix_unitaire, 2) }}</td>
                            <td>{{ number_format($l->montant, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($facture->frais_deplacement)
                <p><strong>Frais de déplacement :</strong> {{ number_format($facture->frais_deplacement, 2) }}</p>
                @endif
                <p><strong>Montant HT :</strong> {{ number_format($facture->montant_ht, 2) }}</p>
                <p><strong>TVA ({{ $facture->taux_tva }}%) :</strong> {{ number_format($facture->montant_tva, 2) }}</p>
                <p><strong>Montant TTC :</strong> {{ number_format($facture->montant_ttc, 2) }}</p>
                <p><strong>Réglé :</strong> {{ number_format($facture->montant_regle, 2) }}</p>
                <p><strong>Reste à payer :</strong> {{ number_format($facture->reste_a_payer, 2) }}</p>

                <p class="mt-4">Arrêtée la présente facture à la somme de <strong>{{ \App\Helpers\MontantEnLettres::convertir($facture->montant_ttc) }}</strong> TTC.</p>
                <p>Nous vous prions d'agréer, cher client, nos sincères salutations.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5>Échéancier de paiement</h5></div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr><th>Partie</th><th>Échéance</th><th>Montant prévu</th><th>Réglé</th><th>Reste</th><th>Moyen</th><th>Statut</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($facture->echeancesPaiement as $e)
                        <tr class="{{ $e->est_en_retard ? 'table-danger' : '' }}">
                            <td>{{ $e->partie }}</td>
                            <td>{{ $e->date_echeance->format('d/m/Y') }}</td>
                            <td>{{ number_format($e->montant_prevu, 2) }}</td>
                            <td>{{ number_format($e->montant_regle, 2) }}</td>
                            <td>{{ number_format($e->reste, 2) }}</td>
                            <td>{{ $e->moyen_paiement }}</td>
                            <td>{{ $e->reglement_recu ? 'Réglé' : ($e->est_en_retard ? 'En retard' : 'En attente') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <a href="{{ route('recouvrement.index', ['facture_id' => $facture->id]) }}" class="btn btn-sm btn-light">Gérer les échéances</a>
            </div>
        </div>
    </section>
</div>
@endsection
