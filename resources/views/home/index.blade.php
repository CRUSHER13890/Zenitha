<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Homepage</title>
    <!-- icons -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="container-lg h-screen overflow-hidden">
        <!-- Navbar Start -->
        @include('home.navbar', ['title' => 'Homepage'])
        <!-- Navbar End -->

        <!-- Content Start -->
        <div class="flex items-center justify-center h-full flex-col">
            <h1 class="text-6xl font-bold text-yellow-400">ZENITHA BEAUTYCARE</h1>
            <p class="text-xl text-gray-400">application</p>
            <a href="{{ route('home.products') }}"
                class="border-2 border-gray-500 rounded-md bg-yellow-400 px-5 py-2 mt-2 hover:bg-yellow-500">Explore</a>
        </div>
        <!-- Content End -->
    </div>
</body>

</html>
