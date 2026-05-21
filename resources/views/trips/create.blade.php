<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Станції' => route('stations.index'), 'Створення' => '#']" />

    @php
            $availableStations = \App\Models\Route::all();
            $availableTrains = \App\Models\Train::all();
    @endphp

    <x-card>
        <form id="trips-form" action="{{ route('trips.tripCreate') }}" method="POST">
            @csrf
            @method('POST')
            <label for="trains">Оберіть поїзд</label>
            <livewire:create-form 
            name="trains"
            message="Оберіть поїзд" 
            :button=false 
            :validValues="$availableTrains" 
            :load="['train_number']" /> 

            <label for="route">Оберіть направленя</label>
            <livewire:create-form 
            name="route"
            message="Оберіть направлення" 
            :button=false 
            :validValues="$availableStations"
            :with="['stations']"
            :load="['departStation.address', 'arrivalStation.address']" /> 
            
            <label for="route">Дата та час </label>
            <div class="flex-1 bg-white mb-2">
                    <x-search-bar name="date" type="datetime-local" class="text-lg"/>
                </div>

            <div class="flex justify-center">
                <button type="submit" form="trips-form" class="text-2xl font-bold p-2 bg-teal-300 rounded-md hover:bg-teal-400 hover:shadow-md cursor-pointer">
                    Створити станцію
                </button>
            </div>

        </form>
    </x-card>
</x-layout>