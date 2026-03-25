@push('css')
    <style>
        .summary-aktif-card {
            border: 0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .summary-aktif-card .card-header h3 {
            margin: 0;
            color: #ffffff;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .summary-aktif-subtitle {
            color: rgba(255, 255, 255, 0.92);
            font-size: 0.86rem;
        }

        .summary-aktif-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }

        .summary-aktif-item {
            border: 0;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            color: #ffffff;
        }

        .summary-theme-pink {
            background-color: #e83e8c;
        }

        .summary-theme-green {
            background-color: #28a745;
        }

        .summary-theme-orange {
            background-color: #fd7e14;
        }

        .summary-theme-red {
            background-color: #dc3545;
        }

        .summary-theme-blue {
            background-color: #007bff;
        }

        .summary-theme-cyan {
            background-color: #17a2b8;
        }

        .summary-theme-indigo {
            background-color: #6610f2;
        }

        .summary-theme-purple {
            background-color: #6f42c1;
        }

        .summary-aktif-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .summary-aktif-item-head {
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0 12px;
        }

        .summary-aktif-item .apps-icon {
            border-radius: 0;
            background: transparent !important;
            padding: 10px 0;
            margin: 0;
        }

        .summary-aktif-item .apps-name {
            font-weight: 600;
            letter-spacing: 0.2px;
            color: #ffffff;
            background: transparent !important;
            padding: 10px 10px;
            margin: 0;
        }

        .summary-aktif-item .apps-number {
            width: 100%;
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: right;
            padding: 10px 12px;
            background: transparent !important;
            margin: 0;
            border-top: 1px solid rgba(255, 255, 255, 0.35);
            display: flex;
            align-items: center;
            justify-content: space-between;
            line-height: 1;
        }

        .summary-aktif-item .apps-number::before {
            content: 'Desa Aktif';
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            opacity: 0.9;
        }

        @media (max-width: 576px) {
            .summary-aktif-item .apps-number {
                font-size: 1.2rem;
                padding: 9px 10px;
            }
        }
    </style>
@endpush

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card summary-aktif-card">
            <div class="card-header header-bg py-3">
                <h3>Pengguna Aktif Aplikasi OpenDesa</h3>
                <p class="summary-aktif-subtitle mb-0">Ringkasan jumlah desa aktif per aplikasi</p>
            </div>
            <div class="card-body">
                <div class="summary-aktif-grid">
                    <div class="summary-aktif-item summary-theme-pink">
                        <div class="summary-aktif-item-head">
                            <div class="apps-icon">
                                <i class="fas pantau-icon fa-openkab"></i>
                            </div>
                            <div class="apps-name">OpenKab</div>
                        </div>
                        <div class="apps-number" id="app-openkab-count2">0</div>
                    </div>

                    <div class="summary-aktif-item summary-theme-green">
                        <div class="summary-aktif-item-head">
                            <div class="apps-icon">
                                <i class="fas pantau-icon fa-opendk"></i>
                            </div>
                            <div class="apps-name">OpenDK</div>
                        </div>
                        <div class="apps-number" id="app-opendk-count2">0</div>
                    </div>

                    <div class="summary-aktif-item summary-theme-orange">
                        <div class="summary-aktif-item-head">
                            <div class="apps-icon">
                                <i class="fas pantau-icon fa-opensid"></i>
                            </div>
                            <div class="apps-name">OpenSID</div>
                        </div>
                        <div class="apps-number" id="app-opensid-count2">0</div>
                    </div>

                    <div class="summary-aktif-item summary-theme-red">
                        <div class="summary-aktif-item-head">
                            <div class="apps-icon">
                                <i class="fas pantau-icon fa-layanandesa"></i>
                            </div>
                            <div class="apps-name">LayananDesa</div>
                        </div>
                        <div class="apps-number" id="app-layanandesa-count2">0</div>
                    </div>

                    <div class="summary-aktif-item summary-theme-blue">
                        <div class="summary-aktif-item-head">
                            <div class="apps-icon">
                                <i class="fas pantau-icon fa-keloladesa"></i>
                            </div>
                            <div class="apps-name">KelolaDesa</div>
                        </div>
                        <div class="apps-number" id="app-keloladesa-count2">0</div>
                    </div>
                    <div class="summary-aktif-item summary-theme-cyan">
                        <div class="summary-aktif-item-head">
                            <div class="apps-icon">
                                <i class="fas pantau-icon fa-openkab"></i>
                            </div>
                            <div class="apps-name">PBB</div>
                        </div>
                        <div class="apps-number" id="app-pbb-count2">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
