<?php

namespace App;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements Authenticatable
{
    use \Illuminate\Auth\Authenticatable;
	
	//protected $fillable = ['id', 'provider_id', 'provider'];
    public function sites()
    {
    	return $this->hasMany('App\Site');
    }

    protected $fillable = [
        'name','first_name','type','user_type','email','password','active','status','deleted','provider','provider_id','remember_token','email_notification','expired_date','package_id','profile_icon'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function addNew($input)
    {
        $check = static::where('provider_id',$input['provider_id'])->first();
       // print_r($check); exit;
        if(is_null($check)){
            return static::create($input);
        }

        return $check;
    }
}
