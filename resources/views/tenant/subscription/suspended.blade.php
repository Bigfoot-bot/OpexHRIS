<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facility Suspended</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased flex items-center justify-center min-h-screen">
    <div class="text-center max-w-md mx-auto px-6">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Facility Suspended</h1>
        <p class="text-gray-500 text-sm mb-2">Access to this facility has been suspended.</p>
        <p class="text-gray-400 text-xs mb-8">Please contact support to resolve this issue before you can log in again.</p>
        <form method="POST" action="{{ route('tenant.logout') }}">
            @csrf
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                Sign Out
            </button>
        </form>
    </div>
</body>
</html>
