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
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span>{{ Auth::user()->name }}</span>
        @else
        <a href="{{ route('login') }}">Login</a>
        <a href="{{ route('register') }}">Register</a>
        @endauth
      </div>
      @endif

      <div class="content">@yield('content')</div>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts
  </body>
</html>