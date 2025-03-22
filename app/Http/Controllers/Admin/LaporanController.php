<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Models\Pesertaukt;
use App\Models\Prodi;
use App\Models\Setup;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LaporanController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->ajax()) {
            
            $peserta = Pesertaukt::with(['setup', 'prodi', 'verifikasiberkas'])
                ->orderBy('app_peserta.jalur', 'ASC')->orderBy('app_peserta.prodi_id', 'ASC')->orderBy('app_peserta.nama_peserta', 'ASC');

            return DataTables::eloquent($peserta)
                ->addIndexColumn()
                ->editColumn('tahun', function ($row) {
                    $str = $row->setup?->tahun ?? '-';
                    return $str;
                })
                ->editColumn('prodi', function ($row) {
                    $str = ($row->prodi?->jenjang_prodi) . ' - ' . ($row->prodi?->nama_prodi);
                    return $str;
                })
                ->editColumn('status', function ($row) {
                    $str = strtoupper($row->verifikasiberkas?->vonis_ukt ?? '-');
                    return $str;
                })
                ->editColumn('jalur', function ($row) {
                    return $row->jalur;
                })
                ->editColumn('verifikator', function ($row) {
                    return $row->verifikasiberkas?->verifikator?->name ?? '-';
                })
                ->editColumn('keterangan', function ($row) {
                    return '-';
                })
                ->filter(function ($instance) use ($request) {
                    $filter = false;
                    if ($request->get('setup_id')) {
                        $instance->where('app_peserta.setup_id', $request->get('setup_id'));
                        $filter = true;
                    }

                    if ($request->get('jalur')) {
                        $jalur = data_params($request->get('jalur'), 'jalur');
                        $instance->where('app_peserta.jalur', $jalur);
                        $filter = true;
                    }

                    if ($request->get('registrasi')) {
                        $registrasi = $request->get('registrasi') == 'Y' ? 1 : 0;
                        $instance->where('app_peserta.registrasi', $registrasi);
                        $filter = true;
                    }
                    
                    if ($request->get('status_peserta')) {
                        $status = explode(':', $request->get('status_peserta'));
                        $instance->whereIn('app_peserta.status', $status);
                        $filter = true;
                    }

                    if ($request->get('fakultas_id')) {
                        $instance->where('app_peserta.fakultas_id', $request->get('fakultas_id'));
                        $filter = true;
                    }

                    if ($request->get('prodi_id')) {
                        $instance->where('app_peserta.prodi_id', $request->get('prodi_id'));
                        $filter = true;
                    }

                    if ($request->get('verfikator_id')) {
                        $instance->whereHas('verifikasiberkas', function ($q) use ($request) {
                            $q->where('verifikator_id', $request->get('verfikator_id'));
                        });
                    }

                    if(!$filter) {
                        $instance->where('app_peserta.setup_id', '-');
                    }

                    if (!empty($request->input('search.value'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->input('search.value');
                            $w->orWhere('app_peserta.nomor_peserta', 'LIKE', "%$search%")->orWhere('app_peserta.nama_peserta', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['tahun', 'prodi', 'jalur', 'status', 'keterangan', 'verifikator'])
                ->make(true);
        }
        
        $setup = Setup::orderBy('tahun', 'DESC')->get();
        $fakultas = Fakultas::where('nama_fakultas', '!=', 'PASCASARJANA')->orderBy('nama_fakultas', 'ASC')->get();
        // $prodi = Prodi::jenjang(['S1', 'D3'])->orderBy('fakultas_id', 'ASC')->orderBy('nama_prodi', 'ASC')->get();
        $verfikator = User::role(['verifikator'])->orderBy('name', 'ASC')->get();
        $data = [
            'judul' => 'Laporan UKT',
            'fakultas' => $fakultas,
            // 'prodi' => $prodi,
            'referensi' => master_referensi('Jalur Penerimaan'),
            'setup' => $setup,
            'verfikator' => $verfikator,
            'datatable2' => [
                'url' => route('admin.laporan.index'),
                'id_table' => 'id-datatable',
                'columns' => [
                    ['data' => 'tahun', 'name' => 'tahun', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'nomor_peserta', 'name' => 'nomor_peserta', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'nama_peserta', 'name' => 'nama_peserta', 'orderable' => 'true', 'searchable' => 'true'],
                    ['data' => 'prodi', 'name' => 'prodi', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'jalur', 'name' => 'jalur', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'status', 'name' => 'status', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'verifikator', 'name' => 'verifikator', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'keterangan', 'name' => 'keterangan', 'orderable' => 'false', 'searchable' => 'false'],
                ]
            ]
        ];
        return view('admin.laporan.index', $data);
    }
}
