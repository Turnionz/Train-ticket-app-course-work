<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Перегляд місць' => '#' ]" />

    <h1 class="text-2xl font-bold mb-2">{{ $trip->route->departStation->address }} - {{ $trip->route->arrivalStation->address }}</h1>
    
    <form action="{{ route('trips.details', $trip) }}" method="POST">

        @csrf
        @method('POST')
        <input type="hidden" name="trip_id" value="{{ $trip->id }}">
        @forelse ($trip->train->wagons as $wagon)
            <div>
                <x-card class="m-3 col-span-8">
                    <div>
                        <h2 class="text-xl font-semibold">Номер вагона: {{ $wagon->wagon_number }}</h2>
                        <h3 class="text-lg font-medium">Тип вагона: {{ $wagon->type }}</h3>
                        <h2 class="text-xl font-semibold">{{ $wagon->seats[0]->class ?? '' }}</h2>
                    </div>
                    <div>
                        @php
                            $seats = $wagon->seats->sortBy('seat_number')->values();
                            $seatIndex = 0;
                        @endphp

                        @foreach ($wagon->layout_map as $row)
                            <div class="flex gap-2 m-2 justify-end">
                                @foreach ($row as $cell)
                                    <div class="flex">
                                        @if ($cell === 'seat')
                                            @php
                                                $currentSeat = $seats[$seatIndex] ?? null;
                                                $seatIndex++;
                                            @endphp

                                            @if($currentSeat)
                                                <label class="relative flex items-center justify-center w-16 h-16">
                                                    <input type="checkbox" name="seat_ids[]" value="{{ $currentSeat->id }}" 
                                                        @disabled($currentSeat->ticket)
                                                        class="absolute inset-0 w-full h-full appearance-none rounded-md 
                                                            bg-green-500 hover:bg-green-400 checked:bg-blue-600 cursor-pointer 
                                                            disabled:bg-gray-400 disabled:cursor-not-allowed disabled:hover:bg-gray-400 transition-colors">
                                                    
                                                    <span class="relative z-10 text-white font-bold pointer-events-none">
                                                        {{ $currentSeat->seat_number }}
                                                    </span>
                                                </label>
                                            @endif

                                        @elseif ($cell === null)
                                            <div class="w-8 h-8 bg-transparent"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach 
                    </div>
                </x-card>
            </div>
        @empty
            <h1 class="text-4xl font-semibold text-center mt-8">Поїзд не має вагонів!</h1>
        @endforelse

        @if($trip->train->wagons->isNotEmpty())
            <div class="fixed bottom-0 left-0 w-full bg-white border-t p-4 shadow-lg flex justify-end items-center z-50">
                <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <span class="text-gray-600">Оберіть місця на схемі, щоб продовжити</span>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow transition-colors">
                        Придбати квитки
                    </button>
                </div>
            </div>

            <div class="h-24"></div> 
        @endif
    </form>
</x-layout>