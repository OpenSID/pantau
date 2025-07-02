@push('js')
    <script>
        $('#kode_provinsi').select2({
            ajax: {
                headers: {
                    "Authorization": `Bearer {{ config('tracksid.sandi.sanctum_token') }}`,
                },
                url: '{{ url('api/v1/wilayah/provinsi') }}',
                dataType: 'json',
                delay: 400,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page
                    };
                },
                processResults: function(response, params) {
                    params.page = params.page || 1;

                    return {
                        results: $.map(response.data, function(item) {
                            return {
                                id: item.kode_prov,
                                text: item.nama_prov,
                            }
                        }),
                        pagination: {
                            more: params.page < response.last_page
                        }
                    };
                }
            }
        });

        $('#kode_provinsi').on('select2:select', function(e) {
            $('#kode_kabupaten').attr('disabled', false);
            $('#kode_kecamatan').attr('disabled', true);
            $('#kode_kabupaten').val('').trigger('change');

            $('#kode_kabupaten').select2({
                ajax: {
                    headers: {
                        "Authorization": `Bearer {{ config('tracksid.sandi.sanctum_token') }}`,
                    },
                    url: '{{ url('api/v1/wilayah/kabupaten') }}',
                    dataType: 'json',
                    delay: 400,
                    data: function(params) {
                        return {
                            search: params.term,
                            kode_prov: e.params.data.id,
                            page: params.page
                        };
                    },
                    processResults: function(response, params) {
                        params.page = params.page || 1;

                        return {
                            results: $.map(response.data, function(item) {
                                return {
                                    id: item.kode_kab,
                                    text: item.nama_kab,
                                }
                            }),
                            pagination: {
                                more: params.page < response.last_page
                            }
                        };
                    }
                }
            });
        });

        $('#kode_kabupaten').on('select2:select', function(e) {
            $('#kode_kecamatan').attr('disabled', false);
            $('#kode_kecamatan').val('').trigger('change');
            $('#kode_kecamatan').select2({
                ajax: {
                    headers: {
                        "Authorization": `Bearer {{ config('tracksid.sandi.sanctum_token') }}`,
                    },
                    url: '{{ url('api/v1/wilayah/kecamatan') }}',
                    dataType: 'json',
                    delay: 400,
                    data: function(params) {
                        return {
                            search: params.term,
                            kode_kab: e.params.data.id,
                            page: params.page
                        };
                    },
                    processResults: function(response, params) {
                        params.page = params.page || 1;

                        return {
                            results: $.map(response.data, function(item) {
                                return {
                                    id: item.kode_kec,
                                    text: item.nama_kec,
                                }
                            }),
                            pagination: {
                                more: params.page < response.last_page
                            }
                        };
                    }
                }
            });
        });

        $('#kode_kecamatan').on('select2:select', function(e) {
            $('#kode_desa').select2({
                ajax: {
                    headers: {
                        "Authorization": `Bearer {{ config('tracksid.sandi.sanctum_token') }}`,
                    },
                    url: '{{ url('api/v1/wilayah/desa') }}',
                    dataType: 'json',
                    delay: 400,
                    data: function(params) {
                        return {
                            search: params.term,
                            kode_kec: e.params.data.id,
                            page: params.page
                        };
                    },
                    processResults: function(response, params) {
                        params.page = params.page || 1;

                        return {
                            results: $.map(response.data, function(item) {
                                return {
                                    id: item.kode_desa,
                                    text: item.nama_desa,
                                }
                            }),
                            pagination: {
                                more: params.page < response.last_page
                            }
                        };
                    }
                }
            });
        });

        $('#status').select2();
        $('#akses').select2();
        $('#tte').select2();
    </script>
@endpush()
