<?php

namespace App\Http\Controllers;

use App\ShopifyStore;
use App\User;

class AdminMerchantsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('view-admin-merchants');
        $merchants_list = User::select('users.*')
            ->where('role', 'merchant');

        return view('admin_merchants', array(
            'merchants_list' => $merchants_list->get(),
            'total_count' => $merchants_list->count()
        ));
    }

    public function show(User $merchant)
    {
        $this->authorize('view-admin-merchants');
        $shopify_data = ShopifyStore::where('user_id', $merchant->id)->get();
        return view('admin_merchants_detail', array(
            'merchant' => $merchant,
            'shopify_data' => count($shopify_data) ? $shopify_data[0] : []
        ));
    }
}
