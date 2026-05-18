<div {{ $attributes->class("relative") }} >
    @if ($type === "text")
        <button type="button" class="absolute top-0 right-0 flex h-full items-center pr-2 cursor-pointer"
        onclick="this.nextElementSibling.value = ''" class="absolute top-0 right-0 flex h-full items-center pr-2 cursor-pointer hover:text-slate-700 text-slate-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-slate-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
    <input class="w-full rounded-md border-0 py-1.5 px-2.5 text-md ring-1 placeholder:text-slate-400 focus:ring-2" placeholder="{{ $placeholder }}" name="{{ $name }}" type="{{ $type }}" />
</div>