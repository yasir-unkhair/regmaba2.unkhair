<?php

namespace App\Livewire\Verifikator;

use App\Models\PesertauktVerifikasiBerkas;
use App\Models\ProdiBiayastudi;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class VerifikasiPesertaukt extends Component
{
    public $get;
    public $rekomendasi;
    public $catatan;

    public $kip;

    public $list_rekomendasi = [];

    public function mount($peserta_id)
    {
        $this->get = PesertauktVerifikasiBerkas::where('peserta_id', $peserta_id)->first();
        $this->rekomendasi = $this->get->rekomendasi;
        $this->catatan = $this->get->catatan;
        $this->kip = $this->get->peserta->kip;

        array_push($this->list_rekomendasi, 'wawancara');
        if (trim($this->kip)) {
            array_push($this->list_rekomendasi, 'kip-k');
        }

        $biayastudi = ProdiBiayastudi::select(DB::raw('CONCAT("k", kategori) as kategori'), 'nominal')
            ->where('prodi_id', $this->get->peserta->prodi_id)
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
        return view('livewire.verifikator.verifikasi-pesertaukt');
    }

    public function save()
    {
        $this->validate([
            'rekomendasi' => 'required',
            'catatan' => 'required'
        ]);

        $this->get->peserta->update([
            'status' => 4
        ]);

        $this->get->update([
            'rekomendasi' => $this->rekomendasi,
            'catatan' => $this->catatan,
            'verifies_id' => auth()->user()->id,
            'tgl_verifikasi' => now()
        ]);

        $this->mount($this->get->peserta_id);
        $this->dispatch('alert', type: 'success', message: 'Verifikasi peserta berhasil disimpan');
    }
}
