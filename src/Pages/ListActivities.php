<?php

namespace pxlrbt\FilamentActivityLog\Pages;

use Exception;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables\Enums\PaginationMode;
use Illuminate\Support\Collection;
use Livewire\WithPagination;
use pxlrbt\FilamentActivityLog\Pages\Concerns\CanPaginate;

abstract class ListActivities extends Page implements HasForms
{
    use CanPaginate;
    use WithPagination {
        WithPagination::resetPage as resetLivewirePage;
    }
    use InteractsWithFormActions;
    use InteractsWithRecord;

    protected string $view = 'filament-activity-log::pages.list-activities';

    protected static Collection $fieldLabelMap;

    public function mount($record)
    {
        $this->record = $this->resolveRecord($record);
        $this->recordsPerPage = $this->getDefaultRecordsPerPageSelectOption();
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
        return $this->paginateQuery(
            $this->record->activities()->with('causer')->latest()->getQuery()
        );
    }

    public function getPaginationMode(): PaginationMode
    {
        return PaginationMode::Default;
    }

    public function getFieldLabel(string $name): string
    {
        static::$fieldLabelMap ??= $this->createFieldLabelMap();

        return static::$fieldLabelMap[$name] ?? $name;
    }

    protected function createFieldLabelMap(): Collection
    {
        $schema = static::getResource()::form(new Schema($this));

        $components = collect($schema->getComponents());
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

    public function canRestoreActivity(): bool
    {
        return static::getResource()::canRestore($this->record);
    }

    public function restoreActivity(int|string $key)
    {
        if (! $this->canRestoreActivity()) {
            abort(403);
        }

        $activity = $this->record->activities()
            ->whereKey($key)
            ->first();

        $oldProperties = data_get($activity, 'properties.old');

        if ($oldProperties === null) {
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
}
