<?php

namespace App\Livewire\Postingan;

use App\Models\Postingan;

use Livewire\Component;
use Illuminate\Support\Str;


class EditInformasi extends Component
{

    public $judul = "Edit Informasi";
    public $id;
    public $judul_postingan;
    public $konten;
    public $publish = 0;

    public function render()
    {
        return view('livewire.postingan.edit-informasi')
            ->extends('layouts.backend')
            ->section('content');
    }

    public function mount($id)
    {
        $get = Postingan::where('id', $id)->first();
        $this->id = $get->id;
        $this->judul_postingan = $get->judul;
        $this->konten = $get->konten;
        $this->publish = $get->publish;
    }

    public function save()
    {
        $rules = [
            'judul_postingan' => 'required|string|max:100',
            'konten' => 'required|string',
        ];

        // dd($this->gambar_sampul, $rules);

        $this->validate($rules);

        $data = [
            'judul' => $this->judul_postingan,
            'konten' => $this->konten,
            'slug' => Str::slug($this->judul_postingan),
            'publish' => $this->publish,
        ];

        $posting = Postingan::where('id', $this->id)->update($data);

        alert()->success('Success', 'Informasi berhasil di edit');
        return $this->redirect(route('admin.informasi.index'));
    }


    public function _reset()
    {
        $this->resetErrorBag();
        $this->dispatch('close-modal');
    }
}
