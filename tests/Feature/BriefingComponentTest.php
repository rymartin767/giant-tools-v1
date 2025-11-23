<?php

declare(strict_types=1);

use App\Livewire\Briefing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

uses(RefreshDatabase::class)->group('briefing');

beforeEach(function () {
    // Start session to get session ID
    $this->withSession([]);
    $this->sessionId = session()->getId();

    // Set employee number for this session
    $this->employeeNumber = 'test-emp-123';
    Cache::put("employee_number_{$this->sessionId}", $this->employeeNumber, now()->addDays(30));

    // Mark checklist step 2.0 as complete (required to access briefing)
    Cache::put("checklist_completed_items_{$this->employeeNumber}", [
        '2.0' => ['0', '1', '2', '3', '4', '5', '6'], // Complete all 7 items in step 2.0
    ], now()->addDays(30));

    // Set up aircraft and flight details in cache for conditional items
    Cache::put("aircraft_details_{$this->employeeNumber}", [
        'engines' => 'GE',
        'config' => 'PAX',
        'supernumeraries' => false,
    ], now()->addDays(30));

    Cache::put("flight_details_{$this->employeeNumber}", [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'KLAX',
        'etops' => false,
    ], now()->addDays(30));
});

test('briefing component loads successfully', function () {
    Livewire::test(Briefing::class)
        ->assertSee('Aircraft Status')
        ->assertSee('Review aircraft status and operational requirements');
});

test('briefing displays step 1.0 items', function () {
    Livewire::test(Briefing::class)
        ->assertSee('Daily Check / Autoland Status')
        ->assertSee('DMI Review')
        ->assertSee('Aircraft Differences');
});

test('can toggle briefing items', function () {
    $component = Livewire::test(Briefing::class);

    // Toggle first item of first step
    $component->call('toggleItem', '1.0', 0)
        ->assertDispatched('item-toggled');

    // Check that item is marked as completed (stored as string)
    expect($component->get('completedItems')['1.0'])->toContain('0');
});

test('can navigate between steps', function () {
    $component = Livewire::test(Briefing::class);

    // Currently only one step, so just verify current step is set
    expect($component->get('currentStep'))->toBe('1.0');
});

test('progress percentage calculates correctly', function () {
    $component = Livewire::test(Briefing::class);

    // Initially no steps completed
    expect($component->instance()->getProgressPercentage())->toBe(0);
});

test('can reset briefing', function () {
    $component = Livewire::test(Briefing::class);

    // Complete an item
    $component->call('toggleItem', '1.0', 0);

    // Reset briefing
    $component->call('resetBriefing')
        ->assertSet('completedItems', [])
        ->assertSet('currentStep', '1.0');
});

test('briefing items are filtered by conditions', function () {
    $component = Livewire::test(Briefing::class);

    $filteredItems = $component->instance()->getFilteredItems('1.0');

    // Should include non-conditional items
    expect($filteredItems)->toBeArray();
    expect(count($filteredItems))->toBeGreaterThan(0);
});

test('etops conditional items show when etops is enabled', function () {
    // Enable ETOPS
    Cache::put("flight_details_{$this->employeeNumber}", [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'KLAX',
        'etops' => true,
    ], now()->addDays(30));

    $component = Livewire::test(Briefing::class);

    $filteredItems = $component->instance()->getFilteredItems('1.0');

    // Find ETOPS item
    $etopsItem = collect($filteredItems)->first(fn ($item) => str_contains($item['text'] ?? '', 'ETOPS'));

    expect($etopsItem)->not->toBeNull();
});

test('briefing caches completed items per user', function () {
    $component = Livewire::test(Briefing::class);

    // Complete an item
    $component->call('toggleItem', '1.0', 0);

    // Check cache (stored as string)
    $cachedItems = Cache::get("briefing_completed_items_{$this->employeeNumber}");
    expect($cachedItems)->toBeArray();
    expect($cachedItems['1.0'])->toContain('0');
});

