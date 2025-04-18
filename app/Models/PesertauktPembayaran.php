<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PesertauktPembayaran extends Model
{
    use HasFactory;

    protected $table = 'app_peserta_has_pembayaran';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'peserta_id',
        'jenis_pembayaran',
        'kategori_ukt',
        'detail_pembayaran',
        'bank',
        'trx_id',
        'va',
        'expired',
        'amount',
        'lunas',
        'tgl_pelunasan',
        'rsp_ebilling'
    ];

    public function peserta()
    {
        return $this->belongsTo(Pesertaukt::class, 'peserta_id');
    }
}
