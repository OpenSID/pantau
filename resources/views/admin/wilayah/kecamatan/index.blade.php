@extends('layouts.index')

@section('title', 'Daftar Kecamatan')

@section('content_header')
    <h1>Kecamatan<small class="font-weight-light ml-1 text-md">Daftar Kecamatan</small></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                            <thead>
                                <tr>
                                    <th width="10%" nowrap>Kode Kecamatan</th>
                                    <th>Nama Kecamatan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(function() {
            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('kecamatan.datatables') }}",
                columns: [{
                        data: 'region_code',
                        name: 'region_code'
                    },
                    {
                        data: 'region_name',
                        name: 'region_name'
                    },
                ]
            });
        });
    </script>
@endsection
