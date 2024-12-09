  <header class="bg-gray-800 border-b border-white">
      <div class="mx-auto container px-2">
          <div class="flex h-16 items-center justify-between">
              <div class="flex items-center justify-between w-full">
                  <div class=" flex items-baseline space-x-4">
                      <x-header-nav-link href="{{ route('home') }}" route="home">
                          Главная
                          </x-nav-link>
                          @auth
                              <x-header-nav-link href="{{ route('lk') }}" route="lk*">
                                  Личный кабинет
                                  </x-nav-link>
                              @endauth
                              <x-header-nav-link href="{{ route('admin') }}" route="admin*">
                                  Админка
                                  </x-nav-link>

                  </div>

                  <div class="flex items-center space-x-4">
                      @guest
                          <x-header-nav-link href="{{ route('login') }}" route="login">
                              Вход
                              </x-nav-link>
                          @endguest
                          @auth
                              <span class=" text-l text-white ">
                                  {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                              </span>
                              <x-header-nav-link href="{{ route('logout') }}" route="logout">
                                  Выйти
                                  </x-nav-link>
                              @endauth
                  </div>
              </div>
          </div>
      </div>
  </header>
