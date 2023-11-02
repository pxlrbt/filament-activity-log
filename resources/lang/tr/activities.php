<?php

return [
    'breadcrumb' => 'Geçmiş',

    'title' => ':record Geçmişi',

    'default_datetime_format' => 'd.m.Y, H:i:s',

    'table' => [
        'field' => 'Alan',
        'old' => 'Eski Değer',
        'new' => 'Yeni Değer',
        'restore' => 'Geri Yükle',
    ],

    'events' => [
        'updated' => 'Güncellendi',
        'created' => 'Oluşturuldu',
        'deleted' => 'Silindi',
        'restored' => 'Geri Yüklendi',
        'restore_successful' => 'Başarıyla Geri Yüklendi',
        'restore_failed' => 'Geri Yükleme Başarısız',
    ],
];
