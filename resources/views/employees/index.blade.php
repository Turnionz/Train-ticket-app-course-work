<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Робітники' => '#']" />

    <x-link-button href="{{ route('employees.create') }}" class="text-lg bg-green-300 hover:bg-green-400 hover:shadow-md">
        Додати нового робітника
    </x-link-button>

    @forelse ($employees as $employee)
        <x-card class="hover:shadow-md mt-4 rounded-xl cursor-pointer">
            <a href="{{ route('employees.show', $employee) }}">
                <div class="grid grid-cols-3">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $employee->user->first_name }} {{ $employee->user->last_name }}</h3>
                        <p>
                            {{ Str::ucfirst($employee->user->role) }} {{ $employee->employee_type ? ': ' . $employee->employee_type : '' }}
                        </p>
                    </div>
                    <div class="col-3">
                        <h3 class="text-lg">Номер персоналу - {{ optional($employee->crew)->id ?? 'не призначено' }} </h3>
                        <h3 class="text-lg">Призначено рейсів - 
                            @if (optional($employee->crew)->assignments !== null)
                                {{ $employee->crew->assignments->count() }}
                            @else
                                жодного
                            @endif
                        </h3>
                    </div>
                </div>
            </a>
        </x-card>
    @empty
        <h1 class="text-4xl font-semibold text-center mt-1/2">Немає жодного зареєстрованого працівника!</h1>
    @endforelse 

    @if($employees->count())
        <nav class="mt-4">{{$employees->links()}}</nav>
    @endif
</x-layout>