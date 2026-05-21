<?php

use Livewire\Component;

new class extends Component
{
    public $model;

    public function start(){
        
    }
};
?>

<div>
    
    <livewire:create-form 
        name="stations-add"
        message="Оберіть початкову станцію" 
        messageAdd="станцію" 
        :validValues="$model" 
        :button=false
        :load="['id', 'address']"/>
        
    </form>
</div>