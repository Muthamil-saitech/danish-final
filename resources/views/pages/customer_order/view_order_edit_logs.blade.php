@extends('layouts.app')
@section('script_top')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <?php
    $setting = getSettingsInfo();
    $tax_setting = getTaxInfo();
    $baseURL = getBaseURL();
    ?>
    <link rel="stylesheet" href="{{ getBaseURL() }}frequent_changing/css/pdf_common.css">
@endsection

@section('content')
    <!-- Optional theme -->

    <section class="main-content-wrapper bg-main">
        <section class="content-header">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="top-left-header">{{ isset($title) && $title ? $title : '' }}</h2>
                </div>
                <div class="col-md-6">
                        <a href="javascript:void();" target="_blank" class="btn bg-second-btn print_invoice"
                            data-id="{{ isset($orderDetails) ? $orderDetails->id : '' }}"><iconify-icon icon="solar:printer-broken"></iconify-icon>
                            @lang('index.print')</a>
                        <a href="{{ route('order-edit-log-download', encrypt_decrypt($orderDetails->id, 'encrypt')) }}"
                            target="_blank" class="btn bg-second-btn print_btn"><iconify-icon
                                icon="solar:cloud-download-broken"></iconify-icon>
                            @lang('index.download')</a>
                    @if (routePermission('order.index'))
                        <a class="btn bg-second-btn" href="{{ route('customer-orders.index') }}"><iconify-icon
                                icon="solar:round-arrow-left-broken"></iconify-icon>@lang('index.back')</a>
                    @endif
                </div>
            </div>
        </section>

        <section class="content">
            <div class="col-md-12">
                <div class="card" id="dash_0">
                    <div class="card-body p30">
                        <div class="m-auto b-r-5">
                            <table>
                                <tr>
                                    <td class="w-50">
                                        <img src="{!! getBaseURL() .
                                            (isset(getWhiteLabelInfo()->logo) ? 'uploads/white_label/' . getWhiteLabelInfo()->logo : 'images/logo.png') !!}" alt="site-logo">
                                    </td>
                                    <td class="w-50 text-right">
                                        <h3 class="pb-7">{{ getCompanyInfo()->company_name }}</h3>
                                        <p class="pb-7 rgb-71">{{ getCompanyInfo()->address }}</p>
                                        <p class="pb-7 rgb-71">@lang('index.email') : {{ getCompanyInfo()->email }}</p>
                                        <p class="pb-7 rgb-71">@lang('index.phone') : {{ getCompanyInfo()->phone }}</p>
                                        <p class="pb-7 rgb-71">@lang('index.website') : <a href="{{ getCompanyInfo()->website }}" target="_blank">{{ getCompanyInfo()->website }}</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <div class="text-center pt-10 pb-10">
                                <h2 class="color-000000 pt-20 pb-20">@lang('index.order_details')</h2>
                            </div>
                            <table>
                                <tr>
                                    <td class="w-50">
                                        <h4 class="pb-7">@lang('index.customer_info'):</h4>
                                        <p class="pb-7">{{ $obj->customer->name }}</p>
                                        <p class="pb-7 rgb-71">{{ $obj->customer->phone }}</p>
                                        <p class="pb-7 rgb-71">{{ $obj->customer->email }}</p>
                                        <p class="pb-7 rgb-71">{{ $obj->customer->address }}</p>
                                    </td>
                                    <td class="w-50 text-right">
                                        <h4 class="pb-7">@lang('index.order_info'):</h4>
                                        <p class="pb-7">
                                            <b><span class="">@lang('index.so_entry_no'):</span>{{ isset($orderDetails) ? $orderDetails->so_entry_no : '' }}</b>
                                        </p>
                                        <p class="pb-7">
                                            <span class="">@lang('index.po_no'):</span>
                                            {{ $obj->reference_no }}/{{ isset($orderDetails) ? $orderDetails->line_item_no : '' }}
                                        </p>
                                        <p class="pb-7 rgb-71">
                                            <span class="">@lang('index.order_type'):</span>
                                            {{ $obj->order_type=='Quotation' ? 'Labor' : 'Sales' }}
                                        </p>
                                        <p class="pb-7 rgb-71">
                                            <span class="">@lang('index.delivery_address'):</span>
                                            {{ $obj->delivery_address }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <table class="w-100 mt-20">
                                <thead class="b-r-3 bg-color-000000">
                                    <tr>
                                        <th class="w-5 text-start">@lang('index.sn')</th>
                                        <th class="w-5 text-start">@lang('index.po_date')</th>
                                        <th class="w-20 text-start">Part Name(Part No)</th>
                                        <th class="w-25 text-start">@lang('index.raw_material_name')<br>(@lang('index.code'))</th>
                                        {{-- <th class="w-25 text-start">Heat No</th> --}}
                                        <th class="w-5 text-center">@lang('index.raw_quantity')</th>
                                        <th class="w-5 text-center">@lang('index.prod_quantity')</th>
                                        <th class="w-15 text-center">@lang('index.unit_price')</th>
                                        <th class="w-220-p text-start">@lang('index.price')</th>
                                        {{-- <th class="w-15 text-center">@lang('index.discount')</th> --}}
                                        {{-- <th class="w-15 text-center">@lang('index.subtotal')</th> --}}
                                        <th class="w-15 text-center">@lang('index.tax')</th>
                                        <th class="w-15 text-center">@lang('index.total')</th>
                                        <th class="w-15 text-center">@lang('index.created_on')</th>
                                        {{-- <th class="w-10 text-right pr-5">@lang('index.total')</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($order_edit_logs) && $order_edit_logs)
                                        @php 
                                            $i = 1;
                                        @endphp
                                        @foreach($order_edit_logs as $order_edit_log)
                                            <?php
                                                $productRawInfo = getProductRawMaterialByProductId($orderDetails->product_id);
                                                $productInfo = getFinishedProductInfo($orderDetails->product_id);
                                            ?>
                                            <tr class="rowCount" data-id="{{ $orderDetails->product_id }}">
                                                <td class="width_1_p">
                                                    <p class="set_sn">{{ $loop->iteration }}</p>
                                                </td>
                                                <td class="text-start">{{ $obj->po_date != null ? getDateFormat($obj->po_date): getDateFormat($obj->created_at) }}</td>
                                                <td class="text-start">{{ $productInfo->name }}({{ $productInfo->code }})</td>
                                                <td class="text-start">{{ getRMName($orderDetails->raw_material_id) }}</td>
                                                <td class="text-center">{{ $order_edit_log->mat_qty }}</td>
                                                <td class="text-center">{{ $order_edit_log->prod_qty }}</td>
                                                <td class="text-center">{{ getAmtCustom($order_edit_log->unit_price) }}</td>
                                                <td class="text-start">{{ getAmtCustom($order_edit_log->price) }}</td>
                                                <td class="text-center">{{ getAmtCustom($order_edit_log->tax_amount) }}</td>
                                                <td class="text-center">{{ getAmtCustom($order_edit_log->subtotal) }}</td>
                                                <td class="text-center">{{ getDateFormat($order_edit_log->created_at) }}</td>
                                            </tr>
                                        @endforeach 
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </section>
@endsection
@section('script')
    <script src="{!! $baseURL . 'frequent_changing/js/order.js' !!}"></script>
@endsection
