<?php

namespace App\Libraries;

use Illuminate\Support\Carbon;

class BankBtn
{
    private $message;
    private $apikey = '*#un1v3RS1T45Kh41Run*#*';
    private $demo = false;

    public function createva($pembayaran, $expired_va)
    {
        $params = [];
        if ($pembayaran->jenis_pembayaran == 'pemkes') {
            $no_identitas = $pembayaran->peserta->nomor_peserta;
            if ($pembayaran->peserta?->npm) {
                $no_identitas = $pembayaran->peserta->npm;
            }

            $params = [
                'aksi' => 'store-pemkes',
                'apikey' => $this->apikey,
                'demo' => $this->demo,
                'expired_va' => 1, // expired_va
                'kode_payment' => '006',
                'jenis_payment' => 'PEMKES Mahasiswa Baru',
                'prefix_trx' => 'PKM',
                'nama' => $pembayaran->peserta->nama_peserta,
                'nominal' => $pembayaran->amount,
                'deskripsi' => 'Pemeriksaan Kesehatan Mahasiswa Baru ' . $pembayaran->peserta->setup->tahun,
                'jenis_bayar' => $pembayaran->jenis_pembayaran,
                'detail' => [
                    'no_identitas' => $no_identitas,
                    'angkatan' => $pembayaran->peserta->setup->tahun,
                    'kode_prodi' => $pembayaran->peserta->prodi->kode_prodi,
                    'nama_prodi' => $pembayaran->peserta->prodi->nama_prodi,
                    'nama_fakultas' => $pembayaran->peserta->fakultas->nama_fakultas,
                    'kategori_ukt' => strtoupper($pembayaran->kategori_ukt),
                    'jalur' => $pembayaran->peserta->jalur
                ]
            ];
        } elseif ($pembayaran->jenis_pembayaran == 'ipi') {
            $no_identitas = $pembayaran->peserta->nomor_peserta;
            if ($pembayaran->peserta?->npm) {
                $no_identitas = $pembayaran->peserta->npm;
            }

            $params = [
                'aksi' => 'store-ipi',
                'apikey' => $this->apikey,
                'demo' => $this->demo,
                'expired_va' => 1, // expired_va
                'kode_payment' => '003',
                'jenis_payment' => 'IPI Mahasiswa Baru',
                'prefix_trx' => 'IPI',
                'nama' => $pembayaran->peserta->nama_peserta,
                'nominal' => $pembayaran->amount,
                'deskripsi' => 'IPI Mahasiswa Baru ' . $pembayaran->peserta->setup->tahun,
                'jenis_bayar' => $pembayaran->jenis_pembayaran,
                'detail' => [
                    'no_identitas' => $no_identitas,
                    'angkatan' => $pembayaran->peserta->setup->tahun,
                    'kode_prodi' => $pembayaran->peserta->prodi->kode_prodi,
                    'nama_prodi' => $pembayaran->peserta->prodi->nama_prodi,
                    'nama_fakultas' => $pembayaran->peserta->fakultas->nama_fakultas,
                    'kategori_ukt' => strtoupper($pembayaran->kategori_ukt),
                    'jalur' => $pembayaran->peserta->jalur
                ]
            ];
        } elseif ($pembayaran->jenis_pembayaran == 'ukt') {
            $params = [
                'aksi' => 'store-umb',
                'apikey' => $this->apikey,
                'demo' => $this->demo,
                'expired_va' => ($expired_va) ? $expired_va : 1, // expired_va
                'kode_payment' => '007',
                'jenis_payment' => 'UKT Mahasiswa Baru',
                'prefix_trx' => 'IPI',
                'nama' => $pembayaran->peserta->nama_peserta,
                'nominal' => $pembayaran->amount,
                'deskripsi' => 'UKT Mahasiswa Baru ' . $pembayaran->peserta->setup->tahun,
                'jenis_bayar' => 'umb',
                'detail' => [
                    'no_identitas' => $pembayaran->peserta->nomor_peserta,
                    'angkatan' => $pembayaran->peserta->setup->tahun,
                    'kode_prodi' => $pembayaran->peserta->prodi->kode_prodi,
                    'nama_prodi' => $pembayaran->peserta->prodi->nama_prodi,
                    'nama_fakultas' => $pembayaran->peserta->fakultas->nama_fakultas,
                    'kategori_ukt' => strtoupper($pembayaran->kategori_ukt),
                    'jalur' => $pembayaran->peserta->jalur
                ]
            ];
        }

        $response = json_decode(post_data(env('URL_ECOLL') . '/btn/createva.php', $params), TRUE);
        // dd($response);
        if (!$response['response']) {
            $this->message = $response['pesan'];
            return [
                'rsp' => false,
                'msg' => $this->message
            ];
        }

        $this->message = 'Berhasil Membuat Virtual Account BANK BTN';
        return [
            'rsp' => true,
            'msg' => $this->message,
            'data' => [
                'trx_id' => $response['data']['trx_id'],
                'va' => $response['data']['va'],
                'expired' => $response['data']['expired_va'],
            ]
        ];
    }

