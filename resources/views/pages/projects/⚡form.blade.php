<?php

use App\Livewire\Forms\ProjectForm;
use App\Models\Project;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Project')] class extends Component {
    use WithFileUploads;

    public ProjectForm $form;

    /**
     * Load the project being edited, or start a blank one on the create route.
     */
    public function mount(?Project $project = null): void
    {
        if ($project?->exists) {
            $this->form->setProject($project);
        } else {
            $this->form->initialize();
        }
    }

    /**
     * Suggest a slug from the Spanish title while creating a project.
     */
    public function updated(string $property, mixed $value): void
    {
        $titleProperty = 'form.title.'.config('site.locales')[0];

        if ($property === $titleProperty && ! $this->form->project && blank($this->form->slug)) {
            $this->form->slug = Str::slug((string) $value);
        }
    }

    /**
     * Drop a stored "what I built" screenshot from the project (deleted on save).
     */
    public function removeBuiltImage(int $index): void
    {
        $this->form->removeBuiltImage($index);
    }

    /**
     * Save the project; reload the edit page when its URL changed.
     */
    public function save(): void
    {
        $previousSlug = $this->form->project?->slug;

        $project = $this->form->save();

        Flux::toast(variant: 'success', text: __('Project saved.'));

        if ($project->slug !== $previousSlug) {
            $this->redirectRoute('dashboard.projects.edit', $project, navigate: true);
        }
    }
}; ?>

