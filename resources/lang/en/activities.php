<?php

return [
    'breadcrumb' => 'History',

    'title' => 'History :record',

    'default_datetime_format' => 'Y-m-d, H:i:s',

    'table' => [
        'field' => 'Field',
        'old' => 'Old',
        'new' => 'New',
        'restore' => 'Restore',
    ],

    'events' => [
        'updated' => 'Updated',
        'created' => 'Created',
        'deleted' => 'Deleted',
        'restored' => 'Restored',
        'restore_successful' => 'Restored successfully',
        'restore_failed' => 'Restore failed',
    ],
];
