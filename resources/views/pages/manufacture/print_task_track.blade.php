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
    <title>{{ $obj->po_no }}</title>
    <link rel="stylesheet" href="{{ getBaseURL() }}frequent_changing/css/pdf_common.css">
</head>

<body>
    <div class="m-auto b-r-5 p-30">
        <div class="text-center pt-10 pb-10">
            <h2 class="color-000000 pt-20 pb-20">Production Scheduling</h2>
        </div>
        <table>
            <tr>
                <td class="w-25" style="float: inline-end">
                    <p class="pb-7 rgb-71">
                        <span class=""><strong>@lang('index.ppcrc_no'):</strong></span>
                        {{ $obj->reference_no }}
                    </p>
                </td>
            </tr>
        </table>
        <table class="w-100 mt-20 order_details" style="border: 1px solid #000;">
            <thead class="b-r-3">
                <tr>
                    <th class="w-5 text-start" style="border:1px solid #000;">Stage</th>
                    <th class="w-5 text-start" style="border:1px solid #000;">@lang('index.task')</th>
                    <th class="w-15 text-start" style="border:1px solid #000;">@lang('index.assign_to')</th>
                    <th class="w-20 text-start" style="border:1px solid #000;">@lang('index.total_hours')</th>
                    <th class="w-30 text-start" style="border:1px solid #000;">@lang('index.task_status')</th>
                    <th class="w-5 text-center" style="border:1px solid #000;">@lang('index.start_date')</th>
                    <th class="w-5 text-center" style="border:1px solid #000;">@lang('index.complete_date')</th>
                    <th class="w-10 text-center" style="border:1px solid #000;">Completed Time<br>(in mins)</th>
                    <th class="w-10 text-center" style="border:1px solid #000;">Work Centre</th>
                    <th class="w-30 text-center" style="border:1px solid #000;">@lang('index.note')</th>
                    <th class="w-5 text-center" style="border:1px solid #000;">@lang('index.latest_update_on')</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                @if(isset($productionScheduling) && $productionScheduling->count())
                    @foreach($productionScheduling as $value)
                        <tr class="rowCount" data-id="{{ $value->id }}">
                            <td class="width_1_p" style="border:1px solid #000;">
                                <span class="text-bold">{{ getProductionStage($value->production_stage_id) }}</span>
                            </td>
                            <td class="text-start" style="border:1px solid #000;">{{ $value->task }}</td>
                            <td class="text-start" style="border:1px solid #000;">{{ getEmpCode($value->user_id) }}</td>
                            <td class="text-start" style="border:1px solid #000;">{{ $value->task_hours > 1 ? $value->task_hours.' hrs' : $value->task_hours.' hr' }}</td>
                            <td class="text-start" style="border:1px solid #000;"><span class="text-bold">@if($value->task_status == "1") Pending @elseif($value->task_status == "2") In Progress @else Completed @endif</span></td>
                            <td class="text-start" style="border:1px solid #000;">{{ getDateFormat($value->start_date) }}</td>
                            <td class="text-start" style="border:1px solid #000;">{{ getDateFormat($value->end_date) }}</td>
                            <td class="text-start" style="border:1px solid #000;">{{ $value->complete_hours > 1 ? $value->complete_hours.' mins' : $value->complete_hours.' min' }}</td>
                            <td class="text-start" style="border:1px solid #000;">{{ $value->work_centre!='' ? $value->work_centre : ' - ' }}</td>
                            <td class="text-start" style="border:1px solid #000;">{{ $value->task_note }}</td>
                            <td class="text-start" style="border:1px solid #000;">{{ notificationDateFormat($value->created_at) }}</td>
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