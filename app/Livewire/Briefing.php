<?php

namespace App\Livewire;

use App\Data\BriefingSteps;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Briefing extends Component
{
    public $employeeNumber = '';

    public $currentStep = '1.0';

    public $steps;

    public $completedItems = [];

    public $stepKeys = [];

    public $totalSteps;

    public $checklistStep2Complete = false;

    protected $listeners = ['item-toggled' => 'handleItemToggled'];

    public function mount()
    {
        $this->loadEmployeeNumber();

        // Check if checklist step 2.0 is complete
        $this->checkChecklistRequirement();

        // Load steps from external data file
        $this->steps = BriefingSteps::getSteps();

        // Get step keys for navigation - ensure we preserve decimal keys
        $this->stepKeys = array_keys($this->steps);
        $this->totalSteps = count($this->stepKeys);

        // Load cached completed items for the authenticated user
        if ($this->checklistStep2Complete) {
            $this->loadCompletedItems();
        }
    }

    private function loadEmployeeNumber()
    {
        $sessionKey = session()->getId();
        $cacheKey = "employee_number_{$sessionKey}";

        $this->employeeNumber = Cache::get($cacheKey, '');
    }

    private function checkChecklistRequirement()
    {
        if (! $this->employeeNumber) {
            $this->checklistStep2Complete = false;

            return;
        }

        $cacheKey = "checklist_completed_items_{$this->employeeNumber}";

        $completedItems = Cache::get($cacheKey, []);

        // Check if step 2.0 exists and has any items completed
        // A step is considered complete when all its items are completed
        $this->checklistStep2Complete = $this->isChecklistStepComplete('2.0', $completedItems);
    }

    private function isChecklistStepComplete($stepKey, $completedItems)
    {
        // Get the step items from ChecklistSteps
        $checklistSteps = \App\Data\ChecklistSteps::getSteps();

        if (! isset($checklistSteps[$stepKey])) {
            return false;
        }

        $totalItems = count($checklistSteps[$stepKey]['items']);
        $completedItemsForStep = $completedItems[$stepKey] ?? [];

        // Convert to strings for consistent comparison
        $completedItemsForStep = array_map('strval', $completedItemsForStep);

        $completedCount = count($completedItemsForStep);

        return $totalItems > 0 && $completedCount === $totalItems;
    }

    #[Computed]
    public function engines()
    {
        if (! $this->employeeNumber) {
            return '';
        }

        $cacheKey = "aircraft_details_{$this->employeeNumber}";
        $config = Cache::get($cacheKey, []);

        return $config['engines'] ?? '';
    }

    #[Computed]
    public function config()
    {
        if (! $this->employeeNumber) {
            return '';
        }

        $cacheKey = "aircraft_details_{$this->employeeNumber}";
        $config = Cache::get($cacheKey, []);

        return $config['config'] ?? '';
    }

    #[Computed]
    public function supernumeraries()
    {
        if (! $this->employeeNumber) {
            return false;
        }

        $cacheKey = "aircraft_details_{$this->employeeNumber}";
        $config = Cache::get($cacheKey, []);

        return $config['supernumeraries'] ?? false;
    }

    #[Computed]
    public function etops()
    {
        if (! $this->employeeNumber) {
            return false;
        }

        $cacheKey = "flight_details_{$this->employeeNumber}";
        $config = Cache::get($cacheKey, []);

        return $config['etops'] ?? false;
    }

    #[Computed]
    public function liveAnimals()
    {
        if (! $this->employeeNumber) {
            return false;
        }

        $cacheKey = "flight_details_{$this->employeeNumber}";
        $config = Cache::get($cacheKey, []);

        return $config['liveAnimals'] ?? false;
    }

    #[Computed]
    public function hazmat()
    {
        if (! $this->employeeNumber) {
            return false;
        }

        $cacheKey = "flight_details_{$this->employeeNumber}";
        $config = Cache::get($cacheKey, []);

        return $config['hazmat'] ?? false;
    }

    #[Computed]
    public function coldWeatherOps()
    {
        if (! $this->employeeNumber) {
            return false;
        }

        $cacheKey = "flight_details_{$this->employeeNumber}";
        $config = Cache::get($cacheKey, []);

        return $config['coldWeatherOps'] ?? false;
    }

    #[Computed]
    public function llws()
    {
        if (! $this->employeeNumber) {
            return false;
        }

        $cacheKey = "flight_details_{$this->employeeNumber}";
        $config = Cache::get($cacheKey, []);

        return $config['llws'] ?? false;
    }

    private function loadCompletedItems()
    {
        if (! $this->employeeNumber) {
            return;
        }

        $cacheKey = "briefing_completed_items_{$this->employeeNumber}";

        $this->completedItems = Cache::get($cacheKey, []);

        // Initialize empty arrays for steps that don't have cached data
        foreach ($this->stepKeys as $stepKey) {
            if (! isset($this->completedItems[$stepKey])) {
                $this->completedItems[$stepKey] = [];
            }
        }

        // Clean up stale completed items that are no longer visible
        $this->cleanupStaleCompletedItems();
    }

    private function cleanupStaleCompletedItems()
    {
        foreach ($this->stepKeys as $stepKey) {
            // Get currently visible items for this step (convert to strings)
            $visibleItems = array_map('strval', array_keys($this->getFilteredItems($stepKey)));

            // Filter out completed items that are no longer visible
            if (isset($this->completedItems[$stepKey])) {
                // Convert completed items to strings for comparison
                $this->completedItems[$stepKey] = array_map('strval', $this->completedItems[$stepKey]);

                // Keep only items that are in the visible items list (using strict comparison)
                $this->completedItems[$stepKey] = array_values(
                    array_filter($this->completedItems[$stepKey], function ($completedIndex) use ($visibleItems) {
                        return in_array($completedIndex, $visibleItems, true);
                    })
                );
            }
        }

        // Save cleaned up items back to cache
        $this->saveCompletedItems();
    }

    private function saveCompletedItems()
    {
        if (! $this->employeeNumber) {
            return;
        }

        $cacheKey = "briefing_completed_items_{$this->employeeNumber}";

        // Cache for 30 days (or until manually reset)
        Cache::put($cacheKey, $this->completedItems, now()->addDays(30));
    }

    public function handleItemToggled($data)
    {
        $stepKey = $data['stepKey'];
        // Convert to string for consistent comparison (handles both int and string indices like "1.0")
        $itemIndex = (string) $data['itemIndex'];
        $completed = $data['completed'];

        if (! isset($this->completedItems[$stepKey])) {
            $this->completedItems[$stepKey] = [];
        }

        // Ensure all items in the array are strings
        $this->completedItems[$stepKey] = array_map('strval', $this->completedItems[$stepKey]);

        if ($completed) {
            // Add to completed items if not already there (use strict comparison)
            if (! in_array($itemIndex, $this->completedItems[$stepKey], true)) {
                $this->completedItems[$stepKey][] = $itemIndex;
            }
        } else {
            // Remove from completed items (use strict comparison)
            $this->completedItems[$stepKey] = array_values(
                array_filter($this->completedItems[$stepKey], function ($existing) use ($itemIndex) {
                    return $existing !== $itemIndex;
                })
            );
        }

        // Save to cache after each toggle
        $this->saveCompletedItems();
    }

    public function toggleItem($stepKey, $itemIndex)
    {
        if (! isset($this->completedItems[$stepKey])) {
            $this->completedItems[$stepKey] = [];
        }

        // Convert to string for consistent comparison (handles both int and string indices like "1.0")
        $itemIndex = (string) $itemIndex;

        // Ensure all items in the array are strings
        $this->completedItems[$stepKey] = array_map('strval', $this->completedItems[$stepKey]);

        // Check if item is currently completed (use strict comparison)
        $key = array_search($itemIndex, $this->completedItems[$stepKey], true);
        $isCurrentlyCompleted = $key !== false;

        // Toggle the completion state
        if ($isCurrentlyCompleted) {
            // Remove from completed items
            unset($this->completedItems[$stepKey][$key]);
            $this->completedItems[$stepKey] = array_values($this->completedItems[$stepKey]); // Re-index
        } else {
            // Add to completed items
            $this->completedItems[$stepKey][] = $itemIndex;
        }

        // Save to cache after each toggle
        $this->saveCompletedItems();

        // Trigger haptic feedback and offline sync
        $this->dispatch('item-toggled', [
            'stepKey' => $stepKey,
            'itemIndex' => $itemIndex,
            'completed' => ! $isCurrentlyCompleted,
        ]);
    }

    public function isStepComplete($stepKey)
    {
        $items = $this->getFilteredItems($stepKey);
        $totalItems = count($items);

        // Get the keys of currently visible items (convert to strings)
        $visibleItemKeys = array_map('strval', array_keys($items));

        // Get completed items for this step (convert to strings)
        $completedItems = array_map('strval', $this->completedItems[$stepKey] ?? []);

        // Count how many of the visible items are completed
        // This ensures we only count items that are currently visible
        $completedCount = 0;
        foreach ($visibleItemKeys as $itemKey) {
            // Use strict comparison now that all keys are strings
            if (in_array($itemKey, $completedItems, true)) {
                $completedCount++;
            }
        }

        return $totalItems > 0 && $completedCount === $totalItems;
    }

    public function getFilteredItems($stepKey)
    {
        $items = $this->steps[$stepKey]['items'];
        $filteredItems = [];

        foreach ($items as $index => $item) {
            if (is_array($item)) {
                // Check if it has conditions (conditional item) or notes (item with notes)
                if (isset($item['conditions'])) {
                    // New conditional item format
                    if ($this->itemMatchesConditions($item['conditions'])) {
                        $filteredItems[$index] = [
                            'text' => $item['text'],
                            'notes' => $item['notes'] ?? null,
                            'isSubItem' => false,
                        ];

                        // Add sub-items if they exist
                        if (isset($item['subItems'])) {
                            foreach ($item['subItems'] as $subIndex => $subItem) {
                                $filteredItems["{$index}.{$subIndex}"] = [
                                    'text' => $subItem['text'],
                                    'notes' => $subItem['notes'] ?? null,
                                    'isSubItem' => true,
                                ];
                            }
                        }
                    }
                } else {
                    // Item with notes only (no conditions)
                    $filteredItems[$index] = [
                        'text' => $item['text'],
                        'notes' => $item['notes'] ?? null,
                        'isSubItem' => false,
                    ];

                    // Add sub-items if they exist
                    if (isset($item['subItems'])) {
                        foreach ($item['subItems'] as $subIndex => $subItem) {
                            $filteredItems["{$index}.{$subIndex}"] = [
                                'text' => $subItem['text'],
                                'notes' => $subItem['notes'] ?? null,
                                'isSubItem' => true,
                            ];
                        }
                    }
                }
            } else {
                // Legacy string format - always include
                $filteredItems[$index] = [
                    'text' => $item,
                    'notes' => null,
                    'isSubItem' => false,
                ];
            }
        }

        return $filteredItems;
    }

    private function itemMatchesConditions($conditions)
    {
        if (empty($conditions)) {
            return true; // No conditions means always show
        }

        foreach ($conditions as $field => $allowedValues) {
            // Get value from cache for aircraft and flight configuration fields
            if ($field === 'engines') {
                $currentValue = $this->engines;
            } elseif ($field === 'config') {
                $currentValue = $this->config;
            } elseif ($field === 'supernumeraries') {
                $currentValue = $this->supernumeraries;
            } elseif ($field === 'etops') {
                $currentValue = $this->etops;
            } elseif ($field === 'liveAnimals') {
                $currentValue = $this->liveAnimals;
            } elseif ($field === 'hazmat') {
                $currentValue = $this->hazmat;
            } elseif ($field === 'coldWeatherOps') {
                $currentValue = $this->coldWeatherOps;
            } elseif ($field === 'llws') {
                $currentValue = $this->llws;
            } else {
                $currentValue = $this->{$field} ?? null;
            }

            if (! in_array($currentValue, $allowedValues)) {
                return false;
            }
        }

        return true;
    }

    public function nextStep()
    {
        $currentIndex = array_search($this->currentStep, $this->stepKeys);
        if ($currentIndex !== false && $currentIndex < count($this->stepKeys) - 1 && $this->isStepComplete($this->currentStep)) {
            $this->currentStep = $this->stepKeys[$currentIndex + 1];
        }
    }

    public function previousStep()
    {
        $currentIndex = array_search($this->currentStep, $this->stepKeys);
        if ($currentIndex !== false && $currentIndex > 0) {
            $this->currentStep = $this->stepKeys[$currentIndex - 1];
        }
    }

    public function goToStep($stepKey)
    {
        if (in_array($stepKey, $this->stepKeys)) {
            $this->currentStep = $stepKey;
        }
    }

    public function getProgressPercentage()
    {
        $completedSteps = 0;
        foreach ($this->stepKeys as $stepKey) {
            if ($this->isStepComplete($stepKey)) {
                $completedSteps++;
            }
        }

        return ($completedSteps / $this->totalSteps) * 100;
    }

    public function resetBriefing()
    {
        if (! $this->employeeNumber) {
            return;
        }

        $this->completedItems = [];
        $this->currentStep = $this->stepKeys[0];

        // Clear cache for the authenticated user
        $cacheKey = "briefing_completed_items_{$this->employeeNumber}";
        Cache::forget($cacheKey);
    }

    public function getItemByIndex($stepKey, $index)
    {
        $items = $this->getFilteredItems($stepKey);

        return $items[$index] ?? null;
    }

    public function getItemText($stepKey, $index)
    {
        $item = $this->getItemByIndex($stepKey, $index);
        if (! $item) {
            return '';
        }

        return is_array($item) ? $item['text'] : $item;
    }

    public function getItemNotes($stepKey, $index)
    {
        $item = $this->getItemByIndex($stepKey, $index);
        if (! $item) {
            return null;
        }

        return is_array($item) && isset($item['notes']) ? $item['notes'] : null;
    }

    public function hasNotes($stepKey, $index)
    {
        $notes = $this->getItemNotes($stepKey, $index);

        return ! empty($notes);
    }

    public function getNotesType($stepKey, $index)
    {
        $notes = $this->getItemNotes($stepKey, $index);

        return $notes ? ($notes['type'] ?? 'text') : null;
    }

    public function getModalId($stepKey, $index)
    {
        return "briefing-modal-{$stepKey}-{$index}";
    }

    public function render()
    {
        if (! $this->checklistStep2Complete) {
            return view('livewire.briefing-requires-checklist');
        }

        return view('livewire.briefing');
    }
}
