<?php

namespace App\Exports;

use App\Models\Contact;
use App\Models\Store;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContactsExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithCustomValueBinder, WithHeadings, WithMapping, WithStyles, WithTitle
{
    use Exportable;

    public function __construct(
        protected Store $store,
        protected Collection $contacts,
    ) {
    }

    public function collection(): Collection
    {
        return $this->contacts;
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }

    /**
     * @throws Exception
     */
    public function bindValue(Cell $cell, $value): bool
    {
        if ($cell->getColumn() === 'D') {
            $cell->setValueExplicit(value: $value, dataType: DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue(cell: $cell, value: $value);
    }

    /**
     * @param  Contact  $row
     */
    public function map($row): array
    {
        return [
            $row->provider_id,
            $row->name,
            $row->email ?? '-----',
            $row->mobile,
            Date::dateTimeToExcel(dateValue: $row->created_at),
            isInBlacklistedMobiles(mobile: $row->mobile, store: $this->store) ? __(key: 'dashboard.common.yes') : __(key: 'dashboard.common.no'),
        ];
    }

    public function headings(): array
    {
        return [
            __(key: 'dashboard.pages.contacts.columns.id'),
            __(key: 'dashboard.pages.contacts.columns.name'),
            __(key: 'dashboard.pages.contacts.columns.email'),
            __(key: 'dashboard.pages.contacts.columns.mobile'),
            __(key: 'dashboard.pages.contacts.columns.created_at'),
            __(key: 'dashboard.pages.contacts.columns.is_blacklisted'),
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle(cellCoordinate: 1)->getFont()->setBold(bold: true);
    }

    public function title(): string
    {
        return __(key: 'dashboard.pages.contacts.index.title')." - {$this->store->name} ({$this->store->provider_type->label()})";
    }
}
