<?php

namespace App\Livewire\Auth;

use App\Jobs\SendMail;
use App\Models\Pesertaukt;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ResetPassword extends Component
{
    public $currentStep = 1;


    public $peserta_id;

    public $nomor_peserta;
    public $nisn;

    public $password;

    public $get;

    public function mount($params = NULL)
    {
        if ($params && data_params($params, 'nomor_peserta')) {
            $this->get = decode_arr($params);
            $this->currentStep = 2;
        }
    }

    public function render()
    {
        $data = [
            'judul' => 'Reset Password'
        ];
        return view('livewire.auth.reset-password', $data)
        ->extends('layouts.auth')
            ->section('content');
    }

    public function back($step)
    {
        $this->currentStep = $step;
    }

    public function check()
    {
        $this->validate(
            [
                'nomor_peserta' => ['required', 'exists:app_peserta,nomor_peserta'],
                'nisn' => ['required', 'exists:app_peserta,nisn']
            ],
            [
                'nomor_peserta.required' => 'Kolom Nomor Peserta jangan dikosongkan!',
                'nomor_peserta.exists' => 'Nomor Peserta ' . $this->nomor_peserta . ' tidak ditemukan!',

                'nisn.required' => 'Kolom NISN jangan dikosongkan!',
                'nisn.exists' => 'NISN ' . $this->nisn . ' tidak ditemukan!',
            ]
        );

        $cekdata = Pesertaukt::with(['prodi'])->where('nomor_peserta', $this->nomor_peserta)->where('nisn', $this->nisn)->first();
        // dd($cekdata);
        if (!$cekdata) {
            $this->dispatch('alert', type: 'error', message: 'Kombinasi Nomor Peserta ' . $this->nomor_peserta . ' dan NISN ' . $this->nisn . ' tidak ditemukan!');
        } else {

            if (!$cekdata->registrasi) {
                $this->dispatch('alert', type: 'error', message: 'Nomor Peserta ' . $this->nomor_peserta . ' belum melakukan registrasi!');
            } else {
                $err = 0;
                
                if (!$err) {
                    $this->get = [
                        'peserta_id' => $cekdata->id,
                        'nomor_peserta' => $cekdata->nomor_peserta,
                        'nisn' => $cekdata->nisn,
                        'email' => $cekdata->email,
                        'nama_peserta' => $cekdata->nama_peserta,
                        'prodi' => $cekdata->prodi->jenjang_prodi . ' - ' . $cekdata->prodi->nama_prodi,
                        'jalur' => $cekdata->jalur
                    ];

                    $this->peserta_id = $cekdata->id;

                    $this->currentStep = 2;
                }
            }
        }
    }

    public function save()
    {
        $this->validate(
            [
                'password' => 'required|max:15'
            ],
            [
                'password.required' => 'Kolom Password jangan dikosongkan!',
                'password.max' => 'Password terlalu panjang, maksimal 15 karekter!',
            ]
        );

        $peserta = Pesertaukt::where('id', $this->peserta_id);
        $get = $peserta->first();


        if (!$get->registrasi) {
            $this->dispatch('alert', type: 'error', message: 'Nomor Peserta <b>' . $this->nomor_peserta . '</b> belum melakukan registrasi!');
        } else {
            $password = str_replace(" ", "", trim($this->password));

            // update password

            $peserta->update([
                'pass' => $password
            ]);

            User::where('id', $get->user_id)->update([
                'password' => Hash::make($password)
            ]);

            SendMail::dispatch([
                'email' => $get->email,
                'get' => Pesertaukt::where('id', $this->peserta_id)->first(),
                'content' => 'reset-password'
            ])->delay(Carbon::now()->addSecond(5));

            alert()->success('Success', 'Berhasil Reset Password, Silahkan coba login dengan password baru');
            return $this->redirect(route('auth.reset'));
        }
    }
}
