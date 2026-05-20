<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Потяги' => route('trains.index'), 'Потяг №' . $train->id => route('trains.show', $train), 'Додавання вагонів' => '#' ]" />

    @php
        // Find all wagons that are NOT currently attached to a train
        $availableWagons = \App\Models\Wagon::whereNull('train_id')->get();
    @endphp
    <form action="{{ route('trains.update', $train)}}" method="POST">
        @csrf
        @method('PUT')
        <livewire:create-form 
            name="wagons"
            message="Оберіть вагон" 
            messageAdd="вагон" 
            :validValues="$availableWagons" 
            :load="['id', 'type']" /> 

        <button class="text-xl p-2 rounded-md bg-teal-300 font-semibold mt-4 hover:shadow-md hover:bg-teal-400 cursor-pointer">Додати Вагон</button>
    </form>
</x-layout>