<?php

/*
|--------------------------------------------------------------------------
| PITLABS — konfigurasi frontend (Fase 2)
|--------------------------------------------------------------------------
| Satu sumber kebenaran untuk identitas, navigasi, dan label status.
| Label status mengikuti BUSINESS_RULES.md: status order, pembayaran,
| pengambilan, dan penyewaan selalu terpisah.
| Nilai null / "pending" = data belum diputuskan mitra (lihat README.md pack).
*/

return [

    'name'        => 'PITLABS',
    'tagline'     => 'Your Premium IoT & AI Technology Hub',
    'description' => 'Sewa perangkat AI, IoT, dan teknologi untuk proyek, riset, dan pembelajaran. Ambil langsung di lokasi, pantau masa sewa dari dashboard.',
    'company'     => 'PT. Pulau Inovasi Teknologi',

    // Dipakai HANYA oleh AdminSeeder manual (php artisan db:seed --class=AdminSeeder). Nilai dari .env.
    'admin_seed'  => [
        'name'     => env('ADMIN_NAME', 'Admin PITLABS'),
        'email'    => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    // true selama login/registrasi/ketersediaan masih simulasi (Fase 4). Set false di Fase 5.
    'demo_mode'   => true,

    // Data yang masih menunggu konfirmasi mitra → tampil sebagai "Informasi belum tersedia".
    'contact' => [
        'address' => null,
        'email'   => null,
        'phone'   => null,
        'shopee'  => null,
    ],

    /*
    | Navigasi utama. 'route' = nama route Laravel. Jika route belum dibuat
    | (halaman dikerjakan di fase berikutnya), tautan tampil nonaktif dengan
    | keterangan, bukan tautan mati yang diam saja.
    */
    'nav' => [
        ['label' => 'Beranda',            'route' => 'home'],
        ['label' => 'Katalog',            'route' => 'catalog.index'],
        ['label' => 'Cek Ketersediaan',   'route' => 'availability'],
        ['label' => 'Sewa AI',            'route' => 'ai', 'soon' => true],
        ['label' => 'Bantuan', 'children' => [
            ['label' => 'Panduan',      'route' => 'guide'],
            ['label' => 'Artikel',      'route' => 'articles.index'],
            ['label' => 'FAQ',          'route' => 'faq'],
            ['label' => 'Tentang Kami', 'route' => 'about'],
        ]],
    ],

    /*
    | Navigasi dashboard. Grup => item [label, route, ikon].
    */
    'dash_nav' => [
        'admin' => [
            'Ringkasan' => [
                ['Dashboard',               'admin.dashboard',     'bi-grid-1x2'],
            ],
            'Transaksi' => [
                ['New Orders',              'admin.orders.new',    'bi-bag-plus', 'new_orders'],
                ['Ongoing Rentals',         'admin.rentals.ongoing','bi-arrow-repeat'],
                ['Verifikasi pembayaran',   'admin.payments',      'bi-cash-coin', 'unpaid'],
            ],
            'Operasional' => [
                ['QR Scanner',              'admin.scanner',       'bi-qr-code-scan'],
                ['Verifikasi pengambilan',  'admin.pickups',       'bi-box-arrow-up', 'paid_not_picked'],
                ['Pengembalian',            'admin.returns',       'bi-box-arrow-in-down'],
                ['Pemeriksaan kondisi',     'admin.inspections',   'bi-clipboard2-check'],
                ['Pengelolaan denda',       'admin.fines',         'bi-exclamation-diamond'],
            ],
            'Katalog' => [
                ['Produk',                  'admin.products',      'bi-boxes'],
                ['Inventaris',              'admin.inventory',     'bi-hdd-stack'],
            ],
            'Pelanggan' => [
                ['Pelanggan',               'admin.customers',     'bi-people'],
                ['Antrean rental',          'admin.waitlist',      'bi-hourglass-split'],
            ],
            'Laporan' => [
                ['Laporan keuangan',        'admin.reports',       'bi-graph-up'],
            ],
        ],
        'user' => [
            'Akun saya' => [
                ['Ringkasan',               'dashboard',           'bi-grid-1x2'],
                ['Penyewaan aktif',         'dashboard.rentals',   'bi-play-circle'],
                ['Riwayat transaksi',       'dashboard.history',   'bi-receipt'],
                ['Antrean rental',          'dashboard.waitlist',  'bi-hourglass-split'],
                ['Informasi pengembalian',  'dashboard.returns',   'bi-box-arrow-in-down'],
                ['Profil',                  'dashboard.profile',   'bi-person'],
            ],
        ],
    ],

    /*
    | Label status. Format: grup => [KODE => [label, varian warna, ikon]].
    | Varian: success | warning | danger | info | progress | neutral.
    | Badge selalu menampilkan teks + ikon, tidak mengandalkan warna saja.
    */
    'status_groups' => [
        'order'   => 'Pesanan',
        'payment' => 'Pembayaran',
        'pickup'  => 'Pengambilan',
        'rental'  => 'Penyewaan',
        'unit'    => 'Status unit',
        'availability' => 'Ketersediaan',
        'waitlist' => 'Antrean',
        'account' => 'Akun',
        'operational' => 'Status operasional',
        'condition' => 'Kondisi unit',
    ],

    'status' => [

        'order' => [
            'NEW_ORDER'        => ['label' => 'Pesanan baru',         'variant' => 'info',     'icon' => 'bi-bag-plus'],
            'WAITING_PAYMENT'  => ['label' => 'Menunggu pembayaran',  'variant' => 'warning',  'icon' => 'bi-hourglass-split'],
            'PAID'             => ['label' => 'Lunas',                'variant' => 'success',  'icon' => 'bi-check-circle-fill'],
            'PROCESSING'       => ['label' => 'Diproses',             'variant' => 'progress', 'icon' => 'bi-gear'],
            'READY_FOR_PICKUP' => ['label' => 'Siap diambil',         'variant' => 'info',     'icon' => 'bi-box-seam'],
            'CANCELLED'        => ['label' => 'Dibatalkan',           'variant' => 'neutral',  'icon' => 'bi-x-circle'],
            'COMPLETED'        => ['label' => 'Selesai',              'variant' => 'success',  'icon' => 'bi-check2-all'],
        ],

        'payment' => [
            'UNPAID'   => ['label' => 'Belum dibayar',  'variant' => 'warning', 'icon' => 'bi-clock'],
            'PAID'     => ['label' => 'Lunas',          'variant' => 'success', 'icon' => 'bi-check-circle-fill'],
            'FAILED'   => ['label' => 'Gagal',          'variant' => 'danger',  'icon' => 'bi-x-octagon-fill'],
            'REFUNDED' => ['label' => 'Dikembalikan',   'variant' => 'neutral', 'icon' => 'bi-arrow-counterclockwise'],
        ],

        'pickup' => [
            'NOT_PICKED_UP' => ['label' => 'Belum diambil', 'variant' => 'neutral', 'icon' => 'bi-box'],
            'PICKED_UP'     => ['label' => 'Sudah diambil', 'variant' => 'success', 'icon' => 'bi-box-arrow-up'],
        ],

        'unit' => [
            'AVAILABLE' => ['label' => 'Tersedia', 'variant' => 'success',  'icon' => 'bi-check-circle'],
            'RESERVED'  => ['label' => 'Dipesan',  'variant' => 'info',     'icon' => 'bi-calendar-check'],
            'RENTED'    => ['label' => 'Disewa',   'variant' => 'progress', 'icon' => 'bi-person-check'],
        ],

        'waitlist' => [
            'WAITING'   => ['label' => 'Menunggu',    'variant' => 'warning', 'icon' => 'bi-hourglass-split'],
            'NOTIFIED'  => ['label' => 'Diberi tahu', 'variant' => 'info',    'icon' => 'bi-bell'],
            'FULFILLED' => ['label' => 'Terpenuhi',   'variant' => 'success', 'icon' => 'bi-check-circle-fill'],
            'CANCELLED' => ['label' => 'Dibatalkan',  'variant' => 'neutral', 'icon' => 'bi-x-circle'],
        ],

        'operational' => [
            'ACTIVE'      => ['label' => 'Aktif',        'variant' => 'success', 'icon' => 'bi-check-circle'],
            'MAINTENANCE' => ['label' => 'Perawatan',    'variant' => 'warning', 'icon' => 'bi-wrench'],
            'RETIRED'     => ['label' => 'Dipensiunkan', 'variant' => 'neutral', 'icon' => 'bi-archive'],
        ],

        'condition' => [
            'GOOD'        => ['label' => 'Baik',           'variant' => 'success', 'icon' => 'bi-check-circle'],
            'MINOR_ISSUE' => ['label' => 'Masalah ringan', 'variant' => 'warning', 'icon' => 'bi-exclamation-circle'],
            'MAJOR_ISSUE' => ['label' => 'Masalah berat',  'variant' => 'danger',  'icon' => 'bi-exclamation-triangle'],
            'DAMAGED'     => ['label' => 'Rusak',          'variant' => 'danger',  'icon' => 'bi-x-octagon'],
            'LOST'        => ['label' => 'Hilang',         'variant' => 'neutral', 'icon' => 'bi-question-circle'],
        ],

        'account' => [
            'ACTIVE'   => ['label' => 'Aktif',    'variant' => 'success', 'icon' => 'bi-check-circle'],
            'INACTIVE' => ['label' => 'Nonaktif', 'variant' => 'neutral', 'icon' => 'bi-slash-circle'],
        ],

        'availability' => [
            'FREE'     => ['label' => 'Tersedia',       'variant' => 'success', 'icon' => 'bi-check-circle-fill'],
            'CONFLICT' => ['label' => 'Bentrok jadwal', 'variant' => 'danger',  'icon' => 'bi-calendar-x'],
        ],

        'rental' => [
            'NOT_STARTED'      => ['label' => 'Belum dimulai',       'variant' => 'neutral',  'icon' => 'bi-calendar-event'],
            'RENTING'          => ['label' => 'Sedang disewa',       'variant' => 'info',     'icon' => 'bi-play-circle'],
            'DUE_SOON'         => ['label' => 'Mendekati batas',     'variant' => 'warning',  'icon' => 'bi-alarm'],
            'OVERDUE'          => ['label' => 'Terlambat',           'variant' => 'danger',   'icon' => 'bi-exclamation-octagon-fill'],
            'RETURNED'         => ['label' => 'Sudah dikembalikan',  'variant' => 'progress', 'icon' => 'bi-arrow-return-left'],
            'UNDER_INSPECTION' => ['label' => 'Dalam pemeriksaan',   'variant' => 'progress', 'icon' => 'bi-search'],
            'COMPLETED'        => ['label' => 'Selesai',             'variant' => 'success',  'icon' => 'bi-check2-all'],
        ],
    ],
];
