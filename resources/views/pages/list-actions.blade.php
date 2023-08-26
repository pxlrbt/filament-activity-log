<x-filament::page>
    <div class="space-y-6">
        @foreach($this->getActivities() as $activityItem)
            <div @class([
                'p-2 space-y-2 bg-white rounded-xl shadow',
                'dark:border-gray-600 dark:bg-gray-800' => config('filament.dark_mode'),
            ])>
                <div class="px-4 py-2">
                    <div class="flex justify-between">
                        <div class="flex gap-4 items-center">
                            @php
                                $resource = \Filament\Facades\Filament::getModelResource($activityItem->subject_type);
                            @endphp

                            {{ $resource::getModelLabel() }}:

                            @if ($resource::hasRecordTitle())
                                {{ $resource::getRecordTitle($activityItem->subject) }}
                            @else
                                {{ $activityItem->subject_id }}
                            @endif
                        </div>
                        <div class="flex flex-col gap-0.5 text-xs text-gray-500">
                            <span>@lang('filament-activity-log::activities.events.' . $activityItem->event)</span>
                            <span>{{ $activityItem->created_at->format(__('filament-activity-log::activities.default_datetime_format')) }}</span>
                        </div>
                    </div>
                </div>

                <x-filament::hr />

                <x-tables::table class="w-full overflow-hidden text-sm">
                    <x-slot:header>
                        <x-tables::header-cell>
                            @lang('filament-activity-log::activities.table.field')
                        </x-tables::header-cell>
                        <x-tables::header-cell>
                            @lang('filament-activity-log::activities.table.old')
                        </x-tables::header-cell>
                        <x-tables::header-cell>
                            @lang('filament-activity-log::activities.table.new')
                        </x-tables::header-cell>
                    </x-slot:header>
                        @php
                            /* @var \Spatie\Activitylog\Models\Activity $activityItem */
                            $changes = $activityItem->getChangesAttribute();
                        @endphp
                        @foreach(data_get($changes, 'attributes', []) as $field => $change)
                            @php
                                $oldValue = data_get($changes, "old.{$field}");
                                $newValue = data_get($changes, "attributes.{$field}");
                            @endphp
                            <x-tables::row @class(['bg-gray-100/30' => $loop->even])>
                                <x-tables::cell width="20%" class="px-4 py-2 align-top">
                                    {{ $this->getFieldLabel($field) }}
                                </x-tables::cell>
                                <x-tables::cell width="40%" class="px-4 py-2 align-top break-all !whitespace-normal">
                                    @if(is_array($oldValue))
                                        <pre class="text-xs text-gray-500">{{ json_encode($oldValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    @else
                                        {{ $oldValue }}
                                    @endif
                                </x-tables::cell>
                                <x-tables::cell width="40%" class="px-4 py-2 align-top break-all !whitespace-normal">
                                    @if(is_array($newValue))
                                        <pre class="text-xs text-gray-500">{{ json_encode($newValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    @else
                                        {{ $newValue }}
                                    @endif
                                </x-tables::cell>
                            </x-tables::row>
                        @endforeach
                </x-tables::table>
            </div>
        @endforeach

        <x-tables::pagination
            :paginator="$this->getActivities()"
            :records-per-page-select-options="$this->getTableRecordsPerPageSelectOptions()"
        />
    </div>
</x-filament::page>
