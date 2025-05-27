<?php

namespace App\Jobs;

use App\Libraries\AppSupport;
use App\Models\Pesertaukt;
use App\Models\ProsesData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateNPM implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $peserta_id;
    public function __construct($peserta_id)
    {
        $this->peserta_id = $peserta_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $peserta = Pesertaukt::with(['setup', 'prodi'])->where('id', $this->peserta_id)->first();
        $tahun = $peserta->setup->tahun ?? date('Y');
        $kode_prefix = $peserta->prodi->prefix_npm;

        if (empty(trim($peserta->npm))) { // update npm
            $support = new AppSupport();
            $npm = $support->GenerateNpm($peserta->setup_id, $tahun, $kode_prefix);

            // ambil tahun pembayaran di ebilling
            $setup = json_decode(getdata_ebilling(env('URL_EBILLING') . '/api/tahun-pembayaran'), TRUE);

            // send npm ke ebilling
            $ebilling = [
                "detail" => [
                    "npm" => $npm,
                    "tgl_generate" => now()
                ],
                "npm" => $peserta->nomor_peserta,
                "tahun_akademik" => $setup['data']['tahun_akademik'],
                "jenis_bayar" => "umb"
            ];
            $res = json_decode(patchdata_ebilling(env('URL_EBILLING') . '/api/billing-mahasiswa/update-detail', $ebilling), true);

            $rsp_ebilling = [
                'response' => $res['response'],
                'message' => $res['message']
            ];

            if ($res['response']) {
                $rsp_ebilling += $ebilling['detail'];
            }

            $peserta->update([
                'npm' => $npm,
                'tgl_generate' => now(),
                'rsp_ebilling' => json_encode($rsp_ebilling)
            ]);
        }

        // hapus all notif generate npm
        ProsesData::where([
            'source' => $this->peserta_id,
            'queue' => 'generate-npm'
        ])->delete();
    }
}
