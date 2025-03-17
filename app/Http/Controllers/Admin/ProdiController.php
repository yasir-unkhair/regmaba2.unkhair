<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProdiController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $listdata = Prodi::with(['fakultas'])
                ->orderBy('fakultas_id', 'ASC')
                ->orderBy('jenjang_prodi', 'ASC')
                ->orderBy('nama_prodi', 'ASC');
            return DataTables::eloquent($listdata)
                ->addIndexColumn()
                ->editColumn('action', function ($row) {
                    $onclick = "edit('" . $row->id . "')";
                    
                    $update_biaya_studi = '<a href="' . route('admin.prodi.importbiayastudi', $row->id) . '" class="btn btn-sm btn-info">Update Biaya Studi</a>';
                    $actionBtn = '
                    <center>
                        <a href="' . route('admin.prodi.biayastudi', encode_arr(['prodi_id' => $row->id])) . '" class="btn btn-sm btn-primary">Biaya Studi</a>
                        <button type="button" onclick="' . $onclick . '" class="btn btn-sm btn-warning">Edit</button>
                    </center>';
                    return $actionBtn;
                })
                ->editColumn('prodi', function ($row) {
                    return $row->kode_prodi . ' - ' . $row->nama_prodi;
                })
                ->editColumn('fakultas', function ($row) {
                    return $row->fakultas->nama_fakultas;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('fakultas_id')) {
                        $instance->where('fakultas_id', $request->get('fakultas_id'));
                    }

                    if (!empty($request->input('search.value'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->input('search.value');
                            $w->orWhere('kode_prodi', 'LIKE', "%$search%")->orWhere('nama_prodi', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['prodi', 'action'])
                ->make(true);
        }

        $fakultas = Fakultas::orderBy('nama_fakultas', 'ASC')->get();
        $data = [
            'judul' => 'Data Program Studi',
            'fakultas' => $fakultas,
            'datatable2' => [
                'url' => route('admin.prodi.index'),
                'id_table' => 'id-datatable',
                'columns' => [
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'prodi', 'name' => 'prodi', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'jenjang_prodi', 'name' => 'jenjang_prodi', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'fakultas', 'name' => 'fakultas', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'action', 'name' => 'action', 'orderable' => 'false', 'searchable' => 'false']
                ]
            ]
        ];

        return view('backend.admin.prodi', $data);
    }

    public function biayastudi($params)
    {
        $params = decode_arr($params);
        $prodi = Prodi::where('id', $params['prodi_id'])->first();
        $data = [
            'judul' => 'Biaya Studi Program Studi',
            'prodi' => $prodi
        ];

        return view('backend.admin.biayastudi', $data);
    }

    public function importSimak()
    {
        $token = get_token();
        if ($token && $token['status'] == 200) {
            $response = json_decode(get_data(str_curl(pengaturan('url-simak') . '/apiv2/index.php/prodi', ['token' => $token['data']['token']])), TRUE);
            if ($response && $response['status'] == 200) {
                $success = 0;
                $prodi = new Prodi();
                foreach ($response['data'] as $row) {
                    $check = $prodi->where('id', $row['id_prodi'])->first();
                    if (!$check) {
                        $proses = $prodi->create(
                            [
                                'id' => $row['id_prodi'],
                                'fakultas_id' => trim($row['id_fakultas']) ? $row['id_fakultas'] : NULL,
                                'kode_prodi' => $row['kode_program_studi'],
                                'nama_prodi' => ucwords(strtolower($row['nama_program_studi'])),
                                'status' => $row['status'],
                                'jenjang_prodi' => $row['nama_jenjang_pendidikan']
                            ]
                        );
                        if ($proses) {
                            $success++;
                        }
                    }
                }
                alert()->success('Success', 'Sebanyak ' . $success . ' prodi berhasil diimport dari SIMAK!');
            } else {
                alert()->error('Error', 'Terjadi kesalahan saat ambil data!');
            }
        } else {
            alert()->error('Error', 'Terjadi kesalahan saat pembuatan token!');
        }
        return redirect(route('admin.prodi.index'));
    }

    public function import_biayastudi($id)
    {
        try {
            $prodi = Prodi::with('biayastudi')->where('id', $id)->first();

            $mspst = DB::table('mspst')->where('KDPSTMSPST', $prodi->kode_prodi)->first();
            
            $ukt = [
                [
                    'kategori' => '1',
                    'nominal' => $mspst->k1
                ],
                [
                    'kategori' => '2',
                    'nominal' => $mspst->k2
                ],
                [
                    'kategori' => '3',
                    'nominal' => $mspst->k3
                ],
                [
                    'kategori' => '4',
                    'nominal' => $mspst->k4
                ],
                [
                    'kategori' => '5',
                    'nominal' => $mspst->k5
                ],
                [
                    'kategori' => '6',
                    'nominal' => $mspst->k6
                ],
                [
                    'kategori' => '7',
                    'nominal' => $mspst->k7
                ],
                [
                    'kategori' => '8',
                    'nominal' => $mspst->k8
                ],
            ];

            $prodi->biayastudi()->where('jenis_biaya', 'ukt')->delete();
            foreach ($ukt as $value) {
                $prodi->biayastudi()->updateOrCreate(
                    [
                        'prodi_id' => $prodi->prodi_id,
                        'jenis_biaya' => 'ukt',
                        'kategori' => $value['kategori']
                    ],
                    [
                        'prodi_id' => $prodi->prodi_id,
                        'jenis_biaya' => 'ukt',
                        'nominal' => preg_replace("/[^0-9]/", "", $value['nominal']),
                        'kategori' => $value['kategori']
                    ]
                );
            }

            // ipi
            $ipi = [
                [
                    'kategori' => '1',
                    'nominal' => $mspst->spi1
                ],
                [
                    'kategori' => '2',
                    'nominal' => $mspst->spi2
                ],
                [
                    'kategori' => '3',
                    'nominal' => $mspst->spi3
                ]
            ];

            $prodi->biayastudi()->where('jenis_biaya', 'ipi')->delete();
            foreach ($ipi as $value) {
                $prodi->biayastudi()->updateOrCreate(
                    [
                        'prodi_id' => $prodi->prodi_id,
                        'jenis_biaya' => 'ipi',
                        'kategori' => $value['kategori']
                    ],
                    [
                        'prodi_id' => $prodi->prodi_id,
                        'jenis_biaya' => 'ipi',
                        'nominal' => preg_replace("/[^0-9]/", "", $value['nominal']),
                        'kategori' => $value['kategori']
                    ]
                );
            }

            $prodi->update([
                'kode_prodi_dikti' => $mspst->kode_prodi_dikti
            ]);

            alert()->success('Success', 'Biaya Studi Berhasil Diimport!');
            return redirect(route('admin.prodi.index'));

        } catch (\Throwable $th) {
            echo $th->getMessage();   
        }
    }
}
