@extends('layouts.app')
@section('content')
<div class="indexContent" data-page_name="ADMIN ORDER DETAILS">
    <div class="maincontent">
        <div class="wrapinsidecontent">
            <div class="ordertable">
                <div class="otawrap">
                    <div class="twocols2">
                        <div>
                            <div class="box">
                                <div class="cwrap">
                                    <h3>Order Information</h3>
                                    <div class="formg">
                                        <p class="font-weight-bold">Shopify Order ID</p>
                                        <p><a href="https://{{$merchant->shopify_url}}/admin/orders/{{$order->id_shopify}}" target="_blank">{{$order->id_shopify}}</a></p>
                                        <p class="font-weight-bold">Customer Order Number</p>
                                        <p>{{substr($order->order_number_shopify, 1)}}</p>
                                        @if($order->magento_order_id)
                                        <p class="font-weight-bold">GDS Order Number</p>
                                        <p>{{$order->magento_order_id}}</p>
                                        @endif
                                        <p class="font-weight-bold">Date</p>
                                        <p>{{$order->created_at}} {{$order->created_at->tz()}}</p>
                                        <p class="font-weight-bold">Payment Status</p>
                                        <p class="paid text-center" style="background:{{$fs->color}};"><span>{{$fs->name}}</span></p>
                                        <p class="font-weight-bold">Order State</p>
                                        <p class="inprocess text-center" style="background:{{$os->color}};"><span>{{$os->name}}</span></p>

                                        @if($order->fulfillment_status == 9)
                                        <p class="font-weight-bold">Canceld By</p>
                                        <p class="inprocess" style="background-color: transparent;">{{$user_canceled}}</p>
                                        <p class="font-weight-bold">Canceled At</p>
                                        <p class="inprocess" style="background-color: transparent;">{{$order->canceled_at}}</p>
                                        @endif
                                        @if($order->shipping_title)
                                        @if($order->shipping_title)
                                        <p class="font-weight-bold">Shipping</p>
                                        <p>{{$order->shipping_title}}</p>
                                        @if($order->tracking_code)
                                        <p class="font-weight-bold">Tracking Number</p>
                                        <p>{{$order->tracking_code}}</p>
                                        @endif
                                        @endif
                                        @endif

                                    </div>
                                    <h3>Merchant Information</h3>
                                    <div class="formg">
                                        <p class="font-weight-bold">Name</p>
                                        <p>{{$merchant->name}}</p>
                                        <p class="font-weight-bold">Email</p>
                                        <p>{{$merchant->email}}</p>
                                        <p class="font-weight-bold">Plan</p>
                                        <p>{{$merchant->plan}}</p>
                                        
                                    </div>
                                    @if($order->financial_status == 2)
                                    <h3 class="sod-title">Stripe Payment Detail</h3>
                                    <div class="formg">
                                        <p class="font-weight-bold">Date</p><p>{{$order->created_at}} {{$order->created_at->tz()}}</p>
                                        <p class="font-weight-bold">Id Transaction</p><p><a href="https://dashboard.stripe.com/payments/{{$payment_intent}}" target="_blank">{{$payment_intent}}</a></p>
                                    </div>
                                    @endif
                                    <h3>Customer Address</h3>
                                    <div class="formg">
                                        <p class="font-weight-bold">Name</p><p>{{$osa->first_name}} {{$osa->last_name}}</p>
                                        <p class="font-weight-bold">Address</p><p>{{$osa->address1}} {{$osa->address2}}</p>
                                        <p class="font-weight-bold">Zip Code</p><p>{{$osa->zip}}</p>
                                        <p class="font-weight-bold">City</p><p>{{$osa->city}}</p>
                                        <p class="font-weight-bold">State</p><p>{{$osa->province}}</p>
                                        <p class="font-weight-bold">Country</p><p>{{$osa->country}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="rightSide">
                            <div class="box product">
                                <h3>Product Detail</h3>
                                <table class="greentable order-detail" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>IMAGE</th>
                                            <th>PRODUCT NAME</th>
                                            <th>PRICE</th>
                                            <th>QUANTITY</th>
                                            <th>SKU</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach($order_products as $op)
                                        <tr class="order-detail">
                                            <td>
                                                <div class="productphoto">
                                                    <img src="{{$op->image_url}}">
                                                </div>
                                            </td>
                                            <td data-label="PRODUCT NAME" class="product-name">
                                                <span>
                                                    {{$op->name}}
                                                </span>
                                            </td>
                                            <td data-label="PRICE" class="nowrap">
                                                <strong>
                                                    US ${{$op->price}}
                                                </strong>
                                            </td>
                                            <td data-label="QUANTITY">
                                                {{$op->quantity}}
                                            </td>
                                            <td class="sku" data-label="SKU">{{$op->sku}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <table class="resumetable resume my-5" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td><strong>SUB TOTAL</strong></td>
                                            <td>US ${{$order->total}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>SHIPPING & HANDLING</strong></td>
                                            <td>US ${{$order->shipping_price}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>STORE CREDIT</strong></td>
                                            <td>US ${{$mg_order ? number_format($mg_order->grand_total - $mg_order->subtotal - $mg_order->shipping_amount, 2, '.', '') : 0}}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>GRAND TOTAL</strong></td>
                                            <td>US ${{$mg_order ? $mg_order->grand_total : $order->total + $order->shipping_price}}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="notes">
                                    <textarea class="ta{{$order->id}}">{{$order->notes}}</textarea>
                                    <div class="btns">
                                        @if($order->fulfillment_status== 4)
                                        <button class="cancel my-1" id="cancel-button" data-toggle="modal" data-target="#confirm-modal" data-id="{{$order->id}}">Cancel Order</button>
                                        @endif
                                        <button id='btnNotes' data-id="{{$order->id}}" class="btn my-1">Update Notes</button>
                                    </div>
                                    <span class="text-right text-green d-none" id="success-note">The notes have updated successfully.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#cancel-button").click(function() {
            $('#confirm-modal-body').html('<h5>Do you really want to cancel the order?</h5>');
        });
        
        $('#confirm').click(function() {
            window.location.href = "{{url('admin/orders/cancel')}}/" + $('#cancel-button').attr('data-id');
        });
    });
</script>
@endsection