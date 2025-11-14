@extends('layouts.app')
@section('content')
<?php
$baseURL = getBaseURL();
$setting = getSettingsInfo();
$base_color = '#6ab04c';
if (isset($setting->base_color) && $setting->base_color) {
    $base_color = $setting->base_color;
}
?>
<link rel="stylesheet" href="{{ getBaseURL() }}frequent_changing/css/pdf_common.css">
<section class="main-content-wrapper">
    @include('utilities.messages')
    <section class="content-header">
        <div class="row">
            <div class="col-md-6">
                @if($obj->current_stock == 0 && $obj->float_stock > 0)
                    <h2 class="top-left-header">Material Return Entry</h2>
                @else
                    <h2 class="top-left-header">Material Return</h2>
                @endif
            </div>
            <div class="col-md-6">
                @if($obj->current_stock == 0 && $obj->float_stock > 0 && empty($stock_return))
                @else
                <a href="javascript:void();" target="_blank" class="btn bg-second-btn print_material_return"
                    data-id="{{ $obj->id }}"><iconify-icon icon="solar:printer-broken"></iconify-icon>
                    @lang('index.print')</a>
                @endif
                {{-- <a href="{{ route('download-material-return', encrypt_decrypt($obj->id, 'encrypt')) }}" target="_blank"
                class="btn bg-second-btn print_btn"><iconify-icon icon="solar:cloud-download-broken"></iconify-icon>
                @lang('index.download')</a> --}}
                <a class="btn bg-second-btn" href="{{ route('material_stocks.index') }}"><iconify-icon icon="solar:round-arrow-left-broken"></iconify-icon>@lang('index.back')</a>
            </div>
        </div>
    </section>
    @if(isset($obj))
        @if($obj->current_stock == 0 && $obj->float_stock > 0 && empty($stock_return))
        <div class="box-wrapper">
            <div class="table-box">
                <!-- form start -->
                {!! Form::model(isset($obj) && $obj ? $obj : '', [
                    'id' => 'material_return_form',
                    'method' => 'POST',
                    'route' => ['material_stocks.stockReturn'],
                ]) !!}
                @csrf
                <div>
                    <div class="row">
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.mat_type') </label>
                                @if(isset($obj))
                                <input type="hidden" name="stock_id" value="{{ $obj->id }}">
                                <input type="hidden" name="mat_type" value="{{ $obj->mat_type}}">
                                <select class="form-control @error('mat_type') is-invalid @enderror select2" id="mat_type" {{ isset($obj) ? 'disabled' : ''}}>
                                    <option value="">@lang('index.select')</option>
                                    @foreach ($material_types as $value)
                                        <option {{ (isset($obj->mat_type) && $obj->mat_type == $value->id) || old('mat_type') == $value->id ? 'selected' : '' }} value="{{ $value->id }}">{{ $value->type_name }}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.material_category') </label>
                                @if(isset($obj))
                                <input type="hidden" name="mat_cat_id" class="form-control" id="mat_cat_id" placeholder="@lang('index.material_category') " value="{{ isset($obj) && $obj->mat_cat_id!='' ? $obj->mat_cat_id : old('mat_cat_id') }}" readonly>
                                <select class="form-control @error('mat_cat_id') is-invalid @enderror select2" id="mat_cat_id" {{ isset($obj) ? 'disabled' : '' }}>
                                    <option value="">@lang('index.select')</option>
                                    @foreach ($mat_categories as $value)
                                        <option
                                            {{ (isset($obj->mat_cat_id) && $obj->mat_cat_id == $value->id) || old('mat_cat_id') == $value->id ? 'selected' : '' }}
                                            value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                                @endif
                                <div class="text-danger d-none"></div>
                                @error('mat_cat_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.raw_material_name') (Code) </label>
                                @if(isset($obj))
                                    <input type="hidden" name="mat_id" class="form-control" value="{{ isset($obj) && $obj->mat_id!='' ? $obj->mat_id : old('mat_id') }}" readonly>
                                    <select tabindex="4" class="form-control @error('mat_id') is-invalid @enderror select2 select2-hidden-accessible" id="mat_id" {{ isset($obj) ? 'disabled' : '' }}>
                                        <option value="">@lang('index.select')</option>
                                        @foreach($materials as $rm)
                                            <option value="{{ $rm->id.'|'.$rm->name.'|'.$rm->code }}" {{ isset($obj) && $rm->id === $obj->mat_id ? 'selected' : '' }}>{{ $rm->name }} ({{ $rm->code }})</option>
                                        @endforeach
                                    </select>
                                @endif
                                <div class="text-danger d-none"></div>
                                @error('mat_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.po_no') </label>
                                <input type="hidden" name="reference_no" class="form-control" value="{{ isset($obj) && $obj->reference_no!='' ? $obj->reference_no : old('reference_no') }}" readonly>
                                <input type="hidden" name="line_item_no" class="form-control" value="{{ isset($obj) && $obj->line_item_no!='' ? $obj->line_item_no : old('line_item_no') }}" readonly>
                                <input type="text" class="form-control" value="{{ isset($obj->reference_no) ? $obj->reference_no.'/'.$obj->line_item_no : old('reference_no') }}" placeholder="@lang('index.po_no')" readonly>
                                <div class="text-danger d-none"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.floating_stock') </label>
                                <input type="number" class="form-control @error('float_stock') is-invalid @enderror" name="float_stock" id="float_stock" value="{{ isset($obj->float_stock) ? $obj->float_stock : old('float_stock') }}" placeholder="@lang('index.floating_stock')" min="0" {{ isset($obj) ? 'readonly' : '' }}>
                                <div class="text-danger d-none"></div>
                                @error('float_stock')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>Return Quantity <span class="required_star">*</span></label>
                                <input type="text" class="form-control @error('qty') is-invalid @enderror" name="qty" id="qty" value="{{ isset($obj->qty) ? $obj->qty : old('qty') }}" placeholder="Return Quantity">
                                <div class="text-danger d-none"></div>
                                @error('qty')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-12 col-md-6 mb-2 d-flex gap-3">
                            <button type="submit" name="submit" value="submit" class="btn bg-blue-btn"><iconify-icon
                                    icon="solar:check-circle-broken"></iconify-icon>@lang('index.submit')</button>
                            <a class="btn bg-second-btn" href="{{ route('material_stocks.index') }}"><iconify-icon
                                    icon="solar:round-arrow-left-broken"></iconify-icon>@lang('index.back')</a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        @else
        <section class="content" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
            <div style="width: 98%; max-width: 1200px; margin: 30px auto;">
                <div style="padding: 18px 0; border: 1px solid #000; background: #fff;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <div style="flex: 1; text-align: center;">
                            <img src="{!! getBaseURL() .
                            (isset(getWhiteLabelInfo()->logo) ? 'uploads/white_label/' . getWhiteLabelInfo()->logo : 'images/logo.png') !!}" alt="Logo Image" class="img-fluid my-2">
                            <!-- <h3 style="font-size: 25px; font-weight: 500; margin: 2px 0;">{{ strtoupper(getCompanyInfo()->company_name) }}</h3> -->
                            <p style="font-size: 15px; margin-top: 0px; margin-bottom: 1px;">An ISO 9001:2008 Certified Company</p>
                            <p style="font-size: 16px; margin: 0;">{{ safe(getCompanyInfo()->address) }}
                            </p>
                        </div>
                    </div>
                    <div
                        style="display: flex; align-items: start; justify-content: space-between; font-size: 16px; margin:0px 25px  2px 10px; flex-wrap: wrap">
                        <div style="display: flex; align-items: center;">
                            <p>GSTIN No.: </p><b style="margin-left: 10px">{{ safe(getCompanyInfo()->gst_no) }}</b>
                        </div>
                        <div>
                            <span style="display: flex; align-items: center;">
                                <p style="margin: 5px; display: flex; justify-content: space-between; width: 30%; white-space: nowrap">SSl. No
                                    <span>:</span>
                                </p> <b style="margin-left: 10px">{{ safe(getCompanyInfo()->ssi_no) }}</b>
                            </span>
                            <span style="display: flex; align-items: center;">
                                <p style="margin: 5px;  display: flex; justify-content: space-between; width: 30%; white-space: nowrap">DELIVERY CHALLAN NO SALES INVOLVED</b>
                            </span>
                        </div>
                        <div>
                            <span style="display: flex; align-items: center;">
                                <p style="margin: 5px;  display: flex; justify-content: space-between; width: 30%; white-space: nowrap">Phone No<span>:</span> </p><b style="margin-left: 10px">{{ safe(getCompanyInfo()->phone) }}</b>
                            </span>
                            <span style="display: flex; align-items: center;">
                                <p style="margin: 5px;  display: flex; justify-content: space-between; width: 30%; white-space: nowrap">E-Mail
                                    <span>:</span>
                                </p><b style=" width: 50%;margin-left: 10px">{{ safe(getCompanyInfo()->email) }}</b>
                            </span>
                        </div>
                    </div>
                    <div style="display: flex; margin: 12px 0 0;">
                        <div
                            style="width: 50%; border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px 10px; font-size: 16px;">
                            <b>To</b><br>
                            <div style="padding-left: 30px; padding-bottom:35px; display: flex;"> <span>Mr./Ms.</span> <b
                                    style="padding-left: 10px; font-weight: 700;"> {{ $obj->customer->name }}<br>
                                    {{ $obj->customer->address }}<br>
                                    {{ $obj->customer->pan_no!='' ? 'PAN: '.$obj->customer->pan_no : '' }}</b>
                            </div>
                            <p>Party GSTIN No. : {{ $obj->customer->gst_no }}</p>
                        </div>
                        <div style="width: 50%; border-top: 1px solid #000; padding: 8px 10px; font-size: 16px;">
                            <div style="display: flex; margin-bottom: 4px;">
                                <span style="width: 40%;">Our DC No </span> <b>: {{ $obj->dc_no }}</b>
                            </div>
                            <div style="display: flex;margin-bottom: 4px;">
                                <span style="width: 40%;">Our DC Date</span> <b> : {{ date('d-M-y',strtotime($obj->dc_date)) }}</b>
                            </div>
                            <div style="display: flex; margin-bottom: 4px;">
                                <span style="width: 40%;">Customer Code</span> <b> : {{ $obj->customer->customer_id }}</b>
                            </div>
                        </div>
                    </div>
                    <table style="width:100%; border-collapse:collapse; font-size:16px;">
                        <tr style="text-align: center;">
                            <th style="border:1px solid #000; padding:4px; border-left: none;">S.No</th>
                            <th style="border:1px solid #000; padding:4px;">Part No.</th>
                            <th style="border:1px solid #000; padding:4px;">Description</th>
                            <th style="border:1px solid #000; padding:4px;">Qty</th>
                            <th style="border:1px solid #000; padding:4px;">UOM</th>
                            <th style="border:1px solid #000; padding:4px; border-right: none;">Remarks</th>
                        </tr>
                        @if (isset($obj) && $obj)
                            <?php
                            $productId = getStockProduct($obj->reference_no,$obj->line_item_no);
                            // dd($obj->line_item_no);
                            $productInfo = getFinishedProductInfo($productId);
                            ?>
                            <tr>
                                <td style="border:1px solid #000; padding:4px; text-align:center;  border-left: none;">1</td>
                                <td style="border:1px solid #000; padding:4px; text-align: center;">{{ $productInfo->code }}</td>
                                <td style="border:1px solid #000; padding:4px; ">{{ $productInfo->name }} </td>
                                <td style="border:1px solid #000; padding:4px; text-align:center;">{{ isset($stock_return) ? $stock_return->qty : $obj->current_stock }}</td>
                                <td style="border:1px solid #000; padding:4px; text-align:start;">{{ getRMUnitById($obj->unit_id) }}</td>
                                <td style="border:1px solid #000; padding:4px; text-align:start; border-right: none;">Return</td>
                            </tr>
                        @endif
                    </table>
                    <div
                        style="display: flex; justify-content: space-between; align-items: start; margin: 30px 10px 0px; font-size: 16px;">
                        <div>
                            <p style="margin: 0;"> Received the above Goods in good conditions</p>
                            <p style="margin-top: 30px;"> Receiver's Signature with Seal</p>
                        </div>
                        <div style="text-align: right;">
                            For <b>{{ strtoupper(getCompanyInfo()->company_name) }}</b>

                        </div>
                    </div>
                </div>
                <div style="text-align: end;">
                    <span style="font-size: 12px;">DAN/STR/SF/01</span>
                </div>
            </div>
        </section>
        @endif
    @endif
