<?php
/**
 * File: ManagementController.php
 * This file is part of MM2-dev project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

namespace App\Http\Controllers\Shops\Management;

use App;
use App\Http\Controllers\Controller;
use App\Providers\DynamicPropertiesProvider;
use Auth;
use Illuminate\Http\Request;
use View;

class ManagementController extends Controller
{
    /** @var \App\Shop */
    protected $shop;

    protected $propertiesProvider;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('shopactive');
        $this->middleware(function ($request, $next) {
            $this->shop = Auth::user()->shop();
            $this->propertiesProvider = App::make(DynamicPropertiesProvider::class);
            $this->propertiesProvider->register($this->shop->id);
            View::share('shop', $this->shop);
            View::share('propertiesProvider', $this->propertiesProvider);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        return redirect('/shop/management/goods');
    }
}
