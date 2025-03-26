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
                                <form action="{{ route('admin.laporan.export') }}" method="POST" class="ml-2">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label for="tahun" class="form-label">Tahun Penerimaan</label>
                                            <select class="form-control" id="setup_id" name="setup_id">
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
                                            <select name="jalur" class="form-control" id="jalur" name="jalur">
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
                                            <select class="form-control" id="registrasi" name="registrasi">
                                                <option value="">-- Filter --</option>
                                                <option value="Y">Sudah Registrasi</option>
                                                <option value="N">Belum Registrasi</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="fakultas" class="form-label">Status Peserta</label>
                                            <select class="form-control" id="status_peserta" name="status_peserta">
                                                <option value="">-- Filter --</option>
                                                <option value="1">Melengkapi Formulir UKT</option>
                                                <option value="2:3:4:5">Upload Berkas Dukung</option>
                                                <option value="3:4:5">Finalisasi</option>
                                                <option value="4:5">Verifikasi Berkas</option>
                                                <option value="5">Penetapan UKT</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label for="fakultas" class="form-label">Fakultas</label>
                                            <select class="form-control" id="fakultas_id" onchange="getprodi(this.value)"
                                                name="fakultas_id">
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
                                            <select class="form-control" id="prodi_id" name="prodi_id">
                                                <option value="">-- Semua Program Studi --</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="status" class="form-label">Penetapan UKT</label>
                                            <select class="form-control" id="vonis" name="vonis">
                                                <option value="">-- Semua UKT --</option>
                                                @foreach (listRekomendasi('rekomendasi') as $row)
                                                    <option value="{{ $row }}">
                                                        {{ $row == 'wawancara' ? 'Wawancara' : strtoupper($row) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="status" class="form-label">Verifikator</label>
                                            <select class="form-control" id="verfikator_id" name="verfikator_id">
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
                                        <button type="button" class="btn btn-info mr-2" id="btn-tampilkan">
                                            <i class="fa fa-search"></i> Tampilkan Laporan
                                        </button>
                                        <button type="submit" name="btn" value="excel" id="btn-excel"
                                            class="btn btn-success mr-2">
                                            <i class="fa fa-file-excel"></i> Cetak Excel
                                        </button>
                                        <button type="submit" name="btn" value="pdf" id="btn-pdf"
                                            class="btn btn-danger">
                                            <i class="fa fa-file-pdf"></i> Cetak PDF
                                        </button>
                                    </div>
                                </form>
                            </fieldset>
                        </div>


                        <div class="table-responsive p-0 mb-2" style="font-size: 14px;">
                            <table class="table table-condensed table-bordered table-mini" style="width: 100%;"
                                id="{{ $datatable2['id_table'] }}">
                                <thead>
                                    <tr>
                                        <th class="text-left" style="width:3%">#</th>
                                        <th class="text-left" style="width:12%">No. Peserta</th>
                                        <th class="text-left">Nama Lengkap</th>
                                        <th class="text-left">Program Studi</th>
                                        <th class="text-left">Jalur</th>
                                        <th class="text-left">Tahun</th>
                                        <th class="text-left">UKT</th>
                                        <th class="text-left">Verifikator</th>
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
            var table;
            $(function() {
                table = $("#{{ $datatable2['id_table'] }}").DataTable({
                    autoWidth: true,
                    scrollX: true, // Aktifkan horizontal scroll jika tabel terlalu lebar
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ $datatable2['url'] }}",
                        data: function(d) {
                            d.setup_id = $('#setup_id').val(),
                                d.jalur = $('#jalur').val(),
                                d.registrasi = $('#registrasi').val(),
                                d.status_peserta = $('#status_peserta').val(),
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

                $('#btn-excel').attr("disabled", true);
                $('#btn-pdf').attr("disabled", true);

                $('#btn-tampilkan').click(function() {
                    table.draw();
                    $('#btn-excel').attr("disabled", false);
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
