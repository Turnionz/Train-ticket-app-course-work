<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Деталі замовлення' => '#' ]" />
    
    <h1 class="text-2xl font-bold mb-4">Перевірте деталі замовлення</h1>

    <form action="{{ route('trips.buy') }}" method="POST">
        @csrf
        <input type="hidden" name="trip_id" value="{{ $trip_id }}">

        @foreach ($seats as $seat)
            <x-card class="mt-4 p-4">
                <div class="flex flex-col gap-2 mb-4">
                    <h2 class="text-lg font-bold">
                        Тип потягу: {{ $seat->wagon->train->type }} 
                        <span>(Потяг №{{ $seat->wagon->train->id }})</span>
                    </h2>
                    
                    <h2 class="text-lg">
                        Вагон: <span>{{ $seat->wagon->wagon_number }}</span> 
                        <span>({{ $seat->wagon->type }})</span>
                    </h2>
                    
                    <h2 class="text-lg">
                        Місце: <span>{{ $seat->seat_number }}</span> 
                        <span>({{ $seat->class }})</span>
                    </h2>
                </div>
                
                <div>
                <div>
                    <livewire:create-form 
                        wire:key="depart-{{ $seat->id }}"
                        name="depart[{{ $seat->id }}]"
                        message="Початкова станція" 
                        messageAdd="станцію" 
                        :validValues="$availableStations" 
                        :load="['id', 'address']" 
                        :button="false" 
                        :default-values="$availableStations->first()->id" 
                    /> 
                </div>
                <div>
                    <livewire:create-form 
                        wire:key="arrive-{{ $seat->id }}"
                        name="arrive[{{ $seat->id }}]"
                        message="Кінцева станція" 
                        messageAdd="станцію" 
                        :validValues="$availableStations" 
                        :load="['id', 'address']" 
                        :button="false" 
                        :default-values="$availableStations->last()->id" 
                    /> 
                </div>
                
                <input type="hidden" name="seat_ids[]" value="{{ $seat->id }}">
            </x-card>
        @endforeach

        <button type="submit" class="mt-6 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg w-full md:w-auto text-xl transition-colors">
            Оформити квитки
        </button>
    </form>
</x-layout>