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
            $params = [
                'apikey' => $this->apikey,
                'demo' => $this->demo,
                'expired_va' => $expired_va, // expired_va
                'kode_payment' => '006',
                'jenis_payment' => 'PEMKES Mahasiswa Baru',
                'prefix_trx' => 'PKM',
                'nama' => $pembayaran->peserta->nama_peserta,
                'nominal' => $pembayaran->amount,
                'deskripsi' => 'Pemeriksaan Kesehatan Mahasiswa Baru ' . $pembayaran->peserta->setup->tahun,
                'jenis_bayar' => $pembayaran->jenis_pembayaran
            ];
        } elseif ($pembayaran->jenis_pembayaran == 'ipi') {
            $params = [
                'apikey' => $this->apikey,
                'demo' => $this->demo,
                'expired_va' => $expired_va, // expired_va
                'kode_payment' => '003',
                'jenis_payment' => 'IPI Mahasiswa Baru',
                'prefix_trx' => 'IPI',
                'nama' => $pembayaran->peserta->nama_peserta,
                'nominal' => $pembayaran->amount,
                'deskripsi' => 'IPI Mahasiswa Baru ' . $pembayaran->peserta->setup->tahun,
                'jenis_bayar' => $pembayaran->jenis_pembayaran
            ];
        } elseif ($pembayaran->jenis_pembayaran == 'ukt') {
            $params = [
                'apikey' => $this->apikey,
                'demo' => $this->demo,
                'expired_va' => ($expired_va) ? $expired_va : 1, // expired_va
                'kode_payment' => '007',
                'jenis_payment' => 'UKT Mahasiswa Baru',
                'prefix_trx' => 'UMB',
                'nama' => $pembayaran->peserta->nama_peserta,
                'nominal' => $pembayaran->amount,
                'deskripsi' => 'UKT Mahasiswa Baru ' . $pembayaran->peserta->setup->tahun,
                'jenis_bayar' => 'umb'
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

        $no_identitas = $pembayaran->peserta->nomor_peserta;
        if ($pembayaran->peserta?->npm) {
            $no_identitas = $pembayaran->peserta->npm;
        }

        // get tahun akademik
        $setup = json_decode(getdata_ebilling(env('URL_EBILLING') . '/api/tahun-pembayaran'), TRUE);

        // send data va ke ebilling
        $ebilling = [
            "no_va" => $response['data']['va'],
            "trx_id" => $response['data']['trx_id'],
            "jenis_bayar" => $params['jenis_bayar'],
            "nama_bank" => "BTN",
            "nominal" => $params['nominal'],
            "nama" => $params['nama'],
            "tgl_expire" => date('Y-m-d', strtotime($response['data']['expired_va'])),
            "no_identitas" => $no_identitas,
            "angkatan" => $pembayaran->peserta->setup->tahun,
            "kode_prodi" => $pembayaran->peserta->prodi->kode_prodi,
            "nama_prodi" => $pembayaran->peserta->prodi->nama_prodi,
            "nama_fakultas" => $pembayaran->peserta->fakultas->nama_fakultas,
            "kategori_ukt" => strtoupper($pembayaran->kategori_ukt),
            "jalur" => $pembayaran->peserta->jalur,
            "detail" => $params['deskripsi'],
            'tahun_akademik' => $setup['data']['tahun_akademik']
        ];
        $res = postdata_ebilling(env('URL_EBILLING') . '/api/billing-mahasiswa', $ebilling);

        // dd($response, $ebilling, $res);

        $this->message = 'Berhasil Membuat Virtual Account BANK BTN';
        return [
            'rsp' => true,
            'msg' => $this->message,
            'data' => [
                'trx_id' => $response['data']['trx_id'],
                'va' => $response['data']['va'],
                'expired' => $response['data']['expired_va'],
                'rsp_ebilling' => json_encode($res) ?? NULL
            ]
        ];
    }

    public function updateva($pembayaran, $expired_va)
    {
        if ($pembayaran->jenis_pembayaran == 'pemkes') {
            $params = [
                'apikey' => $this->apikey,
                'demo' => $this->demo,
                'expired_va' => $expired_va, // expired_va
                'kode_payment' => '006',
                'jenis_payment' => 'PEMKES Mahasiswa Baru',
                'prefix_trx' => 'PKM',
                'nama' => $pembayaran->peserta->nama_peserta,
                'nominal' => $pembayaran->amount,
                'deskripsi' => 'Pemeriksaan Kesehatan Mahasiswa Baru ' . $pembayaran->peserta->setup->tahun,
                'jenis_bayar' => $pembayaran->jenis_pembayaran
            ];
        } elseif ($pembayaran->jenis_pembayaran == 'ipi') {
            $params = [
                'apikey' => $this->apikey,
                'demo' => $this->demo,
                'expired_va' => $expired_va, // expired_va
                'kode_payment' => '003',
                'jenis_payment' => 'IPI Mahasiswa Baru',
                'prefix_trx' => 'IPI',
                'nama' => $pembayaran->peserta->nama_peserta,
                'nominal' => $pembayaran->amount,
                'deskripsi' => 'IPI Mahasiswa Baru ' . $pembayaran->peserta->setup->tahun,
                'jenis_bayar' => $pembayaran->jenis_pembayaran
            ];
        } elseif ($pembayaran->jenis_pembayaran == 'ukt') {
            $params = [
                'apikey' => $this->apikey,
                'demo' => $this->demo,
                'expired_va' => ($expired_va) ? $expired_va : 1, // expired_va
                'kode_payment' => '007',
                'jenis_payment' => 'UKT Mahasiswa Baru',
                'prefix_trx' => 'UMB',
                'nama' => $pembayaran->peserta->nama_peserta,
                'nominal' => $pembayaran->amount,
                'deskripsi' => 'UKT Mahasiswa Baru ' . $pembayaran->peserta->setup->tahun,
                'jenis_bayar' => 'umb'
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

        $no_identitas = $pembayaran->peserta->nomor_peserta;
        if ($pembayaran->peserta?->npm) {
            $no_identitas = $pembayaran->peserta->npm;
        }

        // get tahun akademik
        $setup = json_decode(getdata_ebilling(env('URL_EBILLING') . '/api/tahun-pembayaran'), TRUE);

        // send data va ke ebilling
        $ebilling = [
            "no_va" => $response['data']['va'],
            "trx_id" => $response['data']['trx_id'],
            "jenis_bayar" => $params['jenis_bayar'],
            "nama_bank" => "BTN",
            "nominal" => $params['nominal'],
            "tgl_expire" => date('Y-m-d', strtotime($response['data']['expired_va'])),
            "npm" => $no_identitas,
            'tahun_akademik' => $setup['data']['tahun_akademik']
        ];
        $res = patchdata_ebilling(env('URL_EBILLING') . '/api/billing-mahasiswa/update', $ebilling);

        $this->message = 'Berhasil Update Virtual Account BANK BTN';
        return [
            'rsp' => true,
            'msg' => $this->message,
            'data' => [
                'trx_id' => $response['data']['trx_id'],
                'va' => $response['data']['va'],
                'expired' => $response['data']['expired_va'],
                'rsp_ebilling' => json_encode($res) ?? NULL
            ]
        ];
    }
    public function getMessage()
    {
        return $this->message;
    }
}
