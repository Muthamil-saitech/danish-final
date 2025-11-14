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
        <div class="text-center pt-10 pb-10">
            <h2 class="color-000000 pt-20 pb-20">Asset Maintenance</h2>
        </div>
        <table>
            <tr>
                <td class="w-25 text-right">
                    <p class="pb-7 rgb-71">
                        <span class=""><strong>@lang('index.instrument_name'):</strong></span>
                        {{ $instrument->instrument_name.'('.$instrument->code.')' }}
                    </p>
                </td>
            </tr>
        </table>
        <table class="w-100 mt-20 order_details" style="border: 1px solid #000;">
            <thead class="b-r-3">
                <tr>
                    <th class="w-5 text-start" style="border:1px solid #000;">@lang('index.sn')</th>
                    <th class="w-5 text-start" style="border:1px solid #000;">Service Date</th>
                    <th class="w-15 text-start" style="border:1px solid #000;">Next Service Date</th>
                    <th class="w-20 text-start" style="border:1px solid #000;">Notes</th>
                    <th class="w-20 text-start" style="border:1px solid #000;">@lang('index.created_on')</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                @if(isset($instrument_entries) && $instrument_entries->count())
                    @foreach($instrument_entries as $value)
                        <tr class="rowCount" data-id="{{ $value->id }}">
                            <td class="width_1_p" style="border:1px solid #000;">
                                <span class="text-bold">{{ $i++ }}</span>
                            </td>
                            <td class="text-start" style="border:1px solid #000;">{{ getDateFormat($value->service_date) }}</td>
                            <td class="text-start" style="border:1px solid #000;">{{ getDateFormat($value->next_service_date) }}</td>
                            <td class="text-start" style="border:1px solid #000;">{{ $value->notes }}</td>
                            <td class="text-start" style="border:1px solid #000;">{{ getDateFormat($value->created_at) }}</td>                            
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