<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Станції' => route('stations.index'), 'Станція №' .  $station->id => route('stations.show', $station), 'Редагування' => '#']" />

    @php
            $availableStations = \App\Models\Station::all();
            $stationToRemove = $station->getAllConnectedStationsAttribute()
    @endphp

    <h1 class="text-4xl text-center font-bold">Редагування Станції №{{ $station->id }}</h1>

    <form action="{{ route('stations.update', $station)}}" method="POST">
            @csrf
            @method('PUT')
            <div class="flex flex-col">
                <label for="address">Аддресса</label>
                <input 
                type="text" name="address" value="{{ $station->address }}" id=""
                class='rounded-md border border-slate-300 bg-emerald-100 px-4 py-3 shadow-sm focus:outline-none focus:border-transparent focus:ring-2 focus:ring-sky-500'>
                @error('address')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div class="flex flex-col">
                <label for="capacity">Кількість колій</label>
                <input 
                type="number" name="capacity" value="{{ $station->capacity }}" id=""
                class='rounded-md border border-slate-300 bg-emerald-100 px-4 py-3 shadow-sm focus:outline-none focus:border-transparent focus:ring-2 focus:ring-sky-500'>
                @error('capacity')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <livewire:create-form 
                name="stations-add"
                message="Оберіть сусідні станції для додавання" 
                messageAdd="станцію" 
                :validValues="$availableStations" 
                :load="['id', 'address']" /> 

            <livewire:create-form 
                name="stations-remove"
                message="Оберіть станції котрі видалити зі списку сусідів" 
                messageAdd="станцію" 
                :validValues="$stationToRemove" 
                :load="['id', 'address']" /> 
                

            <button class="text-2xl p-2 rounded-md bg-teal-300 w-full font-semibold mt-4 hover:shadow-md hover:bg-teal-400 cursor-pointer">
                Редагувати
            </button>
        </form>
</x-layout>