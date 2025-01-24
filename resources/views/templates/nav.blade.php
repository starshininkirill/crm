<div class=" w-56 bg-gray-800 text-white flex flex-col">
    <!-- Navigation -->
    <nav class="flex-1">
        <ul class="space-y-1 p-4">
            <li>
                <a class="flex items-center p-2 hover:bg-gray-900 rounded {{ Route::is('admin.contract*') ? 'bg-gray-900 text-white' : '' }}"
                    href="{{ route('admin.contract.index') }}">
                    Договоры
                </a>
            </li>
            <li>
                <a class="flex items-center p-2 hover:bg-gray-900 rounded {{ Route::is('admin.payment*') ? 'bg-gray-900 text-white' : '' }}"
                    href="{{ route('admin.payment.index') }}">
                    Платежи
                </a>
            </li>
            <li>
                <a class="flex items-center p-2 hover:bg-gray-900 rounded {{ Route::is('admin.organization*') ? 'bg-gray-900 text-white' : '' }}"
                    href="{{ route('admin.organization.index') }}">
                    Организации
                </a>
            </li>
            <li>
                <a class="flex items-center p-2 hover:bg-gray-900 rounded {{ Route::is('admin.service*') ? 'bg-gray-900 text-white' : '' }}"
                    href="{{ route('admin.service.category.index') }}">
                    Услуги
                </a>
            </li>
            <li>
                <a class="flex items-center p-2 hover:bg-gray-900 rounded {{ Route::is('admin.user*') ? 'bg-gray-900 text-white' : '' }}"
                    href="{{ route('admin.user.index') }}">
                    Сотрудники
                </a>
            </li>
            <li>
                <a class="flex items-center p-2 hover:bg-gray-900 rounded {{ Route::is('admin.department*') ? 'bg-gray-900 text-white' : '' }}"
                    href="{{ route('admin.department.index') }}">
                    Отделы
                </a>
            </li>
            <li>
                <a class="flex items-center p-2 hover:bg-gray-900 rounded {{ Route::is('admin.sale-department*') ? 'bg-gray-900 text-white' : '' }}"
                    href="{{ route('admin.sale-department.index') }}">
                    Отдел продаж
                </a>
            </li>
            <li>
                <a class="flex items-center p-2 hover:bg-gray-900 rounded {{ Route::is('admin.settings*') ? 'bg-gray-900 text-white' : '' }}"
                    href="{{ route('admin.settings.index') }}">
                    Настройки
                </a>
            </li>

        </ul>
    </nav>

</div>
