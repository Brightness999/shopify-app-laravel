@extends('layouts.app')

@section('content')
<div class="indexContent" data-page_name="INTRODUCTION">
    <div class="maincontent">
        <div class="wrapinsidecontent">
            <div class="position-relative">
                <div>
                    <h2 class="font-weight-bold mb-3 mt-5">New Products <a href="/new-products" target="_blank"><span class="font-weight-normal h4 ml-4" style="color: #44b955; cursor:pointer;">View all</span></a></h2>
                    <div id="new-products">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.2" baseProfile="tiny-ps" viewBox="0 0 80 146" width="40" height="146" class="left-arrow d-none">
                            <defs>
                                <image width="79" height="146" id="img1" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAE8AAACSCAMAAAAzSmdFAAAAAXNSR0IB2cksfwAAAKJQTFRFAAAA9fX16urq9/f3+Pj4NDQ0DAwM4+Pj+vr6Ozs7AAAAHx8f5+fm+fn5QkJCAAAABAQEKysr6urqPz8/JCQkGxsb6urqNzc3+Pj4Pz8/+/v7Nzc3S0tL9fX1RkZG9PT0Ozs79PT09/f3S0tLQkJC9vb2/Pz89/f3RkZGaWlp8/PzRkZGS0tL8/Pz9vb2/f396urqQkJC9fX18/PzRkZGKysrzSkcjQAAADZ0Uk5TAAkSAwbJ8hoFw/7fFwa8///SFb/a4xTGB74Ex7IKtwvCCgixuwkDB7iSDLmzAwECE7oBC7bTqYWmgAAAA4dJREFUeJzNWu1y0zAQVAsttoJzgQD+SlU1dUoIpdAG3v/VkJ3IOv3e7UzzADun1d7u6RxjSL+LS/OOhRV+76+uPxDhitLaxUcaXLWU1Uo+fWbBrQPcSuQLB7AqT3AiX78x4NYRLgDicMUywdkahmvKBCdtB1fXK7jNDQpXqcOKu4Wry7irULhGHda6Aq7OK7jNHQqnubMtfhWtgvOwUCp9WLelVudg7rpMKHB1mVDwJus03D1+s4OCG3Dd6auo4a5oan2zcM9mTXYPH7bQ3LU7FK7x3CbT3G3g6vImg92409zVDQrX1FbJGG8yDUdIskFXx3XjmuDGqjoHX0XVWmaSZU3mCU1mmW5cZELBm0wLBU+yjDvCyFO/3sjDTjLyyPMAw73myIPPxoo78fjIow87wNwVNVcoSyp3b3zkKakjT/6uwN245DaZno03b63J8ncFPvL01CbL3hXcx/vbG3kyg8KbLKtuQ3Vj9ruixd24574ruG6stzzC3fKIw8dFvXDDm+y7PuwehjM/EtzKHmA40wQhn+FW8hOWijGHdoYTeYQP/GscyyJcYBCWS7BRN8MF/RE4LFzkMNyJw+/YHGqZSSSEpHnatulO7Po3XuHIYSTR+j84YNFK0jXuMcZs/czhipAf4VKGCBeO3MMePclGYjdbR9DhQxaZBMDxRRRJtEuCbM4cTmXannAp4TU+k0iRTeFtItERPlhVSthSEji8fU7CljVuDi83rU3CxhNv2lYlYe8ZBqt2LtLDY/lpETELm5DK5ujt7LCEievM4Vk5dklJPZtSr6dciko9hmwOtU2phy85QwREYY8cMlqvcDalXktIvW5cxEZzYEwOu8RhEDY8DAdh/1Xm0MOrk6mXVeoRPuB3XgmbknrhUmYhMoal43M0hyBsgmOHYUmlHv6YPbkNNfWm93EUdk3kcDKHJW6wLzuvzIHm2NEcGDoMjj3rWvCP3NMoMo8OnHdKlnqUClXqrQmpV6jUs57hNm1KPcJ6IE892RMMthmSsKUnRECWegPFsZU5OELr6dQTyjvFpdSTkjCKFIOk1NsQONQfq0ippxZBjGHpUr31hODY48fIROIjJfUkkTgcCRXq1CsJrReXaSdzuIDxnnZ+hgscMhzbz3AiawKHnUvTnFzheDr1ZEHAM5c+wlH+fzrtviY40v9jw5H3I9w/Ftzk2LLgwQUOr64nuP8YhZK37dJF7wAAAABJRU5ErkJggg=="/>
                            </defs>
                            <use id="Background" href="#img1" x="1" y="0"/>
                        </svg>
                        <ul class="introduction-products new-products"></ul>
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.2" baseProfile="tiny-ps" viewBox="0 0 80 146" width="40" height="146" class="right-arrow d-none">
                            <defs>
                                <image width="80" height="146" id="img2" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFAAAACSCAMAAADl770gAAAAAXNSR0IB2cksfwAAAMBQTFRFAAAA4+Pj6urq4+PjAAAANzc3+Pj44+PjGxsbOzs7+/v7JCQkAAAAQkJC+fn54+PjHx8f8/PzFBQUNzc39/f3BAQEPz8/6urqNzc3+vr6/f39Pz8/9vb2S0tL9fX1RkZG8/PzNDQ0S0tLOzs79fX1RkZGQkJCPz8/6urq6urq6urq9PT0+fn59vb29vb2+Pj48/PzRkZG/Pz89/f39fX15+fmaWlpQkJCNDQ06urq5+fm5+fmKysr+Pj49PT0BAQEcScp0wAAAEB0Uk5TABcPG/7FBxrjwwTa/7wGHN8M6scI/78RxgUCvgmyCrcLybHCCba7vRIVEwoFAQgGCrgDBwgYkb3KEBMW0wEG+vXzXVQAAAMeSURBVHic5ZrrdtowEISdRiExETSEm8EEGwscl6Q0kN5vef+3qgWVtf7tj3NIqweYszu7MyMZgmB/zt4E6DlXFy0S7/JKqbAN4l1rrVWnS+G9vdF6j9hj8M5v9eGo/oDAO7v6izccqhHR9ThyeLbrCYA4jR1eiRjOAMTZncOzPBKTmSd66IjUyPaki2owEI+tpBpMySNR4zyrBqOVIXicxdVgIBV2LyoeoQ1PM4dH8bhcObwSMSd4bBmHVyLGcwBxeu82vOTREB5erKrBlCokPHyZVIMpeSRqbEeqIlLF7wDEyara8JJHYnvWK+EUI8Ip0lh5p8gRDzfKOwXC4yATThERs35wPNoNz4nJFJHyWZgTGz7Nlc/CPqHrWSayMEI8XGShSvAsjAnEAZ6FS5mFzIb7LBxqRIWPRmRhCGjmfVHLQqLGvYejWTiJ6Cxs5yILCR6DIhFZiPC4yUUWdogarYdXRCIb/iETG45kYVHLQgLxyQin6GAe7vo2BI8bkYU6Irp+yH0WMu+ZJyOyEHnPtBOfhTokdL3MlCcSec9YD3dE6i3RteNxX+iI0PUm87c9zWRhIpwiJza8a5TIQkKFj/c+C3Wf4HHseLSFIu+ZTaJ8Fm6JfdxZHt1GIllo3zM+C5EvSDILEQ9/jkUWbgnEaSw2HPHwZ5mFyA3A8bjHRTz8oxFOgXj4LvEWrkMkC+9EFvaRr1y1LCS+un4SFq4+N8crvng8Dby4vAKtS+wa46WxwDPN92a8kP02r2+cSzygvhp/zZMgNRIPqE/cxXTSvL5C9guk8zpn+00XCp1vEcN4kj/TfJ97sN7WNf5OT29rWG898RsW8eWlMGy/Bd2vnAfwDu8t4H7hfanrDeDPsPwVcv9wf6b5a46H603633+nN2BfjunPtN6A3wPq/nx6eqvdhwi9Ldj6jqo3/D4E7Av8/qjp7RT9+TXp7dT9mZjvUe9DJ6i32n0IeH+8pvsQrrd//z6UfmX9hX6vHvX9AezLN/g+GXy/QfVWnvUtqLcD4jVZnz0/ftqmkV/9DufXZckj+W/n38H4Bfm92J4/2PGPT36XusgAAAAASUVORK5CYII="/>
                            </defs>
                            <use id="Background" href="#img2" x="0" y="0"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h2 class="font-weight-bold mb-3 mt-5">Discount Products <a href="/discount-products" target="_blank"><span class="font-weight-normal h4 ml-4" style="color: #44b955; cursor:pointer;">View all</span></a></h2>
                    <div id="discount-products">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.2" baseProfile="tiny-ps" viewBox="0 0 80 146" width="40" height="146" class="left-arrow d-none">
                            <defs>
                                <image width="79" height="146" id="img1" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAE8AAACSCAMAAAAzSmdFAAAAAXNSR0IB2cksfwAAAKJQTFRFAAAA9fX16urq9/f3+Pj4NDQ0DAwM4+Pj+vr6Ozs7AAAAHx8f5+fm+fn5QkJCAAAABAQEKysr6urqPz8/JCQkGxsb6urqNzc3+Pj4Pz8/+/v7Nzc3S0tL9fX1RkZG9PT0Ozs79PT09/f3S0tLQkJC9vb2/Pz89/f3RkZGaWlp8/PzRkZGS0tL8/Pz9vb2/f396urqQkJC9fX18/PzRkZGKysrzSkcjQAAADZ0Uk5TAAkSAwbJ8hoFw/7fFwa8///SFb/a4xTGB74Ex7IKtwvCCgixuwkDB7iSDLmzAwECE7oBC7bTqYWmgAAAA4dJREFUeJzNWu1y0zAQVAsttoJzgQD+SlU1dUoIpdAG3v/VkJ3IOv3e7UzzADun1d7u6RxjSL+LS/OOhRV+76+uPxDhitLaxUcaXLWU1Uo+fWbBrQPcSuQLB7AqT3AiX78x4NYRLgDicMUywdkahmvKBCdtB1fXK7jNDQpXqcOKu4Wry7irULhGHda6Aq7OK7jNHQqnubMtfhWtgvOwUCp9WLelVudg7rpMKHB1mVDwJus03D1+s4OCG3Dd6auo4a5oan2zcM9mTXYPH7bQ3LU7FK7x3CbT3G3g6vImg92409zVDQrX1FbJGG8yDUdIskFXx3XjmuDGqjoHX0XVWmaSZU3mCU1mmW5cZELBm0wLBU+yjDvCyFO/3sjDTjLyyPMAw73myIPPxoo78fjIow87wNwVNVcoSyp3b3zkKakjT/6uwN245DaZno03b63J8ncFPvL01CbL3hXcx/vbG3kyg8KbLKtuQ3Vj9ruixd24574ruG6stzzC3fKIw8dFvXDDm+y7PuwehjM/EtzKHmA40wQhn+FW8hOWijGHdoYTeYQP/GscyyJcYBCWS7BRN8MF/RE4LFzkMNyJw+/YHGqZSSSEpHnatulO7Po3XuHIYSTR+j84YNFK0jXuMcZs/czhipAf4VKGCBeO3MMePclGYjdbR9DhQxaZBMDxRRRJtEuCbM4cTmXannAp4TU+k0iRTeFtItERPlhVSthSEji8fU7CljVuDi83rU3CxhNv2lYlYe8ZBqt2LtLDY/lpETELm5DK5ujt7LCEievM4Vk5dklJPZtSr6dciko9hmwOtU2phy85QwREYY8cMlqvcDalXktIvW5cxEZzYEwOu8RhEDY8DAdh/1Xm0MOrk6mXVeoRPuB3XgmbknrhUmYhMoal43M0hyBsgmOHYUmlHv6YPbkNNfWm93EUdk3kcDKHJW6wLzuvzIHm2NEcGDoMjj3rWvCP3NMoMo8OnHdKlnqUClXqrQmpV6jUs57hNm1KPcJ6IE892RMMthmSsKUnRECWegPFsZU5OELr6dQTyjvFpdSTkjCKFIOk1NsQONQfq0ippxZBjGHpUr31hODY48fIROIjJfUkkTgcCRXq1CsJrReXaSdzuIDxnnZ+hgscMhzbz3AiawKHnUvTnFzheDr1ZEHAM5c+wlH+fzrtviY40v9jw5H3I9w/Ftzk2LLgwQUOr64nuP8YhZK37dJF7wAAAABJRU5ErkJggg=="/>
                            </defs>
                            <use id="Background" href="#img1" x="1" y="0"/>
                        </svg>
                        <ul class="introduction-products discount-products"></ul>
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.2" baseProfile="tiny-ps" viewBox="0 0 80 146" width="40" height="146" class="right-arrow d-none">
                            <defs>
                                <image width="80" height="146" id="img2" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFAAAACSCAMAAADl770gAAAAAXNSR0IB2cksfwAAAMBQTFRFAAAA4+Pj6urq4+PjAAAANzc3+Pj44+PjGxsbOzs7+/v7JCQkAAAAQkJC+fn54+PjHx8f8/PzFBQUNzc39/f3BAQEPz8/6urqNzc3+vr6/f39Pz8/9vb2S0tL9fX1RkZG8/PzNDQ0S0tLOzs79fX1RkZGQkJCPz8/6urq6urq6urq9PT0+fn59vb29vb2+Pj48/PzRkZG/Pz89/f39fX15+fmaWlpQkJCNDQ06urq5+fm5+fmKysr+Pj49PT0BAQEcScp0wAAAEB0Uk5TABcPG/7FBxrjwwTa/7wGHN8M6scI/78RxgUCvgmyCrcLybHCCba7vRIVEwoFAQgGCrgDBwgYkb3KEBMW0wEG+vXzXVQAAAMeSURBVHic5ZrrdtowEISdRiExETSEm8EEGwscl6Q0kN5vef+3qgWVtf7tj3NIqweYszu7MyMZgmB/zt4E6DlXFy0S7/JKqbAN4l1rrVWnS+G9vdF6j9hj8M5v9eGo/oDAO7v6izccqhHR9ThyeLbrCYA4jR1eiRjOAMTZncOzPBKTmSd66IjUyPaki2owEI+tpBpMySNR4zyrBqOVIXicxdVgIBV2LyoeoQ1PM4dH8bhcObwSMSd4bBmHVyLGcwBxeu82vOTREB5erKrBlCokPHyZVIMpeSRqbEeqIlLF7wDEyara8JJHYnvWK+EUI8Ip0lh5p8gRDzfKOwXC4yATThERs35wPNoNz4nJFJHyWZgTGz7Nlc/CPqHrWSayMEI8XGShSvAsjAnEAZ6FS5mFzIb7LBxqRIWPRmRhCGjmfVHLQqLGvYejWTiJ6Cxs5yILCR6DIhFZiPC4yUUWdogarYdXRCIb/iETG45kYVHLQgLxyQin6GAe7vo2BI8bkYU6Irp+yH0WMu+ZJyOyEHnPtBOfhTokdL3MlCcSec9YD3dE6i3RteNxX+iI0PUm87c9zWRhIpwiJza8a5TIQkKFj/c+C3Wf4HHseLSFIu+ZTaJ8Fm6JfdxZHt1GIllo3zM+C5EvSDILEQ9/jkUWbgnEaSw2HPHwZ5mFyA3A8bjHRTz8oxFOgXj4LvEWrkMkC+9EFvaRr1y1LCS+un4SFq4+N8crvng8Dby4vAKtS+wa46WxwDPN92a8kP02r2+cSzygvhp/zZMgNRIPqE/cxXTSvL5C9guk8zpn+00XCp1vEcN4kj/TfJ97sN7WNf5OT29rWG898RsW8eWlMGy/Bd2vnAfwDu8t4H7hfanrDeDPsPwVcv9wf6b5a46H603633+nN2BfjunPtN6A3wPq/nx6eqvdhwi9Ldj6jqo3/D4E7Av8/qjp7RT9+TXp7dT9mZjvUe9DJ6i32n0IeH+8pvsQrrd//z6UfmX9hX6vHvX9AezLN/g+GXy/QfVWnvUtqLcD4jVZnz0/ftqmkV/9DufXZckj+W/n38H4Bfm92J4/2PGPT36XusgAAAAASUVORK5CYII="/>
                            </defs>
                            <use id="Background" href="#img2" x="0" y="0"/>
                        </svg>
                    </div>
                </div>
                <div class="my-5">
                    <a href="/search-products"><button class="btn btn-lg btn-success greenbutton border-0">Go To Search Products</button></a>
                </div>
                <div>
                    <div id="all-products">
                        <ul class="introduction-products all-products"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="back-to-top" style="display:none">
    <img src=" {{ asset('/img/back_to_top.png') }}" alt="Back to Top">
    <span style="text-align: center;" class="h5">Back<br>to Top</span>
</div>
<input type="text" value="" id="new-products-page-number" hidden>
<input type="text" value="" id="discount-products-page-number" hidden>
<input type="text" value="" id="all-products-page-number" hidden>
<input type="text" value="" id="products-page-size" hidden>

<script>
    $('#new-products').on('click', '.right-arrow', function(e) {
        introductionNewProducts(parseInt($('#new-products-page-number').val()) + 1);
    })

    $('#discount-products').on('click', '.right-arrow', function(e) {
        introductionDiscountProducts(parseInt($('#discount-products-page-number').val()) + 1);
    })

    $('#new-products').on('click', '.left-arrow', function(e) {
        if ($('#new-products-page-number').val() > 0) {
            introductionNewProducts(parseInt($('#new-products-page-number').val()) - 1);
        }
    })

    $('#discount-products').on('click', '.left-arrow', function(e) {
        if ($('#discount-products-page-number').val() > 0) {
            introductionDiscountProducts(parseInt($('#discount-products-page-number').val()) - 1);
        }
    })
</script>
@endsection