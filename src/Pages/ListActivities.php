<?php

namespace pxlrbt\FilamentActivityLog\Pages;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Form;
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
    use HasRecordBreadcrumb;
    use InteractsWithRecord;

    protected static string $view = 'filament-activity-log::pages.list-activities';

    protected static Collection $fieldLabelMap;

    public function mount($record)
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getTitle(): string
    {
        return __('filament-activity-log::activities.title', ['record' => $this->getRecordTitle()]);
    }

    public function getActivities()
    {
        return $this->paginateTableQuery(
            $this->record->activities()->latest()->getQuery()
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

        $components = collect($form->getSchema());
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
