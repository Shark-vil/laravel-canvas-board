<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
				@yield('styles')

        <!-- Scripts -->
				<script>
					window.APP_URL = "{{ env('APP_URL') }}";
				</script>
				<script src="{{ asset('js/jquery.js') }}"></script>
				{{-- <script src="{{ asset('js/gifler.min.js') }}"></script> --}}

				@if (env('APP_DEBUG') == true)
					<script src="{{ asset('js/app.js') }}?t={{ time() }}" defer></script>
				@else
					<script src="{{ asset('js/app.js') }}" defer></script>
				@endif

				@yield('scripts')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
						@if (isset($header))
							<header class="bg-white shadow">
									<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
											{{ $header }}
									</div>
							</header>
						@endif

            <!-- Page Content -->
            <main id="app">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
