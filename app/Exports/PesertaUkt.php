<?php

namespace App\Exports;

use App\Models\Pesertaukt as ModelsPesertaukt;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PesertaUkt implements FromView
{
    public $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $instance = ModelsPesertaukt::with(['setup', 'fakultas', 'prodi', 'verifikasiberkas']);

        if ($this->input['setup_id']) {
            $instance->where('app_peserta.setup_id', $this->input['setup_id']);
        }

        if ($this->input['jalur']) {
            $jalur = data_params($this->input['jalur'], 'jalur');
            $instance->where('app_peserta.jalur', $jalur);
            $filter = true;
        }

        if ($this->input['registrasi']) {
            $registrasi = $this->input['registrasi'] == 'Y' ? 1 : 0;
            $instance->where('app_peserta.registrasi', $registrasi);
        }

        if ($this->input['status_peserta']) {
            if ($this->input['status_peserta'] == '1') {
                $instance->where('app_peserta.update_data_diri', 1)
                    ->where('app_peserta.update_kondisi_keluarga', 1)
                    ->where('app_peserta.update_pembiayaan_studi', 1);
            } else {
                $status = explode(':', $this->input['status_peserta']);
                $instance->whereIn('app_peserta.status', $status);
            }
        }

        if ($this->input['fakultas_id']) {
            $instance->where('app_peserta.fakultas_id', $this->input['fakultas_id']);
        }

        if ($this->input['prodi_id']) {
            $instance->where('app_peserta.prodi_id', $this->input['prodi_id']);
        }

        if ($this->input['verfikator_id']) {
            $verfikator_id = $this->input['verfikator_id'];
            $instance->whereHas('verifikasiberkas', function ($q) use ($verfikator_id) {
                $q->where('verifikator_id', $verfikator_id);
            });
        }

        $instance->orderBy('app_peserta.jalur', 'ASC')->orderBy('app_peserta.prodi_id', 'ASC')->orderBy('app_peserta.nama_peserta', 'ASC');

        $data = [
            'results' => $instance->get()
        ];
        return view('admin.laporan.export-excel', $data);
    }
}
