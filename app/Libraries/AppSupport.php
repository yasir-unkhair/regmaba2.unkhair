<?php

namespace App\Libraries;

use App\Jobs\GenerateNPM;
use App\Models\PesertauktPembayaran;
use App\Models\ProsesData;
use Illuminate\Support\Facades\DB;

class AppSupport
{
    private $message;
    public function GenerateNpm($setup_id, $tahun, $prefix_npm)
    {
        $angkatan = substr($tahun, -2, 2);
        $statis = '11';
        $kdd = $prefix_npm . $angkatan . $statis;
        $nom = DB::table('app_peserta')->select(['npm'])
            ->where('setup_id', $setup_id)
            ->whereRaw('SUBSTRING(npm,1,8)=?', [$kdd])
            ->orderBy('npm', 'DESC')
            ->limit(1)->first();

        $npm = '';
        if (!$nom) {
            $npm = $prefix_npm . $angkatan . $statis . '001';
        } else {
            $noakhir = substr($nom->npm, 8, 3) + 1;
            $jno = strlen($noakhir);
            if ($jno == 1) {
                $jum = '00' . $noakhir;
            } elseif ($jno == 2) {
                $jum = '0' . $noakhir;
            } elseif ($jno == 3) {
                $jum = $noakhir;
            }
            $npm = $prefix_npm . $angkatan . $statis . $jum;
        }

        return $npm;
    }

    public function CekPembayaran($peserta_id = NULL)
    {
        $pembyaran = PesertauktPembayaran::with('peserta')->where('peserta_id', $peserta_id)->get();
        if ($pembyaran->count() > 0) {
            foreach ($pembyaran as $row) {
                if (!$row->lunas && $row->trx_id != NULL) {
                    // cek va di ecoll
                    $rsp = json_decode(get_data(env('URL_ECOLL') . '/cekva.php?trx_id=' . $row->trx_id), TRUE);
                    if ($rsp && $rsp['response'] == true) {
                        // update set lunas pembayaran
                        $row->update([
                            'lunas' => 1,
                            'tgl_pelunasan' => now()
                        ]);

                        if ($row->jenis_pembayaran == 'ukt') {
                            // update notif lunas ukt di verifikasi_peserta
                            $row->peserta->verifikasiberkas->update([
                                'bayar_ukt' => 1
                            ]);

                            if (empty(trim($row->peserta?->npm))) {
                                if ($row->peserta?->id) {
                                    //set notif
                                    ProsesData::updateOrCreate([
                                        'source' => $row->peserta->id,
                                        'queue' => 'generate-npm'
                                    ], [
                                        'source' => $row->peserta->id,
                                        'queue' => 'generate-npm'
                                    ]);

                                    // generate npm
                                    dispatch(new GenerateNPM($row->peserta->id));
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
