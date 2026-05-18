@props(['links' => []])

<nav {{ $attributes }}>
    <ul class="flex space-x-4 text-gray-800 text-2xl">
        <li>
            <a href="/">Домашня</a>
        </li>
        
        @foreach ($links as $label => $link)
            <li class="mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </li>
            <li>
                <a href="{{ $link }}">{{ $label }}</a>
            </li>
        @endforeach
    </ul>
</nav>