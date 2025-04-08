@extends('layouts.backend')

@section('content')
    <div>
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $judul }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">{{ $judul }}</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content pl-2 pr-2">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $judul }}</h3>

                        <div class="card-tools"></div>
                    </div>
                    <div class="card-body">
                        <fieldset class="border p-2 mb-3 shadow-sm">
                            <legend class="float-none w-auto p-2">Filter Data</legend>
                            <form class="ml-2">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="jalur" class="form-label">Jalur Masuk</label>
                                        <select name="jalur" class="form-control" id="jalur" name="jalur">
                                            <option value="">-- Semua Jalur --</option>
                                            @foreach ($referensi as $ref)
                                                <option value="{{ encode_arr(['jalur' => $ref->referensi]) }}"
                                                    {{ data_params(old('jalur'), 'jalur') == $ref->referensi ? 'selected' : '' }}>
                                                    {{ $ref->referensi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="fakultas" class="form-label">Fakultas</label>
                                        <select class="form-control" id="fakultas_id" name="fakultas_id">
                                            <option value="">-- Semua --</option>
                                            @foreach ($fakultas as $row)
                                                <option value="{{ $row->id }}">
                                                    {{ $row->nama_fakultas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <label for="prodi" class="form-label">Program Studi</label>
                                        <select class="form-control" id="prodi_id" name="prodi_id">
                                            <option value="all">-- Semua --</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </fieldset>

                        <div class="table-responsive p-0 mb-2">
                            <table class="table table-condensed table-bordered" style="width: 100%"
                                id="{{ $datatable2['id_table'] }}">
                                <thead>
                                    <tr>
                                        <th class="text-left" style="width:5%">#</th>
                                        <th class="text-left">No. Peserta</th>
                                        <th class="text-left">Nama Lengkap</th>
                                        <th class="text-left">Jalur</th>
                                        <th class="text-left">Program Studi</th>
                                        <th class="text-left">Keterangan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

    @push('style')
        <style>
            table.dataTable {
                width: 100% !important;
                table-layout: auto;
                /* Membuat lebar tabel fleksibel */
            }

            table.dataTable th,
            table.dataTable td {
                white-space: nowrap;
                /* Mencegah teks memanjang ke bawah */
            }
        </style>
    @endpush

    @push('script')
        <script type="text/javascript">
            $(function() {
                var table = $("#{{ $datatable2['id_table'] }}").DataTable({
                    autoWidth: true,
                    scrollX: true, // Aktifkan horizontal scroll jika tabel terlalu lebar
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ $datatable2['url'] }}",
                        data: function(d) {
                            d.jalur = $('#jalur').val(),
                                d.fakultas_id = $('#fakultas_id').val(),
                                d.prodi_id = $('#prodi_id').val()
                        }
                    },
                    columns: [
                        @foreach ($datatable2['columns'] as $row)
                            {
                                data: "{{ $row['data'] }}",
                                name: "{{ $row['name'] }}",
                                orderable: {{ $row['orderable'] }},
                                searchable: {{ $row['searchable'] }}
                            },
                        @endforeach
                    ]
                });

                $('#jalur').change(function() {
                    table.ajax.reload();
                });

                $('#fakultas_id').change(function() {
                    getprodi($(this).val());
                    $('#prodi_id').val(null);
                    table.ajax.reload();
                });

                $('#prodi_id').change(function() {
                    table.ajax.reload();
                });
            });

            function getprodi(fakultas_id = '') {
                $.ajax({
                    url: "{{ route('admin.prodi.byfakultas') }}",
                    type: "GET",
                    data: {
                        fakultas_id: fakultas_id
                    },
                    success: function(response) {
                        var list_option = '';
                        // Kosongkan dulu select agar tidak ada duplikasi
                        $('#prodi_id').empty();
                        $('#prodi_id').append('<option value="all">-- Semua Program Studi --</option>');
                        $('#prodi_id').append('');
                        $.each(response, function(index, prodi) {
                            list_option += '<option value="' + prodi.id + '">' +
                                prodi.nama_prodi +
                                ' (' + prodi.jenjang_prodi + ')' +
                                '</option>';
                        });

                        $('#prodi_id').append(list_option);
                        // console.log(list_option);
                    }
                });
            }
        </script>
    @endpush
@endsection
