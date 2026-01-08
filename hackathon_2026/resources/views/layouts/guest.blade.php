<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css" />

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>

        <style>
            html, body {
                background-color: #f3f4f6;
                color: #000;
                font-family: 'Raleway', sans-serif;
                font-weight: 400;
                margin: 0;
            }
            input[type="text"],
            input[type="email"],
            input[type="password"] {
                border: 2px solid #374151 !important;
                color: #000 !important;
                background-color: #fff !important;
            }
            input[type="text"]:focus,
            input[type="email"]:focus,
            input[type="password"]:focus {
                border-color: #1f2937 !important;
                outline: none !important;
                ring: 2px !important;
            }
            label {
                color: #000 !important;
                font-weight: 600;
            }
        </style>
    </head>
    <body>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-8">
                <a href="/" class="logo-title" style="font-size: 84px;">
                    Constellation
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
