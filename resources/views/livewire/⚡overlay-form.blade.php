<?php

use Livewire\Component;

new class extends Component
{
    public $hidden = 'hidden';

    // key => value -> name => type
    public array $searchValues;

    // Model for the table we wanna look through
    public string $modelSearch = Model::class;

    public string $buttonName;
    public string $buttonStyle;

    // pass an array of Model => [array of of eager loading tables arguments, the eager loading statements here must match with filters array]
    public array $whatRelationsToFind;

    public array $inputValues = [];

    // List as an array of key => value pair where value is an array of fields inside a certain Model
    public array $filters;

    // results are all the rows which meet the LIKE criteria
    public array $results;

    // intersections are only the rows of whatRelationsToFind that intersect with results array, so in short, rows of whatRelationsToFind that are corresponding to the search
    public array $intersections;

    // what field you want to output from the whatRelationsToFind model
    public string $field;

    public function show(){
        $this->hidden = '';
    }

    public function hide(){
        $this->hidden = 'hidden';
    }

    public function search(){
        
        // clean up the input
        $inputs = array_filter($this->inputValues);

        if (empty($inputs)) {
            return;
        }

        $results = [];

        foreach ($this->filters as $model => $fields) {

            $query = $model::query();

            $query->where(function($q) use ($fields, $inputs){
                foreach ($fields as $field) {
                    foreach ($inputs as $inputName => $value) {
                        $q->orWhere($field, 'like', '%' . $value . '%');
                    }
                }
            });

            $modelName = class_basename($model);
            $results[$modelName] = $query->get();
        }

        foreach ($this->whatRelationsToFind as $targetModel => $relationsArray) {
            
            // Passing the entire relationsArray to eager load everything
            $query = $targetModel::with($relationsArray);

            $query->where(function ($q) use ($relationsArray, $results) {
                
                foreach ($relationsArray as $relationPath) {
                    
                    // Use whereHas to search deeply into the relationship
                    $q->orWhereHas($relationPath, function ($subQuery) use ($results) {
                        
                        // Getting the model name
                        $relatedModel = $subQuery->getModel();
                        $relatedModelName = class_basename($relatedModel);

                        // Did our previous search find any matches for this model?
                        if (isset($results[$relatedModelName]) && $results[$relatedModelName]->isNotEmpty()) {
                            
                            // Get the primary key (usually 'id') and the matched IDs
                            $primaryKey = $relatedModel->getKeyName();
                            $tableName = $relatedModel->getTable();
                            $ids = $results[$relatedModelName]->pluck($primaryKey);
                            
                            // Filter the relation by the IDs we found earlier
                            $subQuery->whereIn("{$tableName}.{$primaryKey}", $ids);
                        } else {
                            // If no matches were found for this model, force the condition to fail
                            $subQuery->whereRaw('1 = 0');
                        }
                    });
                }
            });

            // Store the final assignments (or other target models)
            $targetModelName = class_basename($targetModel);
            $this->intersections[$targetModelName] = $query->get();
        }
    }
};
?>

<div>
    <button class="{{ $buttonStyle }}" wire:click='show'>
        {{ $buttonName }}
    </button>

    <div class="{{ $hidden }} fixed inset-0 w-full h-full bg-black/25 z-50 flex items-center justify-center" wire:click="hide">
        <x-card x-on:click.stop='' class="max-w-[60%]">
            @if (!empty($searchValues))
                <h2 class="pt-2 pl-3 text-xl font-semibold">Знайти за критеріями</h2>
                <form wire:submit.prevent="search" class="flex flex-auto w-full gap-4 items-center p-2 mb-2">
                    @foreach ($searchValues as $searchName => $searchType)
                        <div class="bg-white">
                            <x-search-bar class="text-lg" name="{{ $searchName }}" type="{{ $searchType }}" wire:model="inputValues.{{ $searchName }}" />
                        </div>
                    @endforeach
                    <div class="flex-1">
                            <button type="submit" class="cursor-pointer bg-emerald-300 rounded-xl text-xl p-2 font-medium text-center w-content hover:bg-emerald-400 hover:shadow-md">
                                Знайти
                            </button>
                        </div>  
                </form>
                <p>Результати: 
                    @if ($intersections === null)
                        немає    
                    @else
                        @foreach ($intersections as $modelName => $records)
                            @foreach ($records as $record)
                                 {{ $record->$field }}
                            @endforeach
                        @endforeach
                    @endif
                </p>
            @endif
            {{ $slot }}
        </x-card>
    </div>
</div>