test('completed items persist across component mounts', function () {
    // Complete an item in first component instance
    $component1 = Livewire::test(Briefing::class);
    $component1->call('toggleItem', '1.0', 0);

    // Create new component instance
    $component2 = Livewire::test(Briefing::class);

    // Check that item is still completed (stored as string)
    expect($component2->get('completedItems')['1.0'])->toContain('0');
});

test('sub-items are properly indented and included in filtered items', function () {
    $component = Livewire::test(Briefing::class);

    $filteredItems = $component->instance()->getFilteredItems('1.0');

    // Find the DMI Review item (index 1)
    expect($filteredItems[1]['text'])->toBe('DMI Review');
    expect($filteredItems[1]['isSubItem'])->toBe(false);

    // Find the Aerodata Requirements sub-item (index 1.0)
    expect($filteredItems['1.0']['text'])->toBe('Aerodata Requirements');
    expect($filteredItems['1.0']['isSubItem'])->toBe(true);
});

test('sub-items can be toggled independently', function () {
    $component = Livewire::test(Briefing::class);

    // Toggle parent item (DMI Review - index 1)
    $component->call('toggleItem', '1.0', 1);
    $completedItemsAfterFirst = $component->get('completedItems')['1.0'];
    expect($completedItemsAfterFirst)->toContain('1');

    // Toggle sub-item (Aerodata Requirements - index "1.0")
    $component->call('toggleItem', '1.0', '1.0');
    $completedItemsAfterSecond = $component->get('completedItems')['1.0'];

    // Check both items are in the array
    expect($completedItemsAfterSecond)->toBeArray();
    expect(in_array('1', $completedItemsAfterSecond, true))->toBeTrue();
    expect(in_array('1.0', $completedItemsAfterSecond, true))->toBeTrue();
});

test('briefing has multiple steps including fuel plan', function () {
    $component = Livewire::test(Briefing::class);

    $steps = $component->get('steps');

    // Check that we have both steps
    expect($steps)->toHaveKey('1.0');
    expect($steps)->toHaveKey('2.0');

    // Check step 2.0 details
    expect($steps['2.0']['title'])->toBe('Fuel Plan');
    expect($steps['2.0']['description'])->toBe('Review and verify fuel planning and reserves');
});

test('can navigate to fuel plan step', function () {
    $component = Livewire::test(Briefing::class);

    // Start at step 1.0
    expect($component->get('currentStep'))->toBe('1.0');

    // Navigate to step 2.0
    $component->call('goToStep', '2.0');
    expect($component->get('currentStep'))->toBe('2.0');
});

test('fuel plan step displays correct items', function () {
    $component = Livewire::test(Briefing::class);

    $component->call('goToStep', '2.0');

    // Check that fuel plan items are displayed
    $component->assertSee('Actual vs Planned')
        ->assertSee('FMC Reserve vs OFP REMF')
        ->assertSee('Enroute Considerations')
        ->assertSee('Enroute WX / Sigmets');
});

test('live animals items show when liveAnimals flag is enabled', function () {
    // Mark checklist step 2.0 as complete
    Cache::put("checklist_completed_items_{$this->employeeNumber}", [
        '2.0' => ['0', '1', '2', '3', '4', '5', '6'], // Complete all 7 items in step 2.0
    ], now()->addDays(30));

    // Enable Live Animals
    Cache::put("flight_details_{$this->employeeNumber}", [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'KLAX',
        'etops' => false,
        'liveAnimals' => true,
        'hazmat' => false,
    ], now()->addDays(30));

    $component = Livewire::test(Briefing::class);

    // Check that live animals items are displayed
    $component->assertSee('Live Animals & Perishables')
        ->assertSee('Live Animals Only in Bulk')
        ->assertSee('Vent Used');
});

test('live animals items hidden when liveAnimals flag is disabled', function () {
    // Mark checklist step 2.0 as complete
    Cache::put("checklist_completed_items_{$this->employeeNumber}", [
        '2.0' => [0, 1, 2], // Complete all items in step 2.0
    ], now()->addDays(30));

    // Disable Live Animals
    Cache::put("flight_details_{$this->employeeNumber}", [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'KLAX',
        'etops' => false,
        'liveAnimals' => false,
        'hazmat' => false,
    ], now()->addDays(30));

    $component = Livewire::test(Briefing::class);

    // Check that live animals items are NOT displayed
    $component->assertDontSee('Live Animals & Perishables');
});

