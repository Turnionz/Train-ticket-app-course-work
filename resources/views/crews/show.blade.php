<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Бригади' => route('crews.index'), 'Бригада №' . $crew->id => '#']" />

    @forelse ($crew->assignments as $assignment)
        <div class="grid grid-cols-7 grid-rows-1 gap-2">
            <x-card class="col-span-6 rounded-lg">
                <div class="grid grid-cols-3 justify-between">
                    <div class="grid grid-cols-2 justify-between w-full">
                        <div>
                            <h3 class="text-xl font-semibold">Відбуває з</h3>
                            <p>{{ $assignment->trip->route->departStation->address }}</p>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold">Прибуває в</h3>
                            <p>{{ $assignment->trip->route->arrivalStation->address }}</p>
                        </div>
                        <div class="w-full text-center col-span-2 mt-2">
                            <h2 class="text-2xl">До відправки: <span class="font-bold">{{ $assignment->trip->depart_time->diffForHumans() }}</span></h2>
                            <p class="text-lg">Номер потягу - {{ $assignment->trip->train->train_number }}</p>
                            <p>Час рейсу - {{ date_diff($assignment->trip->depart_time, $assignment->trip->arrival_time, true)->format('%H:%I:%S') }} </p>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <h2 class="w-full text-center text-2xl font-semibold">Склад персоналу</h2>
                        <div class="w-full text-center text-lg grid grid-cols-2">
                            @foreach ($assignment->crew->employees as $crewEmployee)
                                <p>{{ $crewEmployee->user->first_name }} {{ $crewEmployee->user->last_name }} - 
                                    <span class="font-medium">{{ $crewEmployee->employee_type }}</span> </p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </x-card>
            <div class="grid grid-rows-2 h-full mb-4">
                <livewire:overlay-form 
                    buttonName='Видалити бригаду' 
                    buttonStyle='bg-red-600 rounded-lg text-3xl font-semibold p-2 hover:bg-red-500 hover:shadow-md cursor-pointer h-full w-full'>
                    <h2 class="text-4xl font-semibold">Точно видалити бригаду?</h2>
                    <form action="#" method="POST" class="flex flex-auto w-full gap-4 items-center p-2 mb-2">
                        @csrf
                        @method('DELETE')
                        <div class="flex justify-center w-full">
                            <button type="submit" class="text-4xl font-semibold p-5 bg-red-600 rounded-md hover:bg-red-700 hover:shadow-md cursor-pointer w-full h-full mt-2">
                                Видалити бригаду
                            </button>
                        </div>
                    </form>
                </livewire:overlay-form>
                <x-link-button href="{{ route('crews.edit', $crew) }}" class="rounded-lg bg-sky-400 hover:bg-sky-500 text-3xl hover:shadow-md">
                    <h2>Редагувати бригаду</h2>
                </x-link-button>
            </div>
        </div>
    @empty
        <h1 class="text-4xl font-bold">Немає назначених рейсів для бригади {{ $crew->id }}</h1>
    @endforelse
</x-layout>