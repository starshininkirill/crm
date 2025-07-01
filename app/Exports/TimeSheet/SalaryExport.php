<?php

namespace App\Exports\TimeSheet;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SalaryExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    use Exportable;

    private int $rowNumber = 1;

    public function __construct(
        protected mixed $users,
        protected int $halfOfMonth
    ) {}

    public function collection()
    {
        return $this->users;
    }

    public function headings(): array
    {
        return [
            'Номер',
            'Фамилия',
            'Имя',
            'Отчество',
            'Номер дебетового счета',
            'Сумма',
            'Назначение платежа',
            'Код вида дохода',
            'БИК банка',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function map($user): array
    {
        $amount = $this->halfOfMonth === 1
            ? round($user['amount_first_half_salary_with_compensation'])
            : round($user['amount_second_half_salary_with_compensation']);

        $firstName = $user['first_name'];
        $lastName = $user['last_name'];
        $surname = $user['surname'] ?? '';

        if ($user['employment_detail']->employmentType->is_another_recipient) {
            $firstName = $user['employment_detail']['details']['first_name'];
            $lastName = $user['employment_detail']['details']['last_name'];
            $surname = $user['employment_detail']['details']['surname'];
        }

        return [
            $this->rowNumber++,
            $lastName,
            $firstName,
            $surname,
            $user['payment_account'],
            $amount,
            'Выплата самозанятому по договору',
            1,
            '',
        ];
    }
}
