<?php

namespace App\Livewire\Admin;

use App\Jobs\SendMail;
use App\Models\Pesertaukt;
use App\Models\PesertauktPembayaran;
use App\Models\ProdiBiayastudi;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\On;
use Livewire\Component;

use function Laravel\Prompts\error;

class Penetapanukt extends Component
{
    public $peserta_id;
    public $get;
    public $verifikator;
    public $tgl_verifikasi;
    public $catatan;
    public $rekomendasi;

    public $listdata_ukt = [];
    public $listdata_ipi = [];

    public $kategori_ukt;
    public $kategori_ipi;
    public $vonis_ipi = 1;
    public $nominal_ipi = 0;

    public function render()
    {
        // abort(505, 'Sedang perbaikan sistem!');
        return view('livewire.admin.penetapanukt');
    }

    public function save()
    {
        // Clear previous errors
        $this->resetErrorBag();

        $rules = ['kategori_ukt' => 'required'];

        $jalur = strtolower($this->get->jalur);
        if ($jalur == 'mandiri') {
            $rules += ['kategori_ipi' => 'required'];
        }

        $this->validate($rules);

        $kategori_ukt = $this->kategori_ukt;
        $nominal_ukt = 0;

        $vonis_ipi = '';
        $nominal_ipi = 0;

        $message_ukt = "";
        $message_ipi = "";

        $update_ukt = 0;

        if (!in_array($this->kategori_ukt, ['wawancara', 'kip-k'])) {
            $ukt = ProdiBiayastudi::where('id', $this->kategori_ukt)->first();
            $kategori_ukt = 'k' . $ukt->kategori;
            $nominal_ukt = (int) abs($ukt->nominal);
            if (!$nominal_ukt) {
                $this->addError("kategori_ukt", "Nomimal UKT tidak valid!");
            }

            // jika ada update nominal ukt
            $pembayaran = PesertauktPembayaran::where('peserta_id', $this->peserta_id)->where('jenis_pembayaran', 'ukt')->first();
            if ($pembayaran && ($pembayaran->amount != $nominal_ukt)) {
                if ($pembayaran->lunas) {
                    $this->addError("kategori_ukt", "Peserta sudah melakukan Pembayaran UKT, maka tidak dapat melakukan update UKT!");
                } else {
                    if ($pembayaran->trx_id) {
                        $message_ukt = "Hubungi Peserta agar melakukan cetak ulang slip pembayaran UKT";
                    }
                    // dd($message_ukt);

                    $pembayaran->update([
                        'trx_id' => NULL,
                        'va' => NULL,
                        'expired' => NULL,
                        'amount' => $nominal_ukt
                    ]);

                    $update_ukt = 1;
                }
            }
        } else {
            // hapus pembayaran ukt jika vonis = wawancara dan kip-k
            $pembayaran = PesertauktPembayaran::where('peserta_id', $this->peserta_id)->where('jenis_pembayaran', 'ukt')->first();
            if ($pembayaran) {
                $pembayaran->delete();
            }

            $update_ukt = 1;
        }

        if ($jalur == 'mandiri') {
            $ipi = ProdiBiayastudi::where('id', $this->kategori_ipi)->first();
            $vonis_ipi = 'k' . $ipi->kategori;
            $nominal_ipi = (int) abs($ipi->nominal);
            if (!$nominal_ipi) {
                $this->addError("kategori_ipi", "Nomimal IPI tidak valid!");
            }

            // jika ada update nominal ukt
            $pembayaran = PesertauktPembayaran::where('peserta_id', $this->peserta_id)->where('jenis_pembayaran', 'ipi')->first();
            // dd($pembayaran);
            if ($pembayaran && ($pembayaran->amount != $nominal_ipi)) {
                if ($pembayaran->lunas) {
                    $this->addError("kategori_ipi", "Peserta sudah melakukan Pembayaran IPI, maka tidak dapat melakukan update IPI!");
                } else {
                    if ($pembayaran->trx_id) {
                        $message_ipi = "Hubungi Peserta agar melakukan cetak ulang slip pembayaran IPI";
                    }
                    $pembayaran->update([
                        'trx_id' => NULL,
                        'va' => NULL,
                        'expired' => NULL,
                        'amount' => $nominal_ipi
                    ]);

                    $update_ukt = 1;
                }
            }
        }


        if (!$this->getErrorBag()->all()) {
            $data = [
                'vonis_ukt' => $kategori_ukt,
                'nominal_ukt' => $nominal_ukt,
                'vonis_ipi' => $vonis_ipi,
                'nominal_ipi' => $nominal_ipi,
                'tgl_vonis' => now(),
                'user_id_vonis' => auth()->user()->id
            ];

            $this->get->update(['status' => 5]);
            $this->get->verifikasiberkas()->update($data);

            // dd($this->get->email, $this->get, $data, $message_ukt, $message_ipi);

            // kirim notif email ke peserta bahwa admin telah melakukan vonis ukt
            if ($update_ukt) {
                SendMail::dispatch([
                    'email' => $this->get->email,
                    'get' => $this->get,
                    'content' => 'update-penetapanukt'
                ]);
            } else {
                SendMail::dispatch([
                    'email' => $this->get->email,
                    'get' => $this->get,
                    'content' => 'penetapanukt'
                ]);
            }
            $message_add = '';
            if ($message_ukt) {
                $message_add .= ' , ' . $message_ukt;
            }
            if ($message_ipi) {
                $message_add .= ' , ' . $message_ipi;
            }

            $this->_after_save();

            $this->dispatch('alert', type: 'success', message: 'Penetapan UKT Berhasil Disimpan ' . $message_add);
            $this->dispatch('load-datatable');
            $this->dispatch('close-modal');


            // if (!$message_add) {
            //     $this->dispatch('alert', type: 'success', message: 'Penetapan UKT Berhasil Disimpan ' . $message_add);
            //     $this->dispatch('load-datatable');
            //     $this->dispatch('close-modal');
            // } else {
            //     alert()->success('Success', 'Penetapan UKT Berhasil Disimpan ' . $message_add);
            //     return $this->redirect(route('admin.penetapanukt.index'));
            // }
        }
    }

