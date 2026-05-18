<x-layout>

    <x-breadcrumbs class="mb-4" :links="['Рейси' => '#']" />
        
    <div class="mb-2 bg-teal-200 rounded-lg pb-1">
        <h2 class="pt-2 pl-3 text-3xl font-semibold">Знайти білет</h2>
        <form action="" class="flex flex-auto w-full gap-4 items-center p-2">
            <div class="flex-1 bg-white">
                <x-search-bar placeholder="Звідки" name="from" type="text" class="text-lg"/>
            </div>
            <div class="flex-1 bg-white">
                <x-search-bar placeholder="Куди" name="to" type="text" class="text-lg"/>
            </div>
            <div class="flex-1 bg-white">
                <x-search-bar name="dd/mm/yyyy" type="date" class="text-lg"/>
            </div>
            <div class="flex-1">
                <button class="bg-emerald-400 rounded-xl text-2xl p-2 font-semibold text-center w-full">Знайти</button>
            </div>  
        </form>
    </div>
    
    @foreach ($trips as $trip)
        <x-card class="hover:shadow-md mt-4 rounded-xl">
            <a href="{{ route('trips.show', $trip) }}">
                <div class="grid grid-cols-3 justify-between">
                    <div>
                        <div class="grid grid-cols-2 w-1/3">
                            <div>{{ $trip->train->train_number }}</div>
                            <div>{{ $trip->train->type }}</div>
                        </div>
                    </div>
                    <div class="justify-between w-full col-3">
                        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 justify-between mt-1">
                            <div class="align-left">
                                <h3 class="text-xl font-semibold justify-between">{{ $trip->depart_time->format('M-d') }}</h3>
                                <span class="text-lg font-semibold">{{ substr($trip->route->departStation->address, 0, 20) }}</span>
                                <div>Відбуєває о {{ $trip->depart_time->format('H:i') }}</div>
                            </div>
                            <div class="hidden md:flex flex-1 mt-3 flex justify-between relative">
                                <div class="absolute left-1/2 -translate-x-1/2 -translate-y-3/7 bg-white rounded-xl px-1 py-1 text-center">
                                    <p>{{ date_diff($trip->depart_time, $trip->arrival_time, true)->format('%H:%I') }}</p>
                                </div>
                                <div class="w-[70%] mx-auto bg-black h-1 rounded-xl"></div>
                            </div>
                            <div class="align-left">
                                <h3 class="text-xl font-semibold justify-between">{{ $trip->arrival_time->format('M-d') }}</h3>
                                <span class="text-lg font-semibold">{{ substr($trip->route->arrivalStation->address, 0, 20) }}</span>
                                <div>Прибуває о {{ $trip->arrival_time->format('H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </x-card>
    @endforeach

    @if($trips->count())
        <nav class="mt-4">{{$trips->links()}}</nav>
    @endif
</x-layout>