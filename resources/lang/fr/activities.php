<?php

return [
    'breadcrumb' => 'Historique',

    'title' => 'Historique :record',

    'default_datetime_format' => 'd/m/Y, H:i:s',

    'table' => [
        'field' => 'Champ',
        'old' => 'Ancien',
        'new' => 'Nouveau',
        'restore' => 'Restaurer',
    ],

    'events' => [
        'updated' => 'Mis à jour',
        'created' => 'Créé',
        'deleted' => 'Effacé',
        'restored' => 'Restauré',
        'restore_successful' => 'Restauré avec succès',
        'restore_failed' => 'Échec de la restauration',
    ],
];
