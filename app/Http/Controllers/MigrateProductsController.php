<?php

namespace App\Http\Controllers;

use App\Jobs\ShopifyBulkDelete;
use App\MyProducts;
use Illuminate\Http\Request;
use App\Products;
use App\User;
use App\Settings;
use App\ImportList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MigrateProductsController extends Controller
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
        $this->authorize('view-merchant-my-products');
        $this->authorize('plan_view-my-products');
        $mig_products = DB::table('temp_migrate_products')
            ->where('user_id', Auth::User()->id);
        $total_count = $mig_products->count();
        $settings = Settings::where('id_merchant', Auth::user()->id)->first();
        if ($settings == null) {
            $settings = new Settings();
            $settings->set8 = 0;
        }
        return view('migrate-products', [
            'total_count' => $total_count,
            'default_profit' => $settings->set8
        ]);
    }

    public function deleteMigrateProduct(Request $request)
    {
        $product_id = $request['product_id'];
        ShopifyBulkDelete::dispatchNow(Auth::User(), $product_id, 'MigrateProducts');
        $result = DB::table('temp_migrate_products')
            ->where('user_id', Auth::user()->id)
            ->where('id_shopify', $product_id[0])
            ->get();
        return response()->json([
            'result' => count($result) == 0,
            'product_id' => $product_id[0]
        ]);
    }

    public function deleteMigrateProducts(Request $request)
    {
        $product_ids = json_decode($request['product_ids']);
        $user = User::find(Auth::user()->id);
        ShopifyBulkDelete::dispatch($user, $product_ids, 'MigrateProducts');
        return 'success';
    }

    public function checkDeleteMigrateProducts(Request $request)
    {
        $product_ids = $request['product_ids'];
        $result = DB::table('temp_migrate_products')
            ->where('user_id', Auth::user()->id)
            ->whereIn('id_shopify', $product_ids)->pluck('id_shopify');
        $data = [];
        foreach ($product_ids as $product_id) {
            $flag = true;
            foreach ($result as $res) {
                if ($product_id == $res) {
                    $flag = false;
                }
            }
            if ($flag) {
                $data[] = $product_id;
            }
        }

        return response()->json([
            'product_ids' => $data
        ]);
    }

    public function confirmMigrateProducts(Request $request)
    {
        $result = [];
        foreach (json_decode($request['products']) as $data) {
            $mig_product = DB::table('temp_migrate_products')
                ->where('user_id', Auth::user()->id)
                ->where('id_shopify', $data->id)->first();
            $product = Products::where('sku', $mig_product->sku)->first();
            if ($product == null) {
                $data->result = false;
            } else {
                $import_product = ImportList::where('id_product', $product->id)
                    ->where('id_customer', Auth::User()->id)->first();
                $my_product = MyProducts::where('id_product', $product->id)
                    ->where('id_customer', Auth::User()->id)->first();
                $flag = false;
                $check_price = false;
                if ($import_product == null) {
                    $import_product = new ImportList();
                    $import_product->id_customer = Auth::User()->id;
                    $import_product->id_product = $product->id;
                    $import_product->save();
                    $flag = true;
                    $check_price = true;
                    $data->result = true;
                } else {
                    if ($my_product == null) {
                        $flag = true;
                        $check_price = true;
                        $data->result = true;
                    } else {
                        DB::table('my_products')
                            ->where('id_product', $product->id)
                            ->update([
                                'id_shopify' => $mig_product->id_shopify,
                                'id_variant_shopify' => $mig_product->id_variant_shopify
                            ]);
                        if ($mig_product->location_id_shopify == Auth::User()->fulfillment_location_id) {
                            $check_price = true;
                            $data->result = true;
                        } else {
                            DB::table('temp_migrate_products')
                                ->where('user_id', Auth::user()->id)
                                ->where('id_shopify', $data->id)
                                ->update(['type' => 'delete']);
                            DB::table('my_products')->where('id_shopify', $mig_product->id_shopify)->delete();
                            $data->result = false;
                        }
                    }
                }
                if ($flag) {
                    $my_product = new MyProducts();
                    $my_product->id_customer = Auth::User()->id;
                    $my_product->id_imp_product = ImportList::where('id_customer', Auth::User()->id)->where('id_product', $product->id)->pluck('id')->first();
                    $my_product->id_product = $product->id;
                    $my_product->id_shopify = $mig_product->id_shopify;
                    $my_product->id_variant_shopify = $mig_product->id_variant_shopify;
                    $my_product->inventory_item_id_shopify = $mig_product->inventory_item_id_shopify;
                    $my_product->location_id_shopify = $mig_product->location_id_shopify;
                    $my_product->stock = $product->stock;
                    $my_product->save();
                }
                if ($check_price) {
                    $price = $product->price * (100 + $my_product->profit) / 100;
                    if (number_format($price, 2, '.', '') != $mig_product->price) {
                        MyProducts::where('id_shopify', $mig_product->id_shopify)
                            ->update([
                                'profit' => $data->profit,
                                'cron' => 1
                            ]);
                        DB::table('temp_migrate_products')
                            ->where('user_id', Auth::User()->id)
                            ->where('id_shopify', $data->id)->delete();
                    } else {
                        DB::table('temp_migrate_products')
                            ->where('user_id', Auth::User()->id)
                            ->where('id_shopify', $data->id)->delete();
                    }
                }
            }
            $result[] = $data;
        }
        return response()->json([
            'products' => $result
        ]);
    }

}