</section>
@endsection
@section('script')
<?php
$baseURL = getBaseURL();
?>
<script type="text/javascript" src="{!! $baseURL . 'frequent_changing/js/addRMPurchase.js' !!}"></script>
<script type="text/javascript" src="{!! $baseURL . 'frequent_changing/js/supplier.js' !!}"></script>
<script src="{!! $baseURL . 'frequent_changing/js/quotation.js' !!}"></script>
<script>
$(document).ready(function () {
    $(document).on("click", ".print_material_return", function () {
        viewChallan($(this).attr("data-id"));
    });
    function viewChallan(id) {
        let base_url = $("#hidden_base_url").val();
        open(
            base_url + "material-return-print/" + id,
            "Print Material Return",
            "width=1600,height=550"
        );
        newWindow.focus();
        newWindow.onload = function () {
            newWindow.document.body.insertAdjacentHTML("afterbegin");
        };
    }
});
$("#material_return_form").submit(function (e) {
    let status = true;
    let qty = $("#qty").val().trim();
    let qtyVal = parseFloat(qty);
    let float_stock = parseFloat($("#float_stock").val());

    if (!qty) {
        status = false;
        showErrorMessage("qty", "The Return Quantity field is required");
    } else if (qtyVal == 0) {
        status = false;
        showErrorMessage("qty", "The Return Quantity should not be Zero.");
    } else if (qtyVal >= float_stock) {
        status = false;
        showErrorMessage("qty", "The Return Quantity should not be greater than or equal to Floating Stock.");
    } else {
        clearError("qty");
    }

    if (!status) {
        e.preventDefault(); // prevent form from submitting
        $("html, body").animate({ scrollTop: 0 }, "slow");
        return false;
    }
});
function showErrorMessage(id, message) {
    $("#" + id).addClass("is-invalid");
    const group = $("#" + id).closest(".form-group");
    const errorBox = group.find(".text-danger");
    errorBox.text(message);
    errorBox.removeClass("d-none");
}
function clearError(fieldId) {
    $("#" + fieldId).removeClass("is-invalid");
    $("#" + fieldId).closest("div").find(".text-danger").addClass("d-none");
}
</script>
@endsection