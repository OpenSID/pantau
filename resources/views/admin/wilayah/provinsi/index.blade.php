@extends('layouts.index')

@section('title', 'Daftar Provinsi')

@section('content_header')
    <h1>Provinsi<small class="font-weight-light ml-1 text-md">Daftar Provinsi</small></h1>
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
                                    <th width="10%" nowrap>Kode Provinsi</th>
                                    <th>Nama Provinsi</th>
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
                ajax: "{{ route('provinsi.datatables') }}",
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
