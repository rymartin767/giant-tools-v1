<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Trip extends Component
{
    public $employeeNumber = '';

    public function mount()
    {
        $this->loadEmployeeNumber();
    }

    private function loadEmployeeNumber()
    {
        $sessionKey = session()->getId();
        $cacheKey = "employee_number_{$sessionKey}";

        $this->employeeNumber = Cache::get($cacheKey, '');
    }

    public function saveEmployeeNumber()
    {
        $this->validate([
            'employeeNumber' => 'required|string|min:3|max:6',
        ], [
            'employeeNumber.required' => 'Please enter your employee number.',
        ]);

        // Save to cache
        $sessionKey = session()->getId();
        $cacheKey = "employee_number_{$sessionKey}";

        Cache::put($cacheKey, $this->employeeNumber, now()->addDays(30));

        session()->flash('message', 'Employee number saved successfully.');
    }

    public function clearEmployeeNumber()
    {
        $this->employeeNumber = '';

        // Clear from cache
        $sessionKey = session()->getId();
        $cacheKey = "employee_number_{$sessionKey}";

        Cache::forget($cacheKey);

        session()->flash('message', 'Employee number cleared successfully.');
    }

    public function render()
    {
        return view('livewire.trip');
    }
}
