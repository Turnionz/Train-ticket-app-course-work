<x-layout>
    <form action="{{ route('trips.payment') }}" method="POST">
        @csrf
        
        <div>
            {{-- Оскільки це просто список ID, назвемо змінну $seatId для зрозумілості --}}
            @foreach($tickets as $seatId)
                <x-card id="{{ $seatId }}">
                    Білет за ID {{ $seatId }}
                    <input type="hidden" name="seat_ids[]" value="{{ $seatId }}">
                </x-card>
            @endforeach
        </div>

        <button type="submit" class="text-2xl p-2 rounded-md bg-teal-300 w-full font-semibold mt-4 hover:shadow-md hover:bg-teal-400 cursor-pointer">
            Підтвердити
        </button>
    </form>
</x-layout>