@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Devis {{ $devis->numero }}</h1>
            <div class="section-header-button">
                <a href="{{ route('devis.imprimer', $devis) }}" target="_blank" class="btn btn-light">Imprimer</a>
                <a href="{{ route('devis.edit', $devis) }}" class="btn btn-primary">Modifier</a>
                <form action="{{ route('devis.destroy', $devis) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce devis ?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <p><strong>Client :</strong> {{ $devis->client->nom_complet }}</p>
                <p><strong>Adresse :</strong> {{ $devis->client->adresse ?: '—' }}</p>
                <p><strong>ICE :</strong> {{ $devis->client->ice ?: '—' }}</p>
                <p><strong>Date :</strong> {{ $devis->date_devis->format('d/m/Y') }}</p>
                <p><strong>Statut :</strong> {{ ucfirst($devis->statut) }}</p>

                <table class="table">
                    <thead><tr><th>Désignation</th><th>Qté</th><th>PU</th><th>Montant</th></tr></thead>
                    <tbody>
                        @foreach ($devis->lignes as $l)
                        <tr>
                            <td>{{ $l->designation }}</td>
                            <td>{{ $l->quantite }}</td>
                            <td>{{ number_format($l->prix_unitaire, 2) }}</td>
                            <td>{{ number_format($l->montant, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($devis->frais_deplacement)
                <p><strong>Frais de déplacement :</strong> {{ number_format($devis->frais_deplacement, 2) }}</p>
                @endif
                <p><strong>Montant HT :</strong> {{ number_format($devis->montant_ht, 2) }}</p>
                <p><strong>TVA ({{ $devis->taux_tva }}%) :</strong> {{ number_format($devis->montant_tva, 2) }}</p>
                <p><strong>Montant TTC :</strong> {{ number_format($devis->montant_ttc, 2) }}</p>

                <p class="mt-4">Arrêté le présent devis à la somme de <strong>{{ \App\Helpers\MontantEnLettres::convertir($devis->montant_ttc) }}</strong> TTC.</p>
                <p>Nous vous prions d'agréer, cher client, nos sincères salutations.</p>

                @if ($devis->statut !== 'converti')
                <form action="{{ route('devis.convertir', $devis) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary">Convertir en facture</button>
                </form>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
