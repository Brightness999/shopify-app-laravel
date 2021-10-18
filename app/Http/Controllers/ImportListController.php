<?php

namespace App\Http\Controllers;

use App\ImportList;
use Illuminate\Http\Request;
use App\Products;
use App\MyProducts;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ShopifyBulkPublish;
use App\Settings;
use Illuminate\Support\Facades\DB;


class ImportListController extends Controller
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
        $this->authorize('view-merchant-import-list');
        $prods = Products::select('products.*', 'import_list.id as id_import_list')
            ->join('import_list', 'import_list.id_product', '=', 'products.id')
            ->whereNotIn('import_list.id', MyProducts::where('id_customer', Auth::User()->id)->pluck('id_imp_product'))
            ->where('import_list.id_customer', Auth::user()->id);
        return view('import-list_v2', [
            'total_count' => $prods->count()
        ]);
    }

    public function publishShopify(Request $request)

    {
        $this->authorize('plan_publish-product-import-list');
        $settings = Settings::where('id_merchant', Auth::User()->id)->first();
        $published = false;
        if ($settings != null) {
            $published = $settings->set1 == 1;
        }
        ShopifyBulkPublish::dispatchNow(Auth::User(), [json_encode((object) $request->product)], $published);
        $myproduct = MyProducts::select('id_shopify')
            ->where('id_customer', Auth::User()->id)
            ->where('id_imp_product', $request->product['id'])
            ->first();
        return response()->json([
            'result' => $myproduct != null,
            'id_shopify' => $myproduct != null ? $myproduct->id_shopify : 0
        ]);
    }

    public function publishAllShopify(Request $request)
    {
        $this->authorize('plan_bulk-publish-product-import-list');
        $settings = Settings::where('id_merchant', Auth::User()->id)->first();
        $published = false;
        if ($settings != null) {
            $published = $settings->set1 == 1;
        }
        $rows = [];
        foreach (json_decode($request->products) as $product) {
            $rows[] = [
                'id' => $product->id,
                'sku' => $product->sku,
                'payload' => json_encode($product),
                'user_id' => Auth::User()->id,
                'action' => 'publish'
            ];
        }
        DB::table('temp_publish_products')->insert($rows);
        $temp_publish_products = DB::table('temp_publish_products')
            ->where('user_id', Auth::User()->id)
            ->where('action', 'publish')
            ->whereIn('id', ImportList::where('id_customer', Auth::User()->id)->pluck('id'))
            ->pluck('payload');
        ShopifyBulkPublish::dispatch(Auth::User(), json_decode($temp_publish_products), $published);
    }

    public function checkPublishProducts(Request $request)
    {
        $this->authorize('plan_bulk-publish-product-import-list');
        $product_ids = $request->product_ids;
        $prods = MyProducts::where('id_customer', Auth::User()->id)
            ->whereIn('id_imp_product', $product_ids)
            ->pluck('id_imp_product', 'id_shopify');
        DB::table('temp_publish_products')->where('user_id', Auth::user()->id)->where('action', 'publish')->delete();
        return response()->json([
            'result' => $prods != null && count($prods) != 0,
            'id_shopify' => $prods != null ? $prods : 0
        ]);
    }
}
