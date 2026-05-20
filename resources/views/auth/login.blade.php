<x-layout>
    <h1 class="my-16 text-center text-4xl font-medium text-slate-600">
        Увійдіть в ваш аккаунт
    </h1>

    <x-card class="py-8 px-16">
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-8">
                <label for="email">E-mail</label>
                <x-search-bar type="text" name="email" />
            </div>

            <div class="mb-8">
                <label for="password">Пароль</label>
                <x-search-bar type="password" name="password" />
            </div>

            <div class="mb-8 flex justify-between text-sm font-medium">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="remember" class="rounder-sm border border-slate-400">
                    <label for="remember">Запам'ятати мене</label>
                </div>
                <div>
                    <a href="#" class="text-indigo-600 hover:underline">
                        Забули пароль
                    </a>
                </div>
            </div>

            <button class="w-full bg-teal-400 hover:bg-teal-500 text-2xl font-semibold rounded-md">Увійти</button>
        </form>
    </x-card>
</x-layout>