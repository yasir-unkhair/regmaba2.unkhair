<?php
if (!function_exists('postdata_ebilling')) {
    function postdata_ebilling($url, $data)
    {
        // Mengonversi data menjadi JSON
        $json_data = json_encode($data);

        // Inisialisasi cURL
        $ch = curl_init();

        // Menentukan opsi untuk cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Mengembalikan hasil dari request
        curl_setopt($ch, CURLOPT_POST, true);  // Menandakan ini adalah POST request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);  // Data yang akan dikirim

        // Menambahkan header Content-Type untuk JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_data),
            'X-API-KEY: secret'
        ));

        // Mengirim request dan menerima respons
        $response = curl_exec($ch);

        // Memeriksa apakah ada error
        if (curl_errno($ch)) {
            return curl_error($ch);
        } else {
            // Jika sukses, menampilkan respons dari server
            return json_decode($response, true);
        }

        // Menutup koneksi cURL
        curl_close($ch);
    }
}

if (!function_exists('patchdata_ebilling')) {
    function patchdata_ebilling($url, $data)
    {
        // Mengonversi data menjadi JSON
        $json_data = json_encode($data);

        // Inisialisasi cURL
        $ch = curl_init();

        // Menentukan opsi untuk cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Mengembalikan hasil dari request
        curl_setopt($ch, CURLOPT_POST, true);  // Menandakan ini adalah POST request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);  // Data yang akan dikirim

        // Menambahkan header Content-Type untuk JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_data),
            'X-API-KEY: secret'
        ));

        // Mengirim request dan menerima respons
        $response = curl_exec($ch);

        // Memeriksa apakah ada error
        if (curl_errno($ch)) {
            return curl_error($ch);
        } else {
            // Jika sukses, menampilkan respons dari server
            return $response;
        }

        // Menutup koneksi cURL
        curl_close($ch);
    }
}

if (!function_exists('getdata_ebilling')) {
    function getdata_ebilling($url)
    {
        // Inisialisasi cURL
        $ch = curl_init();

        // Menentukan opsi untuk cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Mengembalikan hasil dari request

        // Menambahkan header Content-Type untuk JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-API-KEY: secret'
        ));

        // Mengirim request dan menerima respons
        $response = curl_exec($ch);

        // Memeriksa apakah ada error
        if (!empty(curl_error($ch))) {
            return print_r(curl_error($ch) . ' - ' . $url);
        } else {
            // Jika sukses, menampilkan respons dari server
            return $response;
        }
        curl_close($ch);
        return $result;
    }
}
