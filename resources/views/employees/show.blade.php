<x-layout :$employee>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Робочий розклад' => '#']" />

    @if (auth()->user()->role === \App\Models\User::$role[1] || \App\Models\User::$role[0])
        <div class="flex mb-4 gap-4">
            <form action="{{ route('employees.destroy', $employee) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="bg-red-500 rounded-lg text-lg font-semibold p-2 hover:bg-red-600 hover:shadow-md cursor-pointer">
                        ВИДАЛИТИ АККАУНТ
                </button>
            </form>

            <livewire:overlay-form 
                buttonName='Призначити бригаду' 
                buttonStyle='bg-slate-300 rounded-lg text-lg font-semibold p-2 hover:bg-slate-400 hover:shadow-md cursor-pointer'
                :searchValues="['search' => 'text', 'date' => 'date']"
                :filters="[
                    '\App\Models\Station' => ['address'],
                    '\App\Models\Train' => ['train_number', 'id'],
                    '\App\Models\Trip' => ['depart_time', 'train_id']
                ]"
                :whatRelationsToFind="[
                    '\App\Models\Assignment' => ['trip', 'trip.train', 'trip.route.routeStops.station'],    
                ]"
                field='crew_id'
                >
                <h2 class="pt-2 pl-3 text-xl font-semibold">Додати до бригади</h2>
                <form action="{{ route('employees.update', $employee) }}" method="POST" class="flex flex-auto w-full gap-4 items-center p-2 mb-2">
                    @csrf
                    @method('PUT')
                    <div class="flex-1 bg-white">
                        <x-search-bar placeholder="Номер бригади" name="crew_id" type="number" class="text-lg"/>
                    </div>
                    <div class="flex justify-center">
                        <button type="submit" class="text-lg font-medium p-2 bg-teal-300 rounded-md hover:bg-teal-400 hover:shadow-md cursor-pointer">
                            Додати
                        </button>
                    </div>
                </form>
            </livewire:overlay-form>
        </div>
    @endif

    <div class="left-0 mb-2 p-2">
        <h1 class="text-2xl font-semibold">{{ $employee->user->first_name }} {{ $employee->user->last_name }}</h1>
        <h2 class="text-xl">{{ Str::ucfirst($employee->user->role) }} {{ $employee->employee_type ? ': ' . $employee->employee_type : '' }}</h2>
        <div>
            <h3 class="text-lg font-medium">
                @if ($employee->crew_id)
                    Номер бригади - {{ $employee->crew_id }}
                @else
                    Робітник не призначен до бригади!
                @endif
            </h3>
        </div>
    </div>

    @if (optional($employee->crew)->assignments === null)
        <h1 class="text-4xl font-semibold text-center">На разі немає запланованих рейсів!</h1>
    @else
        <h1 class="text-4xl font-semibold text-center m-2">Заплановані рейси</h1>
        @forelse ($employee->crew->assignments as $assignment)
        <x-card>
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
                </div>
                <div class="w-full text-center">
                    <h2 class="text-2xl">До відправки: <span class="font-bold">{{ $assignment->trip->depart_time->diffForHumans() }}</span></h2>
                    <p class="text-lg">Номер потягу - {{ $assignment->trip->train->train_number }}</p>
                    <p>Час рейсу - {{ date_diff($assignment->trip->depart_time, $assignment->trip->arrival_time, true)->format('%H:%I:%S') }} </p>
                </div>
                <div>
                    <h2 class="w-full text-center text-xl font-medium">Склад персоналу</h2>
                    <div class="w-full text-center text-md">
                        @foreach ($assignment->crew->employees as $crewEmployee)
                            <p>{{ $crewEmployee->user->first_name }} {{ $crewEmployee->user->last_name }} - 
                                <span class="font-medium">{{ $crewEmployee->employee_type }}</span> </p>
                        @endforeach
                    </div>
                </div>
            </div>
        </x-card>
    @empty
        <h1 class="text-4xl font-semibold text-center">На разі немає запланованих рейсів!</h1>
    @endforelse 
    @endif
    @livewireScripts
</x-layout>