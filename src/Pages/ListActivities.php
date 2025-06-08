<?php

namespace pxlrbt\FilamentActivityLog\Pages;

use Exception;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\CanPaginateRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Features\SupportPagination\HandlesPagination;

/**
 * Abstract class for displaying and managing activity logs for a record
 */
abstract class ListActivities extends Page implements HasForms
{
    use CanPaginateRecords;
    use HandlesPagination;
    use InteractsWithFormActions;
    use InteractsWithRecord;

    // View template for the activity log
    protected static string $view = 'filament-activity-log::pages.list-activities';

    // Cache for field name to label mappings
    protected static Collection $fieldLabelMap;

    // Pagination items per page (stored in URL)
    #[Url]
    public int $perPage = 10;

    /**
     * Initialize the component with the target record
     */
    public function mount($record)
    {
        $this->record = $this->resolveRecord($record);
    }

    /**
     * Get the breadcrumb for the page
     */
    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb ?? __('filament-activity-log::activities.breadcrumb');
    }

    /**
     * Get the page title with record name
     */
    public function getTitle(): string|Htmlable
    {
        return __('filament-activity-log::activities.title', ['record' => $this->getRecordTitle()]);
    }

    /**
     * Get paginated activities for the record
     */
    public function getActivities()
    {
        return $this->paginateTableQuery(
            $this->getActivitiesQuery()
        );
    }

    /**
     * Build the base activity query with causer relationship
     */
    protected function getActivitiesQuery(): Builder
    {
        $query = $this->record->activities();

        // Check if causer uses SoftDeletes
        $causerModel = config('activitylog.causer_model') ?? null;

        if (
            $causerModel &&
            in_array(SoftDeletes::class, class_uses_recursive($causerModel))
        ) {
            // Include trashed causers if model uses SoftDeletes
            $query->with([
                'causer' => fn($q) => $q->withTrashed(),
            ]);
        } else {
            $query->with('causer');
        }

        return $query->latest(); // Order by latest first
    }

    /**
     * Get human-readable label for a field name
     */
    public function getFieldLabel(string $name): string
    {
        static::$fieldLabelMap ??= $this->createFieldLabelMap();
        return static::$fieldLabelMap->get($name, $name);
    }

    /**
     * Create a mapping of field names to their labels from form components
     */
    protected function createFieldLabelMap(): Collection
    {
        return $this->extractFormFields()
            ->filter(fn($field) => $field instanceof Field)
            ->mapWithKeys(fn(Field $field) => [
                $field->getName() => $field->getLabel() ?? $field->getName(),
            ]);
    }

    /**
     * Extract all form fields from the resource's form definition
     */
    protected function extractFormFields(): Collection
    {
        $form = static::getResource()::form(new Form($this));
        $components = collect($form->getComponents());
        $extracted = collect();

        // Recursively extract all fields and MorphToSelect components
        while ($component = $components->shift()) {
            if ($component instanceof Field || $component instanceof MorphToSelect) {
                $extracted->push($component);
                continue;
            }

            if ($children = $component->getChildComponents()) {
                $components->push(...$children);
            }
        }

        return $extracted;
    }

    /**
     * Check if activity restoration is allowed for this record
     */
    public function canRestoreActivity(): bool
    {
        return static::getResource()::canRestore($this->record);
    }

    /**
     * Restore a record to a previous state from an activity
     */
    public function restoreActivity(int|string $key)
    {
        abort_unless($this->canRestoreActivity(), 403);

        $activity = $this->record->activities()
            ->whereKey($key)
            ->firstOrFail();

        $oldProperties = $activity->properties['old'] ?? null;

        if (!$oldProperties) {
            $this->sendRestoreFailureNotification();
            return;
        }

        try {
            $this->record->update($oldProperties);
            $this->sendRestoreSuccessNotification();
        } catch (Exception $e) {
            $this->sendRestoreFailureNotification($e->getMessage());
        }
    }

    /**
     * Send success notification after restoration
     */
    protected function sendRestoreSuccessNotification(): Notification
    {
        return Notification::make()
            ->title(__('filament-activity-log::activities.events.restore_successful'))
            ->success()
            ->send();
    }

    /**
     * Send failure notification when restoration fails
     */
    protected function sendRestoreFailureNotification(?string $message = null): Notification
    {
        return Notification::make()
            ->title(__('filament-activity-log::activities.events.restore_failed'))
            ->body($message)
            ->danger()
            ->send();
    }

    /**
     * Get query string parameter name for table state
     */
    protected function getIdentifiedTableQueryStringPropertyNameFor(string $property): string
    {
        return $property;
    }

    /**
     * Get default pagination size
     */
    protected function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return $this->perPage;
    }

    /**
     * Available pagination size options
     */
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50];
    }
}
