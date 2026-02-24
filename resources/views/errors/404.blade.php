<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 &mdash; Page Not Found</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="h-full bg-gray-50 flex items-center justify-center">
<div class="text-center px-4">
    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-2xl mb-6">
        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <h1 class="text-4xl font-bold text-gray-900 mb-2">404</h1>
    <h2 class="text-lg font-semibold text-gray-700 mb-2">Page Not Found</h2>
    <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">
        The page you're looking for doesn't exist or has been moved.
    </p>
    <a href="{{ route('dashboard') }}"
        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition">
        &larr; Back to Dashboard
    </a>
</div>
</body>
</html>
