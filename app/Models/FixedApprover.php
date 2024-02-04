<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedApprover extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'approvers_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function get(User $user)
    {
        return self::select('approvers_id')
            ->where('user_id', $user->id)
            ->first();
    }

    public static function storeApprovers(int $user_id, Array $approversID)
    {
        return self::updateOrCreate([
            'user_id' => $user_id,
        ],[
            'approvers_id' => json_encode($approversID),
        ]);
    }
}
