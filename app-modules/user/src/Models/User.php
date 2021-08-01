<?php

namespace Modules\User\Models;

use Illuminate\Support\Facades\Hash;
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
        'account_balance',
        'pin_hash',
    ];

    protected $hidden = [
        'pin','pin_hash'
    ];
    const pin = 'pin';
    const pin_hash= 'pin_hash';

    protected static function boot(){
        parent::boot();
        self::creating(function ($model){
            $r= (int)random_int(1001,99998);
            $model->{self::pin} = $r;
            $model->{self::pin_hash} = Hash::make($r);

        });
    }

    protected $columns = ['pin']; // add all columns from you table

    public function scopeExclude($query, $value = []) 
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }
   
    // public static $registerRules = [
    //     'last_name' => 'required',
    //     'first_name' => 'required',
    //     'email' => 'required|email|unique:users',
    //     'phone_number' => 'required|phone_number|unique:users',
    // ];
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
