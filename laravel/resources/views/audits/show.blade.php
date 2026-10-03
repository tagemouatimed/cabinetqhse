@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"><h1>Audit {{ $audit->norme->code }} — {{ $audit->client->nom_complet }}</h1></div>

        <div class="card"><div class="card-body">
            <p><strong>Type :</strong> {{ $audit->type }} — <strong>Date :</strong> {{ $audit->date_planifiee->format('d/m/Y') }} — <strong>Statut :</strong> {{ $audit->statut }}</p>
            @if ($audit->statut === 'planifie')
            <form action="{{ route('audits.realise', $audit) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-sm btn-success">Marquer réalisé</button>
            </form>
            @endif
        </div></div>

        <div class="card">
            <div class="card-header"><h5>Plan d'audit</h5></div>
            <div class="card-body">
                @if ($audit->planAudit)
                <table class="table table-sm">
                    <thead><tr><th>Processus audité</th><th>Durée (min)</th><th>Auditeur</th><th>Audité</th><th>Date prévue</th></tr></thead>
                    <tbody>
                        @foreach ($audit->planAudit->lignes as $l)
                        <tr>
                            <td>{{ $l->processus_audite }}</td><td>{{ $l->duree_minutes }}</td>
                            <td>{{ $l->auditeur }}</td><td>{{ $l->audite }}</td>
                            <td>{{ $l->date_prevue?->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <form action="{{ route('plans-audit.lignes.store', $audit->planAudit) }}" method="POST" class="form-inline">
                    @csrf
                    <input type="text" name="processus_audite" class="form-control mr-2" placeholder="Processus" required>
                    <input type="number" name="duree_minutes" class="form-control mr-2" placeholder="Durée (min)" style="width:120px">
                    <input type="text" name="auditeur" class="form-control mr-2" placeholder="Auditeur">
                    <input type="text" name="audite" class="form-control mr-2" placeholder="Audité">
                    <input type="datetime-local" name="date_prevue" class="form-control mr-2">
                    <button class="btn btn-sm btn-primary">Ajouter</button>
                </form>
                @else
                <p>Aucun plan d'audit — pas de modèle standard pour cette norme, à créer manuellement.</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5>Rapport d'audit</h5></div>
            <div class="card-body">
                @if (! $audit->rapport)
                <form action="{{ route('audits.rapport.store', $audit) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary">Créer le rapport (brouillon)</button>
                </form>
                @else
                <p><strong>Statut :</strong> {{ $audit->rapport->statut }}
                    @if ($audit->rapport->score !== null) — <strong>Score :</strong> {{ $audit->rapport->score }}/100 @endif
                </p>
                <table class="table table-sm">
                    <thead><tr><th>Type</th><th>Référence norme</th><th>Description</th></tr></thead>
                    <tbody>
                        @foreach ($audit->rapport->constats as $c)
                        <tr>
                            <td><span class="badge badge-{{ $c->type === 'ecart_majeur' ? 'danger' : ($c->type === 'ecart_mineur' ? 'warning' : 'secondary') }}">{{ str_replace('_',' ',$c->type) }}</span></td>
                            <td>{{ $c->reference_norme }}</td>
                            <td>{{ $c->description }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($audit->rapport->statut === 'brouillon')
                <form action="{{ route('rapports-audit.constats.store', $audit->rapport) }}" method="POST" class="form-inline mb-3">
                    @csrf
                    <select name="type" class="form-control mr-2" required>
                        <option value="point_sensible">Point sensible</option>
                        <option value="ecart_mineur">Écart mineur</option>
                        <option value="ecart_majeur">Écart majeur</option>
                        <option value="recommandation">Recommandation</option>
                    </select>
                    <input type="text" name="reference_norme" class="form-control mr-2" placeholder="Réf. norme">
                    <input type="text" name="description" class="form-control mr-2" placeholder="Description" style="width:300px" required>
                    <button class="btn btn-sm btn-primary">Ajouter</button>
                </form>
                <form action="{{ route('rapports-audit.finaliser', $audit->rapport) }}" method="POST" onsubmit="return confirm('Finaliser le rapport ? Le score sera calculé et figé.')">
                    @csrf
                    <button class="btn btn-success">Finaliser le rapport</button>
                </form>
                @endif
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
