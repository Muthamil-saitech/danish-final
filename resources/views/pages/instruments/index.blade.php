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
            <div class="row">
                <div class="col-md-6">
                    <h2 class="top-left-header">{{ isset($title) && $title ? $title : '' }}</h2>
                    <input type="hidden" class="datatable_name" data-title="{{ isset($title) && $title ? $title : '' }}"
                        data-id_name="datatable">
                </div>
                <div class="col-md-6 text-end">
                    <h5 class="mb-0">Total Instruments: {{ isset($obj) ? count($obj) : '0' }} </h5>
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
                            <tr>
                                <th class="w-5 text-start">@lang('index.sn')</th>
                                <th class="w-20">@lang('index.type')</th>
                                <th class="w-20">@lang('index.instrument_category')</th>
                                <th class="w-20">@lang('index.instrument_name')</th>
                                <th class="w-20">@lang('index.instrument_code')</th>
                                <th class="w-20">@lang('index.unit')</th>
                                <th class="w-20">@lang('index.owner')</th>
                                <th class="w-20">@lang('index.customer')</th>
                                <th class="w-20">@lang('index.range/size')</th>
                                <th class="w-20">@lang('index.accuracy')</th>
                                <th class="w-20">@lang('index.make')</th>
                                <th class="w-20">@lang('index.historycardno')</th>
                                <th class="w-20">@lang('index.location')</th>
                                <th class="w-20">@lang('index.due_date')</th>
                                <th class="w-20">@lang('index.remarks')</th>
                                <th class="w-10 ir_txt_center">@lang('index.actions')</th>
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
                                    <td class="text-start">{{ $i++ }}</td>
                                    <td>
                                        @if($value->type == 1)
                                        Gauges/Checking Instruments
                                        @elseif($value->type == 2)
                                        Measuring Instruments
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                    <td>{{ getInstrumentCategoryById($value->category) }}</td>
                                    <td>{{ $value->instrument_name }}</td>
                                    <td>{{ $value->code }}</td>
                                    <td>{{ $value->unit }}</td>
                                    <td>{{ $value->owner_type==1 ? 'Own' : 'Customer' }}</td>
                                    <td>{{ getStockCustomerNameById($value->customer_id) }} <br>{{ $value->owner_type!=1 ? '('.getCustomerCodeById($value->customer_id).')' : '' }}</td>
                                    <td>{{ $value->range }}</td>
                                    <td>{{ $value->accuracy }}</td>
                                    <td>{{ $value->make }}</td>
                                    <td>{{ $value->history_card_no }}</td>
                                    <td>{{ $value->location }}</td>
                                    <td>{{ getDateFormat($value->due_date) }}</td>
                                    <td title="{{ $value->remarks }}">{{ substr_text(safe($value->remarks),20) }}</td>
                                    <td class="ir_txt_center">
                                        @if($value->due_date < date('Y-m-d'))
                                            <a href="#" class="button-warning open-calendar" data-id="{{ $value->id }}"  
                                                data-bs-toggle="modal" data-bs-target="#calendarModal">
                                                <i class="fa fa-calendar tiny-icon"></i>
                                            </a>
                                        @endif
                                        <a href="{{ url('instruments') }}/{{ encrypt_decrypt($value->id, 'encrypt') }}"
                                        class="button-info" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="@lang('index.view_details')"><i class="fa fa-eye tiny-icon"></i></a>
                                        @if (routePermission('instruments.edit'))
                                            <a href="{{ url('instruments') }}/{{ encrypt_decrypt($value->id, 'encrypt') }}/edit"
                                                class="button-success" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="@lang('index.edit')"><i class="fa fa-edit tiny-icon"></i></a>
                                        @endif
                                        @if (routePermission('instruments.delete'))
                                            <a href="#" class="delete button-danger"
                                                data-form_class="alertDelete{{ $value->id }}" type="submit"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('index.delete')">
                                                <form action="{{ route('instruments.destroy', $value->id) }}"
                                                    class="alertDelete{{ $value->id }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <i class="fa fa-trash tiny-icon"></i>
                                                </form>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
        <div class="modal fade" id="calendarModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Asset Maintenance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        {!! Form::model('', [
                        'id' => 'add_form',
                        'method' => 'POST',
                        'route' => ['instruments.service-entry'],
                        ]) !!}
                        @csrf
                        <div class="row d-flex justify-content-center align-items-center">
                            <div class="col-sm-10 mb-3">
                                <div class="form-group">
                                    <input type="hidden" name="instrument_id" id="instrument_id" value="">
                                    <label>Service Date <span class="required_star">*</span></label>
                                    {!! Form::text('service_date', null, [
                                        'class' => 'form-control',
                                        'id' => 'service_date',
                                        'placeholder' => 'Service Date',
                                    ]) !!}
                                    @if ($errors->has('service_date'))
                                    <div class="error_alert text-danger">
                                        {{ $errors->first('service_date') }}
                                    </div>
                                    @endif
                                    <div class="text-danger" id="service_date_error"></div>
                                </div>
                            </div>
                            <div class="col-sm-10 mb-3">
                                <div class="form-group">
                                    <label>Next Service Date <span class="required_star">*</span></label>
                                    {!! Form::text('next_service_date', null, [
                                        'class' => 'form-control',
                                        'id' => 'next_service_date',
                                        'placeholder' => 'Next Service Date',
                                    ]) !!}
                                    @if ($errors->has('next_service_date'))
                                    <div class="error_alert text-danger">
                                        {{ $errors->first('next_service_date') }}
                                    </div>
                                    @endif
                                    <div class="text-danger" id="next_service_date_error"></div>
                                </div>
                            </div>
                            <div class="col-sm-10 mb-3">
                                <div class="form-group">
                                    <label>@lang('index.notes') </label>
                                    <textarea name="notes" id="notes" class="form-control" placeholder="@lang('index.notes')"></textarea>
                                    @if ($errors->has('notes'))
                                    <div class="error_alert text-danger">
                                        {{ $errors->first('notes') }}
                                    </div>
                                    @endif
                                    <div class="text-danger" id="notes_error"></div>
                                </div>
                            </div>
                            <div class="col-md-4 mt-3">
                                <button type="submit" name="submit" value="submit"
                                    class="btn w-100 bg-blue-btn">@lang('index.submit')</button>
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
    <script>
        $(document).on("click", ".open-calendar", function () {
            let instrumentId = $(this).data("id"); 
            $("#instrument_id").val(instrumentId); 
        });
        function parseDMYtoDate(dmy) {
            const [day, month, year] = dmy.split('-');
            return new Date(`${year}-${month}-${day}`);
        }
        $('#service_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
        }).on('changeDate', function (e) {
            const startDate = e.date;
            $('#next_service_date').datepicker('setStartDate', startDate);
            const completeDateVal = $('#next_service_date').val();
            if (completeDateVal) {
                const completeDate = parseDMYtoDate(completeDateVal);
                if (completeDate < startDate) {
                    $('#next_service_date').datepicker('update', startDate);
                }
            }
        });
        $('#next_service_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
        }).on('changeDate', function (e) {
            const completeDate = e.date;
            const startDateVal = $('#service_date').val();
            if (startDateVal) {
                const startDate = parseDMYtoDate(startDateVal);
                if (completeDate < startDate) {
                    $('#next_service_date').datepicker('update', startDate);
                }
            }
        });
        $(document).on("submit", "#add_form", function (e) {
            e.preventDefault(); 
            let form = $(this);
            let actionUrl = form.attr("action"); 
            let formData = form.serialize(); 
            let serviceDate = $('#service_date').val().trim(); 
            let nextServiceDate = $('#next_service_date').val().trim(); 
            let notes = $('#notes').val() ? $('#notes').val().trim() : '';

            let isValid = true;
            $('#service_date_error').text('');
            if (!serviceDate) {
                $('#service_date_error').text('The Service Date field is required.');
                isValid = false;
            }
            if (!nextServiceDate) {
                $('#next_service_date_error').text('The Next Service Date field is required.');
                isValid = false;
            }
            if (notes.length > 255) {
                $('#notes_error').text('The notes cannot exceed 255 characters.');
                isValid = false;
            }
            if (isValid) {
                $.ajax({
                    url: actionUrl,
                    type: "POST",
                    data: formData,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function (response) {
                        if (response.success) {
                            let modalEl = document.getElementById('calendarModal');
                            let modal = bootstrap.Modal.getInstance(modalEl);
                            modal.hide(); 
                            location.reload();
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        alert("Something went wrong. Please try again.");
                    }
                });
            }
        });
        $('#calendarModal').on('hide.bs.modal', function () {
            $('#service_date_error').text('');
            $('#next_service_date_error').text('');
        });
    </script>
@endsection
