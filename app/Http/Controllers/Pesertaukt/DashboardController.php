<?php

namespace App\Http\Controllers\Pesertaukt;

use App\Http\Controllers\Controller;
use App\Models\Pesertaukt;
use App\Models\PesertauktDokumen;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index(Request $request)
    {
        $peserta = Pesertaukt::where('id', session('peserta_id'))->first();

        $setup = get_setup();

        $tgl_registrasi = "";
        $tgl_pengisian = "";
        $tgl_pembayaran = "";

        if ($peserta->jalur == 'SNBP') {
            $tgl_registrasi = $setup->registrasi_snbp;
            $tgl_pengisian = $setup->pengisian_snbp;
            $tgl_pembayaran = $setup->pembayaran_snbp;
        }

        if ($peserta->jalur == 'SNBT') {
            $tgl_registrasi = $setup->registrasi_snbt;
            $tgl_pengisian = $setup->pengisian_snbt;
            $tgl_pembayaran = $setup->pembayaran_snbt;
        }

        if ($peserta->jalur == 'MANDIRI') {
            $tgl_registrasi = $setup->registrasi_mandiri;
            $tgl_pengisian = $setup->pengisian_mandiri;
            $tgl_pembayaran = $setup->pembayaran_mandiri;
        }


        $data = [
            'judul' => 'Dashboard',
            'peserta' => $peserta,
            'tgl_registrasi' => $tgl_registrasi,
            'tgl_pengisian' => $tgl_pengisian,
            'tgl_pembayaran' => $tgl_pembayaran
        ];

        return view('backend.pesertaukt.dashboard', $data);
    }

    public function resume()
    {
        $peserta = auth()->user();

        $status_peserta = $peserta->status_peserta();

        // jika peserta belum di vonis, maka notif akan muncul terus.
        if ($status_peserta != 5) {
            if (!$peserta->formulirukt_selesai_input()) {
                alert()->error('Error', 'Anda belum melengkapi Formulir UKT, Silahkan lengkapi terlebih dahulu!');
                return redirect(route('peserta.dashboard'));
            }
        }


        if ($status_peserta == 1) {
            alert()->error('Error', 'Anda belum Upload Berkas Bukti Dukung, Silahkan upload terlebih dahulu!');
            return redirect(route('peserta.berkasdukung'));
        }

        if ($status_peserta == 2) {
            alert()->error('Error', 'Segera melakukan finalisasi agar data pengajuan anda segera di proses verifikasi oleh panitia.');
            return redirect(route('peserta.finalisai'));
        }

        $peserta = Pesertaukt::with(['kondisikeluarga', 'pembiayaanstudi', 'prodi'])->where('id', session('peserta_id'))->first();
        $kondisi = $peserta->kondisikeluarga;
        $biaya = $peserta->pembiayaanstudi;

        $berkasku = PesertauktDokumen::where('peserta_id', $peserta->id);
        if ($berkasku->count() > 0) {
            $berkasku = $berkasku->get()->toArray();
        }

        $dokumen = [];
        foreach (list_dokumen_upload($kondisi->keberadaan_ortu, $biaya->biaya_studi) as $row) {
            if ($row['urutan'] == 'label') {
                $dokumen[] = $row['keterangan'];
            } else {
                $collection = collect($berkasku);
                $res = $collection->firstWhere('dokumen', $row['dokumen']);
                $dokumen[] = [
                    'urutan' => $row['urutan'],
                    'dokumen' => $row['dokumen'],
                    'detail' => $row['detail'],
                    'wajib' => $row['wajib'],
                    'upload' => $res
                ];
            }
        }

        $data = [
            'judul' => 'Resume Pengajuan',
            'tab1' => $peserta,
            'tab2' => $kondisi,
            'tab3' => $biaya,
            'dokumen' => $dokumen
        ];
        // dd($data);

        return view('backend.pesertaukt.resume', $data);
    }
}
