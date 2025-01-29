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
        ]],
    'daterangepicker_range'=>[
        'ranges' => [
            'Hari Ini'=> [Carbon::now()->format('Y-m-d'), Carbon::now()->format('Y-m-d')],
            'kemarin'=> [Carbon::now()->subDay()->format('Y-m-d'), Carbon::now()->subDay()->format('Y-m-d')],
            '7 Hari Terakhir'=> [Carbon::now()->subDays(6)->format('Y-m-d'), Carbon::now()->format('Y-m-d')],
            '30 Hari Terakhir'=> [Carbon::now()->subDays(29)->format('Y-m-d'), Carbon::now()->format('Y-m-d')],
            'Bulan Ini'=> [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')],
            'Bulan Lalu'=> [Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d'), Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d')],
            'Tahun Ini'=> [Carbon::now()->startOfYear()->format('Y-m-d'), Carbon::now()->endOfYear()->format('Y-m-d')],
            'Semua Tanggal'=> ['1970-01-01', Carbon::now()->format('Y-m-d')]
        ],
    ]    
];
