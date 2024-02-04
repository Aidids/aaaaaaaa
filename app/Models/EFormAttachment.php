<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EFormAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'e_form_id',
        'name',
        'path',
        'hr_upload'
    ];

    protected $casts = [
        'hr_upload' => 'boolean'
    ];

    public function eform()
    {
        return $this->belongsTo(EForm::class, 'e_form_id');
    }
}
