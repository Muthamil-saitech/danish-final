<?php
$setting = getSettingsInfo();
$tax_setting = getTaxInfo();
$baseURL = getBaseURL();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    {{-- <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> --}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $obj->reference_no }}</title>
    <link rel="stylesheet" href="{{ getBaseURL() }}frequent_changing/css/pdf_common.css">
    <style>
        @page {
            margin-top: 100px;  
            margin-bottom: 40px;
            margin-left: 25px;
            margin-right: 25px;
        }
        @page :first {
            margin-top: 40px;
        }
        body { padding-bottom: 10px; padding-top:20px; }

        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        thead {
            display: table-header-group; 
        }

        tfoot {
            display: table-footer-group;
        }

        thead::before {
            content: "";
            display: table-row;
            height: 100px;
        }
    </style>
</head>

<body>
    <div class="m-auto b-r-5 p-30">
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
                    <p class="pb-7 rgb-71">@lang('index.website') : {{ getCompanyInfo()->website }}</p>
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
                        <b><span class="">@lang('index.so_entry_no'):</span>
                        {{ isset($orderDetails) ? $orderDetails->so_entry_no : '' }}</b>
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
        <table class="w-100 mt-20 order_details" style="border: 1px solid #000;">
            <thead class="b-r-3">
                <tr>
                    <th class="w-5 text-center" style="border:1px solid #000;">@lang('index.sn')</th>
                    <th class="w-10 text-center" style="border:1px solid #000;">@lang('index.po_date')</th>
                    <th class="w-20 text-center" style="border:1px solid #000;">Part Name(Part No)</th>
                    <th class="w-15 text-center" style="border:1px solid #000;">@lang('index.raw_material_name')<br>(@lang('index.code'))</th>
                    <th class="w-8 text-center" style="border:1px solid #000;">@lang('index.raw_quantity')</th>
                    <th class="w-8 text-center" style="border:1px solid #000;">@lang('index.prod_quantity')</th>
                    <th class="w-10 text-center" style="border:1px solid #000;">@lang('index.unit_price')</th>
                    <th class="w-10 text-center" style="border:1px solid #000;">@lang('index.price')</th>
                    <th class="w-8 text-center" style="border:1px solid #000;">@lang('index.tax')</th>
                    <th class="w-12 text-center" style="border:1px solid #000;">@lang('index.total')</th>
                    <th class="w-12 text-center" style="border:1px solid #000;">@lang('index.created_on')</th>
                </tr>
            </thead>
            <tbody>
                
                @if(isset($order_edit_logs) && count($order_edit_logs))
                    @foreach($order_edit_logs as $index => $log)
                        @php
                            $productInfo = getFinishedProductInfo($orderDetails->product_id);
                        @endphp
                        <tr class="rowCount" data-id="{{ $orderDetails->product_id }}">
                            <td class="text-center" style="border:1px solid #000;">{{ $index + 1 }}</td>
                            <td class="text-center" style="border:1px solid #000;">{{ $obj->po_date ? getDateFormat($obj->po_date) : getDateFormat($obj->created_at) }}</td>
                            <td class="text-center" style="border:1px solid #000;">{{ $productInfo->name }} ({{ $productInfo->code }})</td>
                            <td class="text-center" style="border:1px solid #000;">{{ getRMName($orderDetails->raw_material_id) }}</td>
                            <td class="text-center" style="border:1px solid #000;">{{ $log->mat_qty }}</td>
                            <td class="text-center" style="border:1px solid #000;">{{ $log->prod_qty }}</td>
                            <td class="text-right" style="border:1px solid #000; font-family: DejaVu Sans, sans-serif;">₹{{ $log->unit_price }}</td>
                            <td class="text-right" style="border:1px solid #000; font-family: DejaVu Sans, sans-serif;">₹{{ $log->price }}</td>
                            <td class="text-right" style="border:1px solid #000; font-family: DejaVu Sans, sans-serif;">₹{{ $log->tax_amount }}</td>
                            <td class="text-right" style="border:1px solid #000; font-family: DejaVu Sans, sans-serif;">₹{{ number_format($log->subtotal,2) }}</td>
                            <td class="text-center" style="border:1px solid #000;">{{ getDateFormat($log->created_at) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="11" class="text-center" style="border:1px solid #000;">@lang('index.no_data_found')</td></tr>
                @endif
            </tbody>
        </table>
        
    </div>
    <script src="{{ $baseURL . ('assets/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ $baseURL . ('frequent_changing/js/onload_print.js') }}"></script>
</body>

</html>