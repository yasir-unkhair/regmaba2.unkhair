<div>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $judul }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">{{ $judul }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content pl-2 pr-2">
        <div class="container-fluid">
            <!-- Default box -->
            <form wire:submit="save">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $judul }}</h3>

                        <div class="card-tools">
                            <a href="{{ route('admin.informasi.index') }}" class="btn btn-sm btn-default"><i
                                    class="fas fa-angle-double-left"></i> Kembali</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 border-right">
                                <div class="form-group">
                                    <label for="judul">Judul Informasi</label>
                                    <input type="text" class="form-control" id="judul"
                                        wire:model="judul_postingan" placeholder="Judul Informasi">
                                    @error('judul_postingan')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="isi-konten">Isi Informasi</label>
                                    <div wire:ignore>
                                        <textarea id="isi-konten">{{ $konten }}</textarea>
                                    </div>
                                    @error('konten')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" wire:model="publish" class="form-check-input" id="publish"
                                        value="1" {{ $publish ? 'checked' : '' }}>
                                    <label class="form-check-label" for="publish">Publish</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire.target="save">Simpan</span>
                            <span wire:loading wire.target="save"><span class="spinner-border spinner-border-sm"
                                    role="status" aria-hidden="true"></span> Please wait...</span>
                        </button>
                    </div>
                    <!-- /.card-footer-->
                </div>
            </form>
            <!-- /.card -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <!-- jQuery -->
    @push('script')
        <script src="{{ asset('adminlte3') }}/plugins/jquery/jquery.min.js"></script>
        <script src="https://cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>

        <script>
            var base_url = "{{ url('') }}";
            var options = {
                filebrowserImageBrowseUrl: base_url + '/laravel-filemanager?type=Images',
                filebrowserImageUploadUrl: base_url + '/laravel-filemanager/upload?type=Images&_token=',
                filebrowserBrowseUrl: base_url + '/laravel-filemanager?type=Files',
                filebrowserUploadUrl: base_url + '/laravel-filemanager/upload?type=Files&_token=',
                height: 500
            };

            $(function() {
                const editor = CKEDITOR.replace('isi-konten', options);
                editor.on('change', function(event) {
                    //console.log(event.editor.getData());
                    @this.set('konten', event.editor.getData());
                });
            });
        </script>
    @endpush
</div>
