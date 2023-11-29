<?php

return [
    'breadcrumb' => 'Riwayat',

    'title' => 'Riwayat :record',

    'default_datetime_format' => 'd/m/Y H:i:s',

    'table' => [
        'field' => 'Bagian',
        'old' => 'Sebelum',
        'new' => 'Sesudah',
        'restore' => 'Pulihkan',
    ],

    'events' => [
        'updated' => 'Terbarui',
        'created' => 'Terbuat',
        'deleted' => 'Terhapus',
        'restored' => 'Terpulihkan',
        'restore_successful' => 'Sukses memulihkan',
        'restore_failed' => 'Gagal memulihkan',
    ],
];
