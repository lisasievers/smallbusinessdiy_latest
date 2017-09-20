<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use Session;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $base_url=env('APP_PROJECT_URL');
        //$user_id=Auth::user()->id;
        $user_id = Session::get('email');
        view()->share('cdata', ['Small Business DIY', $base_url, 'Lisa','Small Business DIY. All Rights Reserved.',$user_id]);
        view()->share('topmenu', array('Home' => $base_url,'About Us' => $base_url.'/#aboutus','Products' => $base_url.'/#product','Resources' => $base_url.'/#resource','Blog' => $base_url.'/blog'));
        view()->share('bmenu1', array('Account' => $base_url.'/#','Support' => $base_url.'/#','Product Catalog' => $base_url.'/#'));
        view()->share('bmenu2', array('Sitemap' => $base_url.'/#','Find Domain' => $base_url.'/#','Whois Search' => $base_url.'/#'));
        view()->share('tools', array('sitedoctor' => $base_url.'/siteresponsive','sitedoctor_compare' => $base_url.'/siteresponsive/home/compare','sitespy' => $base_url.'/siteanalysis/home/login','website-review' => $base_url.'/review','atoz' => 'http://azseo.smallbusinessdiy.com','sitebuilder' => $base_url.'/sitebuilder/dashboard','sitespy_website' => $base_url.'/siteanalysis'));
        view()->share('page','');
        view()->share("data",array('page' => ''));

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
