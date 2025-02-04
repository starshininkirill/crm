@extends('base')

@section('main')
    <div class="grow w-full py-4 px-2" id="lk-page">
        <div
            class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700 mb-8">
            <ul class="flex flex-wrap -mb-px">
                <li class="me-2">
                    <a href="{{ route('lk.contract.create') }}"
                        class="inline-block p-4 rounded-t-lg 
                              {{ request()->routeIs('lk.contract.create') ? 'text-blue-600 border-b-2 border-blue-600 active dark:text-blue-500 dark:border-blue-500' : 'border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}">
                        Договор
                    </a>
                </li>

                <li class="me-2">
                    <a href="{{ route('lk.act.create') }}"
                        class="inline-block p-4 rounded-t-lg 
                              {{ request()->routeIs('lk.act.create') ? 'text-blue-600 border-b-2 border-blue-600 active dark:text-blue-500 dark:border-blue-500' : 'border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}">
                        Платёж
                    </a>
                </li>

            </ul>
        </div>
        @yield('content')
    </div>
@endsection
