<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Вагони' => route('wagons.index'), 'Вагон №' . $wagon->id => '#' ]" />

    <div class="mb-4 flex justify-between items-center w-full">
        <!-- Кнопки Видалити та Редагувати залишаються без змін -->
        <livewire:overlay-form buttonName='Видалити вагон' buttonStyle='bg-red-600 rounded-lg text-lg font-semibold p-2 hover:bg-red-500 hover:shadow-md cursor-pointer h-full w-full'>
            <h2 class="text-4xl font-semibold">Точно видалити вагон?</h2>
            <form action="{{ route('wagons.destroy', $wagon) }}" method="POST" class="flex flex-auto w-full gap-4 items-center p-2 mb-2">
                @csrf @method('DELETE')
                <div class="flex justify-center w-full">
                    <button type="submit" class="text-4xl font-semibold p-5 bg-red-600 rounded-md hover:bg-red-700 hover:shadow-md cursor-pointer w-full h-full mt-2">
                        Видалити вагон
                    </button>
                </div>
            </form>
        </livewire:overlay-form>
        <livewire:overlay-form buttonName='Редагувати вагон' buttonStyle='bg-teal-400 rounded-lg text-lg font-semibold p-2 hover:bg-teal-500 hover:shadow-md cursor-pointer h-full w-full'>
            <h2 class="text-4xl font-semibold text-center">Редагування</h2>
            <form action="{{ route('wagons.update', $wagon) }}" method="POST" class="flex flex-auto w-full gap-4 items-center p-2 mb-2">
                @csrf @method('PUT')
                <div class="grid gap-2 mt-2 justify-center">
                    <div>
                        <input name="train_number" type="number" class="rounded-md bg-teal-100 p-2 text-lg mt--2 w-full focus:bg-teal-200" placeholder="Номер вагона" value="{{ $wagon->train->train_number ?? '' }}">
                    </div>
                    <div>
                        <button type="submit" class="text-xl font-semibold p-2 bg-teal-400 rounded-md hover:bg-teal-500 hover:shadow-md cursor-pointer w-full h-full mt-5">Редагувати вагон</button>
                    </div>
                </div>
            </form>
        </livewire:overlay-form>
    </div>

    @forelse (($wagon->train->trip ?? []) as $trip)
        <x-card class="m-3 col-span-8 mb-8">
            <div class="mb-6 flex justify-between items-end border-b pb-4 border-gray-200">
                <div>
                    <span class="px-3 py-1 text-sm font-semibold mb-2 inline-block">
                        Рейс {{ $trip->name ?? '№' . $trip->id }}
                    </span>
                    <h2 class="text-xl font-bold">Відправлення: {{ \Carbon\Carbon::parse($trip->departure_time)->format('d.m.Y H:i') }}</h2>
                    <h3 class="text-lg text-gray-600">Прибуття: {{ \Carbon\Carbon::parse($trip->arrival_time)->format('d.m.Y H:i') }}</h3>
                </div>
                <div class="text-right">
                    <h2 class="text-xl font-semibold">Вагон №{{ $wagon->wagon_number }} ({{ $wagon->type }})</h2>
                    <span class="text-gray-500">{{ $wagon->seats->first()->class ?? '' }}</span>
                </div>
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
                                        
                                        $isOccupied = false;
                                        if ($currentSeat) {
                                            $isOccupied = $currentSeat->tickets
                                                ->where('trip_id', $trip->id)
                                                ->whereIn('status', [\App\Models\Ticket::$status[0], \App\Models\Ticket::$status[1]])
                                                ->isNotEmpty();
                                        }
                                    @endphp

                                    @if($currentSeat)
                                        <label class="relative flex items-center justify-center w-16 h-16">
                                            <input type="checkbox" name="seat_ids[{{ $trip->id }}][]" value="{{ $currentSeat->id }}" 
                                                @disabled($isOccupied) onclick="return false;"
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
    @empty
        <x-card class="m-3 col-span-8 mb-8">
            <div class="mb-6 flex justify-between items-end border-b pb-4 border-gray-200">
                <div>
                    <h2 class="text-xl font-bold text-red-600">Рейсів не заплановано!</h2>
                    <p class="text-gray-500">Схема вагона відображена для ознайомлення.</p>
                </div>
                <div class="text-right">
                    <h2 class="text-xl font-semibold">Вагон №{{ $wagon->wagon_number }} ({{ $wagon->type }})</h2>
                    <span class="text-gray-500">{{ $wagon->seats->first()->class ?? '' }}</span>
                </div>
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
                                                disabled onclick="return false;"
                                                class="absolute inset-0 w-full h-full appearance-none rounded-md 
                                                        bg-gray-300 cursor-not-allowed transition-colors">
                                            
                                            <span class="relative z-10 text-gray-500 font-bold pointer-events-none">
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
    @endforelse
</x-layout>