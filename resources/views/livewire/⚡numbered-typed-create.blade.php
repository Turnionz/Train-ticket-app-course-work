<?php

use Livewire\Component;

new class extends Component
{
    // input name
    public string $name;

    // Model name
    public string $modelName;

    // Input name
    public string $inputName;

    // Provide the type field here from the model
    public array $typeFields;

    public array $options = ['']; 

    public function addOption()
    {
        $this->options[] = ''; // Adds a new empty slot to trigger a new dropdown
    }
};
?>

<div>
    @foreach ($options as $id => $option)
        <div class="grid bg-emerald-100 gap-2 mt-2">
            <input type="number" placeholder="{{ $inputName }}" name="{{ $name }}-{{ $id }}" class="bg-teal-100 p-2 text-lg m-2 focus:bg-teal-200">
                @foreach ($typeFields as $key => $modelField)
                    <select name="{{ $key }}-{{ $id }}"  id="" class="bg-teal-100 p-2 text-lg m-2 focus:bg-teal-200 hover:bg-teal-200">
                        @foreach ($modelField as $field)
                        <option value="{{ $field }}" class="hover:bg-teal-200">{{ $field }}</option>
                    @endforeach
                </select>
            @endforeach
        </div>
    @endforeach
    <button wire:click.prevent="addOption" class="mt-2 rounded-md bg-emerald-400 px-4 py-2 text-lg cursor-pointer hover:shadow-md">
        Додати {{ $modelName }}
    </button>
</div>