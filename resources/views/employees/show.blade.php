<x-layout :$employee>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Робочий розклад' => '#']" />

    @if (auth()->user()->role === \App\Models\User::$role[1] || \App\Models\User::$role[0])
        <form action="{{ route('employees.destroy', $employee) }}" method="POST">
            @csrf
            @method('DELETE')
            <button class="bg-red-500 rounded-lg text-lg font-semibold p-2 hover:bg-red-600 hover:shadow-md cursor-pointer">
                    ВИДАЛИТИ АККАУНТ
            </button>
        </form>
    @endif

    @if (optional($employee->crew)->assignments === null)
        <h1 class="text-4xl font-semibold text-center">На разі немає запланованих рейсів!</h1>
    @else
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
</x-layout>