<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Потяги' => route('trains.index'), 'Потяг №' . $train->id => '#' ]" />

    <div class="mb-4 flex justify-between items-center w-full">
        <livewire:overlay-form 
            buttonName='Видалити потяг' 
            buttonStyle='bg-red-600 rounded-lg text-lg font-semibold p-2 hover:bg-red-500 hover:shadow-md cursor-pointer h-full w-full'>
            <h2 class="text-4xl font-semibold">Точно видалити потяг?</h2>
            <form action="{{ route('trains.destroy', $train) }}" method="POST" class="flex flex-auto w-full gap-4 items-center p-2 mb-2">
                @csrf
                @method('DELETE')
                <div class="flex justify-center w-full">
                    <button type="submit" class="text-4xl font-semibold p-5 bg-red-600 rounded-md hover:bg-red-700 hover:shadow-md cursor-pointer w-full h-full mt-2">
                        Видалити потяг
                    </button>
                </div>
            </form>
        </livewire:overlay-form>

        <x-link-button href="{{ route('trains.edit', $train) }}" class="rounded-lg bg-sky-400 hover:bg-sky-500 text-lg hover:shadow-md h-full">
            <h2>Додати вагони</h2>
        </x-link-button>
    </div>
    


    @forelse ($train->wagons as $wagon)
        <div class="grid grid-cols-9 gap-2">
            <x-card class="m-3 col-span-8">
                <div>
                    <h2 class="text-xl font-semibold">Номер вагона: {{ $wagon->wagon_number }}</h2>
                    <h3 class="text-lg font-medium">Тип вагона: {{ $wagon->type }}</h3>
                    <h2 class="text-xl font-semibold">{{ $wagon->seats[0]->class }}</h2>
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
                                                            disabled:bg-gray-400 disabled:cursor-not-allowed disabled:hover:bg-gray-400">
                                                
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
            <div class="m-3 grid flex rows-3 justify-between gap-2 items-center">
                <button>
                    <x-card class="flex w-full justify-center cursor-pointer hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                        </svg>
                    </x-card>
                </button>
                    <livewire:overlay-form 
                        buttonName='Зняти вагон з цього составу' 
                        buttonStyle='bg-red-600 rounded-lg text-3xl font-semibold p-2 hover:bg-red-500 hover:shadow-md cursor-pointer h-full w-full'>
                        <h2 class="text-4xl font-semibold">Точно Зняти вагон з цього составу?</h2>
                        <form action="#" method="POST" class="flex flex-auto w-full gap-4 items-center p-2 mb-2">
                            @csrf
                            @method('DELETE')
                            <div class="flex justify-center w-full">
                                <button type="submit" class="text-4xl font-semibold p-5 bg-red-600 rounded-md hover:bg-red-700 hover:shadow-md cursor-pointer w-full h-full mt-2">
                                    Зняти вагон з цього составу
                                </button>
                            </div>
                        </form>
                    </livewire:overlay-form>
                <button>
                    <x-card class="flex w-full justify-center cursor-pointer hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </x-card>
                </button>
            </div>
        </div>
    @empty
        <h1 class="text-4xl font-semibold text-center">Поїзд не має вагонів!</h1>
    @endforelse
</x-layout>