@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>{{ $projet->nom }} — {{ $projet->client->nom_complet }}</h1>
            <div class="section-header-button">
                <a href="{{ route('projets.edit', $projet) }}" class="btn btn-primary">Modifier</a>
                <form action="{{ route('projets.destroy', $projet) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce projet et tout son contenu ?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>

        <div class="card"><div class="card-body">
            <p><strong>Norme :</strong> {{ $projet->norme }} — <strong>Statut :</strong> {{ $projet->statut }} — <strong>Avancement :</strong> {{ $projet->avancement }}%</p>
        </div></div>

        <div class="card">
            <div class="card-header card-header-indigo"><h5 class="mb-0">Plans d'action</h5></div>
            <div class="card-body">
                @foreach ($projet->plansAction as $plan)
                <div class="d-flex justify-content-between align-items-center">
                    <h6>{{ $plan->titre }}</h6>
                </div>
                <table class="table table-sm">
                    <thead><tr><th>Action</th><th>Échéance</th><th>Responsable</th><th>Statut</th></tr></thead>
                    <tbody>
                        @foreach ($plan->actions as $a)
                        <tr class="{{ $a->est_en_retard ? 'table-danger' : '' }}">
                            <td>{{ $a->titre }}</td>
                            <td>{{ $a->date_echeance?->format('d/m/Y') }}</td>
                            <td>{{ $a->responsable }}</td>
                            <td>
                                <form action="{{ route('actions.statut', $a) }}" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <select name="statut" class="form-control form-control-sm d-inline w-auto" onchange="this.form.submit()">
                                        @foreach (['a_faire','en_cours','realisee','en_retard'] as $s)
                                        <option value="{{ $s }}" @selected($a->statut===$s)>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <form action="{{ route('plans-action.actions.store', $plan) }}" method="POST" class="form-inline mb-3">
                    @csrf
                    @if ($plan->phase && $plan->phase->actionsPreetablies->isNotEmpty())
                    <select class="form-control mr-2 select-action-preetablie" onchange="remplirAction(this)">
                        <option value="">-- Action pré-établie (optionnel) --</option>
                        @foreach ($plan->phase->actionsPreetablies as $ap)
                        <option value="{{ $ap->titre }}">{{ $ap->titre }}</option>
                        @endforeach
                    </select>
                    @endif
                    <input type="text" name="titre" class="form-control mr-2 champ-titre-action" placeholder="Titre de l'action" required>
                    <input type="date" name="date_echeance" class="form-control mr-2">
                    <input type="text" name="responsable" class="form-control mr-2" placeholder="Responsable">
                    <button class="btn btn-sm btn-primary">Ajouter</button>
                </form>
                @endforeach

                <form action="{{ route('projets.plans-action.store', $projet) }}" method="POST" class="form-inline">
                    @csrf
                    <select name="phase_id" class="form-control mr-2">
                        <option value="">-- Phase pré-établie (optionnel) --</option>
                        @foreach ($phases as $ph)
                        <option value="{{ $ph->id }}">{{ $ph->nom }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="titre" class="form-control mr-2" placeholder="Ou titre libre">
                    <button class="btn btn-sm btn-light">+ Plan d'action</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header card-header-amber"><h5 class="mb-0">Notes de suivi</h5></div>
            <div class="card-body">
                @foreach ($projet->notes as $n)
                <p><small>{{ $n->created_at->format('d/m/Y H:i') }} — {{ $n->auteur }}</small><br>{{ $n->contenu }}</p>
                @endforeach
                <form action="{{ route('projets.notes.store', $projet) }}" method="POST">
                    @csrf
                    <textarea name="contenu" class="form-control mb-2" placeholder="Nouvelle note" required></textarea>
                    <button class="btn btn-sm btn-primary">Ajouter</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header card-header-cyan"><h5 class="mb-0">Rapports de réunion</h5></div>
            <div class="card-body">
                @foreach ($projet->rapportsReunion as $r)
                <div class="mb-3">
                    <strong>{{ $r->date_reunion->format('d/m/Y') }} — {{ $r->objet }}</strong>
                    <p>{{ $r->compte_rendu }}</p>
                    @foreach ($r->pieceJointes as $pj)
                    <span class="badge badge-light">{{ $pj->nom_original }}</span>
                    @endforeach
                </div>
                @endforeach
                <form action="{{ route('projets.rapports-reunion.store', $projet) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group"><input type="date" name="date_reunion" class="form-control" required></div>
                    <div class="form-group"><input type="text" name="objet" class="form-control" placeholder="Objet"></div>
                    <div class="form-group"><textarea name="compte_rendu" class="form-control" placeholder="Compte-rendu" required></textarea></div>
                    <div class="form-group"><input type="file" name="pieces_jointes[]" class="form-control-file" multiple></div>
                    <button class="btn btn-sm btn-primary">Ajouter le rapport</button>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
@push('scripts')
<script>
    function remplirAction(select) {
        const form = select.closest('form');
        form.querySelector('.champ-titre-action').value = select.value;
    }
</script>
@endpush
