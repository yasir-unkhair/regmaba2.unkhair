<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesertaukt;
use App\Models\PesertauktPembayaran;
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

        $pembyaran = PesertauktPembayaran::where('id', $id_pembayaran)->update([
            'lunas' => 1
        ]);

        alert()->success('Success', 'Pelunasan berhasil di bypass!');
        return redirect(route('admin.setpelunasan.index'));
    }
}
