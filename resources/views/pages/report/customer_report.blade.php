@extends('layouts.app')
@section('content')
<?php
$baseURL = getBaseURL();
$setting = getSettingsInfo();
$base_color = "#6ab04c";
if (isset($setting->base_color) && $setting->base_color) {
    $base_color = $setting->base_color;
}
?>
<section class="main-content-wrapper">
    @include('utilities.messages')
    <section class="content-header">
        <div class="row">
            <div class="col-md-6">
                <h2 class="top-left-header">{{isset($title) && $title?$title:''}}</h2>
            </div>
            <div class="col-md-offset-4 col-md-2">

            </div>
        </div>
    </section>
    <div class="box-wrapper">
        {!! Form::model('', [
            'id' => 'add_form',
            'method' => 'GET',
            'enctype' => 'multipart/form-data',
            'route' => ['customer-report'],
            'autocomplete' => 'off'
        ]) !!}
        @csrf
        <div class="row align-items-center mb-4 g-3">
            <div class="col-md-2">
                <label for="search_customer" class="form-label mb-0">Search Customer</label>
            </div>
            <div class="col-md-4 position-relative">
                <input type="text" name="search_customer" id="search_customer" class="form-control" placeholder="E.g, John or CUS001" value="{{ isset($search_customer) ? $search_customer : '' }}">
                <input type="hidden" id="customer_id" name="customer_id" value="{{ isset($customer_id) ? $customer_id : '' }}">
                <div class="search_results position-absolute w-100 bg-white border rounded shadow-sm" style="z-index: 1000; display:none;"></div>
            </div>
            <div class="col-md-4 d-flex align-items-center gap-2">
                <button type="submit" name="submit" value="submit" class="btn bg-blue-btn">
                    <iconify-icon icon="solar:check-circle-broken"></iconify-icon> Search
                </button>
                <a class="btn bg-second-btn" href="{{ route('customer-report') }}">
                    <iconify-icon icon="solar:round-arrow-left-broken"></iconify-icon> Reset
                </a>
            </div>
        </div>
        {!! Form::close() !!}
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="po-tab" data-bs-toggle="tab" data-bs-target="#po" type="button" role="tab">
                            Orders
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="prod-tab" data-bs-toggle="tab" data-bs-target="#prod" type="button" role="tab">
                            Production
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stock" type="button" role="tab">Stock</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="inspection-tab" data-bs-toggle="tab" data-bs-target="#inspection" type="button" role="tab">Inspection</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="quotation-tab" data-bs-toggle="tab" data-bs-target="#challan" type="button" role="tab">Delivery Challan</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">Sales</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="customer-io-tab" data-bs-toggle="tab" data-bs-target="#customer-io" type="button" role="tab">Customer I/O</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="customer-receives-tab" data-bs-toggle="tab" data-bs-target="#customer-receives" type="button" role="tab">Customer Receives</button>
                    </li>
                </ul>
                <div class="tab-content mt-3" id="reportTabsContent">
                    <div class="tab-pane fade show active" id="po" role="tabpanel">
                        <div class="box-wrapper">
                            <div class="box-header with-border">
                                <h3 class="box-title"><iconify-icon icon="solar:database-broken"></iconify-icon><span class="ms-2">Customer Orders</span></h3>
                                <input type="hidden" class="datatable_name" data-filter="export" data-title="Customer Report" data-id_name="datatable_2">
                            </div>
                            <div class="table-box mt-2">
                                <!-- /.box-header -->
                                <div class="table-responsive">
                                    <table id="datatable_2" class="table table-striped datatable_dashboard">
                                        <thead>
                                            <tr>
                                                <th class="width_10_p">@lang('index.po_date')</th>
                                                <th class="width_10_p">@lang('index.po_no')</th>
                                                <th class="width_10_p">@lang('index.order_type')</th>
                                                <th class="width_10_p">@lang('index.customer_name')<br>(@lang('index.code'))</th>
                                                <th class="width_10_p">@lang('index.total_value')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($orders) && !empty($orders))                                          
                                                @foreach ($orders as $value)
                                                    @foreach ($value->details as $detail)
                                                    <tr>
                                                        <td>{{ $value->po_date ?  getDateFormat($value->po_date) : '-' }}</td>
                                                        <td>{{ $value->reference_no.'/'.$detail->line_item_no }}</td>
                                                        <td>{{ $value->order_type == "Quotation" ? "Labor" : "Sales" }}</td>
                                                        <td>{{ $value->customer->name }}<br> ({{ $value->customer->customer_id }})</td>
                                                        <td>{{ getAmtCustom($detail->sub_total) }}</td>
                                                    </tr>
                                                    @endforeach
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="prod" role="tabpanel">
                        <div class="box-wrapper">
                            <div class="box-header with-border">
                                <h3 class="box-title"><iconify-icon icon="solar:database-broken"></iconify-icon><span class="ms-2">Production</span></h3>
                                <input type="hidden" class="datatable_name" data-filter="export" data-title="Customer Report" data-id_name="datatable_3">
                            </div>
                            <div class="table-box mt-2">
                                <!-- /.box-header -->
                                <div class="table-responsive">
                                    <table id="datatable_3" class="table table-striped datatable_dashboard">
                                        <thead>
                                            <tr>
                                                <th class="width_1_p">@lang('index.sn')</th>
                                                <th class="width_10_p">@lang('index.ppcrc_no')</th>
                                                <th class="width_10_p">@lang('index.customer_name')<br>(@lang('index.code'))</th>
                                                <th class="width_10_p">@lang('index.drawer_no')</th>
                                                <th class="width_10_p">@lang('index.part_no') </th>
                                                <th class="width_10_p">@lang('index.part_name') </th>
                                                <th class="width_10_p">@lang('index.start_date')</th>
                                                <th class="width_10_p">@lang('index.delivery_date')</th>
                                                <th class="width_10_p">@lang('index.manufacture_stages')</th>
                                                <th class="width_10_p">@lang('index.consumed_time')</th>
                                                <th class="width_10_p">@lang('index.prod_quantity')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($productions && !empty($productions))
                                                <?php
                                                $i = 1;
                                                ?>
                                            @endif
                                            @foreach ($productions as $value)
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
                                                <td>{{ $value->customer->name }}<br> ({{ $value->customer->customer_id }})</td>
                                                <td>{{ safe($value->drawer_no) }}</td>
                                                <td>{{ safe($prodInfo->code) }}</td>
                                                <td>{{ safe($prodInfo->name) }}</td>
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
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="stock" role="tabpanel">
                        <div class="box-wrapper">
                            <div class="box-header with-border">
                                <h3 class="box-title"><iconify-icon icon="solar:database-broken"></iconify-icon><span class="ms-2">Material Stock</span></h3>
                                <input type="hidden" class="datatable_name" data-filter="export" data-title="Customer Report" data-id_name="datatable_4">
                            </div>
                            <div class="table-box mt-2">
                                <!-- /.box-header -->
                                <div class="table-responsive">
                                    <table id="datatable_4" class="table table-striped datatable_dashboard">
                                        <thead>
                                            <tr>
                                                <th class="width_1_p">@lang('index.sn')</th>
                                                <th class="width_10_p">@lang('index.mat_type')</th>
                                                <th class="width_10_p">@lang('index.material_category')</th>
                                                <th class="width_10_p">@lang('index.material_name')(@lang('index.code'))</th>
                                                <th class="width_10_p">@lang('index.customer_name')<br>(@lang('index.code'))</th>
                                                <th class="width_10_p">@lang('index.stock')</th>
                                                <th class="width_10_p">@lang('index.alter_level')</th>
                                                <th class="width_10_p">@lang('index.floating_stock')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $i=1; @endphp
                                            @if(isset($material_stock))
                                                @foreach($material_stock as $value)
                                                    <tr>
                                                        <td class="c_center">{{ $i++ }}</td>
                                                        <td>{{ getMatTypeName($value->mat_type) }}</td>
                                                        <td>{{ getCategoryById($value->mat_cat_id) }}</td>
                                                        <td title="{{ getRMName($value->mat_id) }}">{{ substr_text(getRMName($value->mat_id),30) }}</td>
                                                        @if(!$value->customer_id)
                                                        <td title="{{ getStockCustomerNameById($value->customer_id) }}">{{ substr_text(getStockCustomerNameById($value->customer_id),30) }}</td>
                                                        @else
                                                        <td title="{{ getStockCustomerNameById($value->customer_id) }}">{{ substr_text(getStockCustomerNameById($value->customer_id),30) }}<br><small>({{ getCustomerCodeById($value->customer_id) }})</small></td>
                                                        @endif
                                                        <td>{{ $value->current_stock }} {{ getRMUnitById($value->unit_id) }}
                                                            <div id="qty_msg"></div>
                                                        </td>
                                                        <td>{{ $value->close_qty }} {{ getRMUnitById($value->unit_id) }}</td>
                                                        <td>{{ $value->float_stock }} {{ getRMUnitById($value->unit_id) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="inspection" role="tabpanel">
                        <div class="box-wrapper">
                            <div class="box-header with-border">
                                <h3 class="box-title"><iconify-icon icon="solar:database-broken"></iconify-icon><span class="ms-2">Inspection Report</span></h3>
                                <input type="hidden" class="datatable_name" data-filter="export" data-title="Customer Report" data-id_name="datatable_5">
                            </div>
                            <div class="table-box mt-2">
                                <!-- /.box-header -->
                                <div class="table-responsive">
                                    <table id="datatable_5" class="table table-striped datatable_dashboard">
                                        <thead>
                                            <tr>
                                                <th class="ir_w_1"> @lang('index.sn')</th>
                                                <th class="ir_w_16">@lang('index.ppcrc_no')</th>
                                                <th class="ir_w_16">@lang('index.drg_no')</th>
                                                <th class="ir_w_16">@lang('index.part_no')</th>
                                                <th class="ir_w_16">@lang('index.part_name')</th>
                                                <th class="ir_w_16">@lang('index.po_no')</th>
                                                <th class="ir_w_16">@lang('index.customer_name')<br>(@lang('index.code'))</th>
                                                <th class="ir_w_16">@lang('index.start_date')</th>
                                                <th class="ir_w_16">@lang('index.delivery_date')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($inspections) && !empty($inspections))
                                                @foreach ($inspections as $value)
                                                    @php $prodInfo = getFinishedProductInfo($value->product_id); @endphp
                                                    <tr>
                                                        <td class="ir_txt_center">{{ $loop->iteration }}</td>
                                                        <td>{{ $value->reference_no }}</td>
                                                        <td>{{ $value->drawer_no }}</td>
                                                        <td>{{ $prodInfo->code }}</td>
                                                        <td>{{ $prodInfo->name }}</td>
                                                        <td>{{ getPoNo($value->customer_order_id).'/'.getLineItemNo($value->customer_order_id) }}</td>
                                                        <td>{{ getCustomerNameById($value->customer_id) }}<br>({{ getCustomerCodeById($value->customer_id) }})</td>
                                                        <td>{{ getDateFormat($value->start_date) }}</td>
                                                        <td>{{ $value->complete_date!='' ? getDateFormat($value->complete_date) : ' - ' }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="challan" role="tabpanel">
                        <div class="box-wrapper">
                            <div class="box-header with-border">
                                <h3 class="box-title"><iconify-icon icon="solar:database-broken"></iconify-icon><span class="ms-2">Delivery Challan</span></h3>
                                <input type="hidden" class="datatable_name" data-filter="export" data-title="Customer Report" data-id_name="datatable_6">
                            </div>
                            <div class="table-box mt-2">
                                <!-- /.box-header -->
                                <div class="table-responsive">
                                    <table id="datatable_6" class="table table-striped datatable_dashboard">
                                        <thead>
                                            <tr>
                                                <th class="width_1_p">@lang('index.sn')</th>
                                                <th class="width_10_p">@lang('index.challan_date')</th>
                                                <th class="width_10_p">@lang('index.challan_no')</th>
                                                <th class="width_10_p">@lang('index.doc_no')</th>
                                                <th class="width_10_p">@lang('index.customer_name')<br>(@lang('index.code'))</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($quotations && !empty($quotations))
                                                <?php
                                                $i = 1;
                                                ?>
                                            @endif
                                            @foreach ($quotations as $value)
                                            <tr>
                                                <td class="c_center">{{ $i++ }}</td>
                                                <td>{{ getDateFormat($value->challan_date) }}</td>
                                                <td>{{ $value->challan_no }}</td>
                                                <td>{{ $value->material_doc_no }}</td>
                                                <td>{{ getCustomerNameById($value->customer_id) }}<br>({{ getCustomerCodeById($value->customer_id) }})</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="sales" role="tabpanel">
                        <div class="box-wrapper">
                            <div class="box-header with-border">
                                <h3 class="box-title"><iconify-icon icon="solar:database-broken"></iconify-icon><span class="ms-2">Sales</span></h3>
                                <input type="hidden" class="datatable_name" data-filter="export" data-title="Customer Report" data-id_name="datatable_7">
                            </div>
                            <div class="table-box mt-2">
                                <!-- /.box-header -->
                                <div class="table-responsive">
                                    <table id="datatable_7" class="table table-striped datatable_dashboard">
                                        <thead>
                                            <tr>
                                                <th class="width_1_p">@lang('index.sn')</th>
                                                <th class="width_10_p">@lang('index.sale_date')</th>
                                                <th class="width_10_p">@lang('index.invoice_no')</th>
                                                <th class="width_10_p">@lang('index.challan_no')</th>
                                                <th class="width_10_p">@lang('index.customer_name')<br>(@lang('index.code'))</th>
                                                <th class="width_10_p">@lang('index.total_amount')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($sales && !empty($sales))
                                            <?php
                                            $i = 1;
                                            ?>
                                            @endif
                                            @foreach ($sales as $value)
                                            <tr>
                                                <td class="c_center">{{ $i++ }}</td>
                                                <td>{{ getDateFormat($value->sale_date) }}</td>
                                                <td>{{ $value->reference_no }}</td>
                                                <td>{{ $value->challan_no }}</td>
                                                <td>{{ getCustomerNameById($value->customer_id) }}<br>({{ getCustomerCodeById($value->customer_id) }})</td>
                                                <td>{{ getAmtCustom($value->grand_total) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="customer-io" role="tabpanel">
                        <div class="box-wrapper">
                            <div class="box-header with-border">
                                <h3 class="box-title"><iconify-icon icon="solar:database-broken"></iconify-icon><span class="ms-2">Customer I/O</span></h3>
                                <input type="hidden" class="datatable_name" data-filter="export" data-title="Customer Report" data-id_name="datatable_8">
                            </div>
                            <div class="table-box mt-2">
                                <!-- /.box-header -->
                                <div class="table-responsive">
                                    <table id="datatable_8" class="table table-striped datatable_dashboard">
                                        <thead>
                                            <tr>
                                                <th>@lang('index.sn')</th>
                                                <th>@lang('index.po_no')</th>
                                                <th>@lang('index.customer_name')<br>(@lang('index.code'))</th>
                                                <th>@lang('index.date')</th>
                                                <th>@lang('index.status')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($customer_io && !empty($customer_io))
                                            <?php
                                            $i = count($customer_io);
                                            ?>
                                            @endif
                                            @foreach ($customer_io as $value)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td> 
                                                    <td>{{ $value->po_no .'/'. $value->line_item_no }}</td>
                                                    <td>{{ $value->customer->name }}<br> ({{ $value->customer->customer_id }})</td>
                                                    <td>{{  date('d-m-Y', strtotime($value->date)) }}</td>
                                                    <td>
                                                        @if($value->status == 'Inward')
                                                        <span class="badge bg-secondary">Inward</span>
                                                        @else
                                                        <span class="badge bg-success">Outward</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="customer-receives" role="tabpanel">
                        <div class="box-wrapper">
                            <div class="box-header with-border">
                                <h3 class="box-title"><iconify-icon icon="solar:database-broken"></iconify-icon><span class="ms-2">Customer Receives</span></h3>
                                <input type="hidden" class="datatable_name" data-filter="export" data-title="Customer Report" data-id_name="datatable_9">
                            </div>
                            <div class="table-box mt-2">
                                <!-- /.box-header -->
                                <div class="table-responsive">
                                    <table id="datatable_9" class="table table-striped datatable_dashboard">
                                        <thead>
                                            <tr>
                                                <th class="width_1_p">@lang('index.sn')</th>
                                                <th class="width_10_p">@lang('index.po_no')</th>
                                                <th class="width_10_p">@lang('index.po_date')</th>
                                                <th class="width_10_p">@lang('index.customer')<br>(Code)</th>
                                                <th class="width_10_p">@lang('index.total_amount')</th>
                                                <th class="width_10_p">@lang('index.paid_amount')</th>
                                                <th class="width_10_p">@lang('index.due_amount')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($customer_receives && !empty($customer_receives))
                                            <?php
                                            $i = 1;
                                            ?>
                                            @endif
                                            @foreach ($customer_receives as $value)
                                                @foreach ($value->details as $detail)
                                                    @if ($detail->orderInvoice && $detail->orderInvoice->due_amount == 0)
                                                        <tr>
                                                            <td>{{ $i++ }}</td>
                                                            <td>{{ $value->reference_no . '/' . $detail->line_item_no }}</td>
                                                            <td>{{ $value->po_date ? getDateFormat($value->po_date) : '-' }}</td>
                                                            <td>
                                                                {{ getCustomerNameById($value->customer_id) }}<br>
                                                                <small>({{ getCustomerCodeById($value->customer_id) }})</small>
                                                            </td>
                                                            <td>{{ getAmtCustom($detail->orderInvoice->amount) }}</td>
                                                            <td>{{ getAmtCustom($detail->orderInvoice->paid_amount) }}</td>
                                                            <td>{{ getAmtCustom($detail->orderInvoice->due_amount) }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
<script>
    $('#search_customer').on('input', function() {
        let hidden_base_url = $("#hidden_base_url").val();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        let query = $(this).val();
        if (query.length > 1) {
            $.ajax({
                type: "POST",
                url: hidden_base_url + "customer-auto-search",
                data: {
                    query: query,
                    _token: csrfToken
                },
                dataType: "json",
                success: function(data) {
                    let searchResults = $(".search_results");
                    searchResults.empty();
                    if (data.length > 0) {
                        $.each(data, function(index, item) {
                            searchResults.append(`
                                <div class="suggestion-item p-2" 
                                    style="cursor:pointer;" 
                                    data-id="${item.id}" 
                                    data-name="${item.label}">
                                    ${item.label}
                                </div>
                            `);
                        });
                    } else {
                        searchResults.append('<div class="p-2 text-muted">No customers found</div>');
                    }
                    searchResults.show();
                },
                error: function() {
                    console.error("Failed to fetch product details.");
                },
            });
        } else {
            $(".search_results").hide();
        }
    });
    $(document).on('click', '.suggestion-item', function() {
        let name = $(this).data('name');
        let id = $(this).data('id');
        $('#search_customer').val(name);
        $('#customer_id').val(id);
        $('.search_results').hide();
        // loadCustomerReport(id, name);
    });
    function loadCustomerReport(customerId, customerName) {
        let hidden_base_url = $("#hidden_base_url").val();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(".customer_report").html(`<div class='text-center py-4'>Loading report for <strong>${customerName}</strong>...</div>`);
        $.ajax({
            type: "POST",
            url: hidden_base_url + "load-customer-report",
            data: {
                customer_id: customerId,
                _token: csrfToken
            },
            dataType: "json",
            success: function(data) {
                renderDashboard(data,customerName);
            },
            error: function () {
                $(".customer_report").html("<p class='text-danger text-center'>Failed to load data.</p>");
            }
        });
    }
    function renderDashboard(data,customerName) {
        let html = `
            <div class='text-center py-4'>Report for <strong>${customerName} for this week</strong></div>
            <div class="col-md-4">
                <div class="dashboard-card">
                    <div class="card-title text-center">Customer IO</div>
                    <p>Total: ${data.iodetails.total}</p>
                    <p>Inward: ${data.iodetails.inward}</p>
                    <p>Outward: ${data.iodetails.outward}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card">
                    <div class="card-title text-center">Quotation</div>
                    <p>Total: ${data.quotation.total}</p>
                    <p>Amount: ₹${data.quotation.quote_amount.toLocaleString()}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card">
                    <div class="card-title text-center">Orders</div>
                    <p>Total PO: ${data.orders.total_po}</p>
                    <p>Pending: ${data.orders.pending}</p>
                    <p>Confirmed: ${data.orders.confirmed}</p>
                    <p>Cancelled: ${data.orders.cancelled}</p>
                </div>
            </div>
            <div class="col-md-4 mt-2">
                <div class="dashboard-card">
                    <div class="card-title text-center">Production</div>
                    <p>Total: ${data.production.total}</p>
                    <p>Draft: ${data.production.draft}</p>
                    <p>In Progress: ${data.production.inprogress}</p>
                    <p>Done: ${data.production.done}</p>
                </div>
            </div>
            <div class="col-md-4 mt-2">
                <div class="dashboard-card">
                    <div class="card-title text-center">Sales</div>
                    <p>Total Sales: ${data.sales.total.toLocaleString()}</p>
                    <p>Amount: ₹${data.sales.total_amount.toLocaleString()}</p>
                </div>
            </div>
            <div class="col-md-4 mt-2">
                <div class="dashboard-card">
                    <div class="card-title text-center">Customer Receives</div>
                    <p>Total PO: ${data.customer_payment.total_po}</p>
                    <p>Total Amount: ₹${data.customer_payment.total_amount.toLocaleString()}</p>
                    <p>Paid Amount: ₹${data.customer_payment.paid.toLocaleString()}</p>
                    <p>Balanace Amount: ₹${data.customer_payment.balance.toLocaleString()}</p>
                </div>
            </div>
        `;
        $(".customer_report").html(html);
    }
</script>
@endsection