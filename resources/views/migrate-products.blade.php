@extends('layouts.app')

@section('content')
<div class="indexContent" data-page_name="MERGE INVENTORY">
    <div class="maincontent">
        <div class="wrapinsidecontent">
            <div class="alertan level2 alert-publish-single fail" style="display: none;">
                <div class="agrid">
                    <span class="text-danger">Deleting your product from Shopify store was failed.</span>
                </div>
            </div>
            @if(Auth::user()->plan == 'free')
            <div class="alertan">
                <div class="agrid">
                    <img src="img/infogray.png" srcset="img/infogray@2x.png 2x,img/infogray@3x.png 3x">
                    <p>You have a free plan. <a href="/plans">Click here to upgrade your plan.</a></p>
                </div>
            </div>
            @endif

            <div class="alertan level2" id="migration-top-text" style="display: none;">
                <div class="agrid">
                    <p></p>
                </div>
                <i class="fa fa-close text-secondary" aria-hidden="true"></i>
            </div>
            <div class="migrate-products" style="display: block;">
                <button class="btn-migration migration greenbutton" style="display: none;">Merge</button>
                <div class="product-menu migration-menu" id="product-top-menu" style="display: none;">
                    <div class="sendtoshopify">
                        <div class="checksend">
                            <input type="checkbox" id="check-all-products" value="" data-mark="false">
                            <span id="select-all" class="h4 mx-2 my-0 font-weight-bold">Select All</span>
                            <span id="selected-products">0</span>
                        </div>
                        <div class="btn-import-actions">
                            <button class="btn-delete-products alldeletebutton redbutton mx-1">Delete</button>
                            <button class="btn-confirm-products allconfirmbutton greenbutton mx-1">Update all pricing</button>
                            <button class="simple-tooltip btn-set-profit profit greenbutton mx-1" title="This would set the profit margin to the default profit margin under Settings or to the profit margin you have updated on this page.">Update profit margin</button>
                            <button class="btn-setting-profit profit greenbutton mx-1" style="display: none;">Updating...</button>
                        </div>
                    </div>
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
                <table class="greentable" id="migration-table" cellspacing="0" style="display: none;">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Cost</th>
                            <th>Profit</th>
                            <th>Price</th>
                            <th>SKU</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="product_data"></tbody>
                </table>
            </div>
            <div id="pagination" style="display: none;"></div>
        </div>
    </div>
</div>
<div class="back-to-top" style="display:none">
    <img src=" {{ asset('/img/back_to_top.png') }}" alt="Back to Top">
    <span style="text-align: center;" class="h5">Back<br>to Top</span>
</div>

<div id="migrate-products-modal" class="modal fade" role="dialog" data-backdrop="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="display:block">
                <button type="button" class="close" id="merge-close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Merging Inventory</h4>
            </div>
            <div class="modal-body" id="migration-body" style="text-align:left;">
                <progress id="migrating-progress" max="100" value="0" style="width:100%;">0%</progress>
                <p id="percentage"></p>
            </div>
        </div>
    </div>
</div>

