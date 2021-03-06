@extends('layouts.app')
@section('content')

<div class="indexContent" data-page_name="ADMIN ORDERS">
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
                    <input type="text" id="idOrder" list="numbers" placeholder="GDS ORDER #">
                    <datalist id="numbers">
                        <div id="number_data"></div>
                    </datalist>
                </div>
                <div class="merchantname">
                    <span class="merchantlabel">Name</span>
                    <input type="text" id="merchant" list="names" placeholder="Merchant">
                    <datalist id="names">
                        <div id="merchant_data"></div>
                    </datalist>
                </div>
                <div class="paymentstatus">
                    <span class="paymentlabel">
                        Payment Status
                    </span>
                    <select id="paymentstatus">
                        <option value="0">Payment Status</option>
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
                        <option value="0">Order State</option>
                        @foreach($status as $st)
                        @if($st->type == 2)
                        <option value="{{$st->id}}" {{(Request()->selectOS==$st->id?'selected':'')}}>{{$st->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="searchbtn">
                    <button id="search" class="searchbutton greenbutton"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
                </div>
            </div>

            <div class="actions my-5">
                <button class="exporsel">Export Selected Orders</button>
                <div></div>
                <div class="pagesize">
                    <span>Show</span>
                    <select name="PageSize" id="page_size">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="orders">
                <table class="greentable tableorders" cellspacing="0">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="check-orders">
                            </th>
                            <th>
                                GDS ORDER #
                            </th>
                            <th>
                                DATE
                            </th>
                            <th>
                                TOTAL TO PAY
                            </th>
                            <th>
                                MERCHANT
                            </th>
                            <th>
                                PAYMENT STATUS <span class="simple-tooltip" title="This indicates whether or not your payment has been processed.">?</span>
                            </th>
                            <th>
                                ORDER STATE <span class="simple-tooltip" title="This is the status of your customer's order.">?</span>
                            </th>
                            <th>
                                VIEW
                            </th>
                        </tr>
                    </thead>
                    <tbody id="order_data">

                        @php $k = 0 @endphp
                        @foreach($order_list as $ol)
                        @if($k == 0)
                        @php
                        $back = 'transparent';
                        $k = 1;
                        @endphp
                        @else
                        @php
                        $back = '';
                        $k = 0;
                        @endphp

                        @endif
                        <tr class="orderrow">
                            <td class="check">
                                <input type="checkbox" class="checkbox" data-id="{{$ol->id}}">
                            </td>
                            <td data-label="ORDER #">
                                {{$ol->magento_order_id}}
                            </td>
                            <td data-label="DATE">
                                {{$ol->created_at}}
                            </td>
                            <td data-label="TOTAL TO PAY">
                                ${{number_format($ol->total, 2, '.', '')}}
                            </td>
                            <td data-label="MERCHANT">
                                {{$ol->merchant_name}}
                            </td>
                            <td>
                                <div class="buttonge" style="background-color: {{$ol->color1}}">{{$ol->status1}}</div>
                            </td>
                            <td>
                                <div class="buttonge" style="background-color: {{$ol->color2}}">{{$ol->status2}}</div>
                            </td>
                            <td>
                                <a href="/admin/orders/{{$ol->id}}">
                                    <button class="view greenbutton">View</button>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="pagination"></div>
<input type="text" id="total_count" value="{{$total_count}}" hidden>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#total_count').text("{{$total_count}}");

        $('#idOrder').keydown(function(e) {
            let length = $('#idOrder').val().length;
            if (e.key) {
                if (e.key.length == 1) {
                    length += 1;
                } else {
                    if (e.code == 'Backspace') {
                        length -= 1;
                    }
                }
                if (length > 2) {
                    $.getJSON(ajax_link, {
                        action: 'admin-order-number',
                        number: $('#idOrder').val().trim()
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
            let length = $('#merchant').val().length;
            if (e.key) {
                if (e.key.length == 1) {
                    length += 1;
                } else {
                    if (e.code == 'Backspace') {
                        length -= 1;
                    }
                }
                if (length > 2) {
                    console.log(length)
                    $.getJSON(ajax_link, {
                        action: 'admin-order-merchant',
                        name: $('#merchant').val().trim()
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

        $('.exporsel').click(function() {
            var ids = [];
            $("input.checkbox:checked").each(function (index, ele) {
                ids.push($(ele).data('id'));
            });
            if (ids.length) {
                window.location.href = `/admin/orders/exports?ids=${JSON.stringify(ids)}`;
            }

        });
    });
</script>
@endsection