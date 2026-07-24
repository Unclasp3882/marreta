<?php

use App\Services\MarretaCacheService;
use Livewire\Component;

new class extends Component
{
    public int $count = 0;

    public function mount(int $count = 0): void
    {
        $this->count = $count;
    }

    public function refreshCount(): void
    {
        $this->count = app(MarretaCacheService::class)->getCacheFileCount();
    }
};
?>

<p class="walls_destroyed" wire:poll.5s="refreshCount">
    <strong>{{ number_format($count, 0, ',', '.') }}</strong> <span>@lang('marreta.walls_destroyed')</span>
</p>