test('hazmat items show when hazmat flag is enabled', function () {
    // Mark checklist step 2.0 as complete
    Cache::put("checklist_completed_items_{$this->employeeNumber}", [
        '2.0' => ['0', '1', '2', '3', '4', '5', '6'], // Complete all 7 items in step 2.0
    ], now()->addDays(30));

    // Enable Hazmat
    Cache::put("flight_details_{$this->employeeNumber}", [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'KLAX',
        'etops' => false,
        'liveAnimals' => false,
        'hazmat' => true,
    ], now()->addDays(30));

    $component = Livewire::test(Briefing::class);

    // Check that hazmat items are displayed
    $component->assertSee('Hazmat')
        ->assertSee('Red Book on Board!')
        ->assertSee('NOTOC Review');
});

test('hazmat items hidden when hazmat flag is disabled', function () {
    // Mark checklist step 2.0 as complete
    Cache::put("checklist_completed_items_{$this->employeeNumber}", [
        '2.0' => [0, 1, 2], // Complete all items in step 2.0
    ], now()->addDays(30));

    // Disable Hazmat
    Cache::put("flight_details_{$this->employeeNumber}", [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'KLAX',
        'etops' => false,
        'liveAnimals' => false,
        'hazmat' => false,
    ], now()->addDays(30));

    $component = Livewire::test(Briefing::class);

    // Check that hazmat items are NOT displayed
    $component->assertDontSee('Hazmat');
});

test('step is complete when all visible items are checked', function () {
    // Set up minimal config with no conditional items enabled
    Cache::put("aircraft_details_{$this->employeeNumber}", [], now()->addDays(30));
    Cache::put("flight_details_{$this->employeeNumber}", [
        'etops' => false,
        'liveAnimals' => false,
        'hazmat' => false,
    ], now()->addDays(30));

    $component = Livewire::test(Briefing::class);

    // Get the filtered items for step 1.0
    $filteredItems = $component->instance()->getFilteredItems('1.0');
    $visibleItemKeys = array_keys($filteredItems);

    // Step should not be complete initially
    expect($component->instance()->isStepComplete('1.0'))->toBe(false);

    // Mark all visible items as completed
    foreach ($visibleItemKeys as $itemKey) {
        $component->call('toggleItem', '1.0', $itemKey);
    }

    // Now step should be complete
    expect($component->instance()->isStepComplete('1.0'))->toBe(true);
});

test('step completion only counts visible items not hidden conditional items', function () {
    // Mark checklist step 2.0 as complete
    Cache::put("checklist_completed_items_{$this->employeeNumber}", [
        '2.0' => [0, 1, 2], // Complete all items in step 2.0
    ], now()->addDays(30));

    // Set up config with no conditional items enabled
    Cache::put("aircraft_details_{$this->employeeNumber}", [], now()->addDays(30));
    Cache::put("flight_details_{$this->employeeNumber}", [
        'etops' => false,
        'liveAnimals' => false,
        'hazmat' => false,
    ], now()->addDays(30));

    // First, enable ETOPS and complete ETOPS items
    Cache::put("flight_details_{$this->employeeNumber}", [
        'etops' => true,
        'liveAnimals' => false,
        'hazmat' => false,
    ], now()->addDays(30));

    $component = Livewire::test(Briefing::class);
    $filteredItemsWithEtops = $component->instance()->getFilteredItems('1.0');

    // Complete all items including ETOPS
    foreach (array_keys($filteredItemsWithEtops) as $itemKey) {
        $component->call('toggleItem', '1.0', $itemKey);
    }

    // Step should be complete
    expect($component->instance()->isStepComplete('1.0'))->toBe(true);

    // Now disable ETOPS
    Cache::put("flight_details_{$this->employeeNumber}", [
        'etops' => false,
        'liveAnimals' => false,
        'hazmat' => false,
    ], now()->addDays(30));

    // Remount component to pick up new config
    $component2 = Livewire::test(Briefing::class);

    // Get new filtered items (without ETOPS)
    $filteredItemsWithoutEtops = $component2->instance()->getFilteredItems('1.0');

    // The completed ETOPS items should have been cleaned up
    $completedItems = $component2->get('completedItems')['1.0'] ?? [];

    // Only visible items should remain in completed items
    foreach ($completedItems as $completedKey) {
        expect(array_key_exists($completedKey, $filteredItemsWithoutEtops))->toBe(true);
    }
});

