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
                <div class="col-md-6 text-end">
                    <a class="btn bg-second-btn" href="{{ route('sales.index') }}"><iconify-icon icon="solar:round-arrow-left-broken"></iconify-icon>@lang('index.back')</a>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="col-md-12">
                <div class="card" id="dash_0">
                    <div class="card-body p30">
                        <div class="m-auto b-r-5">
                            {{-- <h6 class="text-muted mb-2"><strong>Sales Item List</strong></h6> --}}
                            <div class="row">
                                <div class="col-md-12">
                                    @if(isset($sale_details) && $sale_details)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead class="b-r-3 bg-color-000000">
                                                <tr>
                                                    <th class="w-5 text-start">@lang('index.sn')</th>
                                                    <th class="w-30 text-start">@lang('index.part_name') @lang('index.part_no')</th>
                                                    <th class="w-20 text-start">@lang('index.po_no')</th>
                                                    <th class="w-15 text-start">@lang('index.prod_quantity')</th>
                                                    <th class="w-10 text-start">@lang('index.actions')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sale_details as $detail)
                                                @php $productInfo = getFinishedProductInfo($detail->product_id); @endphp
                                                    <tr class="rowCount">
                                                        <td class="text-start">{{ $loop->iteration }}</td>
                                                        <td class="text-start">{{ $productInfo->name.'('.$productInfo->code.')' }}</td>
                                                        <td class="text-start">{{ getPoNo($detail->order_id).'/'.getLineItemNo($detail->order_id) }}</td>
                                                        <td class="text-start">{{ $detail->product_quantity }}</td>
                                                        <td class="text-start"><a href="{{ url('sales') }}/{{ encrypt_decrypt($detail->id, 'encrypt') }}/invoice_note_entry" class="button-info" data-bs-toggle="tooltip" data-bs-placement="top" title="View Credit/Debit Entries"><i class="fa fa-list"></i></a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection
@section('script')
    <script type="text/javascript" src="{!! $baseURL . 'assets/bower_components/gantt/js/jquery.fn.gantt.js' !!}"></script>
    <script type="text/javascript" src="{!! $baseURL . 'assets/bower_components/gantt/js/jquery.cookie.min.js' !!}"></script>
@endsection