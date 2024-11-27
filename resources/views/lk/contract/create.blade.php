@extends('lk.base')

@section('content')
    <div>
        <h1 class=" text-4xl font-bold mb-5">
            Создание Договора
        </h1>
        @if (session('success'))
            <div class="mb-3 w-fit bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <ul class="flex flex-col gap-1 mb-4">
                @foreach ($errors->all() as $error)
                    <li class="text-red-400">{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <vue-contract-create-form 
            :rk-text='@json($rkText)'
            :string-cats='@json($cats)'
            :string-main-cats='@json($mainCats)' 
            :string-secondary-cats='@json($secondaryCats)'
            action="{{ route('contract.store') }}" 
            token="{{ csrf_token() }}"
            row-old='@json(old())'
            />


    </div>
@endsection
