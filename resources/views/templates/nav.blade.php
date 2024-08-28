<nav class="flex flex-shrink-0 flex-col gap-1 w-1/8 pl-1 pr-6 py-6 bg-light border-r border-solid border-gray-300">
    <a class=" text-xl {{ Route::is('admin.contract*') ? 'font-bold' : '' }}" href="{{ route('admin.contract.index') }}">
        Договора
    </a>
    <a class="text-xl {{ Route::is('admin.payment*') ? 'font-bold' : '' }}" href="{{ route('admin.payment.index') }}">
        Платежи
    </a>
    <a class="text-xl {{ Route::is('admin.user*') ? 'font-bold' : '' }}" href="{{ route('admin.user.index') }}">
        Сотрудники
    </a>
    <a class="text-xl {{ Route::is('admin.service*') ? 'font-bold' : '' }}" href="{{ route('admin.service.category.index') }}">
        Услуги
    </a>
    <a class="text-xl {{ Route::is('admin.department*') ? 'font-bold' : '' }}" href="{{ route('admin.department.index') }}">
        Отделы
    </a>
    <a class="text-xl {{ Route::is('admin.department.sale*') ? 'font-bold' : '' }}" href="{{ route('admin.department.sale.index') }}">
        Отдел продаж
    </a>
</nav>