<?php

namespace App\Livewire;

use Livewire\Component;

class ChecklistItem extends Component
{
    public $stepKey;

    public $itemIndex;

    public $itemText;

    public $notes = null;

    public $images = null;

    public $subitems = null;

    public $isCompleted = false;

    public $isSubItem = false;

    public $completedSubitems = [];

    public function mount($stepKey, $itemIndex, $item, $isCompleted = false, $isSubItem = false, $completedSubitems = [])
    {
        $this->stepKey = $stepKey;
        $this->itemIndex = $itemIndex;
        $this->isCompleted = $isCompleted;
        $this->isSubItem = $isSubItem;
        $this->completedSubitems = $completedSubitems;

        // Parse item data
        if (is_array($item)) {
            $this->itemText = $item['text'];
            $this->notes = $item['notes'] ?? null;
            $this->images = $item['images'] ?? null;
            $this->subitems = $item['subitems'] ?? null;
        } else {
            $this->itemText = $item;
        }
    }

    public function toggle()
    {
        $this->isCompleted = ! $this->isCompleted;

        // Dispatch event to parent component
        $this->dispatch('item-toggled', [
            'stepKey' => $this->stepKey,
            'itemIndex' => $this->itemIndex,
            'completed' => $this->isCompleted,
        ]);
    }

    public function hasNotes()
    {
        return ! empty($this->notes);
    }

    public function getNotesType()
    {
        return $this->notes['type'] ?? 'text';
    }

    public function getModalId()
    {
        return "modal-{$this->stepKey}-{$this->itemIndex}";
    }

    public function hasImages()
    {
        return ! empty($this->images) && is_array($this->images);
    }

    public function getImageModalId()
    {
        return "image-modal-{$this->stepKey}-{$this->itemIndex}";
    }

    public function hasSubitems()
    {
        return ! empty($this->subitems) && is_array($this->subitems);
    }

    public function render()
    {
        return view('livewire.checklist-item');
    }
}
