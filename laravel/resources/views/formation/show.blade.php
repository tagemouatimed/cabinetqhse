@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"><h1>{{ $formation->theme }}</h1></div>

        <div class="card"><div class="card-body">
            <p><strong>Formateur :</strong> {{ $formation->formateur }} — <strong>Date :</strong> {{ $formation->date_planifiee->format('d/m/Y') }} — <strong>Statut :</strong> {{ $formation->statut }}</p>

            <form action="{{ route('participants.store', $formation) }}" method="POST" class="form-inline mb-3">
                @csrf
                <input type="text" name="prenom" class="form-control mr-2" placeholder="Prénom" required>
                <input type="text" name="nom" class="form-control mr-2" placeholder="Nom" required>
                <button class="btn btn-sm btn-primary">Ajouter participant</button>
            </form>

            <form action="{{ route('formations.cloturer', $formation) }}" method="POST">
                @csrf
                <table class="table table-sm">
                    <thead><tr><th>Nom</th><th>Présent</th><th>Note satisfaction /10</th><th>Certificat</th></tr></thead>
                    <tbody>
                        @foreach ($formation->participants as $p)
                        <tr>
                            <td>{{ $p->prenom }} {{ $p->nom }}</td>
                            <td><input type="checkbox" name="participants[{{ $p->id }}][present]" @checked($p->present)></td>
                            <td><input type="number" min="0" max="10" name="participants[{{ $p->id }}][note_satisfaction]" value="{{ $p->note_satisfaction }}" class="form-control form-control-sm" style="width:80px"></td>
                            <td>
                                @if ($p->certificat)
                                    {{ $p->certificat->numero }}
                                @else
                                    <button type="button" class="btn btn-sm btn-light" formaction="{{ route('participants.certificat', $p) }}" formmethod="POST">Générer</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <button class="btn btn-success">Enregistrer présence / clôturer</button>
            </form>
        </div></div>
    </section>
</div>
@endsection
