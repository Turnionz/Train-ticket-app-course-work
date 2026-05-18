<x-layout>
        <x-breadcrumbs class="mb-4" :links="['Рейси' => 'route(trips.index)', 'Перегляд місць' => '#']" />

        <x-card class="hover:shadow-md mt-4 rounded-xl" :$trip>
        <div class="flex justify-between">
            <div>
                <div>{{ $trip->train->train_number }}</div>
            </div>
            <div class="justify-between w-1/3">
                <div class="flex justify-between mt-1">
                    <div class="align-left">
                        <h3 class="text-xl font-semibold justify-between">{{ $trip->depart_time->format('M-d') }}</h3>
                        <span class="text-lg font-semibold">{{ substr($trip->route->departStation->address, 0, 20) }}</span>
                        <div>Departs at: {{ $trip->depart_time->format('H:i') }}</div>
                    </div>
                    <div class="flex-1 mt-3 flex justify-between">
                        <div class="w-7/10 mx-auto bg-black h-1 rounded-xl"></div>
                    </div>
                    <div class="align-left">
                        <h3 class="text-xl font-semibold justify-between">{{ $trip->arrival_time->format('M-d') }}</h3>
                        <span class="text-lg font-semibold">{{ substr($trip->route->arrivalStation->address, 0, 20) }}</span>
                        <div>Arrives at: {{ $trip->arrival_time->format('H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </x-card>
</x-layout>