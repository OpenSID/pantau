@extends('layouts.index')

@section('title', 'Daftar Kabupaten')

@section('content_header')
    <h1>Kabupaten<small class="font-weight-light ml-1 text-md">Daftar Kabupaten</small></h1>
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
                                    <th width="10%" nowrap>Kode Kabupaten</th>
                                    <th>Nama Kabupaten</th>
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
                ajax: "{{ route('kabupaten.datatables') }}",
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
