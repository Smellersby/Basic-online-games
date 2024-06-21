<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <style>
        button{
        background-color: rgb(248, 248, 248);
        border-radius: 6px;
        border-style: solid;
        border-color: black;
        padding: 4px;
        border-width: 1px;
    }
    button:hover{
        background-color: rgb(226, 226, 226);
    }
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                    <br><br>
                    <a href="/lobbies">
                        <button>return to list</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
