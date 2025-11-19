@extends('layouts.app')
@section('script_top')
<?php
    $baseURL = getBaseURL();
?>
@endsection

@section('content')
    <section class="main-content-wrapper">
        <section class="content-header">
            <h3 class="top-left-header">
                {{ isset($title) && $title ? $title : '' }}
            </h3>
        </section>
        <div class="box-wrapper">
            <!-- general form elements -->
            <div class="table-box">
                <!-- form start -->
                {!! Form::model(isset($obj) && $obj ? $obj : '', [
                    'method' => isset($obj) && $obj ? 'PATCH' : 'POST',
                    'route' => ['instruments.update', isset($obj->id) && $obj->id ? $obj->id : ''],
                ]) !!}
                @csrf
                <div>
                    <div class="row">
                        <div class="col-sm-6 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.type') <span class="required_star">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror select2" name="type"
                                    id="instrument_type">
                                    <option value="">@lang('index.select')</option>
                                    <option {{ (isset($obj->type) && $obj->type == 1) || old('type') == 1 ? 'selected' : '' }} value="1">@lang('index.gauges/checkinginstruments')</option>
                                    <option {{ (isset($obj->type) && $obj->type == 2) || old('type') == 2 ? 'selected' : '' }} value="2">@lang('index.measuringinstruments')</option>
                                </select>
                                @error('type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-6 mb-2 col-md-4">
                            <div class="form-group">    
                                <label>@lang('index.instrument_category') <span class="required_star">*</span></label>
                                <select class="form-control @error('category') is-invalid @enderror select2" name="category" id="category">
                                    <option value="">@lang('index.select')</option>
                                    @if(isset($obj->category))
                                        @foreach ($instrument_categories as $value)
                                            <option 
                                                {{ ((isset($obj->category) && $obj->category == $value->id) || old('category') == $value->id) ? 'selected' : '' }}
                                                value="{{ $value->id }}">{{ $value->category }}</option>
                                        @endforeach
                                    @elseif(old('type'))
                                        @php
                                            $old_instrument_categories = App\InstrumentCategory::where('del_status','Live')
                                                ->where('type', old('type'))
                                                ->get();
                                        @endphp
                                        @foreach ($old_instrument_categories as $value)
                                            <option 
                                                {{ (old('category') == $value->id) ? 'selected' : '' }}
                                                value="{{ $value->id }}">{{ $value->category }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('category')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-6 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.instrument_name') <span class="required_star">*</span></label>
                                <input type="text" name="instrument_name" id="instrument_name"
                                    class="check_required form-control @error('instrument_name') is-invalid @enderror instrument_name"
                                    placeholder="@lang('index.instrument_name')"
                                    value="{{ isset($obj->instrument_name) ? $obj->instrument_name : old('instrument_name') }}">
                                @error('instrument_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-6 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.instrument_code') <span class="required_star">*</span></label>
                                <input type="text" name="code" id="code"
                                    class="check_required form-control @error('code') is-invalid @enderror code"
                                    placeholder="@lang('index.instrument_code')"
                                    value="{{ isset($obj->code) ? $obj->code : old('code') }}">
                                @error('code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-6 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.unit') <span class="required_star">*</span></label>
                                <select class="form-control @error('unit') is-invalid @enderror select2" name="unit" id="unit">
                                    <option value="">@lang('index.select_unit')</option>
                                    @foreach ($units as $value)
                                        <option
                                            {{ isset($obj->unit) && $obj->unit == $value->id || old('unit') == $value->id ? 'selected' : '' }}
                                            value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                                @error('unit')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-6 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.owner') <span class="required_star">*</span></label>
                                <select class="form-control @error('owner_type') is-invalid @enderror select2" name="owner_type"
                                    id="owner_type">
                                    <option value="">@lang('index.select')</option>
                                    <option {{ (isset($obj->owner_type) && $obj->owner_type == 1) || old('owner_type') == 1 ? 'selected' : '' }} value="1">@lang('index.owner')</option>
                                    <option {{ (isset($obj->owner_type) && $obj->owner_type == 2) || old('owner_type') == 2 ? 'selected' : '' }} value="2">@lang('index.customer')</option>
                                </select>
                                @error('owner_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4 {{ (isset($obj) && $obj->owner_type == 2) || old('owner_type') == 2 ? '' : 'd-none' }}" id="cust_div">
                            <div class="form-group">
                                <label>@lang('index.customer') <span class="required_star">*</span></label>
                                <select class="form-control @error('customer_id') is-invalid @enderror select2" name="customer_id" id="customer_id">
                                    <option value="">@lang('index.select')</option>
                                    @foreach ($customers as $value)
                                        <option
                                            {{ (isset($obj->customer_id) && $obj->customer_id == $value->id) || old('customer_id') == $value->id ? 'selected' : '' }}
                                            value="{{ $value->id }}">{{ $value->name.'('.$value->customer_id.')' }}</option>
                                    @endforeach
                                </select>
                                <div class="text-danger d-none"></div>
                                @error('customer_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.range/size') <span class="required_star">*</span></label>
                                <input type="text" name="range" id="range"
                                    class="check_required form-control @error('range') is-invalid @enderror range"
                                    placeholder="@lang('index.range/size')"
                                    value="{{ isset($obj->range) ? $obj->range : old('range') }}">
                                <div class="text-danger d-none"></div>
                                @error('range')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.accuracy') <span class="required_star">*</span></label>
                                <input type="text" name="accuracy" id="accuracy"
                                    class="check_required form-control @error('accuracy') is-invalid @enderror range"
                                    placeholder="@lang('index.accuracy')"
                                    value="{{ isset($obj->accuracy) ? $obj->accuracy : old('accuracy') }}">
                                <div class="text-danger d-none"></div>
                                @error('accuracy')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.make') <span class="required_star">*</span></label>
                                <input type="text" name="make" id="make"
                                    class="check_required form-control @error('make') is-invalid @enderror range"
                                    placeholder="@lang('index.make')"
                                    value="{{ isset($obj->make) ? $obj->make : old('make') }}">
                                <div class="text-danger d-none"></div>
                                @error('make')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.historycardno') <span class="required_star">*</span></label>
                                <input type="text" name="history_card_no" id="history_card_no"
                                    class="check_required form-control @error('history_card_no') is-invalid @enderror history_card_no"
                                    placeholder="@lang('index.historycardno')"
                                    value="{{ isset($obj->history_card_no) ? $obj->history_card_no : old('history_card_no') }}">
                                <div class="text-danger d-none"></div>
                                @error('history_card_no')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.due_date') <span class="required_star">*</span></label>
                                <input type="hidden" name="due_date" value="{{ isset($obj->due_date) && $obj->due_date!='' ? $obj->due_date : date('Y-m-d') }}">
                                {!! Form::text('', isset($obj->due_date) && $obj->due_date!='' ? date('d-m-Y',strtotime($obj->due_date)) : (old('due_date') ?: date('d-m-Y')), [
                                'class' => 'form-control',
                                'id' => 'due_date',
                                'placeholder' => 'Due date',
                                ]) !!}
                                @if ($errors->has('due_date'))
                                <div class="error_alert text-danger">
                                    {{ $errors->first('due_date') }}
                                </div>
                                @endif
                                <div class="text-danger d-none"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.location') <span class="required_star">*</span></label>
                                <input type="text" name="location" id="location"
                                    class="check_required form-control @error('location') is-invalid @enderror range"
                                    placeholder="@lang('index.location')"
                                    value="{{ isset($obj->location) ? $obj->location : old('location') }}">
                                <div class="text-danger d-none"></div>
                                @error('location')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.remarks')</label>
                                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror" placeholder="{{ __('index.remarks') }}" rows="3">{{ old('remarks', isset($obj) ? $obj->remarks : '') }}</textarea>
                                @if ($errors->has('remarks'))
                                <div class="error_alert text-danger">
                                    {{ $errors->first('remarks') }}
                                </div>
                                @endif
                                <div class="text-danger d-none"></div>
                            </div>
                        </div>
                    </div>    
                    <div class="add_ins">
                        @if(isset($instrument_ranges) && $instrument_ranges->count() > 0)
                            @foreach($instrument_ranges as $key => $inst_range)
                                <div class="card mb-3 shadow-sm border-0 rounded-3">
                                    <div class="card-body">
                                        <div class="row align-items-center ins-row">
                                            <div class="col-md-1 mb-3 d-flex align-items-center justify-content-center">
                                                <label class="serial-no">{{ $key + 1 }}</label>
                                                <input type="hidden" name="ins_serial[]" value="{{ $key + 1 }}">
                                                <input type="hidden" name="ins_id[]" value="{{ $inst_range->id ?? '' }}">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <div class="form-group">
                                                    <label>Unit <span class="required_star">*</span></label>
                                                    <select name="ins_unit[]" class="form-control select2">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($units as $value)
                                                            <option value="{{ $value->id }}" {{ isset($inst_range->ins_unit_id) && $inst_range->ins_unit_id == $value->id || old('ins_unit') == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2 col-md-2">
                                                <div class="form-group">
                                                    <label>@lang('index.range/size') <span class="required_star">*</span></label>
                                                    <input type="text" name="ins_range[]"
                                                        class="check_required form-control @error('range') is-invalid @enderror range"
                                                        placeholder="@lang('index.range/size')"
                                                        value="{{ $inst_range->ins_range ?? old('ins_range') }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2 col-md-2">
                                                <div class="form-group">
                                                    <label>@lang('index.accuracy') <span class="required_star">*</span></label>
                                                    <input type="text" name="ins_accuracy[]"
                                                        class="check_required form-control @error('accuracy') is-invalid @enderror range"
                                                        placeholder="@lang('index.accuracy')"
                                                        value="{{ $inst_range->ins_accuracy ?? old('ins_accuracy') }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2 col-md-2">
                                                <div class="form-group">
                                                    <label>@lang('index.make') <span class="required_star">*</span></label>
                                                    <input type="text" name="ins_make[]"
                                                        class="check_required form-control @error('make') is-invalid @enderror range"
                                                        placeholder="@lang('index.make')"
                                                        value="{{ $inst_range->ins_make ?? old('ins_make') }}">
                                                </div>
                                            </div>
                                            @if($key==0)
                                                <div class="col-md-2 mb-3 mt-1">
                                                    <button id="insAddMore" class="btn bg-blue-btn mt-4" type="button">@lang('index.add_more')</button>
                                                </div>
                                            @else
                                                @if(isset($instrument_ranges) && $instrument_ranges->count() > 0)
                                                <div class="col-md-2 mt-4">
                                                    <a href="#" class="ins_range_del button-danger"
                                                        data-inst_range_id="{{ $inst_range->id }}" type="submit"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('index.delete')">
                                                        <i class="fa fa-trash tiny-icon"></i>
                                                    </a>
                                                </div>
                                                @else
                                                    <div class="col-md-2 mt-4">
                                                        <button type="button" class="btn btn-xs del_row dlt_button">
                                                            <iconify-icon icon="solar:trash-bin-minimalistic-broken"></iconify-icon>
                                                        </button>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div> 
                            @endforeach
                        @else
                        <div class="card mb-3 shadow-sm border-0 rounded-3">
                            <div class="card-body">
                                <div class="row align-items-center ins-row">
                                    <div class="col-md-1 mb-3 d-flex align-items-center justify-content-center">
                                        <label class="serial-no">1</label>
                                        <input type="hidden" name="ins_serial[]" value="1">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <div class="form-group">
                                            <label>Unit <span class="required_star">*</span></label>
                                            <select name="ins_unit[]" class="form-control select2">
                                                <option value="">Select Unit</option>
                                                @foreach ($units as $value)
                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 mb-2 col-md-2">
                                        <div class="form-group">
                                            <label>@lang('index.range/size') <span class="required_star">*</span></label>
                                            <input type="text" name="ins_range[]"
                                                class="check_required form-control @error('range') is-invalid @enderror range"
                                                placeholder="@lang('index.range/size')"
                                                value="{{ old('range') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-12 mb-2 col-md-2">
                                        <div class="form-group">
                                            <label>@lang('index.accuracy') <span class="required_star">*</span></label>
                                            <input type="text" name="ins_accuracy[]"
                                                class="check_required form-control @error('accuracy') is-invalid @enderror range"
                                                placeholder="@lang('index.accuracy')"
                                                value="{{ old('accuracy') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-12 mb-2 col-md-2">
                                        <div class="form-group">
                                            <label>@lang('index.make') <span class="required_star">*</span></label>
                                            <input type="text" name="ins_make[]"
                                                class="check_required form-control @error('make') is-invalid @enderror range"
                                                placeholder="@lang('index.make')"
                                                value="{{ old('make') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3 mt-1">
                                        <button id="insAddMore" class="btn bg-blue-btn mt-4" type="button">@lang('index.add_more')</button>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        @endif                       
                    </div>                
                </div>
                <!-- /.box-body -->
                <div class="row mt-2">
                    <div class="col-sm-12 col-md-6 mb-2 d-flex gap-3">
                        <button type="submit" name="submit" value="submit" class="btn bg-blue-btn"><iconify-icon icon="solar:check-circle-broken"></iconify-icon>@lang('index.submit')</button>
                        <a class="btn bg-second-btn" href="{{ route('instruments.index') }}"><iconify-icon icon="solar:round-arrow-left-broken"></iconify-icon>@lang('index.back')</a>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@endsection

@section('script_bottom')
@endsection
@section('script')
    <script type="text/javascript" src="{!! $baseURL . 'frequent_changing/js/instrument.js?v=1.0' !!}"></script>
    <script>
        let i = 1;
        function updateSerialLabels() {
            document.querySelectorAll(".serial-no").forEach((el, index) => {
                el.textContent = index + 1;
                el.nextElementSibling.value = index + 1;
            });
        }
        let base_url = $('#base_url').val();
        let hidden_base_url = $("#hidden_base_url").val();
        let hidden_alert = $(".hidden_alert").val();
        let hidden_ok = $(".hidden_ok").val();
        let hidden_cancel = $(".hidden_cancel").val();
        let thischaracterisnotallowed = $(".thischaracterisnotallowed").val();
        let are_you_sure = $(".are_you_sure").val();
        $(document).on("click", "#insAddMore", function (e) {
            ++i;
            let newRow = `
                <div class="card mb-3 shadow-sm border-0 rounded-3">
                    <div class="card-body">
                        <div class="row mt-3" id="ins_row_${i}">
                            <div class="col-md-1 mb-3 d-flex align-items-center justify-content-center">
                                <label class="form-control-plaintext text-center serial-no"></label>
                                <input type="hidden" name="ins_serial[]" value="">
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-group">
                                    <label>Unit <span class="required_star">*</span></label>
                                    <select name="ins_unit[]" class="form-control select2">
                                        <option value="">Select Unit</option>
                                        @foreach ($units as $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-group">
                                    <label>Range/Size <span class="required_star">*</span></label>
                                    <input type="text" name="ins_range[]" class="form-control" placeholder="Range/Size">
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-group">
                                    <label>Accuracy <span class="required_star">*</span></label>
                                    <input type="text" name="ins_accuracy[]" class="form-control" placeholder="Accuracy">
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-group">
                                    <label>Make <span class="required_star">*</span></label>
                                    <input type="text" name="ins_make[]" class="form-control" placeholder="Make">
                                </div>
                            </div>
                            <div class="col-md-3 mb-3 mt-4">
                                <button type="button" class="btn btn-xs del_row dlt_button"><iconify-icon icon="solar:trash-bin-minimalistic-broken"></iconify-icon></button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $(".add_ins").append(newRow);
            updateSerialLabels();
        });
        $(document).on("click", ".del_row", function () {
            if ($(".add_ins .card").length > 1) {
                $(this).closest(".card").remove();
                updateSerialLabels();
            } else {
                alert("At least one contact must remain.");
            }
        });
        $('body').on('click', '.ins_range_del', function (e) {
            e.preventDefault();
            let inst_range_id = $(this).attr('data-inst_range_id');
            swal({
                title: hidden_alert+"!",
                text: are_you_sure,
                cancelButtonText:hidden_cancel,
                confirmButtonText:hidden_ok,
                confirmButtonColor: '#3c8dbc',
                showCancelButton: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        type: "POST",
                        url: hidden_base_url + "instrumentRangeDelete",
                        data: {
                            inst_range_id: inst_range_id
                        },
                        dataType: "json",
                        success: function(data) {
                            let hidden_alert = data.status ? "Success" : "Error";
                            swal({
                                title: hidden_alert + "!",
                                text: data.message,
                                cancelButtonText: hidden_cancel,
                                confirmButtonText: hidden_ok,
                                confirmButtonColor: "#3c8dbc",
                            }, function() {
                                location.reload();
                            });
                        },
                        error: function() {
                            console.error("Failed to fetch product details.");
                        },
                    });
                }
            });
        });
    </script>
@endsection