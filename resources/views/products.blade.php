@extends('layouts.app')

@section('content')
<div class="indexContent" data-page_name="{{$type == 'new' ? 'NEW PRODUCTS' : 'DISCOUNT PRODUCTS'}}">
    <div class="maincontent">
        <div class="wrapinsidecontent">
            <div class="position-relative introduction-content">
                <div class="toolbar">
                    <div id="search-products">
                        <input type="text" value="" id="search-key" placeholder="Search Products">
                        <span><i class="cel-icon-search"></i></span>
                    </div>
                    <div id="sorting">
                        <div class="select-wrapper">
                            <span class="h5 mx-1">Sort By</span>
                            <select id="sort-key">
                                <option value="" selected="selected">Relevancy</option>
                                <option value="a-z">Name: A-Z</option>
                                <option value="z-a">Name: Z-A</option>
                                <option value="l-h">Price: Low to High</option>
                                <option value="h-l">Price: High to Low</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3" style="height: 61px;">
                    <input type="checkbox" class="check-all-products">
                    <span id="select-all" class="h4 mx-2 my-0 font-weight-bold">Select All</span>
                    <span id="selected-products" style="display: none; background-color: #44b955;" class="h5 text-white rounded-circle px-2 py-2 mx-2 my-0">0</span>
                    <img src="/img/loading_1.gif" class="mx-4" style="width:32px; height:32px; display:none" id="import-loading">
                    <button type="submit" class="all-add-products mx-2 my-2 ng-binding">Import Selected</button>
                </div>
                <div>
                    <ul class="introduction-products"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="back-to-top" style="display:none">
    <img src=" {{ asset('/img/back_to_top.png') }}" alt="Back to Top">
    <span style="text-align: center;" class="h5">Back<br>to Top</span>
</div>
<input type="text" value="" id="products-page-number" hidden>
<input type="text" value="{{$type}}" id="product-type" hidden>
<script>
    $(document).ready(function() {
        $('.check-all-products').click(function(e) {
            if (e.target.checked) {
                $('.check-product').prop('checked', true);
            } else {
                $('.check-product').prop('checked', false);
            }
            showBulkImportButton();
        })

        $('.all-add-products').click(function() {
            let product_ids = [];
            $('input.check-product:checked').each(function(index, ele) {
                product_ids.push(ele.dataset.sku);
            })
            if (product_ids.length) {
                $('#confirm').text('Add to Import List');
                $('#confirm-modal-body').html(`<h5>Are you sure you want to add ${product_ids.length} ${product_ids.length == 1 ? 'product' : 'products'} to the import list</h5>`);
                $(this).attr('data-toggle', 'modal');
                $(this).attr('data-target', '#confirm-modal');
            } else {
                $(this).attr('data-toggle', '');
            }
        })

        $('#confirm').click(function() {
            let product_ids = [];
            $(`input.check-product:checked`).each(function(index, ele) {
                product_ids.push(ele.dataset.sku);
            })
            if (product_ids.length) {
                $(`.all-add-products`).hide();
                $(`.check-all-products`).prop('disabled', true);
                $(`.check-all-products`).prop('checked', false);
                $(`#selected-products`).hide();
                $(`#import-loading`).show();
                product_ids.forEach(sku => {
                    $(`#add-${sku}`).text('Adding...');
                    $(`#check-${sku}`).prop('checked', false);
                    $(`#check-${sku}`).prop('disabled', true);
                    $(`#check-${sku}`).removeClass();
                });
                $.getJSON('/ajax',{
                    action:'import-products',
                    skus: JSON.stringify(product_ids)
                }, function(res) {
                    res.skus.forEach(sku => {
                        $(`#add-${sku}`).replaceWith(`<button id="import-${sku}" data-sku="${sku}" class="import-product">Edit in Import List</button>`);
                    });
                    $(`#select-all`).text('Import successful');
                    $(`#select-all`).css('color', '#44b955');
                    $(`#select-all`).show();
                    setTimeout(() => {
                        $(`#select-all`).text('Select All');
                        $(`#select-all`).css('color', '#212529');
                        $(`.all-add-products`).show();
                        $(`.check-all-products`).prop('disabled', false);
                    }, 2000);
                    $(`#selected-products`).hide();
                    $(`#selected-products`).text(0);
                    $(`#import-loading`).hide();
                    showBulkImportButton();
                })
            }
        })

        $('.introduction-content #search-products').keypress(function(e) {
            if (e.key == 'Enter') {
                if ($('#product-type').val() == 'new') {
                    setLoading();
                    newProducts(0);
                } else if ($('#product-type').val() == 'discount') {
                    setLoading();
                    discountProducts(0);
                }
            }
        })

        $('.introduction-content #sorting select').change(function() {
            if ($('#product-type').val() == 'new') {
                setLoading();
                newProducts(0);
            } else if ($('#product-type').val() == 'discount') {
                setLoading();
                discountProducts(0);
            }
        })

        $('.introduction-content #search-products span').click(function() {
            if ($('#product-type').val() == 'new') {
                setLoading();
                newProducts(0);
            } else if ($('#product-type').val() == 'discount') {
                setLoading();
                discountProducts(0);
            }
        })
    })

    $('.introduction-products').on('click', '.add-product', function(e) {
        e.target.innerText = 'Adding...';
        let parameters = {
            action: 'add_import_list',
            sku: e.target.dataset.sku
        }
        $.getJSON('/ajax', parameters, function(res) {
            if (res.result) {
                $(`#check-${e.target.dataset.sku}`).prop('disabled', true);
                $(`#check-${e.target.dataset.sku}`).prop('checked', false);
                $(`#check-${e.target.dataset.sku}`).removeClass();
                $(`#add-${e.target.dataset.sku}`).replaceWith(`<button id="import-${e.target.dataset.sku}" data-sku="${e.target.dataset.sku}" class="import-product">Edit in Import List</button>`);
            } else {
                $(`#check-${e.target.dataset.sku}`).prop('disabled', false);
                $(`#check-${e.target.dataset.sku}`).addClass('check-product');
            }
            showBulkImportButton();
        })
    })

    $('.introduction-products').on('click', '.import-product', function(e) {
        window.open('/import-list');
    })

    $('.introduction-products').on('click', '.check-product', function(e) {
        showBulkImportButton();
    })

    function showBulkImportButton () {
        let count = 0;
        $(`input.check-product:checked`).each(function(index, ele) {
            count++;
        });
        $(`#selected-products`).text(count);
        if ($(`#selected-products`).text() <= 0) {
            $(`.check-all-products`).prop('checked', false);
            $(`#select-all`).show();
            $(`#selected-products`).hide();
        } else {
            if (!$(`.check-all-products`).is(':disabled')) {
                $('.check-all-products').prop('checked', true);
                if ($(`#selected-products`).text() < 10) {
                    $(`#selected-products`).removeClass('py-2');
                    $(`#selected-products`).addClass('py-1');
                } else {
                    $(`#selected-products`).removeClass('py-1');
                    $(`#selected-products`).addClass('py-2');
                }
                $(`#select-all`).hide();
                $(`#selected-products`).show();
            }
        }
    }
</script>
@endsection