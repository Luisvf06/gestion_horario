<?php

namespace App\Models;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Ausencia extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $dates = ['fecha'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
