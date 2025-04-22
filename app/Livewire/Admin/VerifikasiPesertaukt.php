<?php

namespace App\Livewire\Admin;

use App\Models\Pesertaukt;
use App\Models\PesertauktVerifikasiBerkas;
use App\Models\ProdiBiayastudi;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class VerifikasiPesertaukt extends Component
{
    public $get;
    public $peserta;
    public $rekomendasi;
    public $catatan;

    public $verifikator_id;

    public $vonis_ukt;

    public $kip;

    public $list_rekomendasi = [];

    public function mount($peserta_id)
    {
        $this->get = PesertauktVerifikasiBerkas::where('peserta_id', $peserta_id)->first();
        $this->rekomendasi = $this->get->rekomendasi ?? NULL;
        $this->catatan = $this->get->catatan ?? NULL;
        $this->vonis_ukt = $this->get->vonis_ukt ?? NULL;
        $this->verifikator_id = $this->get->verifikator_id ?? NULL;

        $this->peserta = Pesertaukt::where('id', $peserta_id)->first();
        $this->kip = $this->peserta->kip ?? NULL;

        array_push($this->list_rekomendasi, 'wawancara');
        if (trim($this->kip)) {
            array_push($this->list_rekomendasi, 'kip-k');
        }

        $biayastudi = ProdiBiayastudi::select(DB::raw('CONCAT("k", kategori) as kategori'), 'nominal')
            ->where('prodi_id', $this->peserta->prodi_id)
            ->where('jenis_biaya', 'ukt')
            ->where('nominal', '>', 0)
            ->orderBy('kategori', 'ASC')
            ->get();

        foreach ($biayastudi as $row) {
            array_push($this->list_rekomendasi, $row->kategori);
        }
    }
    public function render()
    {
        return view('livewire.admin.verifikasi-pesertaukt');
    }


    public function save()
    {
        $this->validate([
            'rekomendasi' => 'required',
            'catatan' => 'required'
        ]);

        $this->peserta->update([
            'status' => 4
        ]);

        if ($this->get) {
            $this->get->update([
                'rekomendasi' => $this->rekomendasi,
                'catatan' => $this->catatan,
                'verifies_id' => auth()->user()->id,
                'tgl_verifikasi' => now()
            ]);
        } else {
            PesertauktVerifikasiBerkas::create([
                'peserta_id' => $this->peserta->id,
                // 'verifikator_id' => auth()->user()->id,
                'rekomendasi' => $this->rekomendasi,
                'catatan' => $this->catatan,
                'verifies_id' => auth()->user()->id,
                'tgl_verifikasi' => now()
            ]);
        }

        $this->mount($this->peserta->id);
        $this->dispatch('alert', type: 'success', message: 'Verifikasi peserta berhasil disimpan');
    }
}
