@php
    use \Illuminate\Support\Js;
@endphp
<x-filament-panels::page>
    <div class="space-y-6">
        @foreach($this->getActivities() as $activityItem)

            @php
                /* @var \Spatie\Activitylog\Models\Activity $activityItem */
                $changes = $activityItem->getChangesAttribute();
            @endphp

            <div @class([
                'p-2 space-y-2 bg-white rounded-xl shadow',
                'dark:border-gray-600 dark:bg-gray-800',
            ])>
                <div class="p-2">
                    <div class="flex justify-between">
                        <div class="flex items-center gap-4">
                            @if ($activityItem->causer)
                                <x-filament-panels::avatar.user :user="$activityItem->causer" class="!w-7 !h-7"/>
                            @endif
                            <div class="flex flex-col text-start">
                                <span class="font-bold">{{ $activityItem->causer?->name }}</span>
                                <span class="text-xs text-gray-500">
                                    {{ __('filament-activity-log::activities.events.' . $activityItem->event) }} {{ $activityItem->created_at->format(__('filament-activity-log::activities.default_datetime_format')) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col text-xs text-gray-500 justify-end">
                            @if ($this->canRestoreActivity() && $changes->isNotEmpty())
                                <x-filament::button
                                    tag="button"
                                    icon="heroicon-o-arrow-path-rounded-square"
                                    labeled-from="sm"
                                    color="gray"
                                    class="right"
                                    wire:click="restoreActivity({{ Js::from($activityItem->getKey()) }})"
                                >
                                    @lang('filament-activity-log::activities.table.restore')
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($changes->isNotEmpty())
                    <table class="fi-ta-table w-full overflow-hidden text-sm">
                        <thead>
                            <th class="fi-ta-header-cell">
                                {{ __('filament-activity-log::activities.table.field') }}
                            </th>
                            <th class="fi-ta-header-cell">
                                {{ __('filament-activity-log::activities.table.old') }}
                            </th>
                            <th class="fi-ta-header-cell">
                                {{ __('filament-activity-log::activities.table.new') }}
                            </th>
                        </thead>

                        <tbody>
                            @foreach (data_get($changes, 'attributes', []) as $field => $change)
                                @php
                                    $oldValue = isset($changes['old'][$field]) ? $changes['old'][$field] : '';
                                    $newValue = isset($changes['attributes'][$field]) ? $changes['attributes'][$field] : '';
                                @endphp
                            <tr
                                @class([
                                    'fi-ta-row',
                                    'bg-gray-100/30' => $loop->even
                                ])
                            >
                                <td class="fi-ta-cell px-4 py-2 align-top sm:first-of-type:ps-6 sm:last-of-type:pe-6" width="20%">
                                    {{ $this->getFieldLabel($field) }}
                                </td>
                                <td width="40%" class="fi-ta-cell px-4 py-2 align-top break-all whitespace-normal">
                                    @if(is_array($oldValue))
                                        <pre class="text-xs text-gray-500">{{ json_encode($oldValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    @else
                                        {{ $oldValue }}
                                    @endif
                                </td>
                                <td width="40%" class="fi-ta-cell px-4 py-2 align-top break-all whitespace-normal">
                                    @if (is_bool($newValue))
                                        <span class="text-xs text-gray-500">{{ $newValue ? 'true' : 'false' }}</span>
                                    @elseif(is_array($newValue))
                                        <pre class="text-xs text-gray-500">{{ json_encode($newValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    @else
                                        {{ $newValue }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endforeach

        <x-filament::pagination
            currentPageOptionProperty="recordsPerPage"
            :page-options="$this->getRecordsPerPageSelectOptions()"
            :paginator="$this->getActivities()"
        />
    </div>
</x-filament-panels::page>
