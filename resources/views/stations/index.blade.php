<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Станції' => '#']" />

    <div class="mb-4 flex justify-between items-center w-full">
        <x-link-button href="{{ route('stations.create') }}" class="bg-teal-400 hover:bg-teal-500 text-lg">
            Додати станцію
        </x-link-button>
    </div>

    <div class="grid grid-cols-5 w-full gap-2 p-2">
        @forelse ($stations as $station)
            <x-card class="hover:shadow-md mt-4 rounded-xl w-full min-h-[120px]">
                <a href="{{ route('stations.show', $station) }}" class="flex flex-col justify-between h-full p-4">
                        <h1 class="text-2xl font-bold text-center">{{ $station->address }}</h1>
                        <h3 class="text-lg font-semibold text-center">Пролягає маршрутів: {{ $station->routeStops->count()}}</h3>
                        <h3 class="text-lg font-semibold text-center">Сусідніх станцій: {{ $station->all_connected_stations->count()}}</h3>
                </a>
            </x-card>
        @empty
            <h1 class="text-4xl font-bold mb-5">Станцій немає немає!</h1>
        @endforelse
    </div>
    
    @if($stations->count())
        <nav class="mt-4">{{$stations->links()}}</nav>
    @endif
</x-layout>