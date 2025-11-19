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
    <title>{{ isset($instrument) ? $instrument->instrument_name : '' }}</title>
    <link rel="stylesheet" href="{{ getBaseURL() }}frequent_changing/css/pdf_common.css">
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
            <h2 class="color-000000 pt-20 pb-20">Instrument Detail</h2>
        </div>
        <table>
            <tr>
                <td class="w-50 text-left">
                    <p class="pb-7 rgb-71">
                        <span class=""><strong>@lang('index.instrument_code'):</strong></span>
                        {{ $instruments[0]['code'] }}
                    </p>
                    <p class="pb-7 rgb-71">
                        <span class=""><strong>@lang('index.instrument_name'):</strong></span>
                        {{ $instruments[0]['instrument_name'] }}
                    </p>
                    <p class="pb-7 rgb-71">
                        <span class=""><strong>@lang('index.type'):</strong></span>
                        @if($instruments[0]['type'] == 1)
                        Gauges/Checking Instruments
                        @elseif($instruments[0]['type'] == 2)
                        Measuring Instruments
                        @else
                        N/A
                        @endif
                    </p>
                    <p class="pb-7 rgb-71">
                        <span class=""><strong>@lang('index.instrument_category'):</strong></span>
                        {{ getInstrumentCategoryById($instruments[0]['category']) }}
                    </p>
                </td>
                <td class="w-50 text-right">
                    <p class="pb-7 rgb-71">
                        <span class=""><strong>@lang('index.owner'):</strong></span>
                        {{ $instruments[0]['owner_type']==1 ? 'Own' : 'Customer' }}
                    </p>
                    <p class="pb-7 rgb-71">
                        <span class=""><strong>@lang('index.customer_name'):</strong></span> {{ getStockCustomerNameById($instruments[0]['customer_id']) }} {{ $instruments[0]['owner_type']!=1 ? '('.getCustomerCodeById($instruments[0]['customer_id']).')' : '' }}
                    </p>
                </td>
            </tr>
        </table>
        <table class="w-100 mt-20 order_details" style="border: 1px solid #000;">
            <thead class="b-r-3">
                <tr>
                    <th class="w-5 text-start" style="border:1px solid #000;">@lang('index.sn')</th>
                    {{-- <th class="w-5 text-start" style="border:1px solid #000;">@lang('index.instrument_name')</th> --}}
                    <th class="w-15 text-start" style="border:1px solid #000;">@lang('index.unit')</th>
                    <th class="w-20 text-start" style="border:1px solid #000;">@lang('index.range/size')</th>
                    <th class="w-20 text-start" style="border:1px solid #000;">@lang('index.accuracy')</th>
                    <th class="w-20 text-start" style="border:1px solid #000;">@lang('index.make')</th>
                    {{-- <th class="w-20 text-start" style="border:1px solid #000;">@lang('index.historycardno')</th> --}}
                    {{-- <th class="w-20 text-start" style="border:1px solid #000;">@lang('index.location')</th> --}}
                    {{-- <th class="w-20 text-start" style="border:1px solid #000;">@lang('index.due_date')</th> --}}
                    {{-- <th class="w-20 text-start" style="border:1px solid #000;">@lang('index.remarks')</th> --}}
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                @if(isset($instrument_ranges) && $instrument_ranges->count())
                    @foreach($instrument_ranges as $value)
                        <tr class="rowCount" data-id="{{ $value->id }}">
                            <td class="width_1_p" style="border:1px solid #000;">
                                <span class="text-bold">{{ $i++ }}</span>
                            </td>
                            {{-- <td class="text-start" style="border:1px solid #000;">{{ $value->instrument_name }}</td> --}}
                            <td class="text-start" style="border:1px solid #000;">{{ getRMUnitById($value->ins_unit_id) }}</td>
                            <td class="text-start" style="border:1px solid #000;">{{ $value->ins_range }}</td>
                            <td class="text-start" style="border:1px solid #000;">{{ $value->ins_accuracy }}</td>                         
                            <td class="text-start" style="border:1px solid #000;">{{ $value->ins_make }}</td>
                            {{-- <td class="text-start" style="border:1px solid #000;">{{ $value->history_card_no }}</td> --}}
                            {{-- <td class="text-start" style="border:1px solid #000;">{{ $value->location }}</td> --}}
                            {{-- <td class="text-start" style="border:1px solid #000;">{{ getDateFormat($value->due_date) }}</td> --}}
                            {{-- <td class="text-start" style="border:1px solid #000;">{{ $value->remarks }}</td> --}}
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <script src="{{ $baseURL . ('assets/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ $baseURL . ('frequent_changing/js/onload_print.js') }}"></script>
</body>
</html>