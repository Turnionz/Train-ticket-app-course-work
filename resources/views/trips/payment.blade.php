<x-layout>
    <form action="{{ route('trips.payment') }}" method="POST">
        @csrf
        
        <div>
            @foreach($tickets as $ticket)
            <input type="hidden" name="seat_ids[]" value="{{ $ticket['id'] ?? $ticket }}">
            @endforeach
            <x-card>
                <h1 class="text-4xl font-bold text-center">Оплата</h1>
            </x-card>
        </div>

        <button type="submit" class="text-2xl p-2 rounded-md bg-teal-300 w-full font-semibold mt-4 hover:shadow-md hover:bg-teal-400 cursor-pointer">
            Підтвердити
        </button>
    </form>
</x-layout>