    public function updateva($pembayaran, $expired_va)
    {
        return [
            'rsp' => false,
            'msg' => 'Sedang pengembangan!'
        ];

        $params = [];
        if ($pembayaran->jenis_pembayaran == 'pemkes') {
            $params = [
                'apikey' => $this->apikey,
                'demo' => $this->demo,
                'aksi' => 'update-pemkes',
                'trx' => $pembayaran->trx_id,
                'va' => $pembayaran->billing,
                'expired_va' => ($expired_va) ? $expired_va : 1, // expired_va 1 hari
                'kode_payment' => '006',
                'jenis_payment' => 'PEMKES Mahasiswa Baru',
                'nama' => $pembayaran->peserta->nama_peserta,
                'nominal' => $pembayaran->amount,
                'deskripsi' => 'Pemeriksaan Kesehatan Mahasiswa Baru ' . $pembayaran->peserta->setup->tahun,
                'jenis_bayar' => $pembayaran->jenis_pembayaran,
                'detail' => [
                    'nomor_peserta' => $pembayaran->peserta->nomor_peserta,
                    'jalur' => $pembayaran->peserta->jalur,
                    'npm' => $pembayaran->peserta->npm,
                    'prodi' => $pembayaran->peserta->prodi->nama_prodi,
                    'fakultas' => $pembayaran->peserta->fakultas->nama_fakultas,
                    'tahun' => $pembayaran->peserta->setup->tahun
                ]
            ];
        } elseif ($pembayaran->jenis_pembayaran == 'ipi') {
            $params = [
                'apikey' => $this->apikey,
                'demo' => $this->demo,
                'aksi' => 'update-ipi',
                'trx' => $pembayaran->trx_id,
                'va' => $pembayaran->billing,
                'expired_va' => ($expired_va) ? $expired_va : 1, // expired_va 1 hari
                'kode_payment' => '003',
                'jenis_payment' => 'IPI Mahasiswa Baru',
                'nama' => $pembayaran->peserta->nama_peserta,
                'nominal' => $pembayaran->amount,
                'deskripsi' => 'IPI Mahasiswa Baru ' . $pembayaran->peserta->setup->tahun,
                'jenis_bayar' => $pembayaran->jenis_pembayaran,
                'detail' => [
                    'nomor_peserta' => $pembayaran->peserta->nomor_peserta,
                    'jalur' => $pembayaran->peserta->jalur,
                    'npm' => $pembayaran->peserta->npm,
                    'prodi' => $pembayaran->peserta->prodi->nama_prodi,
                    'fakultas' => $pembayaran->peserta->fakultas->nama_fakultas,
                    'tahun' => $pembayaran->peserta->setup->tahun
                ]
            ];
        } elseif ($pembayaran->jenis_pembayaran == 'ukt') {
            $expired_va = Carbon::now()->diffInDays($this->batas_pembayaran_ukt);
            $params = [
                'apikey' => $this->apikey,
                'demo' => $this->demo,
                'aksi' => 'update-umb',
                'trx' => $pembayaran->trx_id,
                'va' => $pembayaran->billing,
                'expired_va' => ($expired_va) ? $expired_va : 1, // expired_va
                'kode_payment' => '007',
                'jenis_payment' => 'UKT Mahasiswa Baru',
                'nama' => $pembayaran->peserta->nama_peserta,
                'nominal' => $pembayaran->amount,
                'deskripsi' => 'UKT Mahasiswa Baru ' . $pembayaran->peserta->setup->tahun,
                'jenis_bayar' => 'UMB',
                'detail' => [
                    'no_identitas' => $pembayaran->peserta->nomor_peserta,
                    'angkatan' => $pembayaran->peserta->setup->tahun,
                    'kode_prodi' => $pembayaran->peserta->prodi->kode_prodi,
                    'nama_prodi' => $pembayaran->peserta->prodi->nama_prodi,
                    'nama_fakultas' => $pembayaran->peserta->fakultas->nama_fakultas,
                    'kategori_ukt' => strtoupper($pembayaran->kategori_ukt),
                    'jalur' => $pembayaran->peserta->jalur
                ]
            ];
        }

        $response = json_decode(post_data(env('URL_ECOLL') . '/btn/updateva.php', $params), TRUE);

        if (!$response['response']) {
            $this->message = $response['pesan'];
            return [
                'rsp' => false,
                'msg' => $this->message
            ];
        }

        $this->message = 'Berhasil Update Virtual Account BANK BTN';
        return [
            'rsp' => true,
            'msg' => $this->message,
            'data' => [
                'trx_id' => $response['data']['trx_id'],
                'va' => $response['data']['va'],
                'expired' => $response['data']['expired_va'],
            ]
        ];
    }
    public function getMessage()
    {
        return $this->message;
    }
}
