<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Станції' => route('stations.index'), 'Створення' => '#']" />

    @php
            $availableStations = \App\Models\Station::all();
    @endphp

    <x-card>
        <form id="station-form" action="{{ route('stations.store') }}" method="POST">
            @csrf
            @method('POST')
            <div class="flex flex-col">
                <label for="address" class="mb-2">Аддресса</label>
                <input 
                class='rounded-md border border-slate-300 bg-emerald-100 px-4 py-3 shadow-sm focus:outline-none focus:border-transparent focus:ring-2 focus:ring-sky-500' 
                type="text" name="address" />
                @error('address')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div class="flex flex-col">
                <label for="address" class="mb-2">Кількість колій</label>
                <input 
                class='rounded-md border border-slate-300 bg-emerald-100 px-4 py-3 shadow-sm focus:outline-none focus:border-transparent focus:ring-2 focus:ring-sky-500' 
                type="number" name="capacity" />
                @error('capacity')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <livewire:create-form 
            name="stations"
            message="Оберіть сусідні станції" 
            messageAdd="станцію" 
            :validValues="$availableStations" 
            :load="['id', 'address']" /> 
            
        </form>
        <div type="submit" class="flex justify-center">
                <button type="submit" form="station-form" class="text-2xl font-bold p-2 bg-teal-300 rounded-md hover:bg-teal-400 hover:shadow-md cursor-pointer">
                    Створити станцію
                </button>
            </div>
    </x-card>
</x-layout>