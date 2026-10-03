@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"><h1>Nouvelle facture — {{ $numero }}</h1></div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('factures.store') }}" method="POST" id="form-facture">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Client *</label>
                            <select name="client_id" class="form-control" required>
                                <option value="">-- Choisir --</option>
                                @foreach ($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->nom_complet }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Date de facture *</label>
                            <input type="date" name="date_facture" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Projet lié (optionnel)</label>
                            <select name="projet_id" class="form-control">
                                <option value="">-- Aucun --</option>
                                @foreach ($projets as $p)
                                <option value="{{ $p->id }}">{{ $p->nom }} ({{ $p->client->nom_complet }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label>TVA %</label>
                            <input type="number" step="0.01" name="taux_tva" class="form-control" value="20">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label>Date d'échéance</label>
                            <input type="date" name="date_echeance" class="form-control">
                        </div>
                    </div>

                    <hr>
                    <h5>Lignes</h5>
                    <table class="table" id="table-lignes">
                        <thead>
                            <tr><th>Prestation</th><th>Désignation</th><th>Qté</th><th>Prix unitaire</th><th></th></tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-light" id="btn-ajouter-ligne">+ Ajouter une ligne</button>

                    <hr>
                    <div class="form-group col-md-3 pl-0">
                        <label>Frais de déplacement (Dhs)</label>
                        <input type="number" step="0.01" name="frais_deplacement" class="form-control" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>

                    <button class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('factures.index') }}" class="btn btn-light">Annuler</a>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    const prestations = @json($prestations);
    let ligneIndex = 0;

    function ajouterLigne() {
        const options = prestations.map(p => `<option value="${p.id}" data-prix="${p.prix_standard}" data-libelle="${p.libelle}">${p.libelle}</option>`).join('');
        const row = `<tr>
            <td>
                <select class="form-control select-prestation" name="lignes[${ligneIndex}][prestation_id]" onchange="remplirLigne(this, ${ligneIndex})">
                    <option value="">Libre</option>${options}
                </select>
            </td>
            <td><input type="text" class="form-control" name="lignes[${ligneIndex}][designation]" required></td>
            <td><input type="number" step="0.01" class="form-control" name="lignes[${ligneIndex}][quantite]" value="1" required></td>
            <td><input type="number" step="0.01" class="form-control" name="lignes[${ligneIndex}][prix_unitaire]" required></td>
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

    document.getElementById('btn-ajouter-ligne').addEventListener('click', ajouterLigne);
    ajouterLigne();
</script>
@endpush
