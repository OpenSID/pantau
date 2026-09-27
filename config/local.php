<?php

use Carbon\Carbon;

return [
    'select2' => [
        'ajax' => ['data-ajax' => 1],
        'tag' => ['tags' => true, 'multiple' => true, 'tokenSeparators' => [',']],
    ],
    'daterangepicker' => [
        'singleDatePicker' => true,
        'locale' => [
            'format' => 'YYYY-MM-DD',
            'separator' => ' - ',
            'applyLabel' => 'Terapkan',
            'cancelLabel' => 'Batal',
            'fromLabel' => 'Dari',
            'toLabel' => 'Untuk',
            'customRangeLabel' => 'Kustom',
            'weekLabel' => 'M',
            'daysOfWeek' => [
                'Mig',
                'Sen',
                'Sel',
                'Rab',
                'Kam',
                'Jum',
                'Sab',
            ],
            'monthNames' => [
                'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember',
            ],
        ]
    ],
    // untuk menghindari cache daterangepicker_range sudah dihapus dan digantikan dengan fungsi helper daterangepicker_range() yang mengembalikan array konfigurasi untuk rentang tanggal
];
