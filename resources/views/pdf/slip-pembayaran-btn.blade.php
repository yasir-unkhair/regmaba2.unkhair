<html>
<title>Billing Tagihan Unkhair</title>

<head>
    <style type='text/css'>
        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }

        table tr.odd td {
            background-color: #f9f9f9;
        }

        table th,
        table td {
            padding: 4px 5px;
            line-height: 20px;
            text-align: left;
            vertical-align: top;
            border: 1px solid #dddddd;
        }

        .ul2 {
            list-style: square;
        }

        .ul2 li {
            color: #31F190;
        }

        .upper-alpha {
            list-style: upper-alpha;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td
                style='width:20%;border-bottom:0px;border-right:0px;border-top:0px;border-left:0px;vertical-align: middle;text-align: center;text-align:center'>
                {{-- <img src='{{ get_image(public_path('images/logo.jpg')) }}' height=50 width=50 /> --}}
            </td>
            <td
                style='width:60%;border-bottom:0px;border-right:0px;border-top:0px;border-left:0px;vertical-align: middle;text-align: center;text-align:center'>
                <h3>Billing Tagihan Pembayaran<br />
                    Universitas Khairun {{ date('Y') }}</h3>
            </td>
            <td
                style='width:20%;border-bottom:0px;border-right:0px;border-top:0px;border-left:0px;vertical-align: middle;text-align: center;text-align:center'>
                {{-- <img src='{{ get_image(public_path('images/btn.jpeg')) }}' height=50 width=50 /> --}}
            </td>
        </tr>
    </table>

    <table>
        <tr style='font-size:12px;font-weight:bold;'>
            <td colspan=3 style='width:100%;border-bottom:0px;'>
                <b>Ternate, {{ tgl_indo(now(), false) }}</b <br />
            </td>
        </tr>
        <tr style='font-size:12px;font-weight:bold;'>
            <td colspan=3 style='width:100%;border-bottom:0px;border-top:0px;'>
                Kepada Yth {{ $pembayaran->peserta->nama_peserta }} <br /><br />
                Berikut kami sampaikan tagihan pembayaran kepada UNKHAIR dengan rincian sebagai berikut :
            </td>
        </tr>

        <tr style='font-size:12px;'>
            <td style='width:30%;border-bottom:0px;border-top:0px;border-right:0px;'>
                Kode Tagihan
            </td>
            <td style='width:5%;border-right:0px;border-left:0px;border-bottom:0px;border-top:0px;'>
                :
            </td>
            <td style='width:65%;border-left:0px;border-bottom:0px;border-top:0px;'>
                {{ $pembayaran->trx_id }}
            </td>
        </tr>
        <tr style='font-size:12px;'>
            <td style='width:30%;border-bottom:0px;border-top:0px;border-right:0px;'>
                Nomor Billing
            </td>
            <td style='width:5%;border-right:0px;border-left:0px;border-bottom:0px;border-top:0px;'>
                :
            </td>
            <td style='width:65%;border-left:0px;border-bottom:0px;border-top:0px;'>
                {{ $pembayaran->va }}
            </td>
        </tr>

        <tr style='font-size:12px;'>
            <td style='width:30%;border-bottom:0px;border-top:0px;border-right:0px;'>
                Nama
            </td>
            <td style='width:5%;border-right:0px;border-left:0px;border-bottom:0px;border-top:0px;'>
                :
            </td>
            <td style='width:65%;border-left:0px;border-bottom:0px;border-top:0px;'>
                {{ $pembayaran->peserta->nama_peserta }}
            </td>
        </tr>
        <tr style='font-size:12px;'>
            <td style='width:30%;border-bottom:0px;border-top:0px;border-right:0px;'>
                Tagihan
            </td>
            <td style='width:5%;border-right:0px;border-left:0px;border-bottom:0px;border-top:0px;'>
                :
            </td>
            <td style='width:65%;border-left:0px;border-bottom:0px;border-top:0px;'>
                <b>Rp. {{ rupiah($pembayaran->amount) }}</b>
            </td>
        </tr>
        <tr style='font-size:12px;'>
            <td style='width:30%;border-bottom:0px;border-top:0px;border-right:0px;'>
                Keterangan
            </td>
            <td style='width:5%;border-right:0px;border-left:0px;border-bottom:0px;border-top:0px;'>
                :
            </td>
            <td style='width:65%;border-left:0px;border-bottom:0px;border-top:0px;'>
                {!! $pembayaran->detail_pembayaran !!}
            </td>
        </tr>
        <tr style='font-size:12px;'>
            <td style='width:30%;border-top:0px;border-right:0px;'>
                Jatuh Tempo
            </td>
            <td style='width:5%;border-right:0px;border-left:0px;border-bottom:0px;border-top:0px;'>
                :
            </td>
            <td style='width:65%;border-top:0px;border-top:0px;border-left:0px;'>
                {{ tgl_indo($pembayaran->expired, false) }}
            </td>
        </tr>
    </table>

    <table>
        <tr style='font-size:12px;font-weight:bold;'>
            <td style='width:100%;' font-color:'red';>
                <br />
                Silahkan lakukan pembayaran menggunakan Nomor Billing {{ $pembayaran->va }} melalui channel BTN
                sebelum tanggal jatuh tempo {{ tgl_indo($pembayaran->expired, false) }}.
                <br />
            </td>
        </tr>
    </table>

</body>

</html>
