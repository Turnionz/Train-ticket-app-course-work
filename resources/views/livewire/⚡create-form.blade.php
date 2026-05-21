<?php

use Livewire\Component;

new class extends Component
{
    public array $options = []; 
    public array $chosenValues = []; 
    
    public string $message;
    public string $messageAdd;
    public array $with = [];
    public array $load = [];
    public $validValues;
    public bool $button = true;
    public string $name;
    
    public $defaultValues = null;

    public function mount($defaultValues = null)
    {
        if ($defaultValues !== null) {
            $defaults = is_array($defaultValues) ? $defaultValues : [$defaultValues];
            
            $this->chosenValues = $defaults;
            $this->options = array_fill(0, count($defaults), '');
        } else {
            $this->options = [''];
        }
    }

    public function addOption()
    {
        $this->options[] = ''; 
    }

    public function updatedValues($value)
    {
        if (!in_array($value, $this->validValues) && $value !== '') {
            $this->chosenValues = [];
            $this->addError('chosenValue', 'You must select an option from the list.');
        }
    }
};
?>

<div class="mt-4">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <style>
        .ts-wrapper.has-items input::placeholder {
            color: transparent !important;
        }

        .ts-wrapper.has-items:not(.focus) input {
            caret-color: transparent !important;
        }
    </style>
    
    @foreach ($options as $index => $option)
        <div wire:key="dropdown-{{ $index }}" class="mb-4">
            
            <div wire:ignore>
                <select 
                    name="{{ $name }}-{{ $index }}"
                    x-data="{ selectedValue: @entangle('chosenValues.' . $index) }"
                    
                    x-init="
                        let ts = new TomSelect($el, { 
                            create: false,
                            hidePlaceholder: true,
                            controlClass: 'text-lg rounded-md border border-slate-300 bg-emerald-100 px-4 py-3 shadow-sm focus:outline-none focus:border-transparent focus:ring-2 focus:ring-sky-500',
                            controlInput: '<input class=\'!border-none !ring-0 !outline-none !shadow-none w-full bg-transparent m-0 p-0\' />',
                            dropdownClass: 'text-lg absolute z-50 w-full bg-emerald-100 border border-slate-200 shadow-lg rounded-md mt-1 overflow-hidden',
                            optionClass: 'px-4 py-2 bg-emerald-200 text-gray-900 cursor-pointer hover:!bg-emerald-100',
                            itemClass: 'item inline-block mr-2 text-gray-900'
                        });

                        // Коли користувач обирає значення - передаємо його в Livewire
                        ts.on('change', (value) => {
                            selectedValue = value;
                        });

                        // Якщо Livewire змінює значення - оновлюємо вигляд TomSelect
                        $watch('selectedValue', (value) => {
                            if (value !== ts.getValue()) {
                                ts.setValue(value);
                            }
                        });
                    " 
                    placeholder="{{ $message }}"
                    class="w-full"
                >
                    <option value="">{{ $message }}</option>
                
                    @foreach($validValues as $item)
                        <option 
                            value="{{ $item->id }}" 
                            @selected(isset($chosenValues[$index]) && $chosenValues[$index] == $item->id)
                        >
                            {{ collect($load)->map(fn($connection) => data_get($item, $connection))->implode(' ') }}
                        </option>
                    @endforeach
                </select>
            </div>

            @error('chosenValues.'.$index) 
                <span class="text-red-500 text-sm">{{ $message }}</span> 
            @enderror

        </div>
    @endforeach

    @if ($button)
        <button wire:click.prevent="addOption" class="mt-2 rounded-md bg-emerald-400 px-4 py-2 text-lg cursor-pointer hover:shadow-md">
            Додати {{ $messageAdd }}
        </button>
    @endif

    @error('chosenValue') 
        <span class="text-red-500 text-sm">{{ $message }}</span> 
    @enderror
</div>