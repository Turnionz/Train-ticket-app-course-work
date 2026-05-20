<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Потяги' => route('trains.index'), 'Створення потягу' => '#' ]" />
        @php
            // Find all wagons that are NOT currently attached to a train
            $availableWagons = \App\Models\Wagon::whereNull('train_id')->get();
        @endphp

    
    
    <form action="{{ route('trains.store')}}" method="POST">
        @csrf
        @method('POST')
        <div class="grid bg-emerald-100 gap-2 mt-2 rounded-md">
            <input type="text" name="train_number" placeholder="Номер потягу" class="rounded-md bg-teal-100 p-2 text-lg m-2 w-full focus:bg-teal-200">
            <select name="type" id="" class="rounded-md bg-teal-100 p-2 text-lg m-2 focus:bg-teal-200 hover:bg-teal-200">
                @foreach (\App\Models\Train::$type as $field)
                    <option value="{{ $field }}" class="hover:bg-teal-200">{{ $field }}</option>
                @endforeach
            </select>
        </div>
        <livewire:create-form 
            name="wagons"
            message="Оберіть вагон" 
            messageAdd="вагон" 
            :validValues="$availableWagons" 
            :load="['id', 'type']" /> 
        <livewire:numbered-typed-create 
            name="wagonAlt"
            modelName="Вагон"
            inputName="Кількість вагонів"
            :typeFields="['wagon' => \App\Models\Wagon::$type, 'seat' => \App\Models\Seat::$class]" > 
        </livewire:numbered-typed-create>
        <button class="text-xl p-2 rounded-md bg-teal-300 font-semibold mt-6 hover:shadow-md hover:bg-teal-400 cursor-pointer">Створити потяг з такими вагонами</button>
    </form>
</x-layout>