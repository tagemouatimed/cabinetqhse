<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis {{ $devis->numero }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #111; font-size:14px; }
        .btn-print { margin-bottom:20px; }
        @media print { .btn-print { display:none; } }

        .top-row { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px; }
        h1 { font-size: 1.6rem; text-decoration:underline; margin:0 0 16px 0; }

        .boite { border:1px solid #333; border-radius:6px; padding:10px 14px; }
        .boite-titre { background:#eee; font-weight:bold; text-align:center; padding:4px; border:1px solid #333; border-radius:6px; margin-bottom:6px; }

        .meta { display:flex; gap:16px; }
        .meta > div { flex:1; }

        .client-box { width:320px; }
        .client-box p { margin:4px 0; }

        table { width:100%; border-collapse: collapse; margin:20px 0; }
        th, td { border:1px solid #333; padding:8px; text-align:left; }
        th { background:#f0f0f0; }
        td.num, th.num { text-align:center; }

        .ligne-totale td { font-weight:bold; }
        .ttc-row { display:flex; justify-content:flex-end; margin-top:14px; }
        .ttc-box { border:1px solid #333; border-radius:6px; overflow:hidden; display:flex; }
        .ttc-box div { padding:8px 16px; }
        .ttc-box .label { background:#eee; font-weight:bold; border-right:1px solid #333; }

        .mention { margin-top:40px; text-align:center; }
        .mention p { margin:6px 0; }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">Imprimer</button>

    <div class="top-row">
        <div>
            <h1>Devis</h1>
            <div class="meta">
                <div class="boite">
                    <div class="boite-titre" style="margin:-10px -14px 8px -14px;">Date</div>
                    {{ $devis->date_devis->format('d/m/Y') }}
                </div>
                <div class="boite">
                    <div class="boite-titre" style="margin:-10px -14px 8px -14px;">Devis N°</div>
                    {{ $devis->numero }}
                </div>
            </div>
        </div>

        <div class="client-box boite">
            <div class="boite-titre" style="margin:-10px -14px 8px -14px;">Client</div>
            <p><strong>Raison sociale :</strong> {{ $devis->client->nom_complet }}</p>
            <p><strong>Adresse :</strong> {{ $devis->client->adresse ?: '—' }}</p>
            <p><strong>ICE :</strong> {{ $devis->client->ice ?: '—' }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th class="num">Quantité</th>
                <th class="num">P.U H.T (Dhs)</th>
                <th class="num">Montant H.T (Dhs)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($devis->lignes as $l)
            <tr>
                <td>{{ $l->designation }}</td>
                <td class="num">{{ $l->quantite }}</td>
                <td class="num">{{ number_format($l->prix_unitaire, 2) }}</td>
                <td class="num">{{ number_format($l->montant, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3"><strong>Frais de déplacement</strong></td>
                <td class="num">{{ $devis->frais_deplacement ? number_format($devis->frais_deplacement, 2) : '-' }}</td>
            </tr>
            <tr class="ligne-totale">
                <td colspan="3">Total H.T en Dh</td>
                <td class="num">{{ number_format($devis->montant_ht, 2) }}</td>
            </tr>
            <tr class="ligne-totale">
                <td colspan="3">Montant TVA ({{ rtrim(rtrim(number_format($devis->taux_tva, 2), '0'), '.') }}%) en Dh</td>
                <td class="num">{{ number_format($devis->montant_tva, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="ttc-row">
        <div class="ttc-box">
            <div class="label">Total TTC en Dh</div>
            <div>{{ number_format($devis->montant_ttc, 2) }}</div>
        </div>
    </div>

    <div class="mention">
        <p>Arrêté le présent devis à la somme de <strong>{{ \App\Helpers\MontantEnLettres::convertir($devis->montant_ttc) }}</strong> TTC.</p>
        <p>Nous vous prions d'agréer, cher client, nos sincères salutations.</p>
    </div>
</body>
</html>
