<?php

use App\Models\Project;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Projects')] class extends Component {
    public ?int $deletingProjectId = null;

    /**
     * All projects, in the order they appear on the site.
     *
     * @return Collection<int, Project>
     */
    #[Computed]
    public function projects(): Collection
    {
        return Project::query()->ordered()->get();
    }

    /**
     * Show or hide a project on the public site.
     */
    public function togglePublished(int $projectId): void
    {
        $project = Project::query()->findOrFail($projectId);

        $project->update(['is_published' => ! $project->is_published]);

        Flux::toast(variant: 'success', text: $project->is_published ? __('Project published.') : __('Project hidden.'));
    }

    /**
     * Ask for confirmation before deleting a project.
     */
    public function confirmDelete(int $projectId): void
    {
        $this->deletingProjectId = $projectId;

        Flux::modal('delete-project')->show();
    }

    /**
     * Delete the project awaiting confirmation (its screenshot is removed by the model).
     */
    public function delete(): void
    {
        Project::query()->findOrFail($this->deletingProjectId)->delete();

        $this->deletingProjectId = null;

        Flux::modal('delete-project')->close();
        Flux::toast(variant: 'success', text: __('Project deleted.'));
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Projects') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Projects shown on the home page and as case studies') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('dashboard.projects.create')" wire:navigate>{{ __('New project') }}</flux:button>
    </div>

    <flux:separator variant="subtle" class="mb-6" />

    @if ($this->projects->isEmpty())
        <flux:text>{{ __('There are no projects yet.') }}</flux:text>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Order') }}</flux:table.column>
                <flux:table.column>{{ __('Title') }}</flux:table.column>
                <flux:table.column>{{ __('Slug') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column><span class="sr-only">{{ __('Actions') }}</span></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->projects as $project)
                    <flux:table.row :key="$project->id">
                        <flux:table.cell>{{ $project->sort_order }}</flux:table.cell>

                        <flux:table.cell variant="strong">
                            <a href="{{ route('dashboard.projects.edit', $project) }}" class="hover:underline" wire:navigate>{{ $project->title }}</a>
                        </flux:table.cell>

                        <flux:table.cell class="font-mono">{{ $project->slug }}</flux:table.cell>

                        <flux:table.cell>
                            @if ($project->is_published)
                                <flux:badge color="green" size="sm" inset="top bottom">{{ __('Published') }}</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm" inset="top bottom">{{ __('Draft') }}</flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:dropdown position="bottom" align="end">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" :aria-label="__('Actions')" />

                                <flux:menu>
                                    <flux:menu.item icon="pencil-square" :href="route('dashboard.projects.edit', $project)" wire:navigate>{{ __('Edit') }}</flux:menu.item>

                                    @if ($project->is_published)
                                        <flux:menu.item icon="arrow-top-right-on-square" :href="route('projects.show', $project)" target="_blank">{{ __('View on site') }}</flux:menu.item>
                                        <flux:menu.item icon="eye-slash" wire:click="togglePublished({{ $project->id }})">{{ __('Hide') }}</flux:menu.item>
                                    @else
                                        <flux:menu.item icon="eye" wire:click="togglePublished({{ $project->id }})">{{ __('Publish') }}</flux:menu.item>
                                    @endif

                                    <flux:menu.separator />

                                    <flux:menu.item icon="trash" variant="danger" wire:click="confirmDelete({{ $project->id }})">{{ __('Delete') }}</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @endif

    <flux:modal name="delete-project" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete project?') }}</flux:heading>
                <flux:text class="mt-2">{{ __('The project and its screenshot will be permanently deleted. This cannot be undone.') }}</flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="delete">{{ __('Delete') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
