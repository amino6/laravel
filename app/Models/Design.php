<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Design extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image',
        'title',
        'description',
        'slug',
        'colse_to_comment',
        'is_live',
        'upload_successful',
        'disk'
    ];

    public function user() {
        $this->belongsTo(User::class);
    }

    public function getImagesAttribute() {
        $thumbnail = Storage::disk($this->disk)->url('uploads/designs/thumbnail/'.$this->image);
        $large = Storage::disk($this->disk)->url('uploads/designs/large/'.$this->image);
        $original = Storage::disk($this->disk)->url('uploads/designs/original/'.$this->image);

        return [
            $large,
            $thumbnail,
            $original
        ];
    }
}
