@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"><h1>Modifier facture {{ $facture->numero }}</h1></div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('factures.update', $facture) }}" method="POST" id="form-facture">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Client *</label>
                            <select name="client_id" class="form-control" required>
                                @foreach ($clients as $c)
                                <option value="{{ $c->id }}" @selected($facture->client_id==$c->id)>{{ $c->nom_complet }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Date de facture *</label>
                            <input type="date" name="date_facture" class="form-control" value="{{ $facture->date_facture->format('Y-m-d') }}" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Projet lié</label>
                            <select name="projet_id" class="form-control">
                                <option value="">-- Aucun --</option>
                                @foreach ($projets as $p)
                                <option value="{{ $p->id }}" @selected($facture->projet_id==$p->id)>{{ $p->nom }} ({{ $p->client->nom_complet }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label>TVA %</label>
                            <input type="number" step="0.01" name="taux_tva" class="form-control" value="{{ $facture->taux_tva }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label>Date d'échéance</label>
                            <input type="date" name="date_echeance" class="form-control" value="{{ $facture->date_echeance?->format('Y-m-d') }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Frais de déplacement (Dhs)</label>
                            <input type="number" step="0.01" name="frais_deplacement" class="form-control" value="{{ $facture->frais_deplacement }}" placeholder="0.00">
                        </div>
                    </div>

                    <hr>
                    <h5>Lignes</h5>
                    <table class="table" id="table-lignes">
                        <thead><tr><th>Prestation</th><th>Désignation</th><th>Qté</th><th>Prix unitaire</th><th></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-light" id="btn-ajouter-ligne">+ Ajouter une ligne</button>

                    <hr>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ $facture->notes }}</textarea>
                    </div>

                    <button class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('factures.show', $facture) }}" class="btn btn-light">Annuler</a>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    const prestations = @json($prestations);
    const lignesExistantes = @json($facture->lignes);
    let ligneIndex = 0;

    function ajouterLigne(ligne = null) {
        const options = prestations.map(p => `<option value="${p.id}" data-prix="${p.prix_standard}" data-libelle="${p.libelle}" ${ligne && ligne.prestation_id == p.id ? 'selected' : ''}>${p.libelle}</option>`).join('');
        const row = `<tr>
            <td>
                <select class="form-control select-prestation" name="lignes[${ligneIndex}][prestation_id]" onchange="remplirLigne(this, ${ligneIndex})">
                    <option value="">Libre</option>${options}
                </select>
            </td>
            <td><input type="text" class="form-control" name="lignes[${ligneIndex}][designation]" value="${ligne ? ligne.designation : ''}" required></td>
            <td><input type="number" step="0.01" class="form-control" name="lignes[${ligneIndex}][quantite]" value="${ligne ? ligne.quantite : 1}" required></td>
            <td><input type="number" step="0.01" class="form-control" name="lignes[${ligneIndex}][prix_unitaire]" value="${ligne ? ligne.prix_unitaire : ''}" required></td>
            <td><button type="button" class="btn btn-sm btn-icon" onclick="this.closest('tr').remove()"><i data-feather="x"></i></button></td>
        </tr>`;
        document.querySelector('#table-lignes tbody').insertAdjacentHTML('beforeend', row);
        ligneIndex++;
        if (window.feather) feather.replace();
    }

    function remplirLigne(select, index) {
        const opt = select.selectedOptions[0];
        const tr = select.closest('tr');
        if (!opt.value) return;
        tr.querySelector(`[name="lignes[${index}][designation]"]`).value = opt.dataset.libelle;
        tr.querySelector(`[name="lignes[${index}][prix_unitaire]"]`).value = opt.dataset.prix;
    }

    document.getElementById('btn-ajouter-ligne').addEventListener('click', () => ajouterLigne());

    if (lignesExistantes.length) {
        lignesExistantes.forEach(l => ajouterLigne(l));
    } else {
        ajouterLigne();
    }
</script>
@endpush
