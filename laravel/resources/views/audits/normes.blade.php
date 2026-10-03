@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"><h1>Normes</h1></div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('normes.store') }}" method="POST" class="form-inline mb-3">
                    @csrf
                    <input type="text" name="code" class="form-control mr-2" placeholder="Code (ex: ISO 9001:2015)" required>
                    <input type="text" name="libelle" class="form-control mr-2" placeholder="Libellé" required>
                    <button class="btn btn-primary btn-sm">Ajouter</button>
                </form>

                <table class="table table-striped">
                    <thead><tr><th>Code</th><th>Libellé</th><th>Exigences</th></tr></thead>
                    <tbody>
                        @foreach ($normes as $n)
                        <tr><td>{{ $n->code }}</td><td>{{ $n->libelle }}</td><td>{{ $n->exigences_count }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
