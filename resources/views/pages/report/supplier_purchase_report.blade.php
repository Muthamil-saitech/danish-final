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
<section class="main-content-wrapper">
    @include('utilities.messages')
    <section class="content-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="top-left-header">{{ isset($title) && $title ? $title : '' }}</h2>
                <input type="hidden" class="datatable_name" data-filter="yes" data-title="{{ isset($title) && $title ? $title : '' }}" data-id_name="datatable">
            </div>
            <div class="col-md-6 text-end">
                <h5 class="mb-0">Total Supplier Purchases: {{ $total_purchase }}</h5>
            </div>
        </div>
    </section>
    <div class="box-wrapper">
        <div class="table-box">
            <!-- /.box-header -->
            <div class="table-responsive">
                <table id="datatable" class="table table-striped">
                    <thead>
                        <tr>
                            <th class="width_1_p">@lang('index.sn')</th>
                            <th class="width_10_p">@lang('index.purchase_no')</th>
                            <th class="width_10_p">@lang('index.purchase_date')</th>
                            <th class="width_10_p">@lang('index.supplier_name')</th>
                            <th class="width_10_p">@lang('index.g_total')</th>
                            <th class="width_10_p">@lang('index.paid')</th>
                            <th class="width_10_p">@lang('index.due')</th>
                            <th class="width_10_p">@lang('index.status')</th>
                            <th class="width_10_p">@lang('index.added_by')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($obj && !empty($obj))
                            <?php $i = 1; ?>
                            @foreach ($obj as $value)
                                <tr>
                                    <td class="c_center">{{ $i++ }}</td>
                                    <td>{{ $value->reference_no }}</td>
                                    <td>{{ getDateFormat($value->date) }}</td>
                                    <td>{{ getSupplierName($value->supplier) }}</td>
                                    <td>{{ getCurrency($value->grand_total) }}</td>
                                    <td>{{ getCurrency($value->paid) }}</td>
                                    <td>{{ getCurrency($value->due) }}</td>
                                    <td><h6>@if($value->status=="Draft") <span class="badge bg-warning">Draft</span>@else <span class="badge bg-success">Completed</span>@endif</h6></td>
                                    <td>{{ getUserName($value->added_by) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
    <div class="modal fade" id="filterModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Supplier Purchase Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    {!! Form::model('', [
                    'id' => 'add_form',
                    'method' => 'GET',
                    'enctype' => 'multipart/form-data',
                    'route' => ['supplier-purchase-report'],
                    ]) !!}
                    @csrf
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <div class="form-group">
                                {!! Form::text('startDate', (isset($startDate)&&$startDate!='') ? date('d-m-Y',strtotime($startDate)) : '', ['class' => 'form-control', 'readonly'=>"", 'placeholder'=>"Start Date", 'id' => 'start_date']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="form-group">
                                {!! Form::text('endDate', (isset($endDate)&&$endDate!='') ? date('d-m-Y',strtotime($endDate)) : '', ['class' => 'form-control', 'readonly'=>"", 'placeholder'=>"End Date", 'id' => 'end_date']) !!}
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="form-group">
                                <label>@lang('index.suppliers') </label>
                                <select name="supplier_id" id="supplier_id" class="form-control select2">
                                    <option value="">@lang('index.select')</option>
                                    @if(isset($supplier_id))
                                        @foreach ($suppliers as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ isset($supplier_id) && $supplier_id == $value->id ? 'selected' : '' }}>
                                                {{ $value->name }} ({{ $value->supplier_id }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="form-group">
                                <label>@lang('index.status') </label>
                                <select name="purchase_status" id="purchase_status" class="form-control select2">
                                    <option value="">@lang('index.select')</option>
                                    @if(isset($purchase_status))
                                        <option value="Draft" {{ isset($purchase_status) && $purchase_status == "Draft" ? 'selected' : '' }}>Draft</option>
                                        <option value="Completed" {{ isset($purchase_status) && $purchase_status == "Completed" ? 'selected' : '' }}>Completed</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 mt-3">
                            <button type="submit" name="submit" value="submit"
                                class="btn w-100 bg-blue-btn">@lang('index.submit')</button>
                        </div>
                        <div class="col-md-4 mt-3">
                            <a href="{{ route('supplier-purchase-report') }}" style="text-decoration: none;color:white;"><button type="button" value="reset" class="btn bg-second-btn w-100">Reset</button></a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
@endsection
@section('script')
<script src="{!! $baseURL . 'assets/datatable_custom/jquery-3.3.1.js' !!}"></script>
<script src="{!! $baseURL . 'assets/dataTable/jquery.dataTables.min.js' !!}"></script>
<script src="{!! $baseURL . 'assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js' !!}"></script>
<script src="{!! $baseURL . 'assets/dataTable/dataTables.bootstrap4.min.js' !!}"></script>
<script src="{!! $baseURL . 'assets/dataTable/dataTables.buttons.min.js' !!}"></script>
<script src="{!! $baseURL . 'assets/dataTable/buttons.html5.min.js' !!}"></script>
<script src="{!! $baseURL . 'assets/dataTable/buttons.print.min.js' !!}"></script>
<script src="{!! $baseURL . 'assets/dataTable/jszip.min.js' !!}"></script>
<script src="{!! $baseURL . 'assets/dataTable/pdfmake.min.js' !!}"></script>
<script src="{!! $baseURL . 'assets/dataTable/vfs_fonts.js' !!}"></script>
<script src="{!! $baseURL . 'frequent_changing/newDesign/js/forTable.js' !!}"></script>
<script src="{!! $baseURL . 'frequent_changing/js/custom_report.js' !!}"></script>
<script src="{!! $baseURL . 'frequent_changing/js/order.js' !!}"></script>
<script>
    function parseDMYtoDate(dmy) {
        const [day, month, year] = dmy.split('-');
        return new Date(`${year}-${month}-${day}`);
    }
    $('#start_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
    }).on('changeDate', function (e) {
        const startDate = e.date;
        $('#end_date').datepicker('setStartDate', startDate);
        const completeDateVal = $('#end_date').val();
        if (completeDateVal) {
            const completeDate = parseDMYtoDate(completeDateVal);
            if (completeDate < startDate) {
                $('#end_date').datepicker('update', startDate);
            }
        }
    });
    $('#end_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
    }).on('changeDate', function (e) {
        const completeDate = e.date;
        const startDateVal = $('#start_date').val();
        if (startDateVal) {
            const startDate = parseDMYtoDate(startDateVal);
            if (completeDate < startDate) {
                $('#end_date').datepicker('update', startDate);
            }
        }
    });
    $("#supplier_id").select2({
        dropdownParent: $("#filterModal"),
    });
    $("#purchase_status").select2({
        dropdownParent: $("#filterModal"),
    });
</script>
@endsection