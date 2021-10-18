@extends('layouts.app')
@section('content')

<div class="indexContent" @if($merchant_name != '') data-page_name="ADMIN ORDERS ({{$merchant_name}})" @else data-page_name="ADMIN ORDERS" @endif>
    <div class="maincontent">
        <div class="wrapinsidecontent">

            <div class="headerorders adminorders-settings" id="searchFilters">
                <div class="date">
                    Date
                </div>
                <div class="dates">
                    <label for="">From</label>
                    <input type="date" id="dateFrom">
                </div>
                <div class="dates">
                    <label for="">To:</label>
                    <input type="date" id="dateTo">
                </div>
                <div class="ordernumber">
                    <span class="ordernumberlabel">Order Number</span>
                    <input type="text" id="idOrder" list="numbers">
                    <datalist id="numbers">
                        <div id="number_data"></div>
                    </datalist>
                </div>
                <div class="merchantname">
                    <span class="merchantlabel">Name</span>
                    <input type="text" id="merchant" list="names">
                    <datalist id="names">
                        <div id="merchant_data"></div>
                    </datalist>
                </div>
                <div class="paymentstatus">
                    <span class="paymentlabel">
                        Payment Status
                    </span>
                    <select id="paymentstatus">
                        <option value="0"></option>
                        @foreach($status as $st)
                        @if($st->type == 1)
                        <option value="{{$st->id}}" {{(Request()->selectFS==$st->id?'selected':'')}}>{{$st->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="orderstate">
                    <span class="orderlabel">
                        Order State
                    </span>
                    <select id="orderstate">
                        <option value="0"></option>
                        @foreach($status as $st)
                        @if($st->type == 2)
                        <option value="{{$st->id}}" {{(Request()->selectOS==$st->id?'selected':'')}}>{{$st->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="searchbtn">
                    <button id="search" class="searchbutton greenbutton cel-icon-search">Search</button>
                    <span class="mx-3 my-3 btn-link h5 admin-order-reset" style="text-decoration: underline; cursor: pointer;">Reset</span>
                </div>
            </div>

            <div class="actions my-4">
                <div class="pagesize">
                    <span class="h5 my-0">Show</span>
                    <select name="PageSize" id="page_size">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="orders">
                <table class="greentable tableorders orders" cellspacing="0">
                    <thead>
                        <tr>
                            <th>SHOPIFY ORDER ID</th>
                            <th>CUSTOMER ORDER NUMBER</th>
                            <th>GDS ORDER NUMBER</th>
                            <th>DATE</th>
                            <th>TOTAL TO PAY</th>
                            <th>MERCHANT</th>
                            <th>PAYMENT STATUS <span class="simple-tooltip" title="This indicates whether or not your payment has been processed.">?</span></th>
                            <th>ORDER STATE <span class="simple-tooltip" title="This is the status of your customer's order.">?</span></th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="order_data"></tbody>
                </table>
            </div>
            <div id="pagination"></div>
        </div>
    </div>
</div>
<div class="back-to-top" style="display:none">
    <img src=" {{ asset('/img/back_to_top.png') }}" alt="Back to Top">
    <span style="text-align: center;" class="h5">Back<br>to Top</span>
</div>

<input type="text" id="total_count" value="{{$total_count ? $total_count : 0}}" hidden>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#total_count').text("{{$total_count ? $total_count : 0}}");

        $('#idOrder').keydown(function(e) {
            let length = e.target.value.length;
            let number = e.target.value;
            if (e.key) {
                if (e.key.length == 1) {
                    length += 1;
                    number += e.key;
                } else {
                    if (e.code == 'Backspace') {
                        length -= 1;
                        number = number.slice(0, -1);
                    }
                }
                if (length > 2 && (e.key.length == 1 || e.key == 'Backspace')) {
                    $.getJSON(ajax_link, {
                        action: 'admin-order-number',
                        number: number
                    }, function(data) {
                        var str = '<div id="number_data">';
                        data.numbers.forEach(number => {
                            str += `<option value="${number}">`;
                        });
                        str += '</div>';
                        $('#number_data').remove();
                        $('#numbers').html(str);
                    })
                } else {
                    $('#number_data').remove();
                }
            }
        })

        $('#merchant').keydown(function(e) {
            let length = e.target.value.length;
            let merchant = e.target.value;
            if (e.key) {
                if (e.key.length == 1) {
                    length += 1;
                    merchant += e.key;
                } else {
                    if (e.code == 'Backspace') {
                        length -= 1;
                        merchant = merchant.slice(0, -1);
                    }
                }
                if (length > 2 && (e.key.length == 1 || e.key == 'Backspace')) {
                    $.getJSON(ajax_link, {
                        action: 'admin-order-merchant',
                        name: merchant
                    }, function(data) {
                        var str = '<div id="merchant_data">';
                        data.names.forEach(name => {
                            str += `<option value="${name}">`;
                        });
                        str += '</div>';
                        $('#merchant_data').remove();
                        $('#names').html(str);
                    })
                } else {
                    $('#merchant_data').remove();
                }
            }
        })

        $('#check-orders').click(function (event) {
            if (event.target.checked) {
                $('.checkbox').prop('checked', true);
            } else {
                $('.checkbox').prop('checked', false);
            }
        })
    });
</script>
@endsection