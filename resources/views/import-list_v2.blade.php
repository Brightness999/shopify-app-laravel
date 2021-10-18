@extends('layouts.app')

@section('content')

<div class="indexContent" data-page_name="IMPORT LIST">
    <div class="maincontent">
        <div class="wrapinsidecontent">
            @if(Auth::user()->plan == 'free')
            <div class="alertan">
                <div class="agrid">
                    <img src="/img/infogray.png" srcset="/img/infogray@2x.png 2x,/img/infogray@3x.png 3x">
                    <p>You have a free plan. <a href="/plans#planBottom">Click here to upgrade your plan.</a></p>
                </div>
            </div>
            @endif
            <div class="empty-product" style="display: none;">
                <div class="empty-text">
                    <h2 class="my-3"><strong>Your import list is empty!</strong></h2>
                    <h4 style="line-height: 1.5;" class="my-0">Go to Search Products and start adding products to your import list.<br>When your import list is ready, you can add the products to your store.</h4>
                    <a href="/search-products"><button class="btn btn-success btn-lg my-3 greenbutton border-0">Go To Search Products</button></a>
                </div>
                <div>
                    <img src="{{ asset('/img/noproduct.png') }}" alt="No Products">
                </div>
            </div>

            @can("plan_view-my-products")
            @if($total_count > 0)
            <div class="product-menu import-product-menu" id="product-top-menu">
                <div class="sendtoshopify">
                    <div class="checksend">
                        <input type="checkbox" id="check-all-products">
                        <span id="select-all" class="h4 mx-2 my-0 font-weight-bold">Select All</span>
                        <span id="selected-products">0</span>
                    </div>
                    <div class="btn-import-actions">
                        <button class="btn-import-list-delete-all redbutton mx-1 my-1">Delete Selected</button>
                        <button class="btn-import-list-send-all greenbutton mx-1 my-1" id="btn-import-list-send-all">Add Selected to Store</button>
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
            @endif
            @endcan
            <div id="import-products"></div>
            @can("plan_view-my-products")
            @if($total_count > 0)
            <div id="pagination"></div>
            @endif
            @endcan
        </div>
    </div>
</div>
<div class="back-to-top" style="display:none">
    <img src=" {{ asset('/img/back_to_top.png') }}" alt="Back to Top">
    <span style="text-align: center;" class="h5">Back<br>to Top</span>
</div>

<input type="text" id="total_count" value="{{$total_count}}" hidden>
<input type="text" id="modal-type" data-id="" value="" hidden>

