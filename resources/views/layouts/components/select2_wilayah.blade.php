@push('js')
    <script>
        $('#provinsi').select2({
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

                    // console.log(response.data);
                    // getWilayah(response.data.kode_prov, kode_kab, kode_kec, status);

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

        $('#provinsi').on('select2:select', function(e) {
            $('#kabupaten').attr('disabled', false);
            $('#kecamatan').attr('disabled', true);
            $('#kabupaten').val('').trigger('change');

            $('#kabupaten').select2({
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

        $('#kabupaten').on('select2:select', function(e) {
            $('#kecamatan').attr('disabled', false);
            $('#kecamatan').val('').trigger('change');
            $('#kecamatan').select2({
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

        $('#kecamatan').on('select2:select', function(e) {
            $('#desa').select2({
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
    </script>
@endpush()
