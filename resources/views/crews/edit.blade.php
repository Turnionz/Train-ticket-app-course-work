<x-layout>
        <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Бригади' => route('crews.index'), 'Створення бригади' => '#']" />

        <form action="{{ route('crews.store')}}" method="POST">
            
            @csrf
            <livewire:create-form 
                name="employees"
                message="Оберіть працівника" 
                messageAdd="Працівника" 
                :validValues="$employees" 
                :with="['user']" 
                :load="['user.first_name', 'user.last_name', 'employee_type']" />
            <livewire:create-form 
                name="trips"
                message="Оберіть рейс" 
                messageAdd="рейс" 
                :validValues="$trips" 
                :with="['route']" 
                :load="['route.departStation.address', 'route.arrivalStation.address', 'depart_time']" /> 

            <button class="text-xl p-2 rounded-md bg-teal-300 font-semibold mt-4 hover:shadow-md hover:bg-teal-400 cursor-pointer">Додати бригаду</button>
        </form>
</x-layout>