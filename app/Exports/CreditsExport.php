<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CreditsExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected $data;
    protected $filters;
    protected $user;

    public function __construct($data, $filters, $user)
    {
        $this->data = $data;
        $this->filters = $filters;
        $this->user = $user;
    }

    public function collection()
    {
        $collection = collect();

        // Ajouter les informations générales
        $collection->push(['RAPPORT DE CRÉDITS']);
        $collection->push(['Filtres appliqués : ' . json_encode($this->filters)]);
        $collection->push(['Généré par : ' . $this->user->name . ' (' . $this->user->role . ')']);
        $collection->push(['Date de génération : ' . now()->format('d/m/Y H:i:s')]);
        $collection->push([]);

        // Ajouter les totaux
        $collection->push(['RÉSUMÉ']);
        $collection->push(['Total des crédits', $this->data['totalCredits']]);
        $collection->push(['Montant total', number_format($this->data['totalAmount'], 0, ',', ' ') . ' FCFA']);
        $collection->push(['Solde restant total', number_format($this->data['totalRemaining'], 0, ',', ' ') . ' FCFA']);
        $collection->push([]);

        // Ajouter le détail des crédits
        $collection->push(['DÉTAIL DES CRÉDITS']);
        $collection->push(['Client', 'Boutique', 'Montant total', 'Solde restant', 'Date d\'échéance', 'Statut', 'Date de création']);
        
        foreach ($this->data['credits'] as $credit) {
            $collection->push([
                $credit->client->nom,
                $credit->vente->boutique->nom,
                number_format($credit->total_amount, 0, ',', ' ') . ' FCFA',
                number_format($credit->remaining_balance, 0, ',', ' ') . ' FCFA',
                $credit->due_date ? $credit->due_date->format('d/m/Y') : 'N/A',
                $credit->status,
                $credit->created_at->format('d/m/Y'),
            ]);
        }

        return $collection;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Rapport Crédits';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['italic' => true]],
            3 => ['font' => ['italic' => true]],
            4 => ['font' => ['italic' => true]],
            6 => ['font' => ['bold' => true]],
            7 => ['font' => ['bold' => true]],
            8 => ['font' => ['bold' => true]],
            9 => ['font' => ['bold' => true]],
            11 => ['font' => ['bold' => true]],
            12 => ['font' => ['bold' => true]],
            // Styles for headers
            'A' => ['font' => ['bold' => true]],
            'B' => ['font' => ['bold' => true]],
            'C' => ['font' => ['bold' => true]],
            'D' => ['font' => ['bold' => true]],
            'E' => ['font' => ['bold' => true]],
            'F' => ['font' => ['bold' => true]],
            'G' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Client
            'B' => 20, // Boutique
            'C' => 15, // Montant total
            'D' => 15, // Solde restant
            'E' => 15, // Date échéance
            'F' => 10, // Statut
            'G' => 15, // Date création
        ];
    }
}
