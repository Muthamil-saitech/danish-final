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
    <input type="hidden" id="edit_mode" value="{{ isset($instrument) && $instrument ? $instrument->id : null }}">
    <section class="main-content-wrapper">
        @include('utilities.messages')
        <section class="content-header">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="top-left-header">{{ isset($title) && $title ? $title : '' }}</h2>
                </div>
                <div class="col-md-6">
                    <a href="javascript:void();"  class="btn bg-second-btn print_invoice"
                        data-id="{{ isset($instrument) ? encrypt_decrypt($instrument->id, 'encrypt') : '' }}"><iconify-icon icon="solar:printer-broken"></iconify-icon>
                        @lang('index.print')</a>
                    <a href="{{ route('download-asset-maintain', encrypt_decrypt($instrument->id, 'encrypt')) }}"
                        target="_blank" class="btn bg-second-btn print_btn"><iconify-icon
                            icon="solar:cloud-download-broken"></iconify-icon>
                        @lang('index.download')</a>
                    <a class="btn bg-second-btn" href="{{ route('instruments.index') }}"><iconify-icon icon="solar:round-arrow-left-broken"></iconify-icon>@lang('index.back')</a>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="col-md-12">
                <div class="card" id="dash_0">
                    <div class="card-body p30">
                        <div class="m-auto b-r-5">
                            {{-- <div class="text-center pt-10 pb-10">
                                <h3 class="color-000000 pt-20 pb-20">Asset Maintenance</h3>
                            </div> --}}
                            <table>
                                <tr>
                                    <td class="w-25" style="float: inline-end">
                                        <p class="pb-7 rgb-71">
                                            <span class=""><strong>@lang('index.instrument_name'):</strong></span>
                                            {{ $instrument->instrument_name.'('.$instrument->code.')' }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <table>
                                <thead>
                                    <tr>
                                        <th>@lang('index.sn')</th>
                                        <th>Service Date</th>
                                        <th>Next Service Date</th>
                                        <th>Notes</th>
                                        <th>@lang('index.created_on')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                @if(isset($instrument_entries) && $instrument_entries->count())
                                    @foreach($instrument_entries as $value)
                                        <tr class="rowCount">
                                            <td><span class="text-bold">{{ $i++ }}</span></td>
                                            <td>{{ getDateFormat($value->service_date) }}</td>
                                            <td>{{ getDateFormat($value->next_service_date) }}</td>
                                            <td>{{ $value->notes }}</td>
                                            <td>{{ getDateFormat($value->created_at) }}</td>
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
            base_url + "print-asset-maintain/" + id,
            "Print Asset Maintain",
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
