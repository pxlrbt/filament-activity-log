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
use Illuminate\Support\Collection;
use Livewire\Features\SupportPagination\HandlesPagination;

abstract class ListActivities extends Page implements HasForms
{
    use CanPaginateRecords;
    use HandlesPagination;
    use InteractsWithFormActions;
    use InteractsWithRecord;

    protected static string $view = 'filament-activity-log::pages.list-activities';

    protected static Collection $fieldLabelMap;

    public function mount($record)
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament-activity-log::activities.breadcrumb');
    }

    public function getTitle(): string
    {
        return __('filament-activity-log::activities.title', ['record' => $this->getRecordTitle()]);
    }

    public function getActivities()
    {
        return $this->paginateTableQuery(
            $this->record->activities()->with('causer')->latest()->getQuery()
        );
    }

    public function getFieldLabel(string $name): string
    {
        static::$fieldLabelMap ??= $this->createFieldLabelMap();

        return static::$fieldLabelMap[$name] ?? $name;
    }

    protected function createFieldLabelMap(): Collection
    {
        $form = static::getResource()::form(new Form($this));

        $components = collect($form->getComponents());
        $extracted = collect();

        while (($component = $components->shift()) !== null) {
            if ($component instanceof Field || $component instanceof MorphToSelect) {
                $extracted->push($component);

                continue;
            }

            $children = $component->getChildComponents();

            if (count($children) > 0) {
                $components = $components->merge($children);

                continue;
            }

            $extracted->push($component);
        }

        return $extracted
            ->filter(fn ($field) => $field instanceof Field)
            ->mapWithKeys(fn (Field $field) => [
                $field->getName() => $field->getLabel(),
            ]);
    }

    public function restoreActivity(int|string $key)
    {
        if (! static::getResource()::canRestore($this->record)) {
            abort(403);
        }

        $activity = $this->record->activities()
            ->whereKey($key)
            ->first();

        $oldProperties = data_get($activity, 'properties.old');

        if ($oldProperties === null) {
            Notification::make()
                ->title(__('filament-activity-log::activities.events.restore_failed'))
                ->danger()
                ->send();

            return;
        }

        try {
            $this->record->update($oldProperties);

            Notification::make()
                ->title(__('filament-activity-log::activities.events.restore_successful'))
                ->success()
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->title(__('filament-activity-log::activities.events.restore_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getIdentifiedTableQueryStringPropertyNameFor(string $property): string
    {
        return $property;
    }

    protected function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return 10;
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50];
    }
}
