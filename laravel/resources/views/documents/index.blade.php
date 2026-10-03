@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"><h1>Gestion documentaire</h1></div>

        <div class="kpi-row">
            <div class="kpi-card" style="--accent:var(--c-documents)">
                <div class="kpi-value">{{ $stats['total'] }}</div>
                <div class="kpi-label">Documents au total</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-audits)">
                <div class="kpi-value">{{ $stats['normes'] }}</div>
                <div class="kpi-label">Normes</div>
            </div>
            <div class="kpi-card" style="--accent:var(--c-formation)">
                <div class="kpi-value">{{ $stats['supports'] }}</div>
                <div class="kpi-label">Supports de formation</div>
            </div>
            <div class="kpi-card" style="--accent:#9ca3af">
                <div class="kpi-value">{{ $stats['autres'] }}</div>
                <div class="kpi-label">Autres documents</div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="form-inline mb-3">
                    @csrf
                    <select name="categorie" class="form-control mr-2" required>
                        <option value="norme">Norme</option>
                        <option value="support_formation">Support de formation</option>
                        <option value="autre">Autre document</option>
                    </select>
                    <input type="text" name="titre" class="form-control mr-2" placeholder="Titre" required>
                    <input type="file" name="fichier" class="form-control-file mr-2" required>
                    <button class="btn btn-primary btn-sm">Ajouter</button>
                </form>

                <div class="filter-btns">
                    <a href="{{ route('documents.index') }}" class="{{ !request('categorie') ? 'active' : '' }}">Tous</a>
                    <a href="{{ route('documents.index', ['categorie'=>'norme']) }}" class="{{ request('categorie')==='norme' ? 'active' : '' }}">Normes</a>
                    <a href="{{ route('documents.index', ['categorie'=>'support_formation']) }}" class="{{ request('categorie')==='support_formation' ? 'active' : '' }}">Supports de formation</a>
                    <a href="{{ route('documents.index', ['categorie'=>'autre']) }}" class="{{ request('categorie')==='autre' ? 'active' : '' }}">Autres</a>
                </div>

                <table class="table table-striped" id="table-documents">
                    <thead><tr><th>Titre</th><th>Catégorie</th><th>Version</th><th>Client</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($documents as $d)
                        <tr>
                            <td>{{ $d->titre }}</td>
                            <td>{{ $d->categorie }}</td>
                            <td>v{{ $d->version }}</td>
                            <td>{{ $d->client?->nom_complet }}</td>
                            <td>
                                <a href="{{ route('documents.telecharger', $d) }}" class="btn btn-sm btn-icon"><i data-feather="download"></i></a>
                                <form action="{{ route('documents.nouvelle-version', $d) }}" method="POST" enctype="multipart/form-data" class="d-inline">
                                    @csrf
                                    <input type="file" name="fichier" style="display:none" onchange="this.form.submit()">
                                    <button type="button" class="btn btn-sm btn-icon" onclick="this.previousElementSibling.click()" title="Nouvelle version"><i data-feather="upload"></i></button>
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
@endsection
@push('scripts')<script>$(function(){$('#table-documents').DataTable();});</script>@endpush
