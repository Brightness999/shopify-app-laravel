@extends('layouts.app')

@section('content')

<div class="indexContent" data-page_name="ORDERS">
    <div class="maincontent">
        <div class="wrapinsidecontent">
            @if($total_count == 0)
            <div class="no-order">
                <h2 class="font-weight-bold">Your orders list is empty!</h2>
                <h4 style="line-height: 1.5;">No orders yet? Go to the Search page to add more products to your import list and get more orders.</h4>
                <h4 style="line-height: 1.5;">When you get an order, you can manage it here and send it to us for processing.</h4>
                <a href="/search-products"><button class="btn btn-lg btn-success greenbutton">Go To Search Products</button></a>
            </div>
            <div class="order-content">
            @else
            @if(Auth::user()->plan == 'free')
            <div class="alertan">
                <div class="agrid">
                    <img src="/img/infogray.png" srcset="/img/infogray@2x.png 2x,/img/infogray@3x.png 3x">
                    <p>You have a free plan. <a href="/plans">Click here to upgrade your plan.</a></p>
                </div>
            </div>
            @endif

            <div class="alertan level2">
                <div class="agrid">
                    <p><strong>You must pay the grand total for each transaction including wholesale price + shipping.</strong></p>
                </div>
                <i class="fa fa-close text-secondary" aria-hidden="true"></i>
            </div>

            @if(Request()->payment=='success')
            <div class="alertan level2">
                <div class="agrid">
                    <p><strong>Success!</strong> Payment successful.</p>
                </div>
                <i class="fa fa-close text-secondary" aria-hidden="true"></i>
            </div>
            @endif

            @if(Request()->payment=='cancel')
            <div class="alertan level2">
                <div class="agrid">
                    <p><strong>Error!</strong> Transaction canceled.
                </div>
                <i class="fa fa-close text-secondary" aria-hidden="true"></i>
            </div>
            @endif

            <div class="headerorders">
                <div class="date">
                    Date
                </div>
                <div class="dates">
                    <label for="">From</label>
                    <input type="date" id="date_from" value="">
                </div>
                <div class="dates">
                    <label for="">To:</label>
                    <input type="date" id="date_to" value="">
                </div>
                <div class="date">
                    Order Number
                </div>
                <div class="search">
                    <input type="text" id="order_id" value="" placeholder="Order Number">
                </div>
                <div class="paymentstatus">
                    <select id="payment_status" name="selectFS">
                        <option value="0">Payment Status</option>
                        @foreach($status as $st)
                        @if($st->type == 1)
                        <option value="{{$st->id}}" {{(Request()->selectFS==$st->id?'selected':'')}}>{{$st->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="orderstate">
                    <select id="order_state" name="selectOS">
                        <option value="0">Order Status</option>
                        @foreach($status as $st)
                        @if($st->type == 2)
                        <option value="{{$st->id}}" {{(Request()->selectOS==$st->id?'selected':'')}}>{{$st->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="searchbtn">
                    <button class="btn-order-search searchbutton greenbutton cel-icon-search">Search</button>
                    <span class="mx-3 my-3 btn-link h5 order-reset" style="text-decoration: underline; cursor: pointer;">Reset</span>
                </div>
            </div>

            @if(Auth::user()->plan == 'basic')
            <div class="results">
                <div><strong>Period</strong></div>
                <div><label id="period" class="my-0"></label></div>
                <div><strong>Total Orders</strong></div>
                <div>
                    <span class="font-weight-bold" id="total_period_orders"></span> of <span class="font-weight-bold" id="total_orders">{{$total_count}}</span>
                </div>
            </div>
            <div class="actions">
                <button class="btn-order-notifications pendingpay simple-tooltip" title="Outstanding orders that require payment"> <span class="badge" id="notifications"></span> Pending Payments</button>
            </div>
            <div class="pagesize">
                <span>Show</span>
                <select name="PageSize" id="page_size">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            @endif

            <div class="orders my-0">
                <table class="greentable tableorders" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="nowrap">
                                ORDER #
                            </th>
                            <th>
                                DATE
                            </th>
                            <th>
                                CUSTOMER NAME
                            </th>
                            <th>
                                TOTAL TO PAY
                            </th>
                            <th>
                                PAYMENT STATUS <span class="simple-tooltip" title="This indicates whether or not your payment has been processed.">?</span>
                            </th>
                            <th>
                                ORDER STATUS <span class="simple-tooltip" title="This is the status of your customer's order.">?</span>
                            </th>
                            <th>
                                PAY ORDER <span class="simple-tooltip" title="You need to pay for the order to submit it to GreenDropShip.">?</span>
                            </th>
                            <th>
                                VIEW
                            </th>
                        </tr>
                    </thead>
                    <tbody id="order_data"></tbody>
                </table>
            </div>
            <div id="pagination"></div>
            </div>
            @endif
        </div>
    </div>
</div>
<input type="text" id="total_count" value="{{$total_count}}" hidden>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#total_count').text("{{$total_count}}");

        $('.btn-order-notifications').click(function() {
            window.location.href = encodeURI('{{url("/orders/")}}?notifications=true');
        });
    });

    $('#order_data').on('click', '.checkout-button', function() {
        let idx = $('.checkout-button').data('id');
        $('.cb' + idx).attr('checked', 'checked');

        let stripe = Stripe('{{env("STRIPE_API_KEY")}}');
        let res = $('input[name=s_method]:checked', '#shipping-methods').val();
        let orders = [$('.checkout-button').attr('data-id')];

        console.log('ordenes... ' + orders);

        // Create a new Checkout Session using the server-side endpoint you
        // created in step 3.
        setLoading();
        fetch('/create-checkout-session', {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            method: 'POST',
            body: JSON.stringify({
                orders: orders
                //shipping: $('input[name=s_method]:checked', '#shipping-methods').val(),
            }),
        })
        .then(function(response) {
            if (response.status == 406) {
                $('#order-limit-modal').modal('show')
            }
            return response.json();
        })
        .then(function(session) {
            return stripe.redirectToCheckout({
                sessionId: session.id
            }).then(function(result) {
                console.log('res', result);
            });
        })
        .then(function(result) {
            // If `redirectToCheckout` fails due to a browser or network
            // error, you should display the localized error message to your
            // customer using `error.message`.
            if (result.error) {
                alert(result.error.message);
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
        });
    });
</script>

@endsection