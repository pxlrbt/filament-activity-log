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
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Features\SupportPagination\HandlesPagination;

abstract class ListActivities extends Page implements HasForms
{
    use CanPaginateRecords;
    use HandlesPagination;
    use InteractsWithFormActions;
    use InteractsWithRecord;

    protected static string $view = 'filament-activity-log::pages.list-activities';

    protected static Collection $fieldLabelMap;

    #[Url]
    public int $perPage = 10;

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getBreadcrumb(): string|Htmlable
    {
        return static::$breadcrumb ?? __('filament-activity-log::activities.breadcrumb');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament-activity-log::activities.title', ['record' => $this->getRecordTitle()]);
    }

    public function getActivities()
    {
        return $this->paginateTableQuery(
            $this->getActivitiesQuery()
        );
    }

    protected function getActivitiesQuery(): Builder
    {
        return $this->record->activities()
            ->with(['causer' => fn($query) => $query->withTrashed()])
            ->latest();
    }

    public function getFieldLabel(string $name): string
    {
        static::$fieldLabelMap ??= $this->createFieldLabelMap();

        return static::$fieldLabelMap->get($name, $name);
    }

    protected function createFieldLabelMap(): Collection
    {
        return $this->extractFormFields()
            ->filter(fn ($field) => $field instanceof Field)
            ->mapWithKeys(fn (Field $field) => [
                $field->getName() => $field->getLabel() ?? $field->getName(),
            ]);
    }

    protected function extractFormFields(): Collection
    {
        $form = static::getResource()::form(new Form($this));
        $components = collect($form->getComponents());
        $extracted = collect();

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

    public function canRestoreActivity(): bool
    {
        return static::getResource()::canRestore($this->record);
    }

    public function restoreActivity(int|string $key): void
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

    protected function sendRestoreSuccessNotification(): Notification
    {
        return Notification::make()
            ->title(__('filament-activity-log::activities.events.restore_successful'))
            ->success()
            ->send();
    }

    protected function sendRestoreFailureNotification(?string $message = null): Notification
    {
        return Notification::make()
            ->title(__('filament-activity-log::activities.events.restore_failed'))
            ->body($message)
            ->danger()
            ->send();
    }

    protected function getIdentifiedTableQueryStringPropertyNameFor(string $property): string
    {
        return $property;
    }

    protected function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return $this->perPage;
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50];
    }
}
