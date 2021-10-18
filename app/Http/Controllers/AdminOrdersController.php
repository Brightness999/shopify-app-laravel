<?php

namespace App\Http\Controllers;

use App\Libraries\Magento\MagentoApi;
use Illuminate\Http\Request;
use App\Order;
use App\OrderShippingAddress;
use App\Status;
use App\OrderDetails;
use App\PaymentSession;
use App\User;
use Illuminate\Support\Facades\Auth;

class AdminOrdersController extends Controller
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

    public function index(Request $request)
    {
        $this->authorize('view-admin-orders');

        $order_list = Order::join('order_shipping_address as osa', 'orders.id', 'osa.id_order')
            ->join('status as st1', 'st1.id', 'orders.financial_status')
            ->join('status as st2', 'st2.id', 'orders.fulfillment_status')
            ->join('users as us', 'us.id', 'orders.id_customer');
        if ($request->merchantid != '') {
            $order_list = $order_list->where('orders.id_customer', $request->merchantid);
        }

        return view('admin_orders', [
            'status' => Status::get(),
            'merchant_name' => $request->merchantid != '' ? User::find($request->merchantid)->name : '',
            'total_count' => $order_list->count()
        ]);
    }

    public function show($id_shopify)
    {
        $order = Order::select('*')->where('id_shopify', $id_shopify)->first();
        $osa = OrderShippingAddress::select('order_shipping_address.*')
            ->where('order_shipping_address.id_order', $order->id)
            ->first();

        $fs = Status::select('status.*')
            ->where('status.id', $order->financial_status)
            ->first();

        $os = Status::select('status.*')
            ->where('status.id', $order->fulfillment_status)
            ->first();

        $merchant = User::find($order->id_customer);

        $order_products = OrderDetails::select(
            'order_details.sku',
            'order_details.price',
            'order_details.quantity',
            'products.name',
            'products.images',
        )
            ->join('products', 'order_details.sku', 'products.sku')
            ->where('order_details.id_order', $order->id)->get();

        foreach ($order_products as $pro) {
            if ($pro['images'] != null && count(json_decode($pro['images'])) > 0) {
                $pro->image_url = env('URL_MAGENTO_IMAGES') . '/dc09e1c71e492175f875827bcbf6a37c' . json_decode($pro->images)[0]->file;
            } else {
                $pro->image_url = '/img/default_image_75.png';
            }
        }

        $sessionPay = PaymentSession::where('id_orders', 'like', "%$order->id%")
            ->whereDate('created_at', '>=', $order->created_at)->orderBy('id', 'desc')->first();

        $user_canceled = User::find($order->user_id_canceled);
        $user_canceled_name = '';
        $api = MagentoApi::getInstance();
        $criteria = [
            'searchCriteria[filterGroups][1][filters][0][field]' => 'increment_id',
            'searchCriteria[filterGroups][1][filters][0][value]' => $order->magento_order_id,
            'searchCriteria[filterGroups][1][filters][0][condition_type]' => "eq"
        ];
        $mg_order = $api->query('GET', 'orders', $criteria);

        if ($user_canceled != null) {
            $user_canceled_name = $user_canceled->name;
        }

        return view('admin_orders_detail', [
            'order' => $order,
            'mg_order' => json_decode($mg_order) ? (json_decode($mg_order)->total_count ? json_decode($mg_order)->items[0] : '' ) : '',
            'osa' => $osa,
            'fs' => $fs,
            'os' => $os,
            'payment_intent' => !is_null($sessionPay) ? $sessionPay->payment_intent : '',
            'payment_card_number' => !is_null($sessionPay) ? $sessionPay->card_last4 : '',
            'order_products' => $order_products,
            'merchant' => $merchant,
            'user_canceled' => $user_canceled_name
        ]);
    }

    public function cancel(Order $order)
    {
        $order->fulfillment_status = 9; //canceled
        $order->user_id_canceled = Auth::user()->id;
        $order->canceled_at = date('Y-m-d H:i:s');
        $order->save();

        return redirect('admin/orders');
    }
}
