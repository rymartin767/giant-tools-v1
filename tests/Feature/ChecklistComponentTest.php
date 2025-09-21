<?php

use App\Livewire\Checklist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('checklist component loads flight details form initially', function () {
    Livewire::test(Checklist::class)
        ->assertSee('Flight Configuration')
        ->assertSee('Engine Type')
        ->assertSee('Configuration')
        ->assertSee('Departure Airport')
        ->assertSee('Arrival Airport');
});

test('can complete flight details form', function () {
    Livewire::test(Checklist::class)
        ->set('engines', 'GE')
        ->set('config', 'PAX')
        ->set('departureAirport', 'KJFK')
        ->set('arrivalAirport', 'KLAX')
        ->call('completeFlightDetails')
        ->assertHasNoErrors()
        ->assertSee('Progress');
});

test('flight details form validates required fields', function () {
    Livewire::test(Checklist::class)
        ->call('completeFlightDetails')
        ->assertHasErrors(['engines', 'config', 'departureAirport', 'arrivalAirport']);
});

test('can toggle checklist items after flight details complete', function () {
    $component = Livewire::test(Checklist::class)
        ->set('engines', 'GE')
        ->set('config', 'PAX')
        ->set('departureAirport', 'KJFK')
        ->set('arrivalAirport', 'KLAX')
        ->call('completeFlightDetails');

    // Toggle first item of first step
    $component->call('toggleItem', '1.0', 0)
        ->assertDispatched('item-toggled');

    // Check that item is marked as completed
    expect($component->get('completedItems')['1.0'])->toContain(0);
});

test('can navigate between steps', function () {
    $component = Livewire::test(Checklist::class)
        ->set('engines', 'GE')
        ->set('config', 'PAX')
        ->set('departureAirport', 'KJFK')
        ->set('arrivalAirport', 'KLAX')
        ->call('completeFlightDetails');

    // Go to next step
    $component->call('goToStep', '1.1')
        ->assertSet('currentStep', '1.1');
});

test('progress percentage calculates correctly', function () {
    $component = Livewire::test(Checklist::class)
        ->set('engines', 'GE')
        ->set('config', 'PAX')
        ->set('departureAirport', 'KJFK')
        ->set('arrivalAirport', 'KLAX')
        ->call('completeFlightDetails');

    // Initially no steps completed
    expect($component->instance()->getProgressPercentage())->toBe(0);
});

test('can reset checklist', function () {
    $component = Livewire::test(Checklist::class)
        ->set('engines', 'GE')
        ->set('config', 'PAX')
        ->set('departureAirport', 'KJFK')
        ->set('arrivalAirport', 'KLAX')
        ->call('completeFlightDetails');

    // Complete an item
    $component->call('toggleItem', '1.0', 0);

    // Reset checklist
    $component->call('resetChecklist')
        ->assertSet('completedItems', [])
        ->assertSet('currentStep', '1.0');
});
