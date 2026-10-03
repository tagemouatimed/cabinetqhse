@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"><h1>Prestations et prix standards</h1></div>
        <div class="card"><div class="card-body">
            <form action="{{ route('prestations.store') }}" method="POST" class="form-inline mb-3">
                @csrf
                <input type="text" name="libelle" class="form-control mr-2" placeholder="Libellé" required>
                <input type="number" step="0.01" name="prix_standard" class="form-control mr-2" placeholder="Prix standard" required>
                <button class="btn btn-primary btn-sm">Ajouter</button>
            </form>

            <table class="table table-striped" id="table-prestations">
                <thead><tr><th>Libellé</th><th>Prix standard</th><th></th></tr></thead>
                <tbody>
                    @foreach ($prestations as $p)
                    <tr>
                        <form action="{{ route('prestations.update', $p) }}" method="POST" class="form-inline">
                            @csrf @method('PUT')
                            <td><input type="text" name="libelle" value="{{ $p->libelle }}" class="form-control form-control-sm"></td>
                            <td><input type="number" step="0.01" name="prix_standard" value="{{ $p->prix_standard }}" class="form-control form-control-sm"></td>
                            <td>
                                <button class="btn btn-sm btn-icon"><i data-feather="save"></i></button>
                        </form>
                                <form action="{{ route('prestations.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-icon"><i data-feather="trash-2"></i></button>
                                </form>
                            </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div></div>
    </section>
</div>
@endsection
@push('scripts')<script>$(function(){$('#table-prestations').DataTable();});</script>@endpush
