<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $username;
    public $password;
    public $bool_peserta = 0;

    public function render()
    {
        $data = [
            'judul' => 'Login Aplikasi'
        ];

        return view('livewire.auth.login', $data)
            ->extends('layouts.auth')
            ->section('content');
    }

    public function checklogin()
    {
        // sleep(3);
        $this->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required'
        ]);

        // dd($this);
        // user peserta ukt
        if ($this->bool_peserta) {
            $login = User::with('peserta')->where(['username' => $this->username])->first();
            $err = 0;

            if (!$login) {
                $err++;
                flash('error', 'Username ' . $this->username . ' tidak di kenali, silahkan perika kembali!!');
                return $this->redirect(route('auth.login'));
            }

            if (!$login->hasRole('peserta')) {
                $err++;
                flash('error', 'Username ' . $this->username . ' tidak mempunyai akses ke aplikasi UKT!!');
                return $this->redirect(route('auth.login'));
            }

            if (!$login->is_active) {
                $err++;
                flash('error', 'Akun anda sedang dinonaktifkan!!');
                return $this->redirect(route('auth.login'));
            }

            $password = password_verify($this->password, $login->password);
            if (!$password) {
                $err++;
                flash('error', 'Password anda salah, silahkan ketik kembali!!');
                return $this->redirect(route('auth.login'));
            }

            if (!$err) {
                Auth::login($login);
                session()->put([
                    'user_id' => $login->peserta->user_id,
                    'peserta_id' => $login->peserta->id,
                    'jalur' => $login->peserta->jalur,
                    'prodi_id' => $login->peserta->prodi_id,
                    'nomor_peserta' => $login->peserta->nomor_peserta,
                    'role' => 'peserta'
                ]);

                alert()->success('Success', 'Login sucses, Selamat datang ' . $login->name);
                return $this->redirect(route('peserta.dashboard'));
            }
        }

        // user admin
        else {
            $login = User::where(['username' => $this->username])->first();
            $err = 0;

            if (!$login) {
                $err++;
                flash('error', 'Username ' . $this->username . ' tidak di kenali, silahkan perika kembali!!');
                return $this->redirect(route('auth.login'));
            }

            if (!$login->hasRole(['developper', 'admin', 'verifikator'])) {
                $err++;
                flash('error', 'Username ' . $this->username . ' tidak mempunyai akses ke aplikasi UKT!!');
                return $this->redirect(route('auth.login'));
            }

            if ($login->user_simak) {
                $token = get_token();

                if (!$token || $token['status'] != 200) {
                    $err++;
                    flash('error', 'Terjadi kesalahan saat pembuatan token!');
                    return $this->redirect(route('auth.login'));
                }

                $response_admin = json_decode(get_data(str_curl(pengaturan('url-simak') . '/apiv2/index.php/admin', ['token' => $token['data']['token'], 'username' => $this->username])), TRUE);
                $response_dosen = json_decode(get_data(str_curl(pengaturan('url-simak') . '/apiv2/index.php/dosen', ['token' => $token['data']['token'], 'nidn' => $this->username])), TRUE);
                // dd($response_admin, $response_dosen);
                if ($response_admin && $response_admin['status'] == 200) {
                    $get = $response_admin['data']['admin'];
                    if ($get['blokir'] == 'Y') {
                        $err++;
                        flash('error', 'Akun anda sedang dinonaktifkan!!');
                        return $this->redirect(route('auth.login'));
                    }
                    $password = password_verify($this->password, $get['password']);
                } elseif ($response_dosen && $response_dosen['status'] == 200) {
                    $get = $response_dosen['data']['dosen'];
                    if (strtolower($get['nama_status_aktif']) != 'aktif') {
                        $err++;
                        flash('error', 'Akun anda sedang dinonaktifkan!!');
                        return $this->redirect(route('auth.login'));
                    }
                    $password = password_verify($this->password, $get['password']);
                } else {
                    $err++;
                    flash('error', 'Identitas ' . $this->username . ' tidak di kenali!');
                    return $this->redirect(route('auth.login'));
                }
            } else {
                $password = password_verify($this->password, $login->password);
            }

            // dd($password);
            if (!$password) {
                $err++;
                flash('error', 'Password anda salah, silahkan ketik kembali!!');
                return $this->redirect(route('auth.login'));
            }


            if (!$err) {
                Auth::login($login);
                $role = $login->roles()->count();

                if ($role == 1) {
                    $role = '';
                    foreach ($login->roles()->get() as $r) {
                        $role = $r->name;
                    }
                    session()->put([
                        'role' => $role
                    ]);
                } else {
                    session()->put([
                        'role' => NULL
                    ]);
                }
                alert()->success('Success', 'Login sucses, Selamat datang ' . $login->name);
                return $this->redirect(route('admin.dashboard'));
            }
        }
    }
}