<section class="w-full">
    @php
        $locales = config('site.locales');
        $labels = ProjectForm::labels();
        $listHints = [
            'built' => __('One item per line.'),
            'stats' => __('One figure per line, as "value | label". E.g. "+12 | leagues in the US".'),
        ];
    @endphp

    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:breadcrumbs class="mb-2">
                <flux:breadcrumbs.item :href="route('dashboard.projects.index')" wire:navigate>{{ __('Projects') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ $form->project ? __('Edit') : __('New') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <flux:heading size="xl" level="1">{{ $form->project ? $form->project->title : __('New project') }}</flux:heading>
        </div>

        @if ($form->project?->is_published)
            <flux:button variant="ghost" icon="arrow-top-right-on-square" :href="route('projects.show', $form->project)" target="_blank">{{ __('View on site') }}</flux:button>
        @endif
    </div>

    <flux:separator variant="subtle" class="mb-6" />

    <form wire:submit="save" class="flex max-w-3xl flex-col gap-10">
        {{-- Datos generales --}}
        <div class="flex flex-col gap-6">
            <flux:heading size="lg" level="2">{{ __('General') }}</flux:heading>

            <div class="grid gap-6 sm:grid-cols-[1fr_8rem]">
                <flux:input wire:model="form.slug" :label="__('Slug')" :description="__('URL: /proyectos/{slug}. Lowercase letters, numbers and hyphens.')" required />
                <flux:input wire:model="form.sort_order" type="number" min="0" :label="__('Order')" required />
            </div>

            <flux:switch wire:model="form.is_published" :label="__('Published')" :description="__('Visible on the home page and at its case study URL.')" align="left" />

            <flux:input wire:model="form.live_url" type="url" :label="__('Live URL')" placeholder="https://" />

            <flux:field>
                <flux:label>{{ __('Screenshot') }}</flux:label>
                <flux:description>{{ __('JPG, PNG or WebP up to 4 MB. Used on the card and the case study.') }}</flux:description>

                @if ($form->image instanceof TemporaryUploadedFile && $form->image->isPreviewable())
                    <img src="{{ $form->image->temporaryUrl() }}" alt="" class="max-h-48 w-fit rounded-lg border border-zinc-700">
                @elseif ($form->project?->image && ! $form->remove_image)
                    <img src="{{ Storage::disk('public')->url($form->project->image) }}" alt="" class="max-h-48 w-fit rounded-lg border border-zinc-700">
                @endif

                <flux:input type="file" wire:model="form.image" accept="image/*" />
                <flux:error name="form.image" />
            </flux:field>

            @if ($form->project?->image)
                <flux:checkbox wire:model.live="form.remove_image" :label="__('Remove current screenshot')" />
            @endif

            <flux:field>
                <flux:label>{{ __('Build screenshots') }}</flux:label>
                <flux:description>{{ __('Shown in the "What I built" section. Up to :count images, 4 MB each.', ['count' => ProjectForm::MAX_BUILT_IMAGES]) }}</flux:description>

                @if ($form->built_images || $form->new_built_images)
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($form->built_images as $index => $builtImage)
                            <div wire:key="built-image-{{ $builtImage }}" class="relative">
                                <img src="{{ Storage::disk('public')->url($builtImage) }}" alt="" class="aspect-[4/3] w-full rounded-lg border border-zinc-700 object-cover object-top">
                                <flux:button size="xs" variant="danger" icon="trash" class="absolute! end-2 top-2" wire:click="removeBuiltImage({{ $index }})" :aria-label="__('Remove')" />
                            </div>
                        @endforeach

                        @foreach ($form->new_built_images as $newBuiltImage)
                            @if ($newBuiltImage instanceof TemporaryUploadedFile && $newBuiltImage->isPreviewable())
                                <img wire:key="new-built-image-{{ $loop->index }}" src="{{ $newBuiltImage->temporaryUrl() }}" alt="" class="aspect-[4/3] w-full rounded-lg border border-dashed border-zinc-500 object-cover object-top">
                            @endif
                        @endforeach
                    </div>
                @endif

                <flux:input type="file" wire:model="form.new_built_images" accept="image/*" multiple />
                <flux:error name="form.new_built_images" />
                <flux:error name="form.new_built_images.*" />
            </flux:field>
        </div>

        {{-- Contenido por idioma --}}
        <div x-data="{ locale: @js($locales[0]) }" class="flex flex-col gap-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <flux:heading size="lg" level="2">{{ __('Content') }}</flux:heading>

                <div role="tablist" aria-label="{{ __('Language') }}" class="flex gap-1 rounded-lg bg-zinc-800 p-1">
                    @foreach ($locales as $locale)
                        <button
                            type="button"
                            role="tab"
                            wire:key="locale-tab-{{ $locale }}"
                            x-on:click="locale = @js($locale)"
                            x-bind:aria-selected="locale === @js($locale)"
                            x-bind:class="locale === @js($locale) ? 'bg-zinc-600 text-white' : 'text-zinc-400 hover:text-white'"
                            class="flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-medium"
                        >
                            {{ config("site.locale_names.{$locale}") }}
                            @if ($errors->get("form.*.{$locale}"))
                                <span class="size-2 rounded-full bg-red-500" aria-label="{{ __('Has errors') }}"></span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            @foreach ($locales as $locale)
                <div wire:key="locale-fields-{{ $locale }}" x-show="locale === @js($locale)" role="tabpanel" lang="{{ $locale }}" class="flex flex-col gap-6">
                    @foreach ($labels as $field => $label)
                        @php($required = in_array($field, ProjectForm::REQUIRED_FIELDS, true))

                        @if (in_array($field, ProjectForm::SHORT_FIELDS, true))
                            <flux:input
                                wire:model.blur="form.{{ $field }}.{{ $locale }}"
                                :label="$label"
                                :badge="$required ? null : __('Optional')"
                            />
                        @else
                            <flux:textarea
                                wire:model="form.{{ $field }}.{{ $locale }}"
                                :label="$label"
                                :description="$listHints[$field] ?? null"
                                :badge="$required ? null : __('Optional')"
                                rows="{{ in_array($field, ProjectForm::LIST_FIELDS, true) ? 4 : 3 }}"
                            />
                        @endif

                        @if ($field === 'summary')
                            <flux:separator variant="subtle" :text="__('Case study')" />
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            <flux:button variant="ghost" :href="route('dashboard.projects.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
        </div>
    </form>
</section>
