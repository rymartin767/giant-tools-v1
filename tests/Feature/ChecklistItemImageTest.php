<?php

declare(strict_types=1);

use App\Livewire\ChecklistItem;
use Livewire\Livewire;

test('checklist item displays image button when images are provided', function () {
    $item = [
        'text' => 'Review Fuel Planning',
        'images' => [
            [
                'title' => 'Fuel Planning Reference',
                'url' => '/images/placeholder.jpg',
                'alt' => 'Fuel planning chart',
                'caption' => 'Review fuel requirements and reserves',
            ],
        ],
    ];

    Livewire::test(ChecklistItem::class, [
        'stepKey' => '1.1',
        'itemIndex' => 2,
        'item' => $item,
        'isCompleted' => false,
    ])
        ->assertSee('Review Fuel Planning')
        ->assertSeeHtml('text-purple-500');
});

test('checklist item does not display image button when no images provided', function () {
    $item = 'Simple checklist item';

    Livewire::test(ChecklistItem::class, [
        'stepKey' => '1.0',
        'itemIndex' => 0,
        'item' => $item,
        'isCompleted' => false,
    ])
        ->assertSee('Simple checklist item')
        ->assertDontSeeHtml('text-purple-500');
});

test('checklist item hasImages method returns true when images exist', function () {
    $item = [
        'text' => 'Test Item',
        'images' => [
            ['url' => '/test.jpg'],
        ],
    ];

    $component = Livewire::test(ChecklistItem::class, [
        'stepKey' => '1.0',
        'itemIndex' => 0,
        'item' => $item,
        'isCompleted' => false,
    ]);

    expect($component->instance()->hasImages())->toBeTrue();
});

test('checklist item hasImages method returns false when no images', function () {
    $item = 'Simple item';

    $component = Livewire::test(ChecklistItem::class, [
        'stepKey' => '1.0',
        'itemIndex' => 0,
        'item' => $item,
        'isCompleted' => false,
    ]);

    expect($component->instance()->hasImages())->toBeFalse();
});
