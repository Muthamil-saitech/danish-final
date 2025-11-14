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
                <h2 class="top-left-header">{{ isset($title) && $title ? $title : ''}}</h2>
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
        'route' => ['payment-report'],
        'autocomplete' => 'off'
        ]) !!}
        @csrf
        <div class="row g-2 mb-3">
            <div class="col-sm-4 mb-2">
                <div class="form-group">
                    <select name="type" class="form-control select2" id="type">
                        <option value="">@lang('index.type')</option>
                        <option {{ isset($type) && $type == 'tbl_customer_due_receives' ? 'selected' : '' }} value="tbl_customer_due_receives">Customer Receives List</option>
                        <option {{ isset($type) && $type == 'tbl_supplier_payments' ? 'selected' : '' }} value="tbl_supplier_payments">Supplier Payment List</option>
                        <option {{ isset($type) && $type == 'tbl_partner_instrument_payments' ? 'selected' : '' }} value="tbl_partner_instrument_payments">Instruments Payment List</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4 mb-2 customers-div {{ isset($customer_id) ? '' : 'd-none' }}">
                <div class="form-group">
                    <select name="customer_id" class="form-control select2" id="customer_id">
                        <option value="">@lang('index.customer')</option>
                        @if(isset($customers) && $customers->isNotEmpty())
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ isset($customer_id) && $customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name.'('.$customer->customer_id.')' }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-sm-4 mb-2 suppliers-div {{ isset($supplier_id) ? '' : 'd-none' }}">
                <div class="form-group">
                    <select name="supplier_id" class="form-control select2" id="supplier_id">
                        <option value="">@lang('index.suppliers')</option>
                        @if(isset($suppliers) && $suppliers->isNotEmpty())
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ isset($supplier_id) && $supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name.'('.$supplier->supplier_id.')' }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-sm-4 mb-2 partners-div {{ isset($partner_id) ? '' : 'd-none' }}">
                <div class="form-group">
                    <select name="partner_id" class="form-control select2" id="partner_id">
                        <option value="">@lang('index.partners')</option>
                        @if(isset($partners) && $partners->isNotEmpty())
                            @foreach($partners as $partner)
                                <option value="{{ $partner->id }}" {{ isset($partner_id) && $partner_id == $partner->id ? 'selected' : '' }}>{{ $partner->name.'('.$partner->partner_id.')' }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-4 position-relative">
                <input type="text" name="search_reference" id="search_reference" class="form-control" placeholder="Search Reference No" value="{{ isset($search_reference) ? $search_reference : ''}}">
                <input type="hidden" name="ref_id" id="ref_id" value="{{ isset($ref_id) ? $ref_id : ''}}">
                <input type="hidden" name="ref_detail_id" id="ref_detail_id" value="{{ isset($ref_detail_id) ? $ref_detail_id : ''}}">
                <div class="search_pay_results position-absolute w-100 bg-white border rounded shadow-sm" style="z-index: 1000; display:none;"></div>
            </div>
            <div class="col-md-2 col-sm-2">
                <div class="form-group">
                    {!! Form::text('startDate', (isset($startDate) && $startDate != '') ? date('d-m-Y', strtotime($startDate)) : '', ['class' => 'form-control', 'readonly' => "", 'placeholder' => "Start Date", 'id' => 'start_date']) !!}
                </div>
            </div>
            <div class="col-md-2 col-sm-2">
                <div class="form-group">
                    {!! Form::text('endDate', (isset($endDate) && $endDate != '') ? date('d-m-Y', strtotime($endDate)) : '', ['class' => 'form-control', 'readonly' => "", 'placeholder' => "End Date", 'id' => 'end_date']) !!}
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="d-flex justify-content-start align-items-center gap-2">
                    <button type="submit" name="submit" value="submit" class="btn bg-blue-btn"><iconify-icon icon="solar:check-circle-broken"></iconify-icon>Search</button>
                    <a class="btn bg-second-btn" href="{{ route('payment-report') }}"><iconify-icon icon="solar:round-arrow-left-broken"></iconify-icon>Reset</a>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
        <div class="row mt-3">
            <div class="col-md-12 mb-3 customers-div {{ isset($customer_id) ? '' : 'd-none' }}">
                <h5 class="mb-2 text-end">Total Customer Receives: {{ isset($total_orders) ? $total_orders : '0' }}</h5>
                <div class="box-wrapper">
                    <div class="box-header with-border">
                        <h3 class="box-title"><iconify-icon icon="solar:database-broken"></iconify-icon><span class="ms-2">Customer Receive List</span></h3>
                        <input type="hidden" class="datatable_name" data-filter="export" data-title="Payment Report" data-id_name="datatable_2">
                    </div>
                    <div class="table-box mt-2">
                        <!-- /.box-header -->
                        <div class="table-responsive">
                            <table id="datatable_2" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="width_1_p">@lang('index.sn')</th>
                                        <th class="width_10_p">@lang('index.po_no')</th>
                                        <th class="width_10_p">@lang('index.po_date')</th>
                                        <th class="width_10_p">@lang('index.customer')<br>(Code)</th>
                                        <th class="width_10_p">@lang('index.total_amount')</th>
                                        <th class="width_10_p">@lang('index.paid_amount')</th>
                                        <th class="width_10_p">@lang('index.due_amount')</th>
                                        <th class="width_10_p">@lang('index.payment_status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($obj1) && $obj1->isNotEmpty())
                                        <?php
                                        $i = 1;
                                        ?>
                                        @foreach ($obj1 as $value)
                                            @foreach ($value->orderPayment as $detail)
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $value->reference_no.'/'.$detail->line_item_no  }}</td>
                                                    <td>{{ $value->po_date ? getDateFormat($value->po_date) : '-' }}</td>
                                                    <td>{{ getCustomerNameById($value->customer_id) }}<br><small>{{ '('.getCustomerCodeById($value->customer_id).')' }}</small></td>
                                                    <td>{{ getAmtCustom($detail->orderInvoice->amount) }}</td>
                                                    <td>{{ getAmtCustom($detail->orderInvoice->paid_amount) }}</td>
                                                    <td>{{ getAmtCustom($detail->orderInvoice->due_amount) }}</td>
                                                    <td>
                                                        <h6>@if($detail->orderInvoice->due_amount==0.00) <span class="badge bg-success">Paid</span> @elseif($detail->orderInvoice->paid_amount==0.00) <span class="badge bg-danger">Unpaid</span> @else <span class="badge bg-info">Partially Paid</span>@endif</h6>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @else
                                        <tr><td colspan="3">No data found</td></tr></p>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>

                </div>
            </div>
            <div class="col-md-12 mb-3 suppliers-div {{ isset($supplier_id) ? '' : 'd-none' }}">
                <h5 class="mb-2 text-end">Total Supplier Payment: {{ isset($obj2) ? count($obj2) : '0' }}</h5>
                <div class="box-wrapper">
                    <div class="box-header with-border">
                        <h3 class="box-title"><iconify-icon icon="solar:database-broken"></iconify-icon><span class="ms-2">Supplier Payment List</span></h3>
                        <input type="hidden" class="datatable_name" data-filter="export" data-title="Payment Report" data-id_name="datatable_3">
                    </div>
                    <div class="table-box mt-2">
                        <!-- /.box-header -->
                        <div class="table-responsive">
                            <table id="datatable_3" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="width_1_p">@lang('index.sn')</th>
                                        <th class="width_1_p">@lang('index.purchase_no')</th>
                                        <th class="width_10_p">@lang('index.purchase_date')</th>
                                        <th class="width_10_p">@lang('index.supplier_name')<br>(Code)</th>
                                        <th class="width_10_p">@lang('index.total_amount')</th>
                                        <th class="width_10_p">@lang('index.paid_amount')</th>
                                        <th class="width_10_p">@lang('index.due_amount')</th>
                                        {{-- <th class="width_10_p">@lang('index.status')</th> --}}
                                        <th class="width_10_p">@lang('index.due_days')</th>
                                        <th class="width_10_p">@lang('index.payment_status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($obj2) && $obj2->isNotEmpty())
                                        <?php
                                        $j = 1;
                                        ?>
                                        @foreach ($obj2 as $value)
                                            {{-- @foreach ($value->details as $detail) --}}
                                            <tr>
                                                <td>{{ $j++ }}</td>
                                                <td>{{ $value->reference_no }}</td>
                                                <td>{{ getDateFormat($value->date) }}</td>
                                                <td>{{ getSupplierName($value->supplier) }}</td>
                                                <td>{{ getAmtCustom($value->subtotal) }}</td>
                                                <td>{{ getAmtCustom($value->paid) }}</td>
                                                <td>{{ getAmtCustom($value->due) }}</td>
                                                @php
                                                $currentDate = \Carbon\Carbon::now();
                                                $purchaseDate = \Carbon\Carbon::parse($value->date);
                                                $due_days = $purchaseDate->diffInDays($currentDate);
                                                @endphp
                                                <td>{{ $due_days }} {{ $due_days <= 1 ? 'day' : 'days' }}</td>
                                                @php
                                                $payments = $value->supplierPayments;
                                                $totalPaid = $payments->sum('pay_amount');
                                                $totalDue = $payments->sum('pay_amount') - $payments->sum('bal_amount');
                                                $isEmpty = $payments->isEmpty();
                                                // dd($payments);
                                                $paymentPurchaseIds = $payments->pluck('purchase_id')->toArray();
                                                $paymentStatus = $payments->pluck('payment_status')->toArray();
                                                @endphp
                                                <td>
                                                    @if($value->due != 0 && !in_array($value->id, $paymentPurchaseIds))
                                                        @if($totalPaid == 0)
                                                            <h6><span class="badge bg-secondary">Hold</span></h6>
                                                        @else
                                                            <h6><span class="badge bg-warning text-dark">Initiated</span></h6>
                                                        @endif
                                                    @elseif($value->paid == 0 && in_array($value->id, $paymentPurchaseIds))
                                                        <h6><span class="badge bg-warning text-dark">Initiated</span></h6>
                                                    @elseif($value->due == 0)
                                                        <h6><span class="badge bg-success">Paid</span></h6>
                                                    @else
                                                        <h6><span class="badge bg-info text-dark">Partially Paid</span></h6>
                                                    @endif
                                                </td>
                                            </tr>
                                            {{-- @endforeach --}}
                                        @endforeach
                                    @else
                                        <tr><td colspan="3">No data found</td></tr></p>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>

                </div>
            </div>
            <div class="col-md-12 mb-3 partners-div {{ isset($partner_id) ? '' : 'd-none' }}">
                <h5 class="mb-2 text-end">Total Instrument Payment: {{ isset($total_io) ? $total_io : '0' }}</h5>
                <div class="box-wrapper">
                    <div class="box-header with-border">
                        <h3 class="box-title"><iconify-icon icon="solar:database-broken"></iconify-icon><span class="ms-2">Instruments Payment List</span></h3>
                        <input type="hidden" class="datatable_name" data-filter="export" data-title="Payment Report" data-id_name="datatable_4">
                    </div>
                    <div class="table-box mt-2">
                        <!-- /.box-header -->
                        <div class="table-responsive">
                            <table id="datatable_4" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="width_1_p">@lang('index.sn')</th>
                                        <th class="width_10_p">@lang('index.reference_no')</th>
                                        <th class="width_10_p">@lang('index.date')</th>
                                        <th class="width_10_p">@lang('index.partner_name')<br>(Code)</th>
                                        <th class="width_10_p">@lang('index.total_amount')</th>
                                        <th class="width_10_p">@lang('index.paid_amount')</th>
                                        <th class="width_10_p">@lang('index.due_amount')</th>
                                        <th class="width_10_p">@lang('index.payment_status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($obj3) && $obj3->isNotEmpty())
                                        <?php
                                        $k = 1;
                                        ?>
                                        @foreach ($obj3 as $value)
                                            @foreach ($value->details as $detail)
                                                <tr>
                                                    <td>{{ $k++ }}</td>
                                                    <td>{{ $value->reference_no.'/'.$detail->line_item_no  }}</td>
                                                    <td>{{ $value->io_date ? getDateFormat($value->io_date) : '-' }}</td>
                                                    <td>{{ $value->partner->name.'('.$value->partner->partner_id.')' }}</td>
                                                    <td>{{ getAmtCustom($detail->paymentInvoice->amount) }}</td>
                                                    <td>{{ getAmtCustom($detail->paymentInvoice->paid_amount) }}</td>
                                                    <td>{{ getAmtCustom($detail->paymentInvoice->due_amount) }}</td>
                                                    <td>
                                                        <h6>@if($detail->paymentInvoice->due_amount==0.00) <span class="badge bg-success">Paid</span> @elseif($detail->paymentInvoice->paid_amount==0.00) <span class="badge bg-danger">Unpaid</span> @else <span class="badge bg-info">Partially Paid</span>@endif</h6>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @else
                                        <tr><td colspan="3">No data found</td></tr></p>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
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
    $(document).ready(function() {
        function toggleDivs() {
            let selectedType = $('#type').val();
            $('.customers-div, .suppliers-div, .partners-div').addClass('d-none');
            if (selectedType === 'tbl_customer_due_receives') {
                $('.customers-div').removeClass('d-none');
            } else if (selectedType === 'tbl_supplier_payments') {
                $('.suppliers-div').removeClass('d-none');
            } else if (selectedType === 'tbl_partner_instrument_payments') {
                $('.partners-div').removeClass('d-none');
            } else {
                $('.customers-div').addClass('d-none');
                $('.suppliers-div').addClass('d-none');
                $('.partners-div').addClass('d-none');
            }
        }
        toggleDivs();
        $('#type').on('change', function() {
            toggleDivs();
            $('#search_reference').val('');
            $('#ref_id').val('');
            $('#ref_detail_id').val('');
        });
    });
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
    $('#search_reference').on('input', function() {
        let hidden_base_url = $("#hidden_base_url").val();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        let query = $(this).val();
        let type = $("#type").val();
        if (query.length > 1) {
            $.ajax({
                type: "POST",
                url: hidden_base_url + "reference-auto-search",
                data: {
                    query: query,
                    type: type,
                    _token: csrfToken
                },
                dataType: "json",
                success: function(data) {
                    let searchResults = $(".search_pay_results");
                    searchResults.empty();
                    if (data.length > 0) {
                        $.each(data, function(index, item) {
                            searchResults.append(`
                                <div class="suggestion-item p-2" 
                                    style="cursor:pointer;" 
                                    data-id="${item.order_id}" 
                                    data-detail-id="${item.detail_id}"
                                    data-name="${item.label}">
                                    ${item.label}
                                </div>
                            `);
                        });
                    } else {
                        searchResults.append('<div class="p-2 text-muted">No reference found</div>');
                    }
                    searchResults.show();
                },
                error: function() {
                    console.error("Failed to fetch product details.");
                },
            });
        } else {
            $(".search_pay_results").hide();
        }
    });
    $(document).on('click', '.suggestion-item', function() {
        let name = $(this).data('name');
        let id = $(this).data('id');
        let detailid = $(this).data('detail-id');
        $('#search_reference').val(name);
        $('#ref_id').val(id);
        $('#ref_detail_id').val(detailid);
        $('.search_pay_results').hide();
        // loadCustomerReport(id, name);
    });
</script>
@endsection