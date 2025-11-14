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
                    <input type="hidden" class="datatable_name" data-filter="export" data-title="{{ isset($title) && $title ? $title : '' }}" data-id_name="datatable">
                </div>
                <div class="col-md-6 text-end">
                    <h5 class="">Total Sales: {{ isset($obj) && $obj ? count($obj) : '0' }}</h5>
                </div>
            </div>
        </section>
        <div class="box-wrapper">
            <div class="table-box">
                <div class="row">
                    <label class="mt-2 mb-2">Filter By</label>
                </div>
                {!! Form::model('', [
                'id' => 'add_form',
                'method' => 'GET',
                'enctype' => 'multipart/form-data',
                'route' => ['report.sales'],
                'autocomplete' => 'off'
                ]) !!}
                @csrf
                <div class="row mb-3">
                    <div class="col-md-3 col-sm-4">
                        <input type="text" class="form-control" name="search_customer" id="search_customer" placeholder="Search Customer" value="{{ isset($search_customer) ? $search_customer : '' }}">
                        <input type="hidden" name="customer_id" id="sale_customer_id" value="{{ isset($customer_id) ? $customer_id : '' }}">
                        <div class="search_sale_results position-absolute w-23 bg-white border rounded shadow-sm" style="z-index: 1000; display:none;"></div>
                    </div>
                    <div class="col-md-3 col-sm-4">
                        <input type="text" class="form-control" name="search_sale" id="search_sale" placeholder="Search Sales Detail" value="{{ isset($search_sale) ? $search_sale : '' }}">
                    </div>
                    <div class="col-md-3 col-sm-2 mb-3">
                        <div class="form-group">
                            {!! Form::text('startDate', (isset($startDate) && $startDate != '') ? date('d-m-Y', strtotime($startDate)) : '', ['class' => 'form-control', 'readonly' => "", 'placeholder' => "Start Date", 'id' => 'sale_start_date']) !!}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-2 mb-3">
                        <div class="form-group">
                            {!! Form::text('endDate', (isset($endDate) && $endDate != '') ? date('d-m-Y', strtotime($endDate)) : '', ['class' => 'form-control', 'readonly' => "", 'placeholder' => "End Date", 'id' => 'sale_complete_date']) !!}
                        </div>
                    </div>
                    <div class="col-md-4 d-flex gap-3">
                        <button type="submit" name="submit" value="submit" class="btn bg-blue-btn"><iconify-icon icon="solar:check-circle-broken"></iconify-icon>Search</button>
                        <a class="btn bg-second-btn" href="{{ route('report.sales') }}"><iconify-icon icon="solar:round-arrow-left-broken"></iconify-icon>Reset</a>
                    </div>
                </div>
                {!! Form::close() !!}
                <!-- /.box-header -->
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped">
                        <thead>
                            <tr>
                                <th class="width_1_p">@lang('index.sn')</th>
                                <th class="width_10_p">@lang('index.sale_date')</th>
                                <th class="width_10_p">@lang('index.invoice_no')</th>
                                <th class="width_10_p">@lang('index.order_type')</th>
                                <th class="width_10_p">@lang('index.challan_no')</th>
                                <th class="width_10_p">Customer Name (Code)</th>
                                <th class="width_10_p">@lang('index.total_amount')</th>
                                {{-- <th class="width_10_p">@lang('index.paid_amount')</th> --}}
                                {{-- <th class="width_10_p">@lang('index.due_amount')</th> --}}
                                {{-- <th class="width_10_p">@lang('index.status')</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @if ($obj && !empty($obj))
                                <?php
                                $i = 1;
                                ?>
                            @endif
                            @foreach ($obj as $value)
                                <tr>
                                    <td class="c_center">{{ $i++ }}</td>
                                    <td>{{ getDateFormat($value->sale_date) }}</td>
                                    <td>{{ $value->reference_no }}</td>
                                    <td>{{ $value->nature_of_business }}</td>
                                    <td>{{ $value->challan_no }}</td>
                                    <td>{{ getCustomerNameById($value->customer_id) }} ({{ getCustomerCodeById($value->customer_id) }})</td>
                                    <td>{{ getAmtCustom($value->grand_total) }}</td>
                                    {{-- <td>{{ getAmtCustom($value->pay) }}</td> --}}
                                    {{-- <td>{{ getAmtCustom($value->bal) }}</td> --}}
                                    {{-- <td>{{ getCurrency($value->discount) }}</td> --}}
                                    {{-- <td><h6>@if($value->receive_status=="Paid") <span class="badge bg-success">Paid</span> @elseif($value->receive_status=="Initiated") <span class="badge bg-danger">Initiated</span> @else <span class="badge bg-info">Partially Paid</span>@endif</h6></td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
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
    <script src="{!! $baseURL . 'frequent_changing/js/sales.js' !!}"></script>
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
                        let searchResults = $(".search_sale_results");
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
                $(".search_sale_results").hide();
            }
        });
        $(document).on('click', '.suggestion-item', function() {
            let name = $(this).data('name');
            let id = $(this).data('id');
            $('#search_customer').val(name);
            $('#sale_customer_id').val(id);
            $('.search_sale_results').hide();
        });
        function parseDMYtoDate(dmy) {
            const [day, month, year] = dmy.split('-');
            return new Date(`${year}-${month}-${day}`);
        }
        $('#sale_start_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
        }).on('changeDate', function (e) {
            const startDate = e.date;
            $('#sale_complete_date').datepicker('setStartDate', startDate);
            const completeDateVal = $('#sale_complete_date').val();
            if (completeDateVal) {
                const completeDate = parseDMYtoDate(completeDateVal);
                if (completeDate < startDate) {
                    $('#sale_complete_date').datepicker('update', startDate);
                }
            }
        });
        $('#sale_complete_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
        }).on('changeDate', function (e) {
            const completeDate = e.date;
            const startDateVal = $('#sale_start_date').val();
            if (startDateVal) {
                const startDate = parseDMYtoDate(startDateVal);
                if (completeDate < startDate) {
                    $('#sale_complete_date').datepicker('update', startDate);
                }
            }
        });
    </script>
@endsection
