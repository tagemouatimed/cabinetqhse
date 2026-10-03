@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"><h1>Tableau de bord</h1></div>

        <div class="card">
            <div class="card-header"><h5>Nouvel indicateur</h5></div>
            <div class="card-body">
                <form action="{{ route('indicateurs.store') }}" method="POST" class="form-inline">
                    @csrf
                    <input type="text" name="nom" class="form-control mr-2" placeholder="Nom de l'indicateur" required>
                    <select name="type" class="form-control mr-2" required>
                        <option value="pourcentage">Pourcentage (D1/D2*100)</option>
                        <option value="nombre">Nombre</option>
                        <option value="autre">Autre</option>
                    </select>
                    <select name="source" class="form-control mr-2" id="select-source" required>
                        <option value="manuel">Saisie manuelle</option>
                        <option value="base">Depuis la base</option>
                    </select>
                    <select name="cle_base" class="form-control mr-2" id="select-cle-base" style="display:none">
                        @foreach ($clesBase as $cle => $libelle)
                        <option value="{{ $cle }}">{{ $libelle }}</option>
                        @endforeach
                    </select>
                    <select name="type_affichage" class="form-control mr-2" required>
                        <option value="carte">Carte KPI</option>
                        <option value="courbe">Courbe</option>
                        <option value="barres">Barres</option>
                        <option value="jauge">Jauge</option>
                    </select>
                    <button class="btn btn-primary btn-sm">Créer</button>
                </form>
            </div>
        </div>

        <div class="row">
            @foreach ($indicateurs as $ind)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6>{{ $ind->nom }} <span class="badge badge-light">{{ $ind->type_affichage }}</span></h6>
                    </div>
                    <div class="card-body">
                        @php $derniere = $ind->valeurs->last(); @endphp
                        <h3>{{ $derniere?->valeur ?? '-' }}{{ $ind->type === 'pourcentage' ? '%' : '' }}</h3>
                        <small class="text-muted">{{ $derniere?->periode }}</small>

                        @if ($ind->source === 'manuel')
                        <form action="{{ route('indicateurs.valeurs.store', $ind) }}" method="POST" class="form-inline mt-2">
                            @csrf
                            <input type="text" name="periode" class="form-control form-control-sm mr-1" placeholder="Période (2026-09)" style="width:100px" required>
                            @if ($ind->type === 'pourcentage')
                            <input type="number" step="0.01" name="valeur_d1" class="form-control form-control-sm mr-1" placeholder="D1" style="width:70px">
                            <input type="number" step="0.01" name="valeur_d2" class="form-control form-control-sm mr-1" placeholder="D2" style="width:70px">
                            @else
                            <input type="number" step="0.01" name="valeur" class="form-control form-control-sm mr-1" placeholder="Valeur" style="width:90px">
                            @endif
                            <button class="btn btn-sm btn-light">OK</button>
                        </form>
                        @else
                        <form action="{{ route('indicateurs.actualiser', $ind) }}" method="POST" class="mt-2">
                            @csrf
                            <button class="btn btn-sm btn-light">Actualiser depuis la base</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
@push('scripts')
<script>
    document.getElementById('select-source').addEventListener('change', function () {
        document.getElementById('select-cle-base').style.display = this.value === 'base' ? 'inline-block' : 'none';
    });
</script>
@endpush
