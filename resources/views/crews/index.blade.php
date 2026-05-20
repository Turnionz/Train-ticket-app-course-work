<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Рейси' => route('trips.index'), 'Бригади' => '#']" />

    <x-link-button href="{{ route('crews.create') }}" class="text-lg bg-teal-400 hover:bg-teal-500 hover:shadow-md mb-2">
        Додати нову бригаду
    </x-link-button> 

    @forelse ($crews as $crew)
        <x-card class="hover:shadow-md mt-4 rounded-xl cursor-pointer">
            <a href="{{ route('crews.show', $crew) }}">
                <div class="grid grid-cols-3">
                    <div>
                        <h3 class="text-lg font-semibold">Бригада номер {{ $crew->id }}</h3>
                    </div>
                    <div class="col-3">
                        <h1>
                            @php
                                $soonest = $crew->assignments
                                    ->sortBy('trip.depart_time')
                                    ->first();
                            @endphp
                            @if ($soonest)
                                Наступний виїзд через {{ $soonest->trip->depart_time->diffForHumans() }}
                            @else
                                Немає
                            @endif
                        </h1>
                    </div>
                </div>
            </a>
        </x-card>
    @empty
        <h1 class="text-4xl font-bold mb-5">Немає бригад!</h1>
    @endforelse

    @if($crews->count())
        <nav class="mt-4">{{$crews->links()}}</nav>
    @endif
</x-layout>