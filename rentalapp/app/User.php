<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table    = 'users';
    protected $fillable = [
        'TenantId', 'Username', 'password', 'LastPasswordUpdate', 'IsAdmin', 'LastUpdateBy', 'LastUpdateIPAddress', 'CreatedBy', 'CreatedIPAddress', 'LastLogin', 'LastLoginIPAddress', 'EmailAddress', 'Roles', 'AccountStatus', 'account_type', 'interest_rate',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $dates = ['deleted_at'];
    public function company_name()
    {
        return $this->hasOne(Tenants::class, 'Id', 'TenantId');
    }

    public function tenants()
    {
        return $this->belongsTo('App\Tenants', 'TenantId', 'Id');
    }

    public function role()
    {
        return $this->belongsToMany(User::class, 'role_user', 'user_id', 'role_id');
    }

    public function staff_members()
    {
        return $this->hasOne(StaffMember::class, 'user_id', 'id');
    }
    public function partnerAssignment()
    {
        return $this->hasMany('App\PartnerAssignment', 'PartnerId', 'id');
    }

    public function partners_list()
    {
        return $this->hasMany(User::class, 'TenantId', 'TenantId')->where('isAdmin', 0)->where('Roles', 3);
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'Tenant_Id', 'TenantId');
    }

    public function partner_settings()
    {
        return $this->hasOne(PartnerSetting::class, 'partner_id', 'id');
    }

    public function tenant_settings()
    {
        return $this->hasOne(TenantSettings::class, 'UserId', 'id');
    }

    public function transaction_details()
    {
        return $this->hasOne(TransactionDetails::class, 'user_id', 'id')->where('status', 1);
    }
    public function siteAccountShareholder()
    {
        return $this->hasMany("App\SiteAccountShareholder", "PartnerId", "id");
    }
}