<input type="text" id="total_count" value="{{$total_count}}" hidden>
<input type="text" id="modal-type" data-id="" value="" hidden>
<script type="text/javascript">
    $(document).ready(function() {

        $('#confirm').click(function() {
            switch ($('#modal-type').val()) {
                case 'set-profit':
                    setProfit();
                    break;
                case 'confirm-product':
                    confirmProduct();
                    break;
                case 'confirm-products':
                    confirmProducts();
                    break;
                case 'delete-product':
                    deleteProduct();
                    break;
                case 'delete-products':
                    deleteProducts();
                    break;
                default:
                    break;
            }
        });

        function setProfit() {
            $('.btn-set-profit').hide();
            $('.btn-delete-products').prop('disabled', true);
            $('.btn-confirm-products').prop('disabled', true);
            $('.btn-confirm-product').prop('disabled', true);
            $('.btn-setting-profit').show();
            var parameters = {
                action: 'set-default-profit',
                shopify_ids: $('#modal-type').data('id')
            }
            $.getJSON(ajax_link, parameters, function(res) {
                JSON.parse($('#modal-type').data('id')).forEach(product_id => {
                    $(`#profit-${product_id}`).val("{{$default_profit}}");
                    var cost = $(`#cost-${product_id}`).text();
                    var profit = "{{$default_profit}}";
                    $(`#price-${product_id}`).val(`${parseFloat(cost.substr(4) * (1 + profit / 100)).toFixed(2)}`);
                });
                $('.btn-setting-profit').hide();
                $('.btn-set-profit').show();
                $('.btn-delete-products').prop('disabled', false);
                $('.btn-confirm-product').prop('disabled', false);
                $('.btn-confirm-products').prop('disabled', false);
            })
        }

        function confirmProduct() {
            let product_id = $('#modal-type').data('id');
            $(`#check-${product_id}`).prop('disabled', true);
            $(`#confirm-${product_id}`).hide();
            $(`#loading-${product_id}`).show();
            $(`#check-${product_id}`).removeClass();
            $(`#check-${product_id}`).prop('disabled', true);
            $.post('{{url("/confirm-migrate-products")}}', {
                "_token": "{{ csrf_token() }}",
                products: JSON.stringify([{
                    id: product_id,
                    profit: $(`#profit-${product_id}`).val()
                }])
            }, function(data, status) {
                data.products.forEach(product => {
                    $(`#confirm-${product.id}`).hide();
                    $(`#loading-${product.id}`).hide();
                    $(`#check-${product.id}`).prop('checked', false);
                    $(`#profit-${product.id}`).replaceWith(`<span>${$(`#profit-${product.id}`).val()}</span>`);
                    $(`#price-${product.id}`).replaceWith(`<span>${$(`#price-${product.id}`).val()}</span>`);
                    if (product.result) {
                        $('#check-all-products').prop('disabled', false);
                        $('.btn-confirm-products').prop('disabled', false);
                        $('.btn-delete-products').prop('disabled', false);
                        $('.btn-set-profit').prop('disabled', false);
                        $(`#confirmed-${product.id}`).show();
                        $(`#check-${product.id}`).prop('checked', false);
                    } else {
                        $(`#delete-${product.id}`).show();
                        $(`#check-${product.id}`).prop('disabled', false);
                        $(`#check-${product.id}`).addClass('checkbox');
                        if ($('#selected-products').text() > 0) {
                            $('#selected-products').text($('#selected-products').text() - 1);
                        }
                    }
                    showBulkActionButtons();
                });
            });
        }

        function confirmProducts() {
            let products = JSON.parse($('#modal-type').data('id'));
            $('.btn-confirm-products').prop('disabled', true);
            $('#check-all-products').prop('disabled', true);
            $('#check-all-products').prop('checked', false);
            $('.btn-delete-products').prop('disabled', true);
            $('.btn-set-profit').prop('disabled', true);
            products.forEach(product => {
                $(`#check-${product.id}`).prop('disabled', true);
                $(`#confirm-${product.id}`).hide();
                $(`#loading-${product.id}`).show();
                $(`#check-${product.id}`).removeClass();
                $(`#check-${product.id}`).prop('disabled', true);
            });
            $.post('{{url("/confirm-migrate-products")}}', {
                "_token": "{{ csrf_token() }}",
                products: JSON.stringify(products),
            }, function(data, status) {
                $('#check-all-products').prop('disabled', false);
                $('.btn-confirm-products').prop('disabled', false);
                $('.btn-delete-products').prop('disabled', false);
                $('.btn-set-profit').prop('disabled', false);
                data.products.forEach(product => {
                    $(`#confirm-${product.id}`).hide();
                    $(`#loading-${product.id}`).hide();
                    $(`#check-${product.id}`).prop('checked', false);
                    $(`#profit-${product.id}`).replaceWith(`<span>${$(`#profit-${product.id}`).val()}</span>`);
                    $(`#price-${product.id}`).replaceWith(`<span>${$(`#price-${product.id}`).val()}</span>`);
                    if (product.result) {
                        $(`#confirmed-${product.id}`).show();
                        $(`#check-${product.id}`).prop('checked', false);
                    } else {
                        $(`#delete-${product.id}`).show();
                        $(`#confirmed-${product.id}`).hide();
                        $(`#check-${product.id}`).prop('disabled', false);
                        $(`#check-${product.id}`).addClass('checkbox');
                    }
                });
                uncheckAllProducts();
            });
        }

        function deleteProduct() {
            let product_id = $('#modal-type').data('id');
            $(`#check-${product_id}`).prop('disabled', true);
            $(`#check-${product_id}`).prop('checked', false);
            $(`#delete-${product_id}`).hide();
            $(`#loading-${product_id}`).show();
            $(`#check-${product_id}`).removeClass();
            $.post('{{url("/delete-migrate-product")}}', {
                "_token": "{{ csrf_token() }}",
                product_id: [product_id]
            }, function(data, status) {
                if (data.result) {
                    uncheckAllProducts();
                    let parameters = {
                        action: getAction(),
                        page_size: $('#page_size').val(),
                        page_number: $('.page_number.selected').text()
                    }
                    getData(parameters);
                }
            }).fail(function() {
                uncheckAllProducts();
                $(`#check-${product_id}`).prop('disabled', false);
                $(`#loading-${product_id}`).hide();
                $(`#delete-${product_id}`).show();
                $(`#check-${product_id}`).addClass('checkbox');
                popupFailMsg('A problem has occured while deleting your product.')
            });
        }

        function deleteProducts() {
            let product_ids = JSON.parse($('#modal-type').data('id'));
            $('#check-all-products').prop('disabled', true);
            $('#check-all-products').prop('checked', false);
            $('.btn-delete-products').prop('disabled', true);
            $('.btn-confirm-products').prop('disabled', true);
            $('.btn-set-profit').prop('disabled', true);
            product_ids.forEach(product_id => {
                $(`#check-${product_id}`).prop('disabled', true);
                $(`#check-${product_id}`).prop('checked', false);
                $(`#delete-${product_id}`).hide();
                $(`#loading-${product_id}`).show();
                $(`#check-${product_id}`).removeClass();
            });
            window.localStorage.removeItem('confirm_product_ids');
            window.localStorage.setItem('confirm_product_ids', JSON.stringify(product_ids));
            uncheckAllProducts();
            $.post('{{url("/delete-migrate-products")}}', {
                "_token": "{{ csrf_token() }}",
                product_ids: JSON.stringify(product_ids),
            }, function(data, status) {
            }).fail(function() {
                $('#check-all-products').prop('disabled', false);
                $('.btn-delete-products').prop('disabled', false);
                $('.btn-confirm-products').prop('disabled', false);
                $('.btn-set-profit').prop('disabled', false);
                product_ids.forEach(product_id => {
                    $(`#check-${product_id}`).prop('disabled', false);
                    $(`#check-${product_id}`).addClass('checkbox');
                    $(`#loading-${product_id}`).hide();
                    $(`#delete-${product_id}`).show();
                });
                popupFailMsg('A problem has occured while deleting your products.')
            });
        }

        var user_id = "{{Auth::user() ? Auth::user()->id : 0}}";

        function deleteProductsAjax() {
            let product_ids = JSON.parse(window.localStorage.getItem('confirm_product_ids'));
            if (product_ids) {
                if (user_id && product_ids.length) {
                    $.post('/check-delete-migrate', {
                        "_token": "{{ csrf_token() }}",
                        product_ids: product_ids,
                    }).then(res => {
                        window.localStorage.removeItem('confirm_product_ids');
                        $('#check-all-products').prop('disabled', false);
                        $('.btn-delete-products').prop('disabled', false);
                        $('.btn-confirm-products').prop('disabled', false);
                        $('.btn-set-profit').prop('disabled', false);
                        let parameters = {
                            action: getAction(),
                            page_size: $('#page_size').val(),
                            page_number: $('.page_number.selected').text()
                        }
                        getData(parameters);
                    });
                }
            }
        }
        deleteProductsAjax();
        setInterval(deleteProductsAjax, 15000);
    });

    $('.migrate-products').on('click', '.btn-mp-delete', function(e) {
        $('#confirm-modal-title').text('Delete product');
        $('#confirm-modal-body').html(`<h5>Deleting this product will delete it from your Shopify store.</h5>`);
        $('#modal-type').val('delete-product');
        $('#modal-type').data('id', e.target.dataset.id);
        $('#confirm').text('Delete');
        $('#confirm').removeClass('btn-success');
        $('#confirm').addClass('btn-danger');
        $('#confirm').css('background-color', '#F72525');
        $('#cancel').show();
    });

    $('.migrate-products').on('click', '#check-all-products', function() {
        if ($('#check-all-products').is(':checked')) {
            $('.checkbox').prop('checked', true);
            let count = 0;
            $("input.checkbox:checked").each(function(index, ele) {
                count++;
            });
            $('#selected-products').text(count);
        } else {
            $('.checkbox').prop('checked', false);
            $('#selected-products').text(0);
        }
        showBulkActionButtons();
    })

    $('.migrate-products').on('click', '.btn-delete-products', function() {
        let product_ids = [];
        let count_confirm_product = 0;
        $("input.checkbox:checked").each(function(index, ele) {
            let product_id = $(ele).attr('id').split('-')[1];
            if ($(`#delete-${product_id}`).is(":visible")) {
                product_ids.push(product_id);
            } else {
                count_confirm_product++;
            }
        });

        if (count_confirm_product > 0) {
            $(this).attr('data-toggle', 'modal');
            $(this).attr('data-target', '#confirm-modal');
            $('#confirm-modal-title').text('Confirm');
            $('#confirm-modal-body').html('<h5>Please select only the products to delete.</h5>');
            $('#modal-type').val('product');
            $('#confirm').text('Confirm');
            $('#confirm').removeClass('btn-danger');
            $('#confirm').addClass('btn-success');
            $('#confirm').css('background-color', '#44b955');
            $('#cancel').hide();
        } else {
            if (product_ids.length == 0) {
                $(this).removeAttr('data-toggle');
                $(this).removeAttr('data-target');
            } else {
                $(this).attr('data-toggle', 'modal');
                $(this).attr('data-target', '#confirm-modal');
                $('#confirm-modal-title').text('Delete product');
                $('#confirm-modal-body').html(`<h5>${product_ids.length == 1 ? 'Deleting this product will delete it from your Shopify store.' : 'Deleting '+ product_ids.length +' products will delete them from your Shopify store.'}</h5>`);
                $('#modal-type').val('delete-products');
                $('#modal-type').data('id', JSON.stringify(product_ids));
                $('#confirm').text('Delete');
                $('#confirm').removeClass('btn-success');
                $('#confirm').addClass('btn-danger');
                $('#confirm').css('background-color', '#F72525');
                $('#cancel').show();
            }
        }
    });

    $('.migrate-products').on('click', '.btn-set-profit', function() {
        let count_delete_product = 0;
        let count_confirm_product = 0;
        let confirm_product_ids = [];
        $("input.checkbox:checked").each(function(index, ele) {
            if ($(`#confirm-${ele.dataset.id}`).is(':visible')) {
                count_confirm_product++;
                confirm_product_ids.push(ele.dataset.id);
            } else {
                count_delete_product++;
            }
        });
        if (count_delete_product > 0) {
            $(this).attr('data-toggle', 'modal');
            $(this).attr('data-target', '#confirm-modal');
            $('#confirm-modal-title').text('Confirm');
            $('#confirm-modal-body').html(`<h5>Please select only the products to update.</h5>`);
            $('#modal-type').val('product');
            $('#confirm').removeClass('btn-danger');
            $('#confirm').addClass('btn-success');
            $('#confirm').css('background-color', '#44b955');
            $('#cancel').hide();
        } else {
            if (count_confirm_product > 0) {
                $(this).attr('data-toggle', 'modal');
                $(this).attr('data-target', '#confirm-modal');
                $('#confirm-modal-title').text('Update profit margin');
                $('#confirm-modal-body').html(`<h5>Are you sure you want to update the margin of ${count_confirm_product} ${count_confirm_product > 1 ? 'products' : 'product'} to {{$default_profit}}%?</h5>`);
                $('#modal-type').val('set-profit');
                $('#modal-type').data('id', JSON.stringify(confirm_product_ids));
                $('#confirm').text('Update');
                $('#confirm').removeClass('btn-danger');
                $('#confirm').addClass('btn-success');
                $('#confirm').css('background-color', '#44b955');
                $('#cancel').show();
            } else {
                $(this).removeAttr('data-toggle');
                $(this).removeAttr('data-target');
            }
        }
    });

    $('.migrate-products').on('click', '.btn-confirm-product', function(e) {
        let profit = $(`#profit-${e.target.dataset.id}`).val();
        $('#confirm-modal-title').text('Update price');
        $('#confirm-modal-body').html(`<h5>Are you sure you want to update this product margin to ${profit}%?</h5>`);
        $('#modal-type').val('confirm-product');
        $('#modal-type').data('id', e.target.dataset.id);
        $('#confirm').text('Update');
        $('#confirm').removeClass('btn-danger');
        $('#confirm').addClass('btn-success');
        $('#confirm').css('background-color', '#44b955');
        $('#cancel').show();
    });

    $('.migrate-products').on('click', '.btn-confirm-products', function() {
        let products = [];
        let count_delete_product = 0;
        $("input.checkbox:checked").each(function(index, ele) {
            let product_id = $(ele).attr('id').split('-')[1];
            let profit = $(`#profit-${$(this).data('id')}`).val();
            if ($(`#confirm-${product_id}`).is(":visible")) {
                products.push({
                    id: product_id,
                    profit: profit
                });
            } else {
                count_delete_product++;
            }
        });

        if (count_delete_product > 0) {
            $(this).attr('data-toggle', 'modal');
            $(this).attr('data-target', '#confirm-modal');
            $('#confirm-modal-title').text('Confirm');
            $('#confirm-modal-body').html(`<h5>Please select only the products to update.</h5>`);
            $('#modal-type').val('product');
            $('#confirm').text('Confirm');
            $('#confirm').removeClass('btn-danger');
            $('#confirm').addClass('btn-success');
            $('#confirm').css('background-color', '#44b955');
            $('#cancel').hide();
        } else {
            if (products.length == 0) {
                $(this).removeAttr('data-toggle');
                $(this).removeAttr('data-target');
            } else {
                $(this).attr('data-toggle', 'modal');
                $(this).attr('data-target', '#confirm-modal');
                $('#confirm-modal-title').text('Update price');
                $('#confirm-modal-body').html(`<h5>Are you sure you want to update the price of ${products.length} ${products.length == 1 ? 'product' : 'products'}?</h5>`);
                $('#modal-type').val('confirm-products');
                $('#modal-type').data('id', JSON.stringify(products));
                $('#confirm').text('Update');
                $('#confirm').removeClass('btn-danger');
                $('#confirm').addClass('btn-success');
                $('#confirm').css('background-color', '#44b955');
                $('#cancel').show();
            }
        }
    });

    $('.migrate-products').on('change', '.box-profit', function() {
        var id_product = $(this).data('id');
        var cost = $('#cost-' + id_product).text();
        var profit = $(this).val();
        var price = cost.substr(4);
        if (profit > 0) {
            price = parseFloat(cost.substr(4) * (100 + profit * 1) / 100).toFixed(2);
        }
        $.getJSON(ajax_link, {
            action: 'change-profit',
            sku: $(this).data('sku'),
            profit: profit
        }, function(res) {
            $('#price-' + id_product).val(price);
        })
    });

    $('.migrate-products').on('change', '.box-price', function() {
        var id_product = $(this).data('id');
        var cost = $('#cost-' + id_product).text();
        var price = $(this).val();
        var profit = $('#profit-' + id_product).val();
        if (price > 0) {
            profit = parseFloat(( price - cost.substr(4) ) / cost.substr(4) * 100).toFixed(2);
        }
        $.getJSON(ajax_link, {
            action: 'change-profit',
            sku: $(this).data('sku'),
            profit: profit
        }, function(res) {
            $('#profit-' + id_product).val(profit);
        })
    });

    $('.migrate-products').on('click', '.checkbox', function(e) {
        let count = parseInt($('#selected-products').text());
        if (e.target.checked) {
            $('#selected-products').text(count + 1);
        } else {
            $('#selected-products').text(count - 1);
        }
        showBulkActionButtons();
    });
</script>
@endsection