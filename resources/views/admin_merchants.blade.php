@extends('layouts.app')
@section('content')

<div class="indexContent" data-page_name="ADMIN MERCHANTS">
    <div class="maincontent">
        <div class="wrapinsidecontent">
            <div class="pagesize">
                <span class="h5 my-0">Show</span>
                <select name="PageSize" id="page_size">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="merchants">
                <h3 class="font-weight-bold my-3">Search</h3>
                <table class="searchtable tableorders" cellspacing="0">
                    <thead>
                        <tr>
                            <th>MERCHANT NAME</th>
                            <th>EMAIL</th>
                            <th>SHOPIFY URL</th>
                            <th>PLAN</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="MERCHANT NAME">
                                <input type="text" id="merchant_name" class="merchant-search" list="names">
                                <datalist id="names">
                                    <div id="name_data"></div>
                                </datalist>
                            </td>
                            <td data-label="EMAIL">
                                <input type="text" id="merchant_email" class="merchant-search" list="emails">
                                <datalist id="emails">
                                    <div id="email_data"></div>
                                </datalist>
                            </td>
                            <td data-label="SHOPIFY URL">
                                <input type="text" id="merchant_url" class="merchant-search" list="urls">
                                <datalist id="urls">
                                    <div id="url_data"></div>
                                </datalist>
                            </td>
                            <td data-label="PLAN">
                                <select name="Plan" id="merchant_plan" class="merchant-search">
                                    <option value="" style="color:gray"></option>
                                    <option value="0">None</option>
                                    <option value="free">Free</option>
                                    <option value="basic">Basic</option>
                                    <option value="advanced">Advanced</option>
                                </select>
                            </td>
                            <td data-label="STATUS">
                                <select name="Active" id="merchant_active" class="merchant-search">
                                    <option value="" style="color: gray"></option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="text-center mb-5">
                    <span class="h5 btn-link merchant-reset" style="text-decoration: underline; cursor:pointer;">Reset</span>
                </p>
                <table class="greentable tableorders" cellspacing="0">
                    <thead>
                        <tr>
                            <th>MERCHANT NAME</th>
                            <th>EMAIL</th>
                            <th>SHOPIFY URL</th>
                            <th>PLAN</th>
                            <th>ACTIVE</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody id="merchant_data"></tbody>
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

<input type="text" id="total_count" value="{{$total_count}}" hidden>
<input type="text" id="user_id" hidden>

<script type="text/javascript">
    $(document).ready(function() {
        $('#total_count').text("{{$total_count}}");

        $('#confirm').click(function() {
            var user_id = $('#user_id').val();
            var status = $(`#switch-label${user_id}`).is(':checked') ? false : true;
            var parameters = {
                action: 'change-user-status',
                user_id: user_id,
                active: status ? 1 : 0,
            }
            $.getJSON(ajax_link, parameters, function(res) {
                $(`#switch-label${user_id}`).prop('checked', status);
            })
        });
    });

    $('#merchant_data').on('click', 'input.change-status', function(e) {
        let status = $(this).is(':checked')
        if ($(this).is(':checked')) {
            $(this).prop('checked', false);
        } else {
            $(this).prop('checked', true);
        }
        $('#user_id').val(e.target.dataset.merchantid);
        $('#confirm-modal-body').html(`<h5>Are you sure to ${$(this).is(':checked') ? 'disable' : 'enable'} this merchant?</h5>`);
    });

    $('#merchant_data').on('click', 'button.orders-customers', function(e) {
        window.open(`/admin/orders?merchantid=${e.target.dataset.merchantid}`, '_blank');
    })

    $('#merchant_data').on('click', 'button.detail-merchants', function(e) {
        window.open(`/admin/merchants/show/${e.target.dataset.merchantid}`, '_blank');
    })
</script>
@endsection