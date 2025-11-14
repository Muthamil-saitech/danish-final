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
        'route' => ['load-instrument-report'],
        'autocomplete' => 'off'
        ]) !!}
        @csrf
        <div class="row g-2">
            <div class="col-md-4 position-relative">
                <input type="text" name="search_instrument" id="search_instrument" class="form-control" placeholder="Search Instrument" value="{{ isset($search_instrument) ? $search_instrument : ''}}">
                <input type="hidden" name="ins_id" id="ins_id" value="{{ isset($ins_id) ? $ins_id : ''}}">
                <div class="search_ins_results position-absolute w-100 bg-white border rounded shadow-sm" style="z-index: 1000; display:none;"></div>
            </div>
            <div class="col-sm-4 mb-2">
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
            <div class="col-sm-4 mb-2">
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
                    <a class="btn bg-second-btn" href="{{ route('instrument-report') }}"><iconify-icon icon="solar:round-arrow-left-broken"></iconify-icon>Reset</a>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
        <div class="row mt-5">
            <div class="col-md-6">
                <div class="box-wrapper">
                    <div class="box-header with-border">
                        <h3 class="box-title"><iconify-icon icon="solar:database-broken"></iconify-icon><span class="ms-2">Customer I/O</span></h3>
                        <input type="hidden" class="datatable_name" data-filter="export" data-title="Instrument Report" data-id_name="datatable_2">
                    </div>
                    <div class="table-box mt-2">
                        <!-- /.box-header -->
                        <div class="table-responsive">
                            <table id="datatable_2" class="table table-striped datatable_dashboard">
                                <thead>
                                    <tr>
                                        <th class="width_1_p">@lang('index.po_no')</th>
                                        <th class="width_13_p">@lang('index.customer_name')(Code)</th>
                                        <th class="width_13_p">@lang('index.date')</th>
                                        <th class="width_13_p">@lang('index.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($customer_io) && $customer_io->isNotEmpty())
                                        @foreach ($customer_io as $value)
                                            {{-- @foreach ($value->details as $detail) --}}
                                                <tr>
                                                    <td>{{ safe($value->po_no.'/'.$value->line_item_no) }}</td>
                                                    <td>{{ safe($value->customer_name) }}({{ $value->customer_id }})</td>
                                                    <td>{{ safe(getDateFormat($value->date)) }}</td>
                                                    <td>
                                                        @if($value->status == 'Inward')
                                                        <span class="badge bg-secondary">Inward</span>
                                                        @else
                                                        <span class="badge bg-success">Outward</span>
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
            <div class="col-md-6">
                <div class="box-wrapper">
                    <div class="box-header with-border">
                        <h3 class="box-title"><iconify-icon icon="solar:database-broken"></iconify-icon><span class="ms-2">Partner I/O</span></h3>
                        <input type="hidden" class="datatable_name" data-filter="no" data-title="Instrument Report" data-id_name="datatable_3">
                    </div>
                    <div class="table-box mt-2">
                        <!-- /.box-header -->
                        <div class="table-responsive">
                            <table id="datatable_3" class="table table-striped datatable_dashboard">
                                <thead>
                                    <tr>
                                        <th class="width_1_p">@lang('index.po_no')</th>
                                        <th class="width_13_p">@lang('index.partner_name')(Code)</th>
                                        <th class="width_13_p">@lang('index.date')</th>
                                        <th class="width_13_p">@lang('index.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($partner_io) && $partner_io->isNotEmpty())
                                        @foreach ($partner_io as $value)
                                            {{-- @foreach ($value->details as $detail) --}}
                                                <tr>
                                                    <td>{{ safe($value->reference_no.'/'.$value->line_item_no) }}</td>
                                                    <td>{{ safe($value->partner_name) }}({{ $value->partner_id }})</td>
                                                    <td>{{ safe(getDateFormat($value->io_date)) }}</td>
                                                    <td>
                                                        @if($value->status == 'Inward')
                                                        <span class="badge bg-secondary">Inward</span>
                                                        @else
                                                        <span class="badge bg-success">Outward</span>
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
    $('#search_instrument').on('input', function() {
        let hidden_base_url = $("#hidden_base_url").val();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        let query = $(this).val();
        if (query.length > 1) {
            $.ajax({
                type: "POST",
                url: hidden_base_url + "instruments-auto-search",
                data: {
                    query: query,
                    _token: csrfToken
                },
                dataType: "json",
                success: function(data) {
                    let searchResults = $(".search_ins_results");
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
                        searchResults.append('<div class="p-2 text-muted">No instruments found</div>');
                    }
                    searchResults.show();
                },
                error: function() {
                    console.error("Failed to fetch product details.");
                },
            });
        } else {
            $(".search_ins_results").hide();
        }
    });
    $(document).on('click', '.suggestion-item', function() {
        let name = $(this).data('name');
        let id = $(this).data('id');
        $('#search_instrument').val(name);
        $('#ins_id').val(id);
        $('.search_ins_results').hide();
        // loadCustomerReport(id, name);
    });
    /* function loadCustomerReport(customerId, customerName) {
        let hidden_base_url = $("#hidden_base_url").val();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(".customer_report").html(`<div class='text-center py-4'>Loading report for <strong>${customerName}</strong>...</div>`);
        $.ajax({
            type: "POST",
            url: hidden_base_url + "load-customer-report",
            data: {
                ins_id: customerId,
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
    } */
</script>
@endsection