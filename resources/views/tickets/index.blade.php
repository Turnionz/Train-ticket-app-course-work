<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Мої квитки' => '#' ]" />

    <h1 class="text-2xl font-bold mb-4">Мої квитки</h1>
    
    <div class="flex flex-col gap-4">
        @forelse ($tickets as $ticket)
            <x-card class="p-4">
                <div class="grid grid-cols-3 justify-between">
                    <div>
                        <div class="grid w-1/3">
                            <h2 class="text-xl font-semibold">Квиток № {{ $ticket->id }}</h2>
                            <p class="text-lg">Статус: {{ $ticket->status }}</p>
                            <div class="text-lg">Номер поїзду: {{ $ticket->trip->train->train_number }}</div>
                            <div class="text-lg">{{ $ticket->trip->train->type }}</div>
                        </div>
                    </div>
                    <div>
                       <div class="grid">
                            <div class="text-lg"></div>
                            <div class="text-lg">Номер місця: {{ $ticket->seat->seat_number }}</div>
                            <div class="text-lg">Клас: {{ $ticket->seat->class}}</div>
                        </div>
                    </div>
                    <div class="justify-between w-full col-3">
                        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 justify-between mt-1">
                            <div class="align-left">
                                <h3 class="text-xl font-semibold justify-between">{{ $ticket->trip->depart_time->format('M-d') }}</h3>
                                <span class="text-lg font-semibold">{{ substr($ticket->departingStation->address, 0, 20) }}</span>
                                <div>Відбуєває о {{ $ticket->trip->depart_time->format('H:i') }}</div>
                            </div>
                            <div class="hidden md:flex flex-1 mt-3 flex justify-between relative">
                                <div class="absolute left-1/2 -translate-x-1/2 -translate-y-3/7 bg-white rounded-xl px-1 py-1 text-center">
                                    <p>{{ date_diff($ticket->trip->depart_time, $ticket->trip->arrival_time, true)->format('%H:%I') }}</p>
                                </div>
                                <div class="w-[70%] mx-auto bg-black h-1 rounded-xl"></div>
                            </div>
                            <div class="align-left">
                                <h3 class="text-xl font-semibold justify-between">{{ $ticket->trip->depart_time->format('M-d') }}</h3>
                                <span class="text-lg font-semibold">{{ substr($ticket->arrivalStation->address, 0, 20) }}</span>
                                <div>Відбуєває о {{ $ticket->trip->arrival_time->format('H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        @empty
            <h2 class="text-2xl font-semibold text-center mt-10">Квитків немає!</h2>
        @endforelse
    </div>
</x-layout>