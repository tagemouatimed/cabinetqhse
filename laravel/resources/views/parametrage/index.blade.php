@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"><h1>Paramétrage</h1></div>

        <div class="card">
            <div class="card-header card-header-indigo"><h5 class="mb-0">Phases et actions d'accompagnement pré-établies</h5></div>
            <div class="card-body">
                <form action="{{ route('parametrage.phases.store') }}" method="POST" class="form-inline mb-3">
                    @csrf
                    <input type="text" name="nom" class="form-control mr-2" placeholder="Nom de la phase" required>
                    <input type="number" name="ordre" class="form-control mr-2" placeholder="Ordre *" min="0" style="width:90px" required>
                    <button class="btn btn-primary btn-sm">Ajouter une phase</button>
                </form>

                @foreach ($phases as $phase)
                <h6>{{ $phase->ordre }}. {{ $phase->nom }}</h6>
                <ul>
                    @foreach ($phase->actionsPreetablies as $a)
                    <li>{{ $a->titre }}</li>
                    @endforeach
                </ul>
                <form action="{{ route('parametrage.phases.actions.store', $phase) }}" method="POST" class="form-inline mb-3">
                    @csrf
                    <input type="text" name="titre" class="form-control mr-2" placeholder="Action pré-établie" required>
                    <button class="btn btn-sm btn-light">Ajouter</button>
                </form>
                @endforeach
            </div>
        </div>

        <div class="card">
            <div class="card-header card-header-violet"><h5 class="mb-0">Permissions par rôle</h5></div>
            <div class="card-body">
                <form action="{{ route('parametrage.permissions.update') }}" method="POST" class="form-inline mb-3">
                    @csrf
                    <input type="text" name="role" class="form-control mr-2" placeholder="Rôle" required>
                    <select name="module" class="form-control mr-2" required>
                        @foreach ($modules as $m)<option value="{{ $m }}">{{ $m }}</option>@endforeach
                    </select>
                    <label class="mr-2"><input type="checkbox" name="peut_voir" value="1"> Voir</label>
                    <label class="mr-2"><input type="checkbox" name="peut_ajouter" value="1"> Ajouter</label>
                    <label class="mr-2"><input type="checkbox" name="peut_modifier" value="1"> Modifier</label>
                    <label class="mr-2"><input type="checkbox" name="peut_supprimer" value="1"> Supprimer</label>
                    <label class="mr-2"><input type="checkbox" name="peut_imprimer" value="1"> Imprimer</label>
                    <button class="btn btn-primary btn-sm">Enregistrer</button>
                </form>

                <table class="table table-sm">
                    <thead><tr><th>Rôle</th><th>Module</th><th>Voir</th><th>Ajouter</th><th>Modifier</th><th>Supprimer</th><th>Imprimer</th></tr></thead>
                    <tbody>
                        @foreach ($permissions as $p)
                        <tr>
                            <td>{{ $p->role }}</td><td>{{ $p->module }}</td>
                            <td>{{ $p->peut_voir ? '✓' : '' }}</td><td>{{ $p->peut_ajouter ? '✓' : '' }}</td>
                            <td>{{ $p->peut_modifier ? '✓' : '' }}</td><td>{{ $p->peut_supprimer ? '✓' : '' }}</td>
                            <td>{{ $p->peut_imprimer ? '✓' : '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header card-header-cyan"><h5 class="mb-0">Interface (couleur / ordre des modules)</h5></div>
            <div class="card-body">
                <form action="{{ route('parametrage.interface.update') }}" method="POST" class="form-inline mb-3">
                    @csrf
                    <select name="module" class="form-control mr-2" required>
                        @foreach ($modules as $m)<option value="{{ $m }}">{{ $m }}</option>@endforeach
                    </select>
                    <input type="color" name="couleur" class="form-control mr-2">
                    <input type="number" name="ordre" class="form-control mr-2" placeholder="Ordre *" min="0" style="width:90px" required>
                    <button class="btn btn-primary btn-sm">Enregistrer</button>
                </form>

                <table class="table table-sm">
                    <thead><tr><th>Module</th><th>Couleur</th><th>Ordre</th></tr></thead>
                    <tbody>
                        @foreach ($interface as $i)
                        <tr><td>{{ $i->module }}</td><td><span style="background:{{ $i->couleur }};padding:0 15px">&nbsp;</span> {{ $i->couleur }}</td><td>{{ $i->ordre }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">Prestations</h5></div>
            <div class="card-body">
                <a href="{{ route('prestations.index') }}" class="btn btn-light btn-sm">Gérer les prestations et prix standards →</a>
            </div>
        </div>
    </section>
</div>
@endsection