<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#check-all-products').click(function() {
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
        });

        $('#btn-import-list-send-all').click(function() {

            //Get all checked products
            let product_ids = [];
            $("input.checkbox:checked").each(function(index, ele) {
                let product_id = $(ele).attr('id').split('-')[1];
                if ($(`.btn-import-list-send-${product_id}`).is(":visible")) {
                    var permission = permission_collection_type(product_id);
                    if (permission) {
                        product_ids.push(product_id);
                    }
                }
            });
            if (product_ids.length > 0) {
                $(this).attr('data-toggle', 'modal');
                $(this).attr('data-target', '#confirm-modal');
                $('#confirm-modal-title').text('Product');
                $('#confirm-modal-body').html(`<h5>Are you sure you want to send ${product_ids.length} ${product_ids.length > 1 ? 'products' : 'product'} to your store?</h5>`);
                $('#confirm-modal-footer').show();
                $('#confirm').text('Add to Store');
                $('#confirm').removeClass('btn-danger');
                $('#confirm').addClass('btn-success');
                $('#confirm').css('background-color', '#44b955');
                $('#modal-type').val('send-products');
                $('#modal-type').data('id', JSON.stringify(product_ids));
            } else {
                $(this).removeAttr('data-toggle');
                $(this).removeAttr('data-target');
            }

        });

        $('.btn-import-list-delete-all').click(function() {
            var product_ids = [];
            $('input.checkbox:checked').each(function(index, ele) {
                let product_id = $(ele).attr('id').split('-')[1];
                product_ids.push(product_id);
            });
            if (product_ids.length > 0) {
                var parameters = {
                    action: 'delete_import_list',
                    id_import_list: product_ids
                }
                product_ids.forEach(product_id => {
                    $(`#delete-${product_id}`).hide();
                    $(`.btn-import-list-send-${product_id}`).hide();
                    $(`.import-list-loading-${product_id}`).show();
                    disableProduct(product_id);
                });
                $('#check-all-products').prop('checked', false);
                $('#check-all-products').prop('disabled', true);
                uncheckAllProducts();
                $.getJSON(ajax_link, parameters, function(data, status) {
                    product_ids.forEach(product_id => {
                        $(`#product${product_id}`).remove();
                        $(`#import-list-delete-banner-${product_id}`).css('display', 'flex');
                    });
                    $('#check-all-products').prop('disabled', false);
                }).fail(function(data, status) {
                    product_ids.forEach(product_id => {
                        $(`.import-list-loading-${product_id}`).hide();
                        $(`#delete-${product_id}`).show();
                        $(`.btn-import-list-send-${product_id}`).show();
                        enableProduct(product_id);
                    });
                    $('#check-all-products').prop('disabled', false);
                    popupFailMsg('A problem has occured while deleting your products.');
                    $('#contact').show();
                })
            }
        })

        $('#confirm').click(function() {
            let action = $('#modal-type').val();
            if (action == 'send-product') {
                sendProduct($('#modal-type').data('id'));
            } else if (action == 'send-products') {
                sendProducts();
            }
            $('#confirm-modal').removeClass('show');
            setTimeout(() => {
                $('#confirm-modal').css('display', 'none');
                $('.modal-backdrop.fade.show').remove();
            }, 150);
        });

        $('#cancel').click(function() {
            if ($('#modal-type').val() == 'collection') {
                $(`#collections${$('#modal-type').data('id')}`).val('');
                $('#modal-type').val('');
            }
            $('#confirm-modal').removeClass('show');
            setTimeout(() => {
                $('#confirm-modal').css('display', 'none');
                $('.modal-backdrop.fade.show').remove();
            }, 150);
        });

        $('#close').click(function() {
            $('#confirm-modal').removeClass('show');
            setTimeout(() => {
                $('#confirm-modal').css('display', 'none');
                $('.modal-backdrop.fade.show').remove();
            }, 150);
        });

        function disableProduct(product_id) {
            $("input.chk-img" + product_id + ":checked").each(function(index, ele) {
                $(ele).prop('disabled', true);
                $(ele).prop('checked', false);
            });
            $(`#check-${product_id}`).prop('checked', false);
            $(`#check-${product_id}`).prop('disabled', true);
            $(`#check-${product_id}`).removeClass();
            $(`#name${product_id}`).prop('disabled', true);
            $(`#collections${product_id}`).prop('disabled', true);
            $(`#tags${product_id}`).prop('disabled', true);
            $(`#types${product_id}`).prop('disabled', true);
            $(`#profit${product_id}`).prop('disabled', true);
            $(`#price${product_id}`).prop('disabled', true);
            $(`#cke_description${product_id}`).css('pointer-events', 'none');
        }

        function enableProduct(product_id) {
            $(`#name${product_id}`).prop('disabled', false);
            $(`#collections${product_id}`).prop('disabled', false);
            $(`#tags${product_id}`).prop('disabled', false);
            $(`#types${product_id}`).prop('disabled', false);
            $(`#profit${product_id}`).prop('disabled', false);
            $(`#price${product_id}`).prop('disabled', false);
            $(`#check-${product_id}`).prop('disabled', false);
            $(`#check-${product_id}`).addClass('checkbox');
            $(`#cke_description${product_id}`).css('pointer-events', 'auto');
            $("input.chk-img" + product_id).each(function(index, ele) {
                $(ele).prop('disabled', false);
                $(ele).prop('checked', true);
            })
        }

        function sendProduct(product_id) {
            let images = [];
            $("input.chk-img" + product_id + ":checked").each(function(index, ele) {
                images.push($('.img' + product_id + '-' + $(ele).attr('data-index')).attr('src'));
                $(ele).prop('disabled', true);
                $(ele).prop('checked', false);
            });
            $(`.btn-import-list-send-${product_id}`).hide();
            $(`.import-list-loading-${product_id}`).show();
            $(`#delete-${product_id}`).hide();
            disableProduct(product_id);
            let product = {
                id: product_id,
                name: $(`#name${product_id}`).val(),
                weight: $(`#weight${product_id}`).text().trim(),
                price: $(`#price${product_id}`).val(),
                cost: $(`#cost${product_id}`).text(),
                description: CKEDITOR.instances[`description${product_id}`].getData(),
                product_type: $(`#types${product_id}`).val().trim(),
                tags: $(`#tags${product_id}`).val().trim(),
                collections: $(`#collections${product_id}`).val().trim(),
                sku: $(`#sku${product_id}`).val(),
                upc: $(`#upc${product_id}`).val(),
                profit: $(`#profit${product_id}`).val(),
                images: images
            };

            $.post('{{url("/publish-product")}}', {
                "_token": "{{ csrf_token() }}",
                product: product
            }, function(data, status) {
                if (data.result) {
                    $(`.import-list-loading-${product_id}`).hide();
                    $(`#sent-msg-${product_id}`).show();
                    $(`.btn-import-list-sent-${product_id}`).show();
                    $(`.btn-import-list-sent-${product_id}`).attr('data-shopifyid', data.id_shopify);
                    $(`#check-${product_id}`).prop('checked', false);
                    showBulkActionButtons();
                } else {
                    enableProduct(product_id);
                    $(`.import-list-loading-${product_id}`).hide();
                    $(`.btn-import-list-send-${product_id}`).show();
                    $(`.btn-import-list-send-${product_id}`).prop('disabled', false);
                    $(`#delete-${product_id}`).show();
                    popupFailMsg('A problem has occured while sending your product to Shopify store.');
                    $('#contact').show();
                    showBulkActionButtons();
                }
            }).fail(function(data, status) {
                enableProduct(product_id);
                $(`.import-list-loading-${product_id}`).hide();
                $(`.btn-import-list-send-${product_id}`).show();
                $(`.btn-import-list-send-${product_id}`).prop('disabled', false);
                $(`#delete-${product_id}`).show();
                popupFailMsg('A problem has occured while sending your product to Shopify store.');
                $('#contact').show();
                showBulkActionButtons();
            });
        }

        function sendProducts() {
            let product_ids = [];
            $("input.checkbox:checked").each(function(index, ele) {
                let product_id = $(ele).attr('id').split('-')[1];
                if ($(`.btn-import-list-send-${product_id}`).is(":visible")) {
                    var permission = permission_collection_type(product_id);
                    if (permission) {
                        product_ids.push(product_id);
                    }
                }
            });
            // let product_ids = JSON.parse($('#modal-type').data('id'));
            let products = [];
            product_ids.forEach(product_id => {
                let images = [];
                $("input.chk-img" + product_id + ":checked").each(function(index, ele) {
                    images.push($('.img' + product_id + '-' + $(ele).attr('data-index')).attr('src'));
                    $(ele).prop('disabled', true);
                    $(ele).prop('checked', false);
                });
                $(`.btn-import-list-send-${product_id}`).hide();
                $(`.import-list-loading-${product_id}`).show();
                $(`#delete-${product_id}`).hide();
                disableProduct(product_id);
                products.push({
                    id: product_id,
                    name: $(`#name${product_id}`).val(),
                    weight: $(`#weight${product_id}`).text().trim(),
                    price: $(`#price${product_id}`).val(),
                    cost: $(`#cost${product_id}`).text(),
                    description: CKEDITOR.instances[`description${product_id}`].getData(),
                    product_type: $(`#types${product_id}`).val(),
                    tags: $(`#tags${product_id}`).val().trim(),
                    collections: $(`#collections${product_id}`).val().trim(),
                    sku: $(`#sku${product_id}`).val().trim(),
                    upc: $(`#upc${product_id}`).val(),
                    profit: $(`#profit${product_id}`).val(),
                    images: images
                });
            });
            window.localStorage.removeItem('send_product_ids');
            window.localStorage.setItem('send_product_ids', JSON.stringify(product_ids));
            $('#selected-products').text(0);
            $('#selected-products').hide();
            $('#check-all-products').prop('disabled', true);
            $('#check-all-products').prop('checked', false);
            $.post('{{url("/publish-all-products")}}', {
                    "_token": "{{ csrf_token() }}",
                    products: JSON.stringify(products)
                }, function(data, status) {})
                .fail(function(data, status) {});
        }

        function publishProductsAjax() {
            let product_ids = JSON.parse(window.localStorage.getItem('send_product_ids'));
            if (product_ids) {
                if (product_ids.length) {
                    $.post('/check-publish-products', {
                        product_ids: product_ids,
                        "_token": "{{ csrf_token() }}",
                    }).then(data => {
                        window.localStorage.removeItem('send_product_ids');
                        if (data.result) {
                            $('.btn-import-list-send-all').prop('disabled', false);
                            $('.btn-import-list-delete-all').show();
                            $('#btn-import-list-send-all').show();
                            $('#check-all-products').prop('disabled', false);
                            for (const key in data.id_shopify) {
                                $(`.btn-import-list-send-${data.id_shopify[key]}`).hide();
                                $(`.import-list-loading-${data.id_shopify[key]}`).hide();
                                $(`#sent-msg-${data.id_shopify[key]}`).show();
                                $(`.btn-import-list-sent-${data.id_shopify[key]}`).show();
                                $(`.btn-import-list-sent-${data.id_shopify[key]}`).attr('data-shopifyid', key);
                                $(`#delete-${data.id_shopify[key]}`).hide();
                                $(`#check-${data.id_shopify[key]}`).prop('checked', false);
                            }
                            showBulkActionButtons();
                        } else {
                            product_ids.forEach(product_id => {
                                enableProduct(product_id);
                                $(`.import-list-loading-${product_id}`).hide();
                                $(`.btn-import-list-send-${product_id}`).show();
                                $(`#delete-${product_id}`).show();
                            });
                            $('#check-all-products').prop('disabled', false);
                            popupFailMsg('A problem has occured while sending your products to your Shopify store.');
                            $('#contact').show();
                            showBulkActionButtons();
                        }
                    });
                }
            }
        }
        publishProductsAjax();
        setInterval(publishProductsAjax, 15000);
    });

    function permission_collection_type(id) {
        var collection_flag = true;
        var type_flag = true;
        if ($(`#collections${id}`).val().indexOf(',') > -1) {
            $(`#collection_error${id}`).show();
            collection_flag = false;
        } else {
            $(`#collection_error${id}`).hide();
            collection_flag = true;
        }
        if ($(`#types${id}`).val().indexOf(',') > -1) {
            $(`#type_error${id}`).show();
            type_flag = false;
        } else {
            $(`#type_error${id}`).hide();
            type_flag = true;
        }
        if (collection_flag && type_flag) {
            return true;
        } else {
            return false;
        }
    }

    $('#import-products').on('click', '.producttabs .thetab', function(e) {
        e.preventDefault();
        $(this).parents(".producttabs").find(".thetab").removeClass("active");
        $(this).addClass("active");
        var thetabid = $(this).attr("href");
        $(this).parents(".producttabs").find(".tabcontent").removeClass("active");
        $(this).parents(".producttabs").find(thetabid).addClass("active");
    });

    $('#import-products').on('click', '.btn-import-list-send', function() {
        let productId = $(this).data('id');
        var permission = permission_collection_type(productId);
        if (permission) {
            $('#confirm-modal-title').text('Product');
            $('#confirm-modal-body').html(`<div style="gap: 1rem;" class="d-flex align-items-center pb-3">
                    <img style="width:75px; height:75px;" src="${$(this).data('img')}"/>
                    <div>
                        <h5 class="font-weight-bold">${$(this).data('name')}</h5>
                        <h5 class="mt-2 mb-0">${$(this).data('sku')}</h5>
                    </div>
                </div>
                <h5 class="my-3">Are you sure you want to publish this product to your Shopify store?</h5>`);
            $('#modal-type').val('send-product');
            $('#modal-type').data('id', $(this).data('id'));
            $(this).attr('data-toggle', 'modal');
            $(this).attr('data-target', '#confirm-modal');
            $('#confirm').text('Add to Store');
            $('#confirm').removeClass('btn-danger');
            $('#confirm').addClass('btn-success');
            $('#confirm').css('background-color', '#44b955');
            $('#confirm-modal-footer').show();

        } else {
            popupFailMsg(`<h5>One product can't have more than one collection or one type.</h5><h5>Please remove the comma(,) in Collection or Type.</h5>`);
            $('#contact').hide();
        }
    });

    $('#import-products').on('click', '.btn-import-list-delete', function() {
        let product_id = $(this).data('id');
        var parameters = {
            action: 'delete_import_list',
            id_import_list: [product_id]
        }
        $(`#delete-${product_id}`).hide();
        $(`.btn-import-list-send-${product_id}`).hide();
        $(`.import-list-loading-${product_id}`).show();
        disableProduct(product_id);
        $.getJSON(ajax_link, parameters, function(data) {
            if (data.result) {
                $(`#product${product_id}`).remove();
                $(`#import-list-delete-banner-${product_id}`).css('display', 'flex');
            } else {
                $(`.import-list-loading-${product_id}`).hide();
                $(`#delete-${product_id}`).show();
                $(`.btn-import-list-send-${product_id}`).show();
                enableProduct(product_id);
                popupFailMsg('A problem has occured while deleting your product.');
                $('#contact').show();
            }
            showBulkActionButtons();
        }).fail(function(data, status) {
            $(`.import-list-loading-${product_id}`).hide();
            $(`#delete-${product_id}`).show();
            $(`.btn-import-list-send-${product_id}`).show();
            enableProduct(product_id);
            popupFailMsg('A problem has occured while deleting your product.');
            $('#contact').show();
            showBulkActionButtons();
        })
    });

    $('#import-products').on('change', '.box-profit', function() {
        var id_product = $(this).data('id');
        var cost = $(`#cost${id_product}`).text();
        var profit = $(this).val();
        if (profit > 0) {
            var value = parseFloat((cost * (100 + profit * 1)) / 100).toFixed(2);
        } else {
            value = cost;
        }

        $(`#price${id_product}`).val(value);
        $(`#price${id_product}`).data('price', value);
    });

    $('#import-products').on('change', '.box-price', function() {
        var id_product = $(this).data('id');
        var cost = $(`#cost${id_product}`).text();
        var precio = $(this).val();
        var value = 0;
        if (precio > 0) {
            value = parseFloat((precio - cost) / cost * 100).toFixed(2);
        }
        $(`#profit${id_product}`).val(value);
        $(`#profit${id_product}`).data('profit', value);
    });

    $('#import-products').on('click', '.edit-in-shopify', function(e) {
        e.preventDefault();
        window.open(`https://{{Auth::user()->shopify_url}}/admin/products/${e.target.dataset.shopifyid}`, '_blank');
    });

    $('#import-products').on('click', '.import-delete-banner', function(e) {
        e.target.parentElement.remove();
    });

    $('#import-products').on('click', '.import-list-undo', function(e) {
        e.preventDefault();
        let parameters = {
            action: 'import-list-undo',
            id: e.target.id
        }
        $.getJSON('/ajax', parameters, function(res) {
            var image_str = '';
            let button_str = `<button class="delete btn-import-list-delete" id="delete-${res.id_import_list}" data-id="${res.id_import_list}" data-name="${res.name}" data-sku="${res.sku}" data-img="${res.delete_image_url}">Delete</button>
                <button class="sendto greenbutton btn-import-list-send btn-import-list-send-${res.id_import_list}" data-id="${res.id_import_list}" data-name="${res.name}" data-sku="${res.sku}" data-img="${res.delete_image_url}">Add to Store</button>
                <img src="/img/loading_1.gif" class="import-list-loading-${res.id_import_list}" style="display:none; ">
                <span id="sent-msg-${res.id_import_list}" class="text-secondary h5 mb-0" style="display:none; cursor: default">Product Added to Store</span>
                <button class="sendto edit-in-shopify btn-import-list-sent-${res.id_import_list}" data-shopifyid="0">Edit in Store</button>`;
            res.images.forEach((image, i) => {
                image_str += `<div class="selectimage">
                    <div class="imagewrap">
                        <img class="img${res.id_import_list}-${i}" src="${image}">
                    </div>
                    <div class="checkim">
                        <input type="checkbox" class="chk-img${res.id_import_list}" data-index="${i}" value="" checked="checked">
                    </div>
                </div>`;
            });
            var collection_str = `<div id="collection_data${res.id_import_list}">`;
            res.collections.forEach(collection => {
                collection_str += `<option value="${collection}">`;
            });
            collection_str += '</div>';
            var type_str = `<div id="type_data${res.id_import_list}">`;
            res.types.forEach(type => {
                type_str += `<option value="${type}">`;
            });
            type_str += '</div>';
            let str = `<div class="productboxelement import-product" id="product${res.id_import_list}" data-id="${res.id_import_list}">
                <h2>${res.name}</h2>
                <div class="producttabs">
                    <div class="headertabs">
                        <div class="checkt">
                            <input type="checkbox" id="check-${res.id_import_list}" class="checkbox" style="display: block; width: 20px; height: 20px;">
                        </div>
                        <div class="tabs">
                            <a href=".tab-1" class="thetab active"> Product </a>
                            <a href=".tab-2" class="thetab"> Description </a>
                            <a href=".tab-3" class="thetab"> Pricing </a>
                            <a href=".tab-4" class="thetab"> Images </a>
                        </div>
                        <div class="buttons import-actions">
                            ${button_str}
                        </div>
                    </div>
                    <div class="contenttabs">
                        <div class="tab-1 wpadding tabcontent active">
                            <div class="productgrid">
                                <div>
                                    <div class="imagewrap">
                                        <img src='${res.image_url}'>
                                    </div>
                                </div>
                                <div>
                                    <h3>
                                        ${res.name}
                                    </h3>
                                    <div class="editform">
                                        <div class="full">
                                            <label for="">Change product name</label>
                                            <input type="text" id="name${res.id_import_list}" value='${res.name}'>
                                        </div>
                                        <div class="full">
                                            <label for="">Collection <span class="simple-tooltip" title="You can assign the product to a Collection in your Shopify store.">?</span></label>
                                            <input type="text" list="collection${res.id_import_list}" id="collections${res.id_import_list}" class="collection" data-id="${res.id_import_list}" value="${res.collection}">
                                            <datalist id="collection${res.id_import_list}">
                                                ${collection_str}
                                            </datalist>
                                            <span id="collection_error${res.id_import_list}" style="color:red; display:none;">One product can have only one collection.</span>
                                        </div>
                                        <div>
                                            <label for="">Type <span class="simple-tooltip" title="You can give this product a classification that will be saved in the 'Product Type' field in Shopify.">?</span></label>
                                            <input type="text" list="type${res.id_import_list}" id="types${res.id_import_list}" class="type" data-id="${res.id_import_list}" value="${res.type}">
                                            <datalist id="type${res.id_import_list}">
                                                ${type_str}
                                            </datalist>
                                            <span id="type_error${res.id_import_list}" style="color:red; display:none;">One product can have only one type.</span>
                                        </div>
                                        <div>
                                            <label for="">Tags <span class="simple-tooltip" title="You can create your own tags separated by commas.">?</span></label>
                                            <input type="text" list="tag${res.id_import_list}" id="tags${res.id_import_list}" class="tag" data-id="${res.id_import_list}" value="${res.tags}">
                                            <datalist id="tag${res.id_import_list}">
                                                <div id="tag_data${res.id_import_list}"></div>
                                            </datalist>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-2 tabcontent wpadding import-content import-description">
                            <textarea class="texteditor editor" name="" id="description${res.id_import_list}" cols="30" rows="10">${res.description}</textarea>
                        </div>
                        <div class="tab-3 tabcontent wpaddingtop">
                            <table class="greentable" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>
                                            SKU <span class="simple-tooltip" title="Do not change this SKU in your Shopify store.">?</span>
                                        </th>
                                        <th>
                                            HEIGHT
                                        </th>
                                        <th>
                                            WIDTH
                                        </th>
                                        <th>
                                            LENGTH
                                        </th>
                                        <th>
                                            WEIGHT
                                        </th>
                                        <th>
                                            COST
                                        </th>
                                        <th>
                                            PROFIT (%) <span class="simple-tooltip" title="Profit excludes shipping charges.">?</span>
                                        </th>
                                        <th>
                                            PRICE
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="productdatarow">
                                        <td data-label="SKU" class="skutd">
                                            <input type="text" id="sku${res.id_import_list}" data-id="${res.id_import_list}" value="${res.sku}" disabled="disabled">
                                            <input type="hidden" id="upc${res.id_import_list}" value="${res.upc}" />
                                        </td>
                                        <td data-label="HEIGHT">
                                            ${res.ship_height}
                                        </td>
                                        <td data-label="WIDTH">
                                            ${res.ship_width}
                                        </td>
                                        <td data-label="LENGTH">
                                            ${res.ship_length}
                                        </td>
                                        <td data-label="WEIGHT" id="weight${res.id_import_list}">
                                            ${res.weight}
                                        </td>
                                        <td data-label="COST" class="w100">
                                            <div class="nowrap">
                                                US $<span id="cost${res.id_import_list}" data-id="${res.id_import_list}">${parseFloat(res.price).toFixed(2)}</span>
                                            </div>
                                        </td>
                                        <td data-label="PROFIT (%) " class="w100">
                                            <span class="simple-tooltip" title="First tooltip">?</span>
                                            <div class="inpupercent">
                                                <input type="number" min="0" style="width: 60px; text-align:right; padding: 0px 3px;" class="box-profit" id="profit${res.id_import_list}" data-id="${res.id_import_list}" value="${res.profit}">%
                                            </div>
                                        </td>
                                        <td data-label="PRICE" class="w100">
                                            <div class="inputprice nowrap">
                                                US $<input type="number" min="0" style="width: 60px; text-align:left; padding: 0px 3px;" class="box-price" id="price${res.id_import_list}" data-price="${res.price}" data-id="${res.id_import_list}" value="${parseFloat(res.price * (100 + res.profit) / 100).toFixed(2)}">
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-4 wpadding tabcontent">
                            <div class="imagesgrid">
                            ${image_str}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="import-list-delete-banner my-3" id="import-list-delete-banner-${res.id_import_list}">
                <span>"${res.name}" has been removed from import list. <a href="#" class="import-list-undo" data-id="#import-list-delete-banner-${res.id_import_list}" id="${res.id}">Undo</a></span>
                <button type="button" class="close import-delete-banner">&times;</button>
            </div>`;
            $(e.target.dataset.id).replaceWith(str);
            Tipped.create('.simple-tooltip');
            CKEDITOR.replace(`description${res.id_import_list}`, {});
        })
    });

    function disableProduct(product_id) {
        let count = 0;
        $(`#check-${product_id}`).prop('checked', false);
        $(`#check-${product_id}`).prop('disabled', true);
        $(`#check-${product_id}`).removeClass();
        $("input.checkbox:checked").each(function(index, ele) {
            count++;
        });
        $('#selected-products').text(count);
        showBulkActionButtons();
    }

    $('#import-products').on('keydown', '.collection', function(e) {
        var product_id = $(this).data('id');
        let length = e.target.value.length;
        let collection = e.target.value;
        if (e.key) {
            if (e.key.length == 1) {
                if (e.key == ',' || e.target.value.indexOf(',') > -1) {
                    $(`#collection_error${product_id}`).show();
                    disableProduct(product_id);
                } else {
                    $(`#collection_error${product_id}`).hide();
                    if ($(`#types${product_id}`).val().indexOf(',') < 0) {
                        $(`#check-${product_id}`).prop('disabled', false);
                        $(`#check-${product_id}`).addClass('checkbox');
                    }
                }
                length += 1;
                collection += e.key;
            } else {
                if (e.key == 'Backspace') {
                    length -= 1;
                    collection = collection.slice(0, -1);
                    if (collection.indexOf(',') > -1) {
                        $(`#collection_error${product_id}`).show();
                        disableProduct(product_id);
                    } else {
                        $(`#collection_error${product_id}`).hide();
                        if ($(`#types${product_id}`).val().indexOf(',') < 0) {
                            $(`#check-${product_id}`).prop('disabled', false);
                            $(`#check-${product_id}`).addClass('checkbox');
                        }
                    }
                }
            }
            if (length > 2 && (e.key.length == 1 || e.key == 'Backspace')) {
                var parameters = {
                    action: 'product_collection',
                    collection: JSON.stringify(collection)
                }
                $.getJSON(ajax_link, parameters, function(data) {
                    var str = `<div id="collection_data${e.target.dataset.id}">`;
                    data.collections.forEach(collection => {
                        str += `<option value="${collection}">`;
                    });
                    str += '</div>';
                    $(`#collection_data${e.target.dataset.id}`).remove();
                    $(`#collection${product_id}`).html(str);
                })
            } else {
                $(`#collection_data${e.target.dataset.id}`).remove();
            }
        }
    });

    $('#import-products').on('blur', '.collection', function(e) {
        if (e.target.value.trim() != '') {
            if (e.target.value.indexOf(',') > -1) {
                popupFailMsg("<h5>One product can't have more than one collection.</h5><h5>Please remove the comma(,).</h5>");
                $('#contact').hide();
            } else {
                let flag = false;
                if ($(`#collection_data${e.target.dataset.id}`).children().length == 0) {
                    flag = true;
                } else {
                    let is_not_exist = true;
                    for (let i = 0; i < $(`#collection_data${e.target.dataset.id}`).children().length; i++) {
                        if ($(`#collection_data${e.target.dataset.id}`).children()[i].value == e.target.value) {
                            is_not_exist = false;
                        }
                    }
                    if (is_not_exist) {
                        flag = true;
                    }
                }
                if (flag) {
                    $('#modal-type').val('collection');
                    $('#modal-type').data('id', e.target.dataset.id);
                    $('body').append('<div class="modal-backdrop fade show"></div>');
                    $('#confirm-modal-title').text("Collection");
                    $('#confirm-modal-body').html("<h5>This collection doesn't exist in your Shopify store.<br>Are you sure you want to add this product to a new collection?</h5>");
                    $('#confirm').text('Add to new Collection');
                    $('#confirm-modal').css('display', 'block');
                    setTimeout(() => {
                        $('#confirm-modal').addClass('show');
                    }, 150);
                    $('#confirm-modal-footer').show();
                }
            }
        }
    });

    $('#import-products').on('blur', '.type', function(e) {
        if (e.target.value.indexOf(',') > -1) {
            popupFailMsg("<h5>One product can't have more than one type.</h5><h5>Please remove the comma(,).</h5>")
            $('#contact').hide();
        }
    });

    $('#import-products').on('keydown', '.type', function(e) {
        var product_id = $(this).data('id');
        let length = e.target.value.length;
        let type = e.target.value;
        if (e.key) {
            if (e.key == ',' || e.target.value.indexOf(',') > -1) {
                $(`#type_error${product_id}`).show();
                disableProduct(product_id);
            } else {
                $(`#type_error${product_id}`).hide();
                if ($(`#collections${product_id}`).val().indexOf(',') < 0) {
                    $(`#check-${product_id}`).prop('disabled', false);
                    $(`#check-${product_id}`).addClass('checkbox');
                }
            }
            if (e.key.length == 1) {
                length += 1;
                type += e.key;
            } else {
                if (e.key == 'Backspace') {
                    length -= 1;
                    type = type.slice(0, -1);
                    if (type.indexOf(',') > -1) {
                        $(`#type_error${product_id}`).show();
                        disableProduct(product_id);
                    } else {
                        $(`#type_error${product_id}`).hide();
                        if ($(`#collections${product_id}`).val().indexOf(',') < 0) {
                            $(`#check-${product_id}`).prop('disabled', false);
                            $(`#check-${product_id}`).addClass('checkbox');
                        }
                    }
                }
            }
            if (length > 2 && (e.key.length == 1 || e.key == 'Backspace')) {
                var parameters = {
                    action: 'product_type',
                    type: type
                }
                $.getJSON(ajax_link, parameters, function(data) {
                    var str = `<div id="type_data${e.target.dataset.id}">`;
                    data.types.forEach(type => {
                        str += `<option value="${type}">`;
                    });
                    str += '</div>';
                    $(`#type_data${e.target.dataset.id}`).remove();
                    $(`#type${product_id}`).html(str);
                })
            } else {
                $(`#type_data${e.target.dataset.id}`).remove();
            }
        }
    });

    $('#import-products').on('keydown', '.tag', function(e) {
        let length = e.target.value.length;
        let tag = e.target.value;
        if (e.key) {
            if (e.key.length == 1) {
                length += 1;
                tag += e.key;
            } else {
                if (e.key == 'Backspace') {
                    length -= 1;
                    tag = tag.slice(0, -1);
                }
            }
            if (length > 2 && (e.key.length == 1 || e.key == 'Backspace')) {
                var parameters = {
                    action: 'product_tag',
                    tag: tag
                }
                var id = $(this).data('id');
                $.getJSON(ajax_link, parameters, function(data) {
                    var str = `<div id="tag_data${e.target.dataset.id}">`;
                    data.tags.forEach(tag => {
                        str += `<option value="${tag}">`;
                    });
                    str += '</div>';
                    $(`#tag_data${e.target.dataset.id}`).remove();
                    $(`#tag${id}`).html(str);
                })
            } else {
                $(`#tag_data${e.target.dataset.id}`).remove();
            }
        }
    });

    $('#import-products').on('click', '.checkbox', function(e) {
        let count = parseInt($('#selected-products').text());
        if (e.target.checked) {
            $('#selected-products').text(count + 1);
        } else {
            if ($('#selected-products').text() > 0) {
                $('#selected-products').text(count - 1);
            }
        }
        showBulkActionButtons();
    });
</script>
@endsection