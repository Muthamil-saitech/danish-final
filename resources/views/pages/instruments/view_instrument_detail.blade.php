@extends('layouts.app')
@section('script_top')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <?php
    $setting = getSettingsInfo();
    $tax_setting = getTaxInfo();
    $baseURL = getBaseURL();
    ?>
@endsection
@push('styles')
    <link rel="stylesheet" href="{!! $baseURL . 'assets/bower_components/gantt/css/style.css' !!}">
    <link rel="stylesheet" href="{{ getBaseURL() }}frequent_changing/css/pdf_common.css">
@endpush
@section('content')
    <section class="main-content-wrapper">
        @include('utilities.messages')
        <section class="content-header">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="top-left-header">{{ isset($title) && $title ? $title : '' }}</h2>
                </div>
                <div class="col-md-6">
                    @if (routePermission('instruments.print_instrument_detail'))
                        <a href="javascript:void();"  class="btn bg-second-btn print_invoice"
                        data-id="{{ isset($instruments[0]['code']) ? encrypt_decrypt($instruments[0]['code'], 'encrypt') : '' }}"><iconify-icon icon="solar:printer-broken"></iconify-icon>
                        @lang('index.print')
                    </a>
                    @endif
                    @if (routePermission('instruments.download-instrument-detail'))
                        <a href="{{ route('download-instrument-detail', encrypt_decrypt($instruments[0]['code'], 'encrypt')) }}" target="_blank" class="btn bg-second-btn print_btn"><iconify-icon icon="solar:cloud-download-broken"></iconify-icon>@lang('index.download')</a>
                    @endif
                    @if (routePermission('instruments.index'))
                    <a class="btn bg-second-btn" href="{{ route('instruments.index') }}"><iconify-icon icon="solar:round-arrow-left-broken"></iconify-icon>@lang('index.back')</a>
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
                                        <p class="pb-7 rgb-71">@lang('index.website') : {{ getCompanyInfo()->website }}</p>
                                    </td>
                                </tr>
                            </table>
                            <div class="text-center pt-10 pb-10">
                                <h2 class="color-000000 pt-20 pb-20">Instrument Detail</h2>
                            </div>
                            <table>
                                <tr>
                                    <td class="w-50" style="float: inline-start">
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
                                    <td class="w-50" style="float: inline-end">
                                        <p class="pb-7 rgb-71"></p>
                                        <p class="pb-7 rgb-71">
                                            <span class=""><strong>@lang('index.owner'):</strong></span>
                                            {{ $instruments[0]['owner_type']==1 ? 'Own' : 'Customer' }}
                                        </p>
                                        <p class="pb-7 rgb-71">
                                            <span class=""><strong>@lang('index.customer_name'):</strong></span>
                                            {{ getStockCustomerNameById($instruments[0]['customer_id']) }} {{ $instruments[0]['owner_type']!=1 ? '('.getCustomerCodeById($instruments[0]['customer_id']).')' : '' }}
                                        </p>
                                    </td>
                                </tr>
                            </table>                                
                            <table>
                                <thead>
                                    <tr>
                                        <th>@lang('index.sn')</th>
                                        {{-- <th>@lang('index.instrument_name')</th> --}}
                                        <th>@lang('index.unit')</th>
                                        <th>@lang('index.range/size')</th>
                                        <th>@lang('index.accuracy')</th>
                                        <th>@lang('index.make')</th>
                                        {{-- <th>@lang('index.historycardno')</th> --}}
                                        {{-- <th>@lang('index.location')</th> --}}
                                        {{-- <th>@lang('index.due_date')</th> --}}
                                        {{-- <th>@lang('index.remarks')</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                @if(isset($instrument_ranges) && $instrument_ranges->count())
                                    @foreach($instrument_ranges as $value)
                                        <tr class="rowCount">
                                            <td><span class="text-bold">{{ $i++ }}</span></td>
                                            {{-- <td>{{ $value->instrument_name }}</td> --}}
                                            <td>{{ getRMUnitById($value->ins_unit_id) }}</td>
                                            <td>{{ $value->ins_range }}</td>
                                            <td>{{ $value->ins_accuracy }}</td>
                                            <td>{{ $value->ins_make }}</td>
                                            {{-- <td>{{ $value->history_card_no }}</td> --}}
                                            {{-- <td>{{ $value->location }}</td> --}}
                                            {{-- <td>{{ getDateFormat($value->due_date) }}</td> --}}
                                            {{-- <td title="{{ $value->remarks }}">{{ substr_text(safe($value->remarks),20) }}</td> --}}
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
<script src="{!! $baseURL . 'assets/datatable_custom/jquery-3.3.1.js' !!}"></script>
<script>
$(document).ready(function () {
    $(document).on("click", ".print_invoice", function () {
        viewChallan($(this).attr("data-id"));
    });
    function viewChallan(id) {
        let base_url = $("#hidden_base_url").val();
        open(
            base_url + "print-instrument-detail/" + id,
            "Print Instrument Detail",
            "width=1600,height=550"
        );
        newWindow.focus();
        newWindow.onload = function () {
            newWindow.document.body.insertAdjacentHTML("afterbegin");
        };
    }
});
</script>
@endsection
