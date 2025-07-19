<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateNPM;
use App\Models\Pesertaukt;
use App\Models\PesertauktPembayaran;
use App\Models\ProsesData;
use Illuminate\Http\Request;

class SetPelunasan extends Controller
{
    //
    public function index()
    {
        $data = [
            'judul' => 'Bypass Pelunasan',
            'nomor_peserta' => null,
            'peserta' => null
        ];

        return view('backend.admin.bypass.pelunasan', $data);
    }

    public function carimaba(Request $request)
    {
        $request->validate(
            [
                'nomor_peserta' => 'required|exists:app_peserta,nomor_peserta'
            ],
            [
                'nomor_peserta.required' => 'Nomor Peserta jangan dikosongkan!',
                'nomor_peserta.exists' => 'Nomor Peserta tidak ditemukan!'
            ]
        );

        $peserta = Pesertaukt::with('pembayaran')->where('nomor_peserta', $request->nomor_peserta)->first();
        $data = [
            'judul' => 'Bypass Pelunasan',
            'nomor_peserta' => $request->nomor_peserta,
            'peserta' => $peserta
        ];
        return view('backend.admin.bypass.pelunasan', $data);
    }

    public function actsetlunas($id_pembayaran = NULL)
    {
        if (!$id_pembayaran) {
            abort(404, 'Halaman tidak ditemukan!');
        }

        $pembyaran = PesertauktPembayaran::with('peserta')->where('id', $id_pembayaran)->first();
        $pembyaran->update([
            'lunas' => 1,
            'tgl_pelunasan' => now()
        ]);

        if ($pembayaran->jenis_pembayaran == 'ukt' && empty(trim($pembayaran->peserta?->npm))) {
            if ($pembayaran->peserta?->id) {
                //set notif
                ProsesData::updateOrCreate([
                    'source' => $pembayaran->peserta->id,
                    'queue' => 'generate-npm'
                ], [
                    'source' => $pembayaran->peserta->id,
                    'queue' => 'generate-npm'
                ]);

                // generate npm
                dispatch(new GenerateNPM($pembayaran->peserta->id));
            }
        }


        alert()->success('Success', 'Pelunasan berhasil di bypass!');
        return redirect(route('admin.setpelunasan.index'));
    }
}
