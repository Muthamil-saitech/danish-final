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
<link rel="stylesheet" href="{{ getBaseURL() . 'frequent_changing/css/pdf_common.css' }}">
<link rel="stylesheet" href="{{ getBaseURL() . 'assets/dist/css/lightbox.min.css' }}">
<section class="main-content-wrapper">
    @include('utilities.messages')
    <section class="content-header">
        <div class="row">
            <div class="col-md-6">
                <h2 class="top-left-header">{{ isset($title) ? $title : '' }}</h2>
            </div>
            <div class="col-md-6">
                <a href="javascript:void();" target="_blank" class="btn bg-second-btn print_invoice" data-id="{{ $partnerDetails->id }}"><iconify-icon icon="solar:printer-broken"></iconify-icon>@lang('index.print')</a>
                <a class="btn bg-second-btn" href="{{ route('instruments-payment.index') }}"><iconify-icon icon="solar:round-arrow-left-broken"></iconify-icon>@lang('index.back')</a>
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
                                    <img src="{!! getBaseURL() . (isset(getWhiteLabelInfo()->logo) ? 'uploads/white_label/' . getWhiteLabelInfo()->logo : 'images/logo.png') !!}" alt="Logo Image" class="img-fluid mb-2">
                                    <h4 class="pb-7">@lang('index.partner_info'):</h4>
                                    <p class="pb-7 arabic">{{ $obj->partner->name }}</p>
                                    <p class="pb-7 rgb-71 arabic">{{ $obj->partner->address }}</p>
                                    <p class="pb-7 rgb-71 arabic">{{ $obj->partner->phone }}</p>
                                </td>
                                <td class="w-50 text-right">
                                    <h4 class="pb-7">@lang('index.partner_io_details'):</h4>
                                    <p class="pb-7">
                                        <span class="">@lang('index.reference_no'):</span>
                                        {{ isset($obj) && isset($partnerDetails) ? $obj->reference_no.'/'.$partnerDetails->line_item_no : ''  }}
                                    </p>
                                    <p class="pb-7 rgb-71">
                                        <span class="">@lang('index.date'):</span>
                                        {{ getDateFormat($obj->io_date) }}
                                    </p>
                                    <p class="pb-7 rgb-71">
                                        <span class="">@lang('index.total_amount'):</span>
                                        {{ getAmtCustom($insInvoice->amount) }}
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <table class="w-100 mt-20">
                            <thead class="b-r-3 bg-color-000000">
                                <tr>
                                    <th class="w-5 text-start">@lang('index.sn')</th>
                                    <th class="w-15 text-start">@lang('index.payment_date')</th>
                                    <th class="w-15 text-start">@lang('index.paid_amount')</th>
                                    <th class="w-15 text-start">@lang('index.payment_type')</th>
                                    <th class="w-15 text-start">@lang('index.payment_img')</th>
                                    <th class="w-15 text-start">@lang('index.note')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                ?>
                                @if(isset($ins_pay_entries) && $ins_pay_entries->isNotEmpty())
                                    @foreach($ins_pay_entries as $ins_pay)
                                        <tr class="rowCount" data-id="{{ $ins_pay->id }}">
                                            <td class="width_1_p">
                                                <p class="set_sn">{{ $i++ }}</p>
                                            </td>
                                            <td class="text-start">
                                                {{ getDateFormat($ins_pay->created_at) }}
                                            </td>
                                            <td class="text-start">{{ getAmtCustom($ins_pay->pay_amount) }}
                                            </td>
                                            <td class="text-start">{{ $ins_pay->payment_type }}
                                            </td>
                                            <td class="text-start">
                                                @if($ins_pay->payment_proof)
                                                <a class="text-decoration-none"
                                                    href="{{ $baseURL }}uploads/instrument_payments/{{ $ins_pay->payment_proof }}"
                                                    data-lightbox="payment-proof"
                                                    data-title="Payment Proof">
                                                    <img src="{{ $baseURL }}uploads/instrument_payments/{{ $ins_pay->payment_proof }}"
                                                        alt="File Preview" class="img-thumbnail mx-2"
                                                        width="50px">
                                                </a>
                                                @endif
                                            </td>
                                            <td class="text-start" title="{{ $ins_pay->note }}">{{ substr_text($ins_pay->note,20) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="rowCount">
                                        <td colspan="7" class="text-center">No Data Found</td>
                                    </tr>
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
<?php
$baseURL = getBaseURL();
?>
<script src="{!! $baseURL . 'assets/dist/js/lightbox.min.js' !!}"></script>
<script>
    $(document).ready(function () {
        "use strict";
        let baseUrl = $("#hidden_base_url").val();
        $(document).on("click", ".print_invoice", function () {
            viewChallan($(this).attr("data-id"));
        });

        function viewChallan(id) {
            open(
                baseUrl + "instruments_payment_print/" + id,
                "Print Instrument Payment",
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