<x-filament-panels::page>
    <div class="space-y-6">
        @foreach($this->getActivities() as $activityItem)
            <div @class([
                'p-2 space-y-2 bg-white rounded-xl shadow',
                'dark:border-gray-600 dark:bg-gray-800' => config('filament.dark_mode'),
            ])>
                <div class="px-4 py-2">
                    <div class="flex justify-between">
                        <div class="flex gap-4 items-center">
                            @if ($activityItem->causer)
                                <x-filament-panels::avatar.user :user="$activityItem->causer" class="!w-7 !h-7"/>
                                <span class="font-bold">{{ $activityItem->causer?->name }}</span>
                            @endif
                        </div>
                        <div class="flex flex-col gap-0.5 text-xs text-gray-500">
                            <span>@lang('filament-activity-log::activities.events.' . $activityItem->event)</span>
                            <span>{{ $activityItem->created_at->format(__('filament-activity-log::activities.default_datetime_format')) }}</span>
                        </div>
                    </div>
                </div>

                <hr />

                <x-filament-tables::table class="w-full overflow-hidden text-sm">
                    <x-slot:header>
                        <x-filament-tables::header-cell>
                            @lang('filament-activity-log::activities.table.field')
                        </x-filament-tables::header-cell>
                        <x-filament-tables::header-cell>
                            @lang('filament-activity-log::activities.table.old')
                        </x-filament-tables::header-cell>
                        <x-filament-tables::header-cell>
                            @lang('filament-activity-log::activities.table.new')
                        </x-filament-tables::header-cell>
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
                            <x-filament-tables::row @class(['bg-gray-100/30' => $loop->even])>
                                <x-filament-tables::cell width="20%" class="px-4 py-2 align-top">
                                    {{ $this->getFieldLabel($field) }}
                                </x-filament-tables::cell>
                                <x-filament-tables::cell width="40%" class="px-4 py-2 align-top break-all !whitespace-normal">
                                    @if(is_array($oldValue))
                                        <pre class="text-xs text-gray-500">{{ json_encode($oldValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    @else
                                        {{ $oldValue }}
                                    @endif
                                </x-filament-tables::cell>
                                <x-filament-tables::cell width="40%" class="px-4 py-2 align-top break-all !whitespace-normal">
                                    @if(is_array($newValue))
                                        <pre class="text-xs text-gray-500">{{ json_encode($newValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    @else
                                        {{ $newValue }}
                                    @endif
                                </x-filament-tables::cell>
                            </x-filament-tables::row>
                        @endforeach
                </x-filament-tables::table>
            </div>
        @endforeach

            <x-filament::pagination
                    :page-options="$this->getTableRecordsPerPageSelectOptions()"
                    :paginator="$this->getActivities()"
                    class="px-3 py-3 sm:px-6"
            />
    </div>
</x-filament-panels::page>
