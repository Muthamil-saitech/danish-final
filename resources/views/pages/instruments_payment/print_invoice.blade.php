<!DOCTYPE html>
<html lang="en">
<?php
$baseURL = getBaseURL();
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $obj->reference_no }}</title>
    <link rel="stylesheet" href="{{ getBaseURL() }}frequent_changing/css/pdf_common.css">
</head>

<body>
    <div class="m-auto b-r-5 p-30">
        <table>
            <tr>
                <td class="w-50">
                    <h3 class="pb-7">{{ getCompanyInfo()->company_name }}</h3>
                    <p class="pb-7 rgb-71">{{ safe(getCompanyInfo()->address) }}</p>
                    <p class="pb-7 rgb-71">@lang('index.email') : {{ safe(getCompanyInfo()->email) }}</p>
                    <p class="pb-7 rgb-71">@lang('index.phone') : {{ safe(getCompanyInfo()->phone) }}</p>
                    <p class="pb-7 rgb-71">@lang('index.website') : {{ safe(getCompanyInfo()->website) }}</p>
                </td>
                <td class="w-50 text-right">
                    <img src="{!! getBaseURL() .
                        (isset(getWhiteLabelInfo()->logo) ? 'uploads/white_label/' . getWhiteLabelInfo()->logo : 'images/logo.png') !!}" alt="site-logo">
                </td>
            </tr>
        </table>
        <div class="text-center pt-10 pb-10">
            <h2 class="color-000000 pt-20 pb-20">{{ isset($title) ? $title : '' }}</h2>
        </div>
        <table>
            <tr>
                <td class="w-50">
                    <h4 class="pb-7">@lang('index.partner_info'):</h4>
                    <p class="pb-7 arabic">{{ $obj->partner->name }}</p>
                    <p class="pb-7 rgb-71 arabic">{{ $obj->partner->address }}</p>
                    <p class="pb-7 rgb-71 arabic">{{ $obj->partner->phone }}</p>
                </td>
                <td class="w-50 text-right">
                    <h4 class="pb-7">{{ isset($title) ? $title : '' }}:</h4>
                    <p class="pb-7">
                        <span class="">@lang('index.reference_no'):</span>
                        {{ $obj->reference_no }}
                    </p>
                    <p class="pb-7 rgb-71">
                        <span class="">@lang('index.date'):</span>
                        {{ getDateFormat($obj->io_date) }}
                    </p>
                </td>
            </tr>
        </table>
        <table class="w-100 mt-20 order_details" style="border:1px solid #000;">
            <thead class="b-r-3">
                <tr>
                    <th class="w-5 text-start" style="border:1px solid #000;">@lang('index.sn')</th>
                    <th class="w-15 text-start" style="border:1px solid #000;">@lang('index.payment_date')</th>
                    <th class="w-15 text-start" style="border:1px solid #000;">@lang('index.paid_amount')</th>
                    <th class="w-15 text-start" style="border:1px solid #000;">@lang('index.balance_amount')</th>
                    <th class="w-15 text-start" style="border:1px solid #000;">@lang('index.payment_type')</th>
                    <th class="w-30 text-start" style="border:1px solid #000;">@lang('index.note')</th>
                    <th class="w-20 text-start" style="border:1px solid #000;">@lang('index.payment_img')</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                ?>
                @if(isset($ins_pay_entries) && $ins_pay_entries->isNotEmpty())
                    @foreach($ins_pay_entries as $ins_pay)
                        <tr class="rowCount" data-id="{{ $ins_pay->id }}">
                            <td class="width_1_p" style="border:1px solid #000;">
                                <p class="set_sn">{{ $i++ }}</p>
                            </td>
                            <td class="text-start" style="border:1px solid #000;">
                                {{ getDateFormat($ins_pay->created_at) }}
                            </td>
                            <td class="text-start" style="border:1px solid #000;">{{ getAmtCustom($ins_pay->pay_amount) }}
                            </td>
                            <td class="text-start" style="border:1px solid #000;">{{ getAmtCustom($ins_pay->balance_amount) }}
                            </td>
                            <td class="text-start" style="border:1px solid #000;">{{ $ins_pay->payment_type }}
                            </td>
                            <td class="text-start" style="border:1px solid #000;">{{ $ins_pay->note }}
                            </td>
                            <td class="text-start" style="border:1px solid #000;">
                                @if($ins_pay->payment_proof)
                                <a class="text-decoration-none"
                                    href="{{ $baseURL }}uploads/instrument_payments/{{ $ins_pay->payment_proof }}"
                                    target="_blank">
                                    <img src="{{ $baseURL }}uploads/instrument_payments/{{ $ins_pay->payment_proof }}"
                                        alt="File Preview" class="img-thumbnail mx-2"
                                        width="100px">
                                </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <table>
            <tr>
                <td valign="top" class="w-50">

                </td>
                <td class="w-50">
                    <table>
                        <tr>
                            <td class="w-50">
                                <p class="f-w-600">Total Amount</p>
                            </td>
                            <td class="w-50 text-right pr-0">
                                <p>{{ getAmtCustom($insInvoice->amount) }}</p>
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td class="w-50">
                                <p class="f-w-600">Paid Amount</p>
                            </td>
                            <td class="w-50 text-right pr-0">
                                <p>{{ getAmtCustom($insInvoice->paid_amount) }}</p>
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td class="w-50">
                                <p class="f-w-600">Balance Amount</p>
                            </td>
                            <td class="w-50 text-right pr-0">
                                <p>{{ getAmtCustom($insInvoice->due_amount) }}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table class="mt-50">
            <tr>
                <td class="w-50">
                </td>
                <td class="w-50 text-right">
                    <p class="rgb-71 d-inline border-top-e4e5ea pt-10">@lang('index.authorized_signature')</p>
                </td>
            </tr>
        </table>
    </div>
    <?php
    $baseURL = getBaseURL();
    $setting = getSettingsInfo();
    $base_color = '#6ab04c';
    if (isset($setting->base_color) && $setting->base_color) {
        $base_color = $setting->base_color;
    }
    ?>
    <script src="{{ $baseURL .('assets/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ $baseURL . ('frequent_changing/js/onload_print.js') }}"></script>
</body>
</html>