test('next step button is enabled when all visible items are completed', function () {
    // Set up minimal config
    Cache::put("aircraft_details_{$this->employeeNumber}", [], now()->addDays(30));
    Cache::put("flight_details_{$this->employeeNumber}", [
        'etops' => false,
        'liveAnimals' => false,
        'hazmat' => false,
    ], now()->addDays(30));

    $component = Livewire::test(Briefing::class);

    // Get all visible items
    $filteredItems = $component->instance()->getFilteredItems('1.0');
    $visibleItemKeys = array_keys($filteredItems);

    // Complete all visible items
    foreach ($visibleItemKeys as $itemKey) {
        $component->call('toggleItem', '1.0', $itemKey);
    }

    // Step should be complete, enabling next step button
    expect($component->instance()->isStepComplete('1.0'))->toBe(true);

    // Should be able to navigate to next step
    $component->call('nextStep');
    expect($component->get('currentStep'))->toBe('2.0');
});

test('weather runway condition step displays correct items', function () {
    // Enable cold weather and LLWS to see all items
    Cache::put("flight_details_{$this->employeeNumber}", [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'KLAX',
        'etops' => false,
        'liveAnimals' => false,
        'hazmat' => false,
        'coldWeatherOps' => true,
        'llws' => true,
    ], now()->addDays(30));

    $component = Livewire::test(Briefing::class);

    $component->call('goToStep', '3.0');

    // Check that weather/runway items are displayed
    $component->assertSee('Weather, Runway Conditions')
        ->assertSee('Cold Weather Considerations')
        ->assertSee('ENG A/I ON AFTER START')
        ->assertSee('Contam Taxi or Remote Pad')
        ->assertSee('Wet or Contaminated Runway')
        ->assertSee('ATIS: LLWS');
});

test('cold weather ops items show when coldWeatherOps flag is enabled', function () {
    // Mark checklist step 2.0 as complete
    Cache::put("checklist_completed_items_{$this->employeeNumber}", [
        '2.0' => ['0', '1', '2', '3', '4', '5', '6'], // Complete all 7 items in step 2.0
    ], now()->addDays(30));

    // Enable Cold Weather Ops
    Cache::put("flight_details_{$this->employeeNumber}", [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'KLAX',
        'etops' => false,
        'liveAnimals' => false,
        'hazmat' => false,
        'coldWeatherOps' => true,
        'llws' => false,
    ], now()->addDays(30));

    $component = Livewire::test(Briefing::class);
    $component->call('goToStep', '3.0');

    // Check that cold weather items are displayed
    $component->assertSee('Cold Weather Considerations')
        ->assertSee('ENG A/I ON AFTER START')
        ->assertSee('Contam Taxi or Remote Pad?');
});

test('cold weather ops items hidden when coldWeatherOps flag is disabled', function () {
    // Mark checklist step 2.0 as complete
    Cache::put("checklist_completed_items_{$this->employeeNumber}", [
        '2.0' => ['0', '1', '2', '3', '4', '5', '6'], // Complete all 7 items in step 2.0
    ], now()->addDays(30));

    // Disable Cold Weather Ops
    Cache::put("flight_details_{$this->employeeNumber}", [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'KLAX',
        'etops' => false,
        'liveAnimals' => false,
        'hazmat' => false,
        'coldWeatherOps' => false,
        'llws' => false,
    ], now()->addDays(30));

    $component = Livewire::test(Briefing::class);
    $component->call('goToStep', '3.0');

    // Check that cold weather items are NOT displayed
    $component->assertDontSee('Cold Weather Considerations');
});

