<?php

namespace App\Livewire;

use Livewire\Component;

class SalesPage extends Component
{
    public function render()
    {
        return view('livewire.sales-page')
            ->layout('layouts.app');
    }
}
