<?php

namespace App\Livewire;

use Livewire\Component;

class ReportsPage extends Component
{
    public function render()
    {
        return view('livewire.reports-page')
            ->layout('layouts.app');
    }
}