test('llws items show when llws flag is enabled', function () {
    // Mark checklist step 2.0 as complete
    Cache::put("checklist_completed_items_{$this->employeeNumber}", [
        '2.0' => ['0', '1', '2', '3', '4', '5', '6'], // Complete all 7 items in step 2.0
    ], now()->addDays(30));

    // Enable LLWS
    Cache::put("flight_details_{$this->employeeNumber}", [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'KLAX',
        'etops' => false,
        'liveAnimals' => false,
        'hazmat' => false,
        'coldWeatherOps' => false,
        'llws' => true,
    ], now()->addDays(30));

    $component = Livewire::test(Briefing::class);
    $component->call('goToStep', '3.0');

    // Check that LLWS items are displayed
    $component->assertSee('ATIS: LLWS')
        ->assertSee('Aerodata Updated');
});

test('llws items hidden when llws flag is disabled', function () {
    // Mark checklist step 2.0 as complete
    Cache::put("checklist_completed_items_{$this->employeeNumber}", [
        '2.0' => ['0', '1', '2', '3', '4', '5', '6'], // Complete all 7 items in step 2.0
    ], now()->addDays(30));

    // Disable LLWS
    Cache::put("flight_details_{$this->employeeNumber}", [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'KLAX',
        'etops' => false,
        'liveAnimals' => false,
        'hazmat' => false,
        'coldWeatherOps' => false,
        'llws' => false,
    ], now()->addDays(30));

    $component = Livewire::test(Briefing::class);
    $component->call('goToStep', '3.0');

    // Check that LLWS items are NOT displayed
    $component->assertDontSee('ATIS: LLWS');
});

test('notams step displays correct items', function () {
    $component = Livewire::test(Briefing::class);

    $component->call('goToStep', '5.0');

    // Check that NOTAMs items are displayed (step 5.0)
    $component->assertSee('NOTAMs');
});

test('taxi plan step displays correct items', function () {
    $component = Livewire::test(Briefing::class);

    $component->call('goToStep', '6.0');

    // Check that Taxi Plan items are displayed (step 6.0)
    $component->assertSee('Taxi Plan/Hot Spots')
        ->assertSee('Taxi Plan')
        ->assertSee('Wingtip Restrictions');
});

test('rto step displays correct items', function () {
    $component = Livewire::test(Briefing::class);

    $component->call('goToStep', '7.0');

    // Check that RTO items are displayed (step 7.0)
    $component->assertSee('RTO');
});

test('efp return to alternate step displays correct items', function () {
    $component = Livewire::test(Briefing::class);

    $component->call('goToStep', '8.0');

    // Check that EFP, Return, TO Alternate items are displayed (step 8.0)
    $component->assertSee('EFP, Return, TO Alternate');
});

test('terrain obstacles step displays correct items', function () {
    $component = Livewire::test(Briefing::class);

    $component->call('goToStep', '4.0');

    // Check that terrain/obstacles items are displayed
    $component->assertSee('Terrain/Obstacles')
        ->assertSee('Accel Height vs Area MEA')
        ->assertSee('High Altitude Considerations')
        ->assertSee('CFIT Review');
});

test('terrain obstacles step has correct structure with subitems', function () {
    $component = Livewire::test(Briefing::class);

    $filteredItems = $component->instance()->getFilteredItems('4.0');

    // Verify main items
    expect($filteredItems[0]['text'])->toBe('Accel Height vs Area MEA');
    expect($filteredItems[0]['isSubItem'])->toBe(false);

    expect($filteredItems[1]['text'])->toBe('High Altitude Considerations');
    expect($filteredItems[1]['isSubItem'])->toBe(false);

    // Verify CFIT Review sub-item
    expect($filteredItems['1.0']['text'])->toBe('CFIT Review');
    expect($filteredItems['1.0']['isSubItem'])->toBe(true);

    // Verify total count (2 main items + 1 sub-item)
    expect(count($filteredItems))->toBe(3);
});

