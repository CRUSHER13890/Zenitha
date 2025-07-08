<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('About Us') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <h3 class="text-2xl">Welcome to Our Company!</h3>
                <p class="mt-4 text-lg text-gray-600">We are a passionate team focused on delivering quality products and services to help you achieve your goals. Our mission is to provide innovative solutions to our customers by leveraging cutting-edge technology and industry best practices.</p>
            </div>
        </div>
    </div>
</x-app-layout>