    #[On('modal-penetapanukt')]
    public function modal_penetapanukt($params)
    {
        $params = decode_arr($params);
        $this->get = Pesertaukt::with('verifikasiberkas')->where('id', $params['peserta_id'])->first();
        $this->peserta_id = $params['peserta_id'];

        $this->listdata_ukt = ProdiBiayastudi::byprodi($this->get->prodi_id)->where('nominal', '>', 0)->jenisbiaya('ukt')->orderBy('kategori', 'ASC')->get();
        $this->listdata_ipi = ProdiBiayastudi::byprodi($this->get->prodi_id)->where('nominal', '>', 0)->where('jenis_biaya', '!=', 'ukt')->jenisbiaya('ipi')->orderBy('kategori', 'ASC')->get();

        $this->verifikator = $this->get->verifikasiberkas?->verifikator?->name;
        $this->tgl_verifikasi = $this->get->verifikasiberkas?->tgl_verifikasi;
        $this->catatan = $this->get->verifikasiberkas?->catatan;
        $this->rekomendasi = $this->get->verifikasiberkas?->rekomendasi;

        $vonis_ukt = $this->get->verifikasiberkas?->vonis_ukt;

        if ($vonis_ukt) {
            $vonis_ukt = (int) substr($vonis_ukt, 1);
            $ukt = ProdiBiayastudi::byprodi($this->get->prodi_id)->jenisbiaya('ukt')->where('kategori', $vonis_ukt)->first();
            $this->kategori_ukt = $ukt?->id;
        }

        if (strtolower($this->get->jalur) == 'mandiri') {
            $vonis_ipi = $this->get->verifikasiberkas?->vonis_ipi;
            if ($vonis_ipi) {
                $vonis_ipi = (int) substr($vonis_ipi, 1);
                // dd($vonis_ipi);
                $ipi = ProdiBiayastudi::byprodi($this->get->prodi_id)->jenisbiaya('ipi')->where('kategori', $vonis_ipi)->first();
                $this->kategori_ipi = $ipi?->id;
                // dd($this->vonis_ipi);
            }
            $this->nominal_ipi = $this->get->verifikasiberkas?->nominal_ipi;
        }
        $this->dispatch('open-modal');
    }

    public function _after_save()
    {
        $this->resetErrorBag();

        $this->get = NULL;
        $this->listdata_ukt = [];
        $this->listdata_ipi = [];
        $this->verifikator = NULL;
        $this->tgl_verifikasi = NULL;
        $this->catatan = NULL;
        $this->rekomendasi = NULL;
        $this->kategori_ukt = NULL;
        $this->vonis_ipi = NULL;
    }

    public function _reset()
    {
        $this->resetErrorBag();

        $this->get = NULL;
        $this->listdata_ukt = [];
        $this->listdata_ipi = [];
        $this->verifikator = NULL;
        $this->tgl_verifikasi = NULL;
        $this->catatan = NULL;
        $this->rekomendasi = NULL;
        $this->kategori_ukt = NULL;
        $this->vonis_ipi = NULL;

        $this->dispatch('close-modal');
    }
}
