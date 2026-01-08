<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Laravel - @yield('title')</title>

    <!-- Fonts -->
    <link
      href="https://fonts.googleapis.com/css?family=Raleway:100,600"
      rel="stylesheet"
      type="text/css"
    />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Styles -->
    <style>
      html,
      body {
        background-color: #f3f4f6;
        color: #000;
        font-family: "Raleway", sans-serif;
        font-weight: 400;
        height: 100vh;
        margin: 0;
      }
      .full-height {
        min-height: 100vh;
      }
      .flex-center {
        /* align-items: center; Remove vertical centering to prevent clipping on small screens */
        display: flex;
        justify-content: center;
        padding: 40px 20px; /* Add top/bottom padding */
        box-sizing: border-box;
      }
      .position-ref {
        position: relative;
      }
      .top-right {
        position: absolute;
        right: 10px;
        top: 18px;
      }
      .content {
        text-align: center;
      }
      .title {
        font-size: 84px;
      }
      .logo-title {
        font-size: 48px;
        font-weight: bold;
        color: #000;
        text-decoration: none;
        transition: color 0.3s;
      }
      .logo-title:hover {
        color: #4a5568;
      }
      .links > a {
        color: black;
        padding: 0 25px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.1rem;
        text-decoration: none;
        text-transform: uppercase;
      }
      .m-b-md {
        margin-bottom: 30px;
      }
    </style>
  </head>
  <body>
    <div class="flex-center position-ref full-height">
      @if (Route::has('login'))
      <div class="top-right links">
        @auth
        <a href="{{ route('dashboard') }}">Tableau de bord</a>
        <div class="inline-block relative group">
          <button class="inline-flex items-center">
            <span>{{ Auth::user()->name }}</span>
            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div class="hidden group-hover:block absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Déconnexion</button>
            </form>
          </div>
        </div>
        @else
        <a href="{{ route('login') }}">Connexion</a>
        <a href="{{ route('register') }}">Inscription</a>
        @endauth
      </div>
      @endif

      <div class="content">@yield('content')</div>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts
  </body>
</html>