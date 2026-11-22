<?php

namespace App\Livewire\Forms;

use App\Models\Project;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;
use RuntimeException;

/**
 * Dashboard form for a project. Translatable fields are arrays keyed by locale;
 * list fields (built, stats) are edited as one item per line.
 */
class ProjectForm extends Form
{
    /**
     * Single-line translatable fields.
     *
     * @var list<string>
     */
    public const SHORT_FIELDS = ['label', 'title', 'role', 'period', 'stack'];

    /**
     * Multi-line translatable fields.
     *
     * @var list<string>
     */
    public const LONG_FIELDS = ['summary', 'context', 'problem', 'result'];

    /**
     * Translatable fields that must be filled in every locale.
     *
     * @var list<string>
     */
    public const REQUIRED_FIELDS = ['title', 'summary', 'context', 'problem', 'result'];

    /**
     * Translatable list fields, edited as one item per line.
     *
     * @var list<string>
     */
    public const LIST_FIELDS = ['built', 'stats'];

    /**
     * Most screenshots the "what I built" section can hold.
     */
    public const MAX_BUILT_IMAGES = 6;

    public ?Project $project = null;

    public string $slug = '';

    public int $sort_order = 0;

    public bool $is_published = false;

    public string $live_url = '';

    /** @var UploadedFile|null */
    public $image = null;

    public bool $remove_image = false;

    /** @var list<string> Stored "what I built" screenshots that are kept. */
    public array $built_images = [];

    /** @var array<int, UploadedFile> New "what I built" screenshots to store. */
    public array $new_built_images = [];

    /** @var array<string, string> */
    public array $label = [];

    /** @var array<string, string> */
    public array $title = [];

    /** @var array<string, string> */
    public array $summary = [];

    /** @var array<string, string> */
    public array $role = [];

    /** @var array<string, string> */
    public array $period = [];

    /** @var array<string, string> */
    public array $stack = [];

    /** @var array<string, string> */
    public array $context = [];

    /** @var array<string, string> */
    public array $problem = [];

    /** @var array<string, string> */
    public array $result = [];

    /** @var array<string, string> One item per line. */
    public array $built = [];

    /** @var array<string, string> One "value | label" pair per line. */
    public array $stats = [];

    /**
     * Start an empty form with a blank value for every locale, placed after the last project.
     */
    public function initialize(): void
    {
        foreach ($this->translatableFields() as $field) {
            $this->{$field} = array_fill_keys(config('site.locales'), '');
        }

        $this->sort_order = (int) Project::query()->max('sort_order') + 1;
    }

    /**
     * Fill the form from an existing project.
     */
    public function setProject(Project $project): void
    {
        $this->project = $project;
        $this->slug = $project->slug;
        $this->sort_order = $project->sort_order;
        $this->is_published = $project->is_published;
        $this->live_url = $project->live_url ?? '';
        $this->built_images = $project->built_images ?? [];

        foreach (config('site.locales') as $locale) {
            foreach ([...self::SHORT_FIELDS, ...self::LONG_FIELDS] as $field) {
                $this->{$field}[$locale] = (string) ($project->getTranslations($field)[$locale] ?? '');
            }

            $this->built[$locale] = implode("\n", $project->getTranslations('built')[$locale] ?? []);
            $this->stats[$locale] = implode("\n", array_map(
                fn (array $stat): string => "{$stat['value']} | {$stat['label']}",
                $project->getTranslations('stats')[$locale] ?? [],
            ));
        }
    }

    /**
     * Validate and persist the project, storing or removing its screenshots.
     */
    public function save(): Project
    {
        $this->validate();

        $project = $this->project ?? new Project;
        $previousImages = array_filter([$project->image, ...($project->built_images ?? [])]);

        $translatedTexts = [];

        foreach ([...self::SHORT_FIELDS, ...self::LONG_FIELDS] as $field) {
            $translatedTexts[$field] = $this->translatedText($this->{$field});
        }

        $project->fill([
            ...$translatedTexts,
            'slug' => $this->slug,
            'sort_order' => $this->sort_order,
            'is_published' => $this->is_published,
            'live_url' => filled($this->live_url) ? $this->live_url : null,
            'built' => $this->translatedList($this->built, fn (string $line): string => $line),
            'stats' => $this->translatedList($this->stats, function (string $line): array {
                [$value, $label] = array_map(trim(...), explode('|', $line, 2));

                return ['value' => $value, 'label' => $label];
            }),
        ]);

        if ($this->image) {
            $project->image = $this->storeImage($this->image);
        } elseif ($this->remove_image) {
            $project->image = null;
        }

        $builtImages = [...$this->built_images, ...array_map($this->storeImage(...), $this->new_built_images)];
        $project->built_images = $builtImages === [] ? null : $builtImages;

        $project->save();

        Storage::disk('public')->delete(array_values(array_diff(
            $previousImages,
            array_filter([$project->image, ...$builtImages]),
        )));

        $this->project = $project;
        $this->image = null;
        $this->remove_image = false;
        $this->built_images = $builtImages;
        $this->new_built_images = [];

        return $project;
    }

