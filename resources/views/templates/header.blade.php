  <header class="bg-gray-800">
      <div class="mx-auto container px-2">
          <div class="flex h-16 items-center justify-between">
              <div class="flex items-center justify-between w-full">
                  <div class=" flex items-baseline space-x-4">
                      <x-header-nav-link href="{{ route('home') }}" route="home">
                          Главная
                          </x-nav-link>
                          <x-header-nav-link href="{{ route('admin') }}" route="admin*">
                              Админка
                              </x-nav-link>
                  </div>
                  <div class="flex items-center space-x-4">
                        @guest
                          <x-header-nav-link href="{{ route('home') }}" route="home">
                              Вход
                              </x-nav-link>
                              <x-header-nav-link href="{{ route('home') }}" route="home">
                                  Регистрация
                                </x-nav-link>
                        @endguest
                  </div>
              </div>
          </div>
      </div>
  </header>
