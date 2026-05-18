<x-layout>
    <h1 class="my-16 text-center text-4xl font-medium text-slate-600">
        Зареєструйте аккаунт
    </h1>

    <x-card class="py-8 px-16">
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-8">
                <label for="email">E-mail</label>
                <x-search-bar type="text" name="email" />
            </div>

            <div class="mb-8">
                <label for="first_name">Ім'я</label>
                <x-search-bar type="text" name="first_name" />
            </div>

            <div class="mb-8">
                <label for="last_name">Прізвище</label>
                <x-search-bar type="text" name="last_name" />
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
            </div>

            <button class="w-full bg-green-500 text-2xl font-semibold">Зареєструватися</button>
        </form>
    </x-card>
</x-layout>