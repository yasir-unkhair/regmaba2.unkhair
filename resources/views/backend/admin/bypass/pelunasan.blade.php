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
                        <form action="{{ route('admin.pelunasan.carimaba') }}" method="post">
                            @csrf
                            <div class="form-group row">
                                <label for="level" class="col-sm-2 col-form-label">Ketik No. Peserta</label>
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <input type="text" name="nomor_peserta"
                                            value="{{ old('nomor_peserta', $nomor_peserta) }}"
                                            class="form-control @error('nomor_peserta') is-invalid @enderror"
                                            placeholder="No. Peserta">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search"></i> Cari Maba
                                            </button>
                                        </div>
                                    </div>

                                    @error('nomor_peserta')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </form>

                        @if ($peserta)
                            <br>
                            <div class="table-responsive">
                                <table class="table border border-black" style="font-size:15px;">
                                    <tr>
                                        <td class="text-right bg-light" width="15%">Nomor Peserta :</td>
                                        <td width="35%">
                                            {{ $peserta->nomor_peserta }}
                                        </td>

                                        <td class="text-right bg-light" width="15%">Jalur Pendaftaran :</td>
                                        <td width="35%">
                                            {{ $peserta->jalur }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right bg-light">Nama Lengkap :</td>
                                        <td>{{ $peserta->nama_peserta }}</td>

                                        <td class="text-right bg-light">Tahun :</td>
                                        <td>{{ $peserta->setup?->tahun }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right bg-light">Fakultas :</td>
                                        <td>{{ $peserta->fakultas?->nama_fakultas }}</td>
                                        <td class="text-right bg-light">Program Studi :</td>
                                        <td>
                                            {{ $peserta->prodi?->jenjang_prodi . ' - ' . $peserta->prodi?->nama_prodi }}
                                        </td>
                                    </tr>
                                </table>

                                <h4>Daftar Pembayaran:</h4>
                                <table class="table border border-black" style="font-size:15px;">
                                    <tr>
                                        <th>No</th>
                                        <th>Pembayaran</th>
                                        <th>Bank</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>

                                    @if ($peserta?->pembayaran)
                                        @foreach ($peserta->pembayaran as $pembayaran)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{!! $pembayaran->detail_pembayaran !!}</td>
                                                <td>{{ $pembayaran->bank }}</td>
                                                <td>{{ rupiah($pembayaran->amount) }}</td>
                                                <td>{!! $pembayaran->lunas
                                                    ? '<span class="text-success">Lunas</span>'
                                                    : '<span class="text-danger">Belum Lunas</span>' !!}
                                                </td>
                                                <td>
                                                    @if (!$pembayaran->lunas)
                                                        <a href="{{ route('admin.pelunasan.actsetlunas', $pembayaran->id) }}"
                                                            onclick="return confirm('Set Lunas?')"
                                                            class="btn btn-sm btn-info">
                                                            Set Lunas
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection
