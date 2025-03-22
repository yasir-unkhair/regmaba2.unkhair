<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nomor Peserta</th>
            <th>Nama Peserta</th>
            <th>JK</th>
            <th>Tahun Lulus</th>
            <th>Fakultas</th>
            <th>Program Studi</th>
            <th>Jalur</th>
            <th>Tahun Penerimaan</th>
            <th>Verifikator</th>
            <th>UKT</th>
            <th>NPM</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($results as $row)
            <tr>
                <td style="text-align: left;">{{ $loop->iteration }}</td>
                <td style="text-align: left;">{{ $row->nomor_peserta }}</td>
                <td>{{ $row->nama_peserta }}</td>
                <td>{{ $row->jk ?? '-' }}</td>
                <td style="text-align: left;">{{ $row->thn_lulus ?? '-' }}</td>
                <td>{{ $row->fakultas->nama_fakultas ?? '-' }}</td>
                <td>{{ $row->prodi?->jenjang_prodi }} - {{ $row->prodi?->nama_prodi }}</td>
                <td>{{ $row->jalur }}</td>
                <td style="text-align: left;">{{ $row->setup->tahun ?? '-' }}</td>
                <td>{{ $row->verifikasiberkas->verifikator->name ?? '-' }}</td>
                <td>{{ $row->verifikasiberkas->vonis_ukt ?? '-' }}</td>
                <td style="text-align: left;">{{ $row->npm ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
