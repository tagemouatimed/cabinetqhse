<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Export Excel générique : passer les en-têtes + une collection de tableaux associatifs.
 * Utilisé par tous les boutons "Exporter Excel" du module (convention commune à tous les modules).
 *
 * Exemple : Excel::download(new TableauExport($headings, $rows), 'clients.xlsx');
 */
class TableauExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected array $headings,
        protected Collection $rows
    ) {
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function collection(): Collection
    {
        return $this->rows;
    }
}
