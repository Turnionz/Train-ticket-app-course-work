<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Вагони' => '#']" />

    <div class="mb-4 flex justify-between items-center w-full">
        <livewire:overlay-form 
            buttonName='Створити вагон(и)' 
            buttonStyle='bg-teal-400 rounded-lg text-lg font-semibold p-2 hover:bg-teal-500 hover:shadow-md cursor-pointer h-full w-full'>
            <h2 class="text-4xl font-semibold text-center">Введіть дані</h2>
            <form action="{{ route('wagons.store') }}" method="POST" class="flex flex-auto w-full gap-4 items-center p-2 mb-2">
                @csrf
                <div class="grid gap-2 mt-2">
                    <div>
                        <input name="amount" type="number" class="rounded-md bg-teal-100 p-2 text-lg mt-2 w-full focus:bg-teal-200" placeholder="Кількість вагонів">
                    </div>
                    <div>
                        <select name="type_select" id="" class="bg-teal-100 p-2 rounded-md w-full text-lg m-2 focus:bg-teal-200 hover:bg-teal-200">
                            @foreach (\App\Models\Wagon::$type as $type)
                                <option value="{{ $type }}" class="hover:bg-teal-200">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="class_select" id="" class="rounded-md bg-teal-100 p-2 text-lg m-2 w-full focus:bg-teal-200 hover:bg-teal-200">
                            @foreach (\App\Models\Seat::$class as $class)
                                <option value="{{ $class }}" class="hover:bg-teal-200">{{ $class }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <input name="train_number" type="number" class="rounded-md bg-teal-100 p-2 text-lg m-2 w-full focus:bg-teal-200" placeholder="Присвоїти до поїзда номер">
                    </div>
                    <div class="flex justify-center w-full">
                    <button type="submit" class="text-2xl font-semibold p-5 bg-teal-400 rounded-md hover:bg-teal-500 hover:shadow-md cursor-pointer w-full h-full mt-5">
                        Створити вагон(и)
                    </button>
                </div>
                </div>
            </form>
        </livewire:overlay-form>
    </div>

    @forelse ($wagons as $wagon)
        <x-card class="hover:shadow-md mt-4 rounded-xl">
            <a href="{{ route('wagons.show', $wagon) }}">
                <div class="grid justify-items">
                    <div class="flex p-2 gap-2 justify-between">
                        <h2 class="text-xl font-semibold "> {{ $wagon->wagon_number ? 'Номер вагону: ' . $wagon->wagon_number : 'Номер не призначено' }}</h2>
                        <h2 class="text-lg font-semibold">ІД вагона: {{ $wagon->id }}</h2>
                        <h2 class="text-lg font-medium">Тип: {{ $wagon->type }}</h2>
                        <p class="text-md font-medium">Кількість місць: {{ $wagon->seats->count() }}</p>
                        <p class="text-md font-medium">З них зайнято: {{ $wagon->seats->count() - \App\Models\Ticket::whereIn('seat_id', $wagon ->seats->pluck('id'))->count() }}</p>
                    </div>
                </div>
            </a>
        </x-card>
    @empty
        <h1 class="text-4xl font-bold mb-5">Вагонів немає!</h1>
    @endforelse

    @if($wagons->count())
        <nav class="mt-4">{{$wagons->links()}}</nav>
    @endif
</x-layout>