    /**
     * Stop keeping a stored "what I built" screenshot (the file is deleted on save).
     */
    public function removeBuiltImage(int $index): void
    {
        $this->built_images = array_values(array_diff_key($this->built_images, [$index => true]));
    }

    /**
     * Get the validation rules.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        $rules = [
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('projects', 'slug')->ignore($this->project)],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_published' => ['boolean'],
            'live_url' => ['nullable', 'url:https,http', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'remove_image' => ['boolean'],
            'built_images' => ['array'],
            'new_built_images' => ['array', 'max:'.(self::MAX_BUILT_IMAGES - count($this->built_images))],
            'new_built_images.*' => ['image', 'max:4096'],
        ];

        foreach (config('site.locales') as $locale) {
            foreach (self::SHORT_FIELDS as $field) {
                $rules["{$field}.{$locale}"] = [$this->presenceRule($field), 'string', 'max:255'];
            }

            foreach (self::LONG_FIELDS as $field) {
                $rules["{$field}.{$locale}"] = [$this->presenceRule($field), 'string', 'max:5000'];
            }

            foreach (self::LIST_FIELDS as $field) {
                $rules["{$field}.{$locale}"] = ['nullable', 'string', 'max:5000'];
            }

            $rules["stats.{$locale}"][] = function (string $attribute, mixed $value, Closure $fail): void {
                foreach ($this->lines($value) as $line) {
                    if (! preg_match('/^[^|]+\|[^|]+$/', $line)) {
                        $fail(__('Each figure must be written as "value | label".'));

                        return;
                    }
                }
            };
        }

        return $rules;
    }

    /**
     * Get readable names for the per-locale fields, e.g. "title (ES)".
     *
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        $attributes = [
            'new_built_images' => __('Build screenshots'),
            'new_built_images.*' => __('Build screenshots'),
        ];

        foreach (config('site.locales') as $locale) {
            foreach (self::labels() as $field => $label) {
                $attributes["{$field}.{$locale}"] = $label.' ('.Str::upper($locale).')';
            }
        }

        return $attributes;
    }

    /**
     * Get the translated label of every translatable field, in form order.
     *
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            'title' => __('Title'),
            'label' => __('Tags'),
            'summary' => __('Summary'),
            'role' => __('Role'),
            'period' => __('Period'),
            'stack' => __('Stack'),
            'context' => __('Context'),
            'problem' => __('Problem'),
            'built' => __('What I built'),
            'stats' => __('Figures'),
            'result' => __('Result'),
        ];
    }

    /**
     * Every translatable field the form edits.
     *
     * @return list<string>
     */
    private function translatableFields(): array
    {
        return [...self::SHORT_FIELDS, ...self::LONG_FIELDS, ...self::LIST_FIELDS];
    }

    /**
     * Store an uploaded screenshot on the public disk and return its path.
     */
    private function storeImage(UploadedFile $file): string
    {
        $path = $file->store('projects', 'public');

        if ($path === false) {
            throw new RuntimeException('The project screenshot could not be stored.');
        }

        return $path;
    }

    /**
     * Whether a translatable field must be filled in every locale.
     */
    private function presenceRule(string $field): string
    {
        return in_array($field, self::REQUIRED_FIELDS, true) ? 'required' : 'nullable';
    }

    /**
     * Keep a text field's locales, or null when every locale is empty.
     *
     * @param  array<string, string|null>  $values
     * @return array<string, string|null>|null
     */
    private function translatedText(array $values): ?array
    {
        $values = array_map(fn (?string $value): ?string => filled($value) ? trim($value) : null, $values);

        return array_filter($values) === [] ? null : $values;
    }

    /**
     * Split a list field's locales into items, or null when every locale is empty.
     *
     * @param  array<string, string|null>  $values
     * @param  Closure(string): mixed  $toItem
     * @return array<string, list<mixed>>|null
     */
    private function translatedList(array $values, Closure $toItem): ?array
    {
        $lists = array_map(fn (?string $value): array => array_map($toItem, $this->lines($value)), $values);

        return array_filter($lists) === [] ? null : $lists;
    }

    /**
     * Non-empty, trimmed lines of a textarea value.
     *
     * @return list<string>
     */
    private function lines(?string $value): array
    {
        return array_values(array_filter(array_map(trim(...), preg_split('/\R/', (string) $value) ?: [])));
    }
}
