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
                        <p>
                            <button class="btn btn-primary" type="button" data-toggle="collapse"
                                data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                <i class="fa fa-sliders"></i> Filter Laporan
                            </button>
                        </p>
                        <div class="collapse" id="collapseExample">
                            <fieldset class="border p-2 mb-3 shadow-sm">
                                <legend class="float-none w-auto p-2">Filter Data</legend>
                                <form class="ml-2">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label for="tahun" class="form-label">Tahun Penerimaan</label>
                                            <select class="form-control" id="setup_id">
                                                <option value="">-- Semua Tahun --</option>
                                                @foreach ($setup as $row)
                                                    <option value="{{ $row->id }}">
                                                        {{ $row->tahun }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="jalur" class="form-label">Jalur Masuk</label>
                                            <select name="jalur" class="form-control" id="jalur">
                                                <option value="">-- Semua Jalur --</option>
                                                @foreach ($referensi as $ref)
                                                    <option value="{{ encode_arr(['jalur' => $ref->referensi]) }}"
                                                        {{ data_params(old('jalur'), 'jalur') == $ref->referensi ? 'selected' : '' }}>
                                                        {{ $ref->referensi }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="fakultas" class="form-label">Registrasi</label>
                                            <select class="form-control" id="registrasi">
                                                <option value="">-- Filter --</option>
                                                <option value="1">Sudah Registrasi</option>
                                                <option value="0">Belum Registrasi</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="fakultas" class="form-label">Finalisasi</label>
                                            <select class="form-control" id="finalisasi">
                                                <option value="">-- Filter --</option>
                                                <option value="1">Sudah Finalisasi</option>
                                                <option value="0">Belum Finalisasi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label for="fakultas" class="form-label">Fakultas</label>
                                            <select class="form-control" id="fakultas_id" onchange="getprodi(this.value)">
                                                <option value="">-- Semua Fakultas --</option>
                                                @foreach ($fakultas as $row)
                                                    <option value="{{ $row->id }}">
                                                        {{ $row->nama_fakultas }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="prodi" class="form-label">Program Studi</label>
                                            <select class="form-control" id="prodi_id">
                                                <option value="">-- Semua Program Studi --</option>
                                                {{-- @foreach ($prodi as $row)
                                                    <option value="{{ $row->id }}">
                                                        {{ $row->nama_prodi }} ({{ $row->jenjang_prodi }})
                                                    </option>
                                                @endforeach --}}
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="status" class="form-label">Status UKT</label>
                                            <select class="form-control" id="vonis">
                                                <option value="">-- Semua Status UKT --</option>
                                                @foreach (listRekomendasi('rekomendasi') as $row)
                                                    <option value="{{ $row }}">
                                                        {{ $row == 'wawancara' ? 'Wawancara' : strtoupper($row) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="status" class="form-label">Verifikator</label>
                                            <select class="form-control" id="verfikator_id">
                                                <option value="">-- Semua Verifikator --</option>
                                                @foreach ($verfikator as $row)
                                                    <option value="{{ $row->id }}">
                                                        {{ $row->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success mr-2" id="btn-tampilkan">
                                            <i class="fa fa-search"></i> Tampilkan Laporan
                                        </button>
                                        <a href="button" class="btn btn-info mr-2 disabled">
                                            <i class="fa fa-file-excel"></i> Cetak Excel
                                        </a>
                                        <a href="" class="btn btn-danger disabled">
                                            <i class="fa fa-file-pdf"></i> Cetak PDF
                                        </a>
                                    </div>
                                </form>
                            </fieldset>
                        </div>


                        <div class="table-responsive p-0 mb-2" style="font-size: 12px">
                            <table class="table table-condensed table-bordered" style="width: 100%; font-size: 12px"
                                id="{{ $datatable2['id_table'] }}">
                                <thead>
                                    <tr>
                                        <th class="text-left" style="width:5%">Tahun</th>
                                        <th class="text-left">No. Peserta</th>
                                        <th class="text-left">Nama Lengkap</th>
                                        <th class="text-left">Program Studi</th>
                                        <th class="text-left">Jalur</th>
                                        <th class="text-left">UKT</th>
                                        <th class="text-left">Verifikator</th>
                                        <th class="text-left">Keterangan</th>
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

    @push('script')
        <script type="text/javascript">
            var table;
            $(function() {
                table = $("#{{ $datatable2['id_table'] }}").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ $datatable2['url'] }}",
                        data: function(d) {
                            d.setup_id = $('#setup_id').val(),
                                d.jalur = $('#jalur').val(),
                                d.registrasi = $('#registrasi').val(),
                                d.finalisasi = $('#finalisasi').val(),
                                d.fakultas_id = $('#fakultas_id').val(),
                                d.prodi_id = $('#prodi_id').val(),
                                d.vonis = $('#vonis').val(),
                                d.verfikator_id = $('#verfikator_id').val()
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

                $('#btn-tampilkan').click(function() {
                    table.draw();
                });

                getprodi();
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
                        $('#prodi_id').append('<option value="">-- Semua Program Studi --</option>');
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
