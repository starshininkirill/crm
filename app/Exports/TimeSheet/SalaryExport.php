<?php

namespace App\Exports\TimeSheet;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class SalaryExport implements FromCollection, WithHeadings, WithMapping
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

    public function map($user): array
    {
        $amount = $this->halfOfMonth === 1
            ? $user['amount_first_half_salary_with_compensation']
            : $user['amount_second_half_salary_with_compensation'];

        return [
            $this->rowNumber++,
            $user['last_name'],
            $user['first_name'],
            $user['surname'] ?? '',
            $user['payment_account'],
            $amount,
            'Выплата самозанятому по договору',
            1,
            '', // БИК банка
        ];
    }
}
