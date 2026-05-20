<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Потяги' => '#']" />

    @forelse ($trains as $train)
        <x-card class="hover:shadow-md mt-4 rounded-xl">
            <a href="{{ route('trains.show', $train) }}">
                <div class="grid grid-cols-3 justify-between">
                    <div>
                        <div class="grid grid-cols-2 w-1/3">
                            <h2 class="text-xl font-semibold justify-between">Номер потяга: {{ $train->train_number }}</h2>
                            <h2 class="text-lg font-medium justify-between">Тип: {{ $train->type }}</h2>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-lg font-medium">Кількість вагонів: {{ $train->wagons->count() }}</h2>
                        <h2 class="text-lg font-medium">Кількість місць: {{ $train->seats->count() }}</h2>
                        <h2 class="text-lg font-medium">З них вільних: {{ $train->seats->count() - \App\Models\Ticket::where('id', '=', $train->id)->count() }}</h2>
                    </div>
                    @php
                        $soonest = $train->trip
                            ->sortBy('depart_time')
                            ->first();
                    @endphp
                    @if ($soonest !== null)
                        <div class="justify-between w-full col-3">
                        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 justify-between mt-1">
                            <div class="align-left">
                                <h3 class="text-xl font-semibold justify-between">
                                        {{ $soonest->depart_time->format('M-d') }}
                                </h3>
                                <span class="text-lg font-semibold">
                                        {{ substr($soonest->route->departStation->address, 0, 20) }}
                                </span>
                                <div>
                                        Відбуєває о {{ $soonest?->depart_time?->format('H:i') }}
                                </div>
                            </div>
                            <div class="hidden md:flex flex-1 mt-3 flex justify-between relative">
                                <div class="absolute left-1/2 -translate-x-1/2 -translate-y-3/7 bg-white rounded-xl px-1 py-1 text-center">
                                    <p>{{ date_diff($soonest->depart_time, $soonest->arrival_time, true)->format('%H:%I') }}</p>
                                </div>
                                <div class="w-[70%] mx-auto bg-black h-1 rounded-xl"></div>
                            </div>
                            <div class="align-left">
                                <h3 class="text-xl font-semibold justify-between">{{ $soonest->arrival_time->format('M-d') }}</h3>
                                <span class="text-lg font-semibold">{{ substr($soonest->route->arrivalStation->address, 0, 20) }}</span>
                                <div>Прибуває о {{ $soonest->arrival_time->format('H:i') }}</div>
                            </div>
                        </div>
                    </div>
                    @else
                        <h2 class="text-xl font-semibold mb-5 col-3 text-center">Найблжичого рейса поки що нема!</h2>
                    @endif
                </div>
            </a>
        </x-card>
    @empty
        <h1 class="text-4xl font-bold mb-5">Немає потягів!</h1>
    @endforelse

    @if($trains->count())
        <nav class="mt-4">{{$trains->links()}}</nav>
    @endif
</x-layout>