<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Мої квитки' => '#' ]" />

    <h1 class="text-2xl font-bold mb-4">Мої квитки</h1>
    
        <div class="mb-2 bg-teal-200 rounded-lg pb-1">
            <h2 class="pt-2 pl-3 text-3xl font-semibold">Знайти білет</h2>
            <form action="{{ route('tickets.index') }}" method="GET" class="flex flex-auto w-full gap-4 items-center p-2">
                <div class="flex-1 bg-white">
                    <x-search-bar placeholder="Звідки" name="from" type="text" class="text-lg"/>
                </div>
                <div class="flex-1 bg-white">
                    <x-search-bar placeholder="Куди" name="to" type="text" class="text-lg"/>
                </div>
                <div class="flex-1 bg-white">
                    <x-search-bar name="date" type="date" class="text-lg"/>
                </div>
                <div class="flex-1">
                    <button class="cursor-pointer bg-emerald-300 rounded-xl text-2xl p-2 font-semibold text-center w-full hover:bg-emerald-400 hover:shadow-md">Знайти</button>
                </div>  
            </form>
        </div>

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

                <div class="mt-4 flex justify-end">
                    <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" onsubmit="return confirm('Ви впевнені, що хочете скасувати цей квиток?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                            Скасувати квиток
                        </button>
                    </form>
                </div>
            </x-card>
        @empty
            <h2 class="text-2xl font-semibold text-center mt-10">Квитків немає!</h2>
        @endforelse
    </div>
</x-layout>