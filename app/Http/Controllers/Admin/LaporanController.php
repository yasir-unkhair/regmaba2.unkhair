<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Models\Pesertaukt;
use App\Models\Prodi;
use App\Models\Setup;
use App\Models\User;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $peserta = Pesertaukt::with(['fakultas', 'prodi'])
                ->join('app_peserta_has_verifikasiberkas as b', 'app_peserta.id', '=', 'b.peserta_id')
                ->join('users as c', 'b.user_id_vonis', '=', 'c.id')
                ->select(['app_peserta.id', 'app_peserta.prodi_id', 'app_peserta.jalur', 'app_peserta.nomor_peserta', 'app_peserta.nama_peserta', 'b.vonis_ukt', 'b.tgl_vonis', 'c.name'])
                ->setup($setup->id)
                ->registrasi(true)
                ->status([5])
                ->orderBy('jalur', 'ASC')->orderBy('prodi_id', 'ASC')->orderBy('nama_peserta', 'ASC');
            return DataTables::eloquent($peserta)
                ->addIndexColumn()
                ->editColumn('prodi', function ($row) {
                    $str = ($row->prodi?->jenjang_prodi) . ' - ' . ($row->prodi?->nama_prodi);
                    return $str;
                })
                ->editColumn('status', function ($row) {
                    $str = strtoupper($row->vonis_ukt);
                    return $str;
                })
                ->editColumn('jalur', function ($row) {
                    return $row->jalur;
                })
                ->editColumn('keterangan', function ($row) {
                    $str = '<b>Ditetapkan:</b> <i>' . tgl_indo($row->tgl_vonis) . '</i>';
                    $str .= '<br><b>Oleh:</b> <i>' . $row->name . '</i>';
                    return $str;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('setup_id')) {
                        $instance->where('app_peserta.setup_id', $request->get('setup_id'));
                    }
                    if ($request->get('jalur')) {
                        $jalur = data_params($request->get('jalur'), 'jalur');
                        $instance->where('app_peserta.jalur', $jalur);
                    }

                    if ($request->get('registrasi')) {
                        $instance->where('app_peserta.registrasi', $request->get('registrasi'));
                    }

                    if ($request->get('fakultas_id')) {
                        $instance->where('app_peserta.fakultas_id', $request->get('fakultas_id'));
                    }

                    if ($request->get('prodi_id')) {
                        $instance->where('app_peserta.prodi_id', $request->get('prodi_id'));
                    }

                    if ($request->get('vonis')) {
                        $instance->where('b.vonis_ukt', $request->get('vonis'));
                    }

                    if (!empty($request->input('search.value'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->input('search.value');
                            $w->orWhere('app_peserta.nomor_peserta', 'LIKE', "%$search%")->orWhere('app_peserta.nama_peserta', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['prodi', 'status', 'keterangan'])
                ->make(true);
        }
        
        $setup = Setup::orderBy('tahun', 'DESC')->get();
        $fakultas = Fakultas::where('nama_fakultas', '!=', 'PASCASARJANA')->orderBy('nama_fakultas', 'ASC')->get();
        $prodi = Prodi::jenjang(['S1', 'D3'])->orderBy('fakultas_id', 'ASC')->orderBy('nama_prodi', 'ASC')->get();
        $verfikator = User::role(['verifikator'])->orderBy('name', 'ASC')->get();
        $data = [
            'judul' => 'Laporan UKT',
            'fakultas' => $fakultas,
            'prodi' => $prodi,
            'referensi' => master_referensi('Jalur Penerimaan'),
            'setup' => $setup,
            'verfikator' => $verfikator,
            'datatable2' => [
                'url' => route('admin.laporan.index'),
                'id_table' => 'id-datatable',
                'columns' => [
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'nomor_peserta', 'name' => 'nomor_peserta', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'nama_peserta', 'name' => 'nama_peserta', 'orderable' => 'true', 'searchable' => 'true'],
                    ['data' => 'ket_jalur', 'name' => 'ket_jalur', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'prodi', 'name' => 'prodi', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'verifikator', 'name' => 'verifikator', 'orderable' => 'false', 'searchable' => 'false']
                ]
            ]
        ];
        return view('admin.laporan.index', $data);
    }
}
