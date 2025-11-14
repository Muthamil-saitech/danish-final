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
                <h5 class="mb-0">Total PO: {{ $total_manufactures }}</h5>
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
                            <th class="width_10_p">@lang('index.ppcrc_no')</th>
                            <th class="width_10_p">@lang('index.drawer_no')</th>
                            <th class="width_10_p">@lang('index.part_no') </th>
                            <th class="width_10_p">@lang('index.part_name') </th>
                            <th class="width_10_p">@lang('index.status')</th>
                            <th class="width_10_p">@lang('index.start_date')</th>
                            <th class="width_10_p">@lang('index.delivery_date')</th>
                            <th class="width_10_p">@lang('index.manufacture_stages')</th>
                            <th class="width_10_p">@lang('index.consumed_time')</th>
                            <th class="width_10_p">@lang('index.prod_quantity')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($obj && !empty($obj))
                        <?php
                        $i = 1;
                        ?>
                        @endif
                        @foreach ($obj as $value)
                        @php
                        $status = $value->manufacture_status;
                        $badgeClass = match($status) {
                        'done' => 'badge bg-success',
                        'draft' => 'badge bg-secondary',
                        'inProgress' => 'badge bg-warning text-dark',
                        };
                        $prodInfo = getFinishedProductInfo($value->product_id);
                        @endphp
                        <tr>
                            <td class="c_center">{{ $i++ }}</td>
                            <td>{{ safe($value->reference_no) }}</td>
                            <td>{{ safe($value->drawer_no) }}</td>
                            <td>{{ safe($prodInfo->code) }}</td>
                            <td>{{ safe($prodInfo->name) }}</td>
                            <td>
                                <span class="{{ $badgeClass }}">
                                    {{ safe(manufactureStatusShow($status)) }}
                                </span>
                            </td>
                            <td>{{ safe(getDateFormat($value->start_date)) }}</td>
                            <td>{{ $value->complete_date != null ? safe(getDateFormat($value->complete_date)) : 'N/A' }}
                            </td>
                            <td>{{ safe($value->stage_name) }}</td>
                            <td>{{ safe($value->consumed_time) }}</td>
                            <td>{{ $value->product_quantity ?? 0 }}</td>
                        </tr>
                        @endforeach
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
                    <h5 class="modal-title" id="exampleModalLabel">Production Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    {!! Form::model('', [
                    'id' => 'add_form',
                    'method' => 'GET',
                    'enctype' => 'multipart/form-data',
                    'route' => ['production-report'],
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
                                {!! Form::text('endDate', (isset($endDate)&&$endDate!='') ? date('d-m-Y',strtotime($endDate)) : '', ['class' => 'form-control', 'readonly'=>"", 'placeholder'=>"Delivery Date", 'id' => 'delivery_date']) !!}
                            </div>
                        </div>
                        {{-- <div class="col-md-12 mb-2">
                            <div class="form-group">
                                <label>@lang('index.customer') </label>
                                <select name="customer_id" id="fil_customer_id" class="form-control select2">
                                    <option value="">@lang('index.select')</option>
                                    @if(isset($customer_id))
                                    @foreach ($customers as $key => $value)
                                    <option value="{{ $value->id }}"
                                        {{ isset($customer_id) && $customer_id == $value->id ? 'selected' : '' }}>
                                        {{ $value->name }} ({{ $value->customer_id }})
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div> --}}
                        <div class="col-md-12 mb-2">
                            <div class="form-group">
                                <label>@lang('index.drawer_no') </label>
                                <select name="drawer_id" id="drawer_id" class="form-control select2">
                                    <option value="">@lang('index.select')</option>
                                    @if(isset($drawer_id))
                                        @foreach ($drawings as $value)
                                        <option value="{{ $value->id }}"
                                            {{ isset($drawer_id) && $drawer_id == $value->id ? 'selected' : '' }}>
                                            {{ $value->drawer_no }}
                                        </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="form-group">
                                <label>@lang('index.manufacture_stages') </label>
                                <select name="stage_name" id="stage_name" class="form-control select2">
                                    <option value="">@lang('index.select')</option>
                                    @if(isset($stage_name))
                                        @foreach ($production_stages as $value)
                                        <option value="{{ $value->name }}"
                                            {{ isset($stage_name) && $stage_name == $value->name ? 'selected' : '' }}>
                                            {{ $value->name }}
                                        </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="form-group">
                                <label>@lang('index.status') </label>
                                <select name="m_status" id="m_status" class="form-control select2">
                                    <option value="">@lang('index.select')</option>
                                    @if(isset($m_status))
                                        <option value="Draft" {{ isset($m_status) && $m_status == "Draft" ? 'selected' : '' }}>@lang('index.draft')</option>
                                        <option value="inProgress" {{ isset($m_status) && $m_status == "inProgress" ? 'selected' : '' }}>@lang('index.in_progress')</option>
                                        <option value="Done" {{ isset($m_status) && $m_status == "Done" ? 'selected' : '' }}>@lang('index.done')</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 mt-3">
                            <button type="submit" name="submit" value="submit" class="btn w-100 bg-blue-btn">@lang('index.submit')</button>
                        </div>
                        <div class="col-md-4 mt-3">
                            <a href="{{ route('production-report') }}" style="text-decoration: none;color:white;"><button type="button" value="reset" class="btn bg-second-btn w-100">Reset</button></a>
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
        $('#delivery_date').datepicker('setStartDate', startDate);
        const completeDateVal = $('#delivery_date').val();
        if (completeDateVal) {
            const completeDate = parseDMYtoDate(completeDateVal);
            if (completeDate < startDate) {
                $('#delivery_date').datepicker('update', startDate);
            }
        }
    });
    $('#delivery_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
    }).on('changeDate', function (e) {
        const completeDate = e.date;
        const startDateVal = $('#start_date').val();
        if (startDateVal) {
            const startDate = parseDMYtoDate(startDateVal);
            if (completeDate < startDate) {
                $('#delivery_date').datepicker('update', startDate);
            }
        }
    });
    $("#drawer_id").select2({
        dropdownParent: $("#filterModal"),
    });
    $("#m_status").select2({
        dropdownParent: $("#filterModal"),
    });
    $("#stage_name").select2({
        dropdownParent: $("#filterModal"),
    });
</script>
@endsection