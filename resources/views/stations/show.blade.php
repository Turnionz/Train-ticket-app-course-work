<x-layout :$station>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Станції' => route('stations.index'), 'Станція №' .  $station->id => '#']" />

    <div class="mb-4 flex justify-between items-center w-full">
        <livewire:overlay-form 
                buttonName='Видалити станцію' 
                buttonStyle='bg-red-600 rounded-lg text-lg font-semibold p-2 hover:bg-red-500 hover:shadow-md cursor-pointer h-full w-full'>
                <h2 class="text-4xl font-semibold">Точно видалити станцію?</h2>
                <form action="{{ route('stations.destroy', $station) }}" method="POST" class="flex flex-auto w-full gap-4 items-center p-2 mb-2">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-center w-full">
                        <button type="submit" class="text-4xl font-semibold p-5 bg-red-600 rounded-md hover:bg-red-700 hover:shadow-md cursor-pointer w-full h-full mt-2">
                            Видалити станцію
                        </button>
                    </div>
                </form>
            </livewire:overlay-form>
            
        <livewire:overlay-form 
                buttonName='Додати сусідню станцію' 
                buttonStyle='bg-teal-300 rounded-lg text-lg font-semibold p-2 hover:bg-teal-400 hover:shadow-md cursor-pointer'
                :searchValues="['search' => 'text']"
                :filters="[
                    '\App\Models\Station' => ['address'],
                ]"
                :whatRelationsToFind="[
                    '\App\Models\Station' => ['address'],    
                ]"
                field='id'
                :globalSearch=false
                >
                <h2 class="pt-2 pl-3 text-xl font-semibold">Додати сусідню станцію</h2>
                <form action="{{ route('registerNeighbour', $station) }}" method="POST" class="flex flex-auto w-full gap-4 items-center p-2 mb-2">
                    @csrf
                    @method('PUT')
                    <div class="flex-1 bg-teal-300 rounded-md border-0">
                        <x-search-bar placeholder="Номер станції" name="station_id" type="number" class="text-lg"/>
                    </div>
                    <div class="flex justify-center">
                        <button type="submit" class="text-lg font-medium p-2 bg-teal-300 rounded-md hover:bg-teal-400 hover:shadow-md cursor-pointer">
                            Додати сусідню станцію
                        </button>
                    </div>
                </form>
            </livewire:overlay-form>

        <form action="{{ route('stations.edit', $station) }}">
            <button class="bg-teal-300 rounded-lg text-lg font-semibold p-2 hover:bg-teal-400 hover:shadow-md cursor-pointer"
                type="submit">
                Редагувати станцію
            </button>
        </form>
    </div>

    <h1 class="text-2xl font-semibold">{{ $station->address }} - ID: {{ $station->id }}</h1>

    <div class="grid grid-cols-5 w-full gap-2 p-2 relative">
    @forelse ($station->all_connected_stations as $connectedStation)
        <x-card class="relative hover:shadow-md mt-4 rounded-xl w-full min-h-[120px]" id="{{ $connectedStation->id }}">
            
            <form action="{{ route('deregNeighbour', $station->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="station_b" value="{{ $connectedStation->id }}">
                <button type="submit" class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 rounded-md z-10 cursor-pointer" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </form>

            <h3 class="absolute top-2 left-4 text-lg font-medium">{{ $connectedStation->id }}</h3>
            
            <a href="{{ route('stations.show', $connectedStation) }}" class="flex flex-col justify-between h-full p-4 pt-8">
                <h1 class="text-2xl font-bold text-center">{{ $connectedStation->address }}</h1>
                <h3 class="text-lg font-semibold text-center">Пролягає маршрутів: {{ $connectedStation->routeStops->count()}}</h3>
                <h3 class="text-lg font-semibold text-center">Сусідніх станцій: {{ $station->all_connected_stations->count()}}</h3>
            </a>
        </x-card>
    @empty
        <h1 class="text-4xl font-bold mb-5">Станцій немає!</h1>
    @endforelse
</div>

</x-layout>