test('can navigate through all briefing steps', function () {
    $component = Livewire::test(Briefing::class);

    // Verify we have 9 steps
    expect($component->get('totalSteps'))->toBe(9);

    // Test navigation through all steps
    expect($component->get('currentStep'))->toBe('1.0');

    $component->call('goToStep', '2.0');
    expect($component->get('currentStep'))->toBe('2.0');

    $component->call('goToStep', '3.0');
    expect($component->get('currentStep'))->toBe('3.0');

    $component->call('goToStep', '4.0');
    expect($component->get('currentStep'))->toBe('4.0');

    $component->call('goToStep', '5.0');
    expect($component->get('currentStep'))->toBe('5.0');

    $component->call('goToStep', '6.0');
    expect($component->get('currentStep'))->toBe('6.0');
});

test('briefing completion shows when all steps are complete', function () {
    $component = Livewire::test(Briefing::class);

    // Navigate to final step (9.0)
    $component->call('goToStep', '9.0');

    // Get all visible items for step 9.0
    $filteredItems = $component->instance()->getFilteredItems('9.0');

    // Complete all items in the final step
    foreach (array_keys($filteredItems) as $itemKey) {
        $component->call('toggleItem', '9.0', $itemKey);
    }

    // Verify step is complete
    expect($component->instance()->isStepComplete('9.0'))->toBe(true);

    // Check for completion message
    $component->assertSee('Briefing Complete');
});

test('weather runway condition step has correct structure with subitems', function () {
    // Enable cold weather and LLWS to see all items
    Cache::put("flight_details_{$this->employeeNumber}", [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'KLAX',
        'etops' => false,
        'liveAnimals' => false,
        'hazmat' => false,
        'coldWeatherOps' => true,
        'llws' => true,
    ], now()->addDays(30));

    $component = Livewire::test(Briefing::class);

    $filteredItems = $component->instance()->getFilteredItems('3.0');

    // Verify Cold Weather Considerations item
    expect($filteredItems[0]['text'])->toBe('Cold Weather Considerations');
    expect($filteredItems[0]['isSubItem'])->toBe(false);

    // Verify sub-items under Cold Weather Considerations
    expect($filteredItems['0.0']['text'])->toBe('ENG A/I ON AFTER START');
    expect($filteredItems['0.0']['isSubItem'])->toBe(true);
    expect($filteredItems['0.1']['text'])->toBe('Contam Taxi or Remote Pad?');
    expect($filteredItems['0.1']['isSubItem'])->toBe(true);

    // Verify Wet or Contaminated Runway item
    expect($filteredItems[1]['text'])->toBe('Wet or Contaminated Runway');
    expect($filteredItems['1.0']['text'])->toBe('Aerodata Updated');
    expect($filteredItems['1.0']['isSubItem'])->toBe(true);

    // Verify LLWS item
    expect($filteredItems[2]['text'])->toBe('ATIS: LLWS');
    expect($filteredItems['2.0']['text'])->toBe('Aerodata Updated');

    // Verify total count with all conditional items visible
    expect(count($filteredItems))->toBeGreaterThan(5);
});

test('stale completed items are cleaned up on mount', function () {
    // Start with ETOPS enabled
    Cache::put("flight_details_{$this->employeeNumber}", [
        'etops' => true,
        'liveAnimals' => true,
        'hazmat' => true,
    ], now()->addDays(30));

    $component = Livewire::test(Briefing::class);

    // Complete all items including conditional ones
    $allItems = $component->instance()->getFilteredItems('1.0');
    foreach (array_keys($allItems) as $itemKey) {
        $component->call('toggleItem', '1.0', $itemKey);
    }

    // Now disable all conditional items
    Cache::put("flight_details_{$this->employeeNumber}", [
        'etops' => false,
        'liveAnimals' => false,
        'hazmat' => false,
    ], now()->addDays(30));

    // Remount - this should clean up stale items
    $component2 = Livewire::test(Briefing::class);

    $completedItems = $component2->get('completedItems')['1.0'];
    $visibleItems = array_keys($component2->instance()->getFilteredItems('1.0'));

    // All completed items should be in the visible items list
    foreach ($completedItems as $completedKey) {
        $found = false;
        foreach ($visibleItems as $visibleKey) {
            if ($completedKey == $visibleKey) {
                $found = true;
                break;
            }
        }
        expect($found)->toBe(true);
    }
});
