<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Transaction\Models\Transaction;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [	
        'first_name',
        'last_name',
        'phone',
        'email',
        'account_balance',
        'pin',
        'verified',
        'account_balance'
    ];

    protected $hidden = [
        'pin',
    ];

    /**
     * Get the user that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function money_sent(): HasMany
    {
        return $this->hasMany(Transaction::class,'sender_id');
    }
    
     /**
     * Get the user that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function money_received(): HasMany
    {
        return $this->hasMany(Transaction::class,'receiver_id');
    }
}
