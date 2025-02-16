<?php

namespace App\Livewire\Postingan;

use App\Models\Berkas;
use App\Models\Kategori;
use App\Models\Postingan;
use Livewire\Component;
use Livewire\WithFileUploads;

use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class AddInformasi extends Component
{
    use WithFileUploads;

    public $judul = "Tambah Informasi";
    public $judul_postingan;
    public $konten;
    public $publish = 0;

    public function render()
    {
        return view('livewire.postingan.add-informasi')
            ->extends('layouts.backend')
            ->section('content');
    }

    public function save()
    {
        $this->validate([
            'judul_postingan' => 'required|string|max:100',
            'konten' => 'required|string'
        ]);

        $data = [
            'user_id' => auth()->user()->id,
            'judul' => $this->judul_postingan,
            'konten' => $this->konten,
            'slug' => Str::slug($this->judul_postingan),
            'publish' => $this->publish,
        ];

        Postingan::create($data);
        alert()->success('Success', 'Berhasil membuat informasi');
        return $this->redirect(route('admin.informasi.index'));
    }
}
