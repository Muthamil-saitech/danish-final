@extends('layouts.app')

@section('script_top')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <?php
    $setting = getSettingsInfo();
    $tax_setting = getTaxInfo();
    $baseURL = getBaseURL();
    ?>
@endsection
@section('content')
    <section class="main-content-wrapper">
        <section class="content-header">
            <h3 class="top-left-header">{{ isset($title) && $title ? $title : '' }}</h3>
        </section>
        @include('utilities.messages')
        <div class="box-wrapper">
            <!-- general form elements -->
            <div class="table-box">
                <!-- form start -->
                {!! Form::model(isset($order_io) && $order_io ? $order_io : '', [
                    'id' => 'manufacture_form',
                    'method' => isset($order_io) && $order_io ? 'PATCH' : 'POST',
                    'enctype' => 'multipart/form-data',
                    'route' => ['customer_io.update', isset($order_io->id) && $order_io->id ? $order_io->id : ''],
                ]) !!}
                @csrf
                <div>
                    <div class="row">
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.po_no') <span class="required_star">*</span></label>
                                @if(isset($order_io->po_no) && !empty($order_io->po_no))
                                    <select id="po_no_display" class="form-control select2" disabled>
                                        <option value="">@lang('index.select')</option>
                                        @foreach($customer_orders as $order)
                                            <option {{ $order_io->po_no == $order->reference_no ? 'selected' : '' }}
                                                value="{{ $order->reference_no }}"
                                                data-lineitem="{{ $order->line_item_no }}">
                                                {{ $order->reference_no.'/'.$order->line_item_no }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('po_no')
                                    <div class="text-danger">
                                        {{ $errors->first('po_no') }}
                                    </div>
                                    @enderror
                                    <input type="hidden" name="po_no" value="{{ $order_io->po_no }}">
                                @else
                                    <select name="po_no" id="po_no" class="form-control select2" required>
                                        <option value="">@lang('index.select')</option>
                                        @foreach($customer_orders as $order)
                                            <option {{ old('po_no') == $order->reference_no ? 'selected' : '' }}
                                                value="{{ $order->reference_no }}"
                                                data-lineitem="{{ $order->line_item_no }}">
                                                {{ $order->reference_no.'/'.$order->line_item_no }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('po_no')
                                    <div class="text-danger">
                                        {{ $errors->first('po_no') }}
                                    </div>
                                    @enderror
                                @endif
                                <input type="hidden" name="line_item_no" id="line_item_no" value="{{ old('line_item_no', $order_io->line_item_no ?? '') }}">                                
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                               <label>Delivery Challan Number  <span class="required_star">*</span></label>
                                <input type="text" name="del_challan_no" id="del_challan_no" class="form-control" placeholder="Delivery Challan Number" value="{{ old('del_challan_no', $order_io->del_challan_no ?? '') }}">
                                <div class="text-danger d-none"></div>
                                @error('del_challan_no')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>Delivery Challan Date <span class="required_star">*</span></label>
                                {!! Form::text('date', isset($order_io->date) && $order_io->date!='' ? date('d-m-Y',strtotime($order_io->date)) : (old('date') ?: date('d-m-Y')), [
                                    'class' => 'form-control',
                                    'id' => 'io_date',
                                    'placeholder' => 'Delivery Challan Date',
                                ]) !!}
                                <div class="text-danger d-none"></div>
                                @if ($errors->has('date'))
                                <div class="text-danger">
                                    {{ $errors->first('date') }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.customer_name')(Code) <span class="required_star">*</span></label>
                                <input type="hidden" name="customer_id" id="customer_id" value="{{ isset($order_io->customer_id) ? $order_io->customer_id : ''  }}">
                                <input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="@lang('index.customer_name')(Code)" value="{{ isset($customer) ? $customer->name . '(' . $customer->customer_id . ')' : '' }}" readonly>
                                <div class="text-danger d-none"></div>
                                @error('customer_name')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                               <label>Phone Number <span class="required_star">*</span></label>
                                <input type="text" name="phn_no" id="c_phn_no" class="form-control" placeholder="Phone Number" value="{{ old('phn_no', $customer->phone ?? '') }}">
                                <div class="text-danger d-none"></div>
                                @error('phn_no')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                         <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                               <label>Email </label>
                                <input type="text" name="c_email" id="c_email" class="form-control" placeholder="Email" value="{{ old('c_email', $customer->email ?? '') }}">
                                <div class="text-danger d-none"></div>
                                @error('c_email')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>                        
                    </div>                   
                    <div class="row"> 
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>Return Due Date <span class="required_star">*</span></label>
                                {!! Form::text('return_due_date', isset($order_io->return_due_date) && $order_io->return_due_date!='' ? date('d-m-Y',strtotime($order_io->return_due_date)) : (old('return_due_date') ?: date('d-m-Y')), [
                                    'class' => 'form-control',
                                    'id' => 'return_due_date',
                                    'placeholder' => 'Return Due Date',
                                ]) !!}
                                <div class="text-danger d-none"></div>
                                @if ($errors->has('return_due_date'))
                                <div class="text-danger">
                                    {{ $errors->first('return_due_date') }}
                                </div>
                                @endif
                            </div>
                        </div>                       
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                               <label>Delivery Address <span class="required_star">*</span></label>
                                <textarea name="d_address" id="d_address" class="form-control" rows="3" placeholder="Delivery Address">{{ old('d_address', $order_io->d_address ?? '') }}</textarea>
                                @error('d_address')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive" id="ciofrm">
                                @php
                                $showIColumns = false;
                                $showCSColumns = false;
                                if (isset($customer_io_details)) {
                                    if ($customer_io_details[0]['inter_state'] === 'Y') {
                                        $showIColumns = true;
                                    }
                                    if ($customer_io_details[0]['inter_state'] === 'N') {
                                        $showCSColumns = true;
                                    }
                                }
                                @endphp
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="w-50-p">@lang('index.sn')</th>
                                            <th class="w-100-p">@lang('index.type') <span class="required_star">*</span></th>
                                            <th class="w-100-p">@lang('index.category') <span class="required_star">*</span></th>
                                            <th class="w-100-p">@lang('index.instrument_name')(Code) <span class="required_star">*</span></th>
                                            <th class="w-100-p">@lang('index.quantity') <span class="required_star">*</span></th>
                                            <th class="w-100-p">Rate (Rs.) <span class="required_star">*</span></th>
                                            <th class="w-100-p">@lang('index.total') (Rs.)</th>
                                            <th class="w-100-p">Tax Rate (%)<span class="required_star">*</span></th>
                                            <th class="w-220-p">@lang('index.inter_state')</th>
                                            <th class="w-75-p" id="cgst_th" style="{{ $showCSColumns ? '' : 'display: none;' }}">CGST (%)</th>
                                            <th class="w-75-p" id="sgst_th" style="{{ $showCSColumns ? '' : 'display: none;' }}">SGST (%)</th>
                                            <th class="w-150-p" id="igst_th" style="{{ $showIColumns ? '' : 'display: none;' }}">IGST (%)</th>
                                            <th class="w-100-p">Tax Amount (Rs.) </th>
                                            <th class="w-100-p">@lang('index.subtotal') (Rs.)</th>
                                            <th class="w-100-p">@lang('index.remarks')</th>
                                            @if(!isset($customer_io_details))<th class="ir_txt_center">@lang('index.actions')</th>@endif
                                        </tr>
                                    </thead>
                                    <tbody class="add_cio">
                                        <?php $i = 1; ?>
                                        @if(isset($customer_io_details))
                                            @foreach($customer_io_details as $io_details)
                                                <tr class="rowCount" data-id="{{ $io_details->id }}">
                                                    <td class="width_1_p ir_txt_center">{{ $i++ }}</td>
                                                    <td>
                                                        <input type="hidden" name="detail_id[]" value="{{ $io_details->id }}">
                                                        <input type="hidden" name="type[]" value="{{ $io_details->type }}">
                                                        <select class="form-control type select2" id="type_{{ $i }}" {{ isset($io_details) ? 'disabled' : ''  }} style="min-width: 120px;">
                                                            <option value="">Please Select</option>
                                                            <option {{ (isset($io_details->type) && $io_details->type == 1) || old('type') == 1 ? 'selected' : '' }} value="1" >@lang('index.gauges/checkinginstruments')</option>
                                                            <option {{ (isset($io_details->type) && $io_details->type == 2) || old('type') == 2 ? 'selected' : '' }} value="2">@lang('index.measuringinstruments')</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="ins_category[]" value="{{ $io_details->ins_category }}">
                                                        <select class="form-control ins_category select2" name="ins_category[]" id="ins_category_{{ $i }}" {{ isset($io_details) ? 'disabled' : ''  }} style="min-width: 120px;">
                                                            <option value="">Please Select</option>
                                                            @foreach($instrument_categories as $instrument_cat)
                                                               <option value="{{ $instrument_cat->id }}" 
                                                                    {{ ($instrument_cat->id == $io_details->ins_category || old('ins_category') == $instrument_cat->id) ? 'selected' : '' }}>
                                                                    {{ $instrument_cat->category }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="ins_name[]" value="{{ $io_details->ins_name }}">
                                                        <select class="form-control ins_name select2" name="ins_name[]" id="ins_name_{{ $i }}" {{ isset($io_details) ? 'disabled' : ''  }} style="min-width: 120px;">
                                                            <option value="">Please Select</option>
                                                            @foreach($instruments as $instrument)
                                                               <option value="{{ $instrument->id }}" 
                                                                    {{ ($instrument->id == $io_details->ins_name || old('ins_name') == $instrument->id) ? 'selected' : '' }}>
                                                                    {{ $instrument->instrument_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="qty[]" class="check_required form-control integerchk qty_c" placeholder="Quantity" id="quantity_{{ $i }}" value="{{ isset($io_details->qty) ? $io_details->qty : '' }}" />
                                                    </td>
                                                    <td>
                                                        <input type="text" name="rate[]" class="form-control rate_c" placeholder="Rate" value="{{ isset($io_details->rate) ? $io_details->rate : 0.00 }}" style="min-width: 120px;" />
                                                    </td>
                                                    <td>
                                                        <input type="text" name="total[]" class="form-control total_c" placeholder="Total" value="{{ isset($io_details->total) ? $io_details->total : 0.00 }}" readonly style="min-width: 120px;"/>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="tax_rate[]" class="form-control tax_percent_c" placeholder="Tax Rate" value="{{ isset($io_details->tax_rate) ? $io_details->tax_rate : 0.00 }}" style="min-width: 120px;"/>
                                                    </td>
                                                    <td>
                                                        <div class="form-group radio_button_problem">
                                                            <div class="radio">
                                                                <label>
                                                                    <input tabindex="1" type="radio" name="disabled_inter_state[{{ $i }}]" id="inter_state_yes_{{ $i }}"
                                                                        value="Y" @if(isset($io_details->inter_state) && $io_details->inter_state === 'Y') checked @endif disabled>
                                                                    @lang('index.yes')
                                                                </label>
                                                                <label>
                                                                    <input tabindex="2" type="radio" name="disabled_inter_state[{{ $i }}]" id="inter_state_no_{{ $i }}"
                                                                        value="N" @if(isset($io_details->inter_state) && $io_details->inter_state === 'N') checked @endif disabled>
                                                                    @lang('index.no')
                                                                </label>
                                                            </div>
                                                            <input type="hidden" name="inter_state[{{ $i }}]" value="{{ $io_details->inter_state }}">
                                                        </div>
                                                    </td>
                                                    <td class="cgst_cell" style="{{ ($showCSColumns && $io_details->inter_state == 'N') ? '' : 'display: none;' }}">
                                                        <input type="hidden" name="cgst[]" class="form-control cgst_input" value="{{ $io_details->cgst }}">
                                                        <input type="text" class="form-control cgst_input" value="{{ $io_details->cgst }}" {{ isset($io_details) ? 'disabled' : ''  }}>
                                                    </td>
                                                    <td class="sgst_cell" style="{{ ($showCSColumns && $io_details->inter_state == 'N') ? '' : 'display: none;' }}">
                                                        <input type="hidden" name="sgst[]" class="form-control sgst_input" value="{{ $io_details->sgst }}">
                                                        <input type="text" class="form-control sgst_input" value="{{ $io_details->sgst }}" {{ isset($io_details) ? 'disabled' : ''  }}>
                                                    </td>
                                                    <td class="igst_cell" style="{{ ($showIColumns && $io_details->inter_state == 'Y') ? '' : 'display: none;' }}">
                                                        <input type="hidden" name="igst[]" class="form-control igst_input" value="{{ $io_details->igst }}" {{ isset($io_details) ? 'disabled' : ''  }}>
                                                        <input type="text" class="form-control igst_input" value="{{ $io_details->igst }}">
                                                    </td>                                                    
                                                    <td>
                                                        <input type="text" name="tax_amount[]" class="form-control tax_amount_c" placeholder="Tax Amount" value="{{ isset($io_details->tax_amount) ? $io_details->tax_amount : 0.00 }}" style="min-width: 120px;"/>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="subtotal[]" class="form-control subtotal_c" placeholder="Subtotal" value="{{ isset($io_details->subtotal) ? $io_details->subtotal : 0.00 }}" style="min-width: 120px;"/>
                                                    </td>
                                                    <td>
                                                        <textarea class="form-control" name="remarks[]" placeholder="Remarks" id="remarks" style="min-width: 150px;">{{ isset($io_details->remarks) ? $io_details->remarks : '' }}</textarea>
                                                    </td>
                                                    @if(!isset($io_details))
                                                    <td class="ir_txt_center"><a class="btn btn-xs del_row remove-tr dlt_button"><iconify-icon icon="solar:trash-bin-minimalistic-broken"></iconify-icon></a></td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @if(!isset($io_details))
                                <button id="customer_io" class="btn bg-blue-btn w-10 mb-2 mt-2" type="button">@lang('index.add_more')</button>
                                @endif
                             </div>
                        </div>
                    </div>
                    <div class="row mt-3 gap-2">
                        <div class="col-sm-6 col-md-6 mb-2">
                            <div class="form-group">
                                <label>Upload Document</label>
                                <input type="hidden" name="file_old" value="{{ isset($order_io->file) && $order_io->file ? $order_io->file : '' }}">
                                <input type="hidden" name="total_amount" id="total_amount" value="{{ isset($order_io->total_amount) ? $order_io->total_amount : 0 }}" class="form-control input_aligning" placeholder="@lang('index.total')" readonly="">
                               <input type="file" name="file_button[]" id="file_button"
                                    class="form-control @error('title') is-invalid @enderror file_checker_global image_preview"
                                    accept="image/png,image/jpeg,image/jpg,application/pdf,.doc,.docx" multiple>
                                <p class="text-danger errorFile"></p>
                                <div class="image-preview-container">
                                @if(isset($order_io->file) && $order_io->file != '')
                                    <div class="pt-10 pb-10">
                                        <div class="text-left">
                                            <h3 class="pt-20 pb-20">Documents</h3>
                                            <div class="d-flex flex-wrap gap-3">
                                                @php
                                                    $files = json_decode($order_io->file, true);
                                                @endphp

                                                @if(is_array($files))
                                                    @foreach($files as $file)
                                                        @php
                                                            $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                        @endphp

                                                        @if(in_array($fileExtension, ['pdf']))
                                                            <a class="text-decoration-none" href="{{ url('uploads/customer_io/' . $file) }}" target="_blank">
                                                                <img src="{{ url('assets/images/pdf.png') }}" alt="PDF Preview" class="img-thumbnail mx-2" width="100">
                                                            </a>
                                                        @elseif(in_array($fileExtension, ['doc', 'docx']))
                                                            <a class="text-decoration-none" href="{{ url('uploads/customer_io/' . $file) }}" target="_blank">
                                                                <img src="{{ url('assets/images/word.png') }}" alt="Word Preview" class="img-thumbnail mx-2" width="100">
                                                            </a>
                                                        @else
                                                            <a class="text-decoration-none" href="{{ url('uploads/customer_io/' . $file) }}" target="_blank">
                                                                <img src="{{ url('uploads/customer_io/' . $file) }}" alt="File Preview" class="img-thumbnail mx-2" width="100">
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-12 col-md-6 mb-2 d-flex gap-3">
                            <button type="submit" name="submit" value="submit"
                                class="btn bg-blue-btn order_io_submit_button"><iconify-icon
                                    icon="solar:check-circle-broken"></iconify-icon>@lang('index.submit')</button>
                            <a class="btn bg-second-btn" href="{{ route('customer_io.index') }}"><iconify-icon
                                    icon="solar:round-arrow-left-broken"></iconify-icon>@lang('index.back')</a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </section>
<select id="hidden_type" class="display_none">
    <option {{ (isset($obj->type) && $obj->type == 1) || old('type') == 1 ? 'selected' : '' }} value="1">@lang('index.gauges/checkinginstruments')</option>
    <option {{ (isset($obj->type) && $obj->type == 2) || old('type') == 2 ? 'selected' : '' }} value="2">@lang('index.measuringinstruments')</option>
</select>
@endsection
@section('script')
    <script type="text/javascript" src="{!! $baseURL . 'frequent_changing/js/inward_outward.js?v=1.0' !!}"></script>
@endsection