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
    <!-- Optional theme -->
    <input type="hidden" id="edit_mode" value="{{ isset($obj) && $obj ? $obj->id : null }}">
    <section class="main-content-wrapper">
        @include('utilities.messages')
        <section class="content-header">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="top-left-header">{{ isset($title) && $title ? $title : '' }}</h2>
                </div>
                <div class="col-md-6">
                    <a href="javascript:void();"  class="btn bg-second-btn print_invoice"
                        data-id="{{ isset($obj) ? encrypt_decrypt($obj->id, 'encrypt') : '' }}"><iconify-icon icon="solar:printer-broken"></iconify-icon>
                        @lang('index.print')</a>
                    <a href="{{ route('download-task-download', encrypt_decrypt($obj->id, 'encrypt')) }}"
                        target="_blank" class="btn bg-second-btn print_btn"><iconify-icon
                            icon="solar:cloud-download-broken"></iconify-icon>
                        @lang('index.download')</a>
                    <a class="btn bg-second-btn" href="{{ route('productions.index') }}"><iconify-icon icon="solar:round-arrow-left-broken"></iconify-icon>@lang('index.back')</a>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="col-md-12">
                <div class="card" id="dash_0">
                    <div class="card-body p30">
                        <div class="m-auto b-r-5">
                            <div class="text-center pt-10 pb-10">
                                <h3 class="color-000000 pt-20 pb-20">Production Scheduling</h3>
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
                            <table>
                                <thead>
                                    <tr>
                                        <th>Stage</th>
                                        <th>@lang('index.task')</th>
                                        <th>@lang('index.assign_to')</th>
                                        <th>@lang('index.total_hours')</th>
                                        <th>@lang('index.task_status')</th>
                                        <th>@lang('index.start_date')</th>
                                        <th>@lang('index.complete_date')</th>
                                        <th>Completed Time<br>(in mins)</th>
                                        <th>Work Centre</th>
                                        <th style="min-width:100px;">@lang('index.note')</th>
                                        <th>@lang('index.latest_update_on')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                @if(isset($productionScheduling) && $productionScheduling->count())
                                    @foreach($productionScheduling as $value)
                                        <tr class="rowCount">
                                            <td><span class="text-bold">{{ getProductionStage($value->production_stage_id) }}</span></td>
                                            <td>{{ $value->task }}</td>
                                            <td>{{ getEmpCode($value->user_id) }}</td>
                                            <td>{{ $value->task_hours > 1 ? $value->task_hours.' hrs' : $value->task_hours.' hr' }}</td>
                                            <td><span class="text-bold">@if($value->task_status == "1") Pending @elseif($value->task_status == "2") In Progress @else Completed @endif</span></td>
                                            <td>{{ getDateFormat($value->start_date) }}</td>
                                            <td>{{ getDateFormat($value->end_date) }}</td>
                                            <td>{{ $value->complete_hours > 1 ? $value->complete_hours.' mins' : $value->complete_hours.' min' }}</td>
                                            <td>{{ $value->work_centre!='' ? $value->work_centre : ' - ' }}</td>
                                            <td>{{ $value->task_note }}</td>
                                            <td ><i class="fa fa-circle-question" title="{{ notificationDateFormat($value->created_at) }}"></i></td>
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
            base_url + "print-task-schedule/" + id,
            "Print Task Schedule",
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
