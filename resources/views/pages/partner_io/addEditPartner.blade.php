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
                {!! Form::model(isset($partner_io) && $partner_io ? $partner_io : '', [
                    'id' => 'manufacture_form',
                    'method' => isset($partner_io) && $partner_io ? 'PATCH' : 'POST',
                    'enctype' => 'multipart/form-data',
                    'route' => ['partner_io.update', isset($partner_io->id) && $partner_io->id ? $partner_io->id : ''],
                ]) !!}
                @csrf
                <div>
                    <div class="row">
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>@lang('index.reference_no') <span class="required_star">*</span></label>
                                <input type="text" name="reference_no" id="reference_no" 
                                    class="form-control" placeholder="@lang('index.reference_no')" 
                                    value="{{ old('reference_no', $partner_io->reference_no ?? '') }}" 
                                    {{ isset($partner_io->reference_no) ? 'readonly' : '' }}>
                                <div class="text-danger d-none"></div>
                                @error('reference_no')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                               <label>Delivery Challan Number  <span class="required_star">*</span></label>
                                <input type="text" name="del_challan_no" id="del_challan_no" class="form-control" placeholder="Delivery Challan Number" value="{{ old('del_challan_no', $partner_io->del_challan_no ?? '') }}">
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
                                {!! Form::text('io_date', isset($partner_io->io_date) && $partner_io->io_date!='' ? date('d-m-Y',strtotime($partner_io->io_date)) : (old('io_date') ?: date('d-m-Y')), [
                                    'class' => 'form-control',
                                    'id' => 'io_date',
                                    'placeholder' => 'Delivery Challan Date',
                                ]) !!}
                                <div class="text-danger d-none"></div>
                                @if ($errors->has('io_date'))
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
                                <label>@lang('index.partners')(Code) <span class="required_star">*</span></label>
                                <input type="hidden" name="partner_id" value="{{ $partner_io->id ?? '' }}">
                                <select name="partner_id" id="partner_id" class="form-control select2">
                                    <option value="">@lang('index.select')</option>
                                    @foreach($partners as $partner)
                                        <option value="{{ $partner->id }}"
                                            {{ old('partner_id', $partner_io->partner_id ?? '') == $partner->id ? 'selected' : '' }}>
                                            {{ $partner->name .'('.$partner->partner_id.')' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="text-danger d-none"></div>
                                @error('partner_id')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>Phone Number <span class="required_star">*</span></label>
                                <input type="text" name="phn_no" id="phn_no" 
                                    class="form-control" placeholder="Phone Number" 
                                    value="{{ old('phn_no', $partner_detail->phone ?? '') }}">
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
                                <label>Email</label>
                                <input type="text" name="email" id="email" 
                                    class="form-control" placeholder="Email" 
                                    value="{{ old('email', $partner_detail->email ?? '') }}">
                                <div class="text-danger d-none"></div>
                                @error('email')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2 col-md-4">
                            <div class="form-group">
                                <label>Return Due Date <span class="required_star">*</span></label>
                                {!! Form::text('return_due_date', isset($partner_io->return_due_date) && $partner_io->return_due_date!='' ? date('d-m-Y',strtotime($partner_io->return_due_date)) : (old('return_due_date') ?: date('d-m-Y')), [
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
                                <textarea name="d_address" id="d_address" class="form-control" rows="3">{{ old('d_address', $partner_io->d_address ?? '') }}</textarea>
                                <div class="text-danger d-none"></div>
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
                            <div class="table-responsive" id="piofrm">
                                @php
                                $showIColumns = false;
                                $showCSColumns = false;
                                if (isset($partnerOrderDetails)) {
                                    if ($partnerOrderDetails->inter_state === 'Y') {
                                        $showIColumns = true;
                                    }
                                    if ($partnerOrderDetails->inter_state === 'N') {
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
                                            {{-- <th class="w-100-p">@lang('index.unit')(UOM)</th> --}}
                                            <th class="w-100-p">Rate (Rs.) <span class="required_star">*</span></th>
                                            <th class="w-100-p">@lang('index.total') (Rs.)</th>
                                            <th class="w-100-p">Tax Rate (%) <span class="required_star">*</span></th>
                                            <th class="w-220-p">@lang('index.inter_state')</th>
                                            <th class="w-75-p" id="cgst_th" style="{{ $showCSColumns ? '' : 'display: none;' }}">CGST (%)</th>
                                            <th class="w-75-p" id="sgst_th" style="{{ $showCSColumns ? '' : 'display: none;' }}">SGST (%)</th>
                                            <th class="w-150-p" id="igst_th" style="{{ $showIColumns ? '' : 'display: none;' }}">IGST (%)</th>
                                            <th class="w-100-p">Tax Amount (Rs.)</th>
                                            <th class="w-100-p">@lang('index.subtotal') (Rs.)</th>
                                            <th class="w-100-p">@lang('index.remarks')</th>
                                            <th class="w-100-p">@lang('index.line_item_number') <span class="required_star">*</span></th>
                                            @if(!isset($partnerOrderDetails))<th class="ir_txt_center">@lang('index.actions')</th>@endif
                                        </tr>
                                    </thead>
                                    <tbody class="add_partner">
                                        <?php $i = 1; ?>
                                        @if(isset($partnerOrderDetails))
                                            <tr class="rowCount" data-id="{{ $partnerOrderDetails->id }}">
                                                <td class="width_1_p ir_txt_center">{{ $i++ }}</td>
                                                <td>
                                                    <input type="hidden" name="detail_id" value="{{ $partnerOrderDetails->id }}">
                                                    <input type="hidden" name="type[]" value="{{ $partnerOrderDetails->type }}">
                                                    <select class="form-control type select2" id="type_{{ $i }}" {{ isset($partnerOrderDetails) ? 'disabled' : ''  }} style="min-width: 150px;">
                                                        <option value="">Please Select</option>
                                                        <option {{ (isset($partnerOrderDetails->type) && $partnerOrderDetails->type == 1) || old('type') == 1 ? 'selected' : '' }} value="1" >@lang('index.gauges/checkinginstruments')</option>
                                                        <option {{ (isset($partnerOrderDetails->type) && $partnerOrderDetails->type == 2) || old('type') == 2 ? 'selected' : '' }} value="2">@lang('index.measuringinstruments')</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="ins_category[]" value="{{ $partnerOrderDetails->ins_category }}">
                                                    <select class="form-control ins_category select2" name="ins_category[]" id="ins_category_{{ $i }}" {{ isset($partnerOrderDetails) ? 'disabled' : ''  }} style="min-width: 150px;">
                                                        <option value="">Please Select</option>
                                                        @foreach($instrument_categories as $instrument_cat)
                                                            <option value="{{ $instrument_cat->id }}" 
                                                                {{ ($instrument_cat->id == $partnerOrderDetails->ins_category || old('ins_category') == $instrument_cat->id) ? 'selected' : '' }}>
                                                                {{ $instrument_cat->category }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="ins_name[]" value="{{ $partnerOrderDetails->ins_name }}">
                                                    <select class="form-control ins_name select2" name="ins_name[]" id="ins_name_{{ $i }}" {{ isset($partnerOrderDetails) ? 'disabled' : ''  }} style="min-width: 150px;">
                                                        <option value="">Please Select</option>
                                                        @foreach($instruments as $instrument)
                                                            <option value="{{ $instrument->id }}" 
                                                                {{ ($instrument->id == $partnerOrderDetails->ins_name || old('ins_name') == $instrument->id) ? 'selected' : '' }}>
                                                                {{ $instrument->instrument_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="qty[]" class="check_required form-control integerchk qty_c" placeholder="Quantity" id="quantity_{{ $i }}" value="{{ isset($partnerOrderDetails->qty) ? $partnerOrderDetails->qty : '' }}" style="min-width: 120px;"/>
                                                </td>
                                                {{-- <td>
                                                    <select class="form-control type select2" name="unit_id[]" id="unit_id_{{ $i }}" style="min-width: 120px;">
                                                        <option value="">Please Select</option>
                                                        @foreach($units as $unit)
                                                            <option {{ (isset($partnerOrderDetails->unit_id) && $partnerOrderDetails->unit_id == $unit->id) || old('unit_id') == $unit->id ? 'selected' : '' }} value="{{ $unit->id }}" >{{ $unit->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td> --}}
                                                <td>
                                                    <input type="text" name="rate[]" class="form-control rate_c" placeholder="Rate" value="{{ isset($partnerOrderDetails->rate) ? $partnerOrderDetails->rate : 0.00 }}" style="min-width: 120px;" />
                                                </td>
                                                <td>
                                                    <input type="text" name="total[]" class="form-control total_c" placeholder="Total" value="{{ isset($partnerOrderDetails->total) ? $partnerOrderDetails->total : 0.00 }}" readonly style="min-width: 120px;"/>
                                                </td>
                                                <td>
                                                    <input type="text" name="tax_rate[]" class="form-control tax_percent_c" placeholder="Tax Rate" value="{{ isset($partnerOrderDetails->tax_rate) ? $partnerOrderDetails->tax_rate : 0.00 }}" style="min-width: 120px;"/>
                                                </td>
                                                <td>
                                                    <div class="form-group radio_button_problem">
                                                        <div class="radio">
                                                            <label>
                                                                <input tabindex="1" type="radio" name="disabled_inter_state[{{ $i }}]" id="inter_state_yes_{{ $i }}"
                                                                    value="Y" @if(isset($partnerOrderDetails->inter_state) && $partnerOrderDetails->inter_state === 'Y') checked @endif disabled>
                                                                @lang('index.yes')
                                                            </label>
                                                            <label>
                                                                <input tabindex="2" type="radio" name="disabled_inter_state[{{ $i }}]" id="inter_state_no_{{ $i }}"
                                                                    value="N" @if(isset($partnerOrderDetails->inter_state) && $partnerOrderDetails->inter_state === 'N') checked @endif disabled>
                                                                @lang('index.no')
                                                            </label>
                                                        </div>
                                                        <input type="hidden" name="inter_state[{{ $i }}]" value="{{ $partnerOrderDetails->inter_state }}">
                                                    </div>
                                                </td>
                                                <td class="cgst_cell" style="{{ ($showCSColumns && $partnerOrderDetails->inter_state == 'N') ? '' : 'display: none;' }}">
                                                    <input type="hidden" name="cgst[]" class="form-control cgst_input" value="{{ $partnerOrderDetails->cgst }}">
                                                    <input type="text" class="form-control cgst_input" value="{{ $partnerOrderDetails->cgst }}" {{ isset($partnerOrderDetails) ? 'disabled' : ''  }}>
                                                </td>
                                                <td class="sgst_cell" style="{{ ($showCSColumns && $partnerOrderDetails->inter_state == 'N') ? '' : 'display: none;' }}">
                                                    <input type="hidden" name="sgst[]" class="form-control sgst_input" value="{{ $partnerOrderDetails->sgst }}">
                                                    <input type="text" class="form-control sgst_input" value="{{ $partnerOrderDetails->sgst }}" {{ isset($partnerOrderDetails) ? 'disabled' : ''  }}>
                                                </td>
                                                <td class="igst_cell" style="{{ ($showIColumns && $partnerOrderDetails->inter_state == 'Y') ? '' : 'display: none;' }}">
                                                    <input type="hidden" name="igst[]" class="form-control igst_input" value="{{ $partnerOrderDetails->igst }}" {{ isset($partnerOrderDetails) ? 'disabled' : ''  }}>
                                                    <input type="text" class="form-control igst_input" value="{{ $partnerOrderDetails->igst }}">
                                                </td> 
                                                <td>
                                                    <input type="text" name="tax_amount[]" class="form-control tax_amount_c" placeholder="Tax Amount" value="{{ isset($partnerOrderDetails->tax_amount) ? $partnerOrderDetails->tax_amount : 0.00 }}" style="min-width: 120px;"/>
                                                </td>
                                                <td>
                                                    <input type="text" name="subtotal[]" class="form-control subtotal_c" placeholder="Subtotal" value="{{ isset($partnerOrderDetails->subtotal) ? $partnerOrderDetails->subtotal : 0.00 }}" style="min-width: 120px;"/>
                                                </td>
                                                <td>
                                                    <textarea class="form-control" name="remarks[]" placeholder="Remarks" id="remarks" style="min-width: 120px;">{{ isset($partnerOrderDetails->remarks) ? $partnerOrderDetails->remarks : '' }}</textarea>
                                                </td>
                                                <td>
                                                    <input type="text" name="line_item_no[]" class="form-control" placeholder="Line Item No" value="{{ isset($partnerOrderDetails->line_item_no) ? $partnerOrderDetails->line_item_no : '' }}" style="min-width: 120px;" readonly/>
                                                </td>
                                                @if(!isset($partnerOrderDetails))
                                                    <td class="ir_txt_center"><a class="btn btn-xs del_row remove-tr dlt_button"><iconify-icon icon="solar:trash-bin-minimalistic-broken"></iconify-icon></a></td>
                                                @endif
                                            </tr>
                                        @endif
                                        <tr class="rowCount">
                                        </tr>
                                    </tbody>
                                </table>
                                @if(!isset($partnerOrderDetails))
                                <button id="partner_io" class="btn bg-blue-btn w-10 mb-2 mt-2" type="button">@lang('index.add_more')</button>
                                @endif
                             </div>
                        </div>
                    </div>
                    <div class="row mt-3 gap-2">
                        <div class="col-sm-6 col-md-6 mb-2">
                            <div class="form-group">
                                <label>Upload Document</label>
                                <input type="hidden" name="file_old" value="{{ isset($partner_io->file) && $partner_io->file ? $partner_io->file : '' }}">
                                <input type="hidden" name="total_amount" id="total_amount" value="{{ isset($partner_io->total_amount) ? $partner_io->total_amount : 0 }}" class="form-control input_aligning" placeholder="@lang('index.total')" readonly="">
                               <input type="file" name="file_button[]" id="file_button"
                                    class="form-control @error('title') is-invalid @enderror file_checker_global image_preview"
                                    accept="image/png,image/jpeg,image/jpg,application/pdf,.doc,.docx" multiple>
                                <p class="text-danger errorFile"></p>
                                <div class="image-preview-container">
                                    @if(isset($partner_io->file) && $partner_io->file != '')
                                        <div class="pt-10 pb-10">
                                            <div class="text-left">
                                                <h3 class="pt-20 pb-20">Documents</h3>
                                                <div class="d-flex flex-wrap gap-3">
                                                    @php
                                                        $files = json_decode($partner_io->file, true);
                                                    @endphp

                                                    @foreach($files as $file)
                                                        @php
                                                            $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                                                        @endphp

                                                        @if(in_array($fileExtension, ['pdf']))
                                                            <a class="text-decoration-none" href="{{ url('uploads/partner_io/' . $file) }}" target="_blank">
                                                                <img src="{{ url('assets/images/pdf.png') }}" alt="PDF Preview" class="img-thumbnail mx-2" width="100">
                                                            </a>
                                                        @elseif(in_array($fileExtension, ['doc', 'docx']))
                                                            <a class="text-decoration-none" href="{{ url('uploads/partner_io/' . $file) }}" target="_blank">
                                                                <img src="{{ url('assets/images/word.png') }}" alt="Word Preview" class="img-thumbnail mx-2" width="100">
                                                            </a>
                                                        @else
                                                            <a class="text-decoration-none" href="{{ url('uploads/partner_io/' . $file) }}" target="_blank">
                                                                <img src="{{ url('uploads/partner_io/' . $file) }}" alt="File Preview" class="img-thumbnail mx-2" width="100">
                                                            </a>
                                                        @endif
                                                    @endforeach
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
                                class="btn bg-blue-btn partner_io_submit_button"><iconify-icon
                                    icon="solar:check-circle-broken"></iconify-icon>@lang('index.submit')</button>
                            <a class="btn bg-second-btn" href="{{ route('partner_io.index') }}"><iconify-icon
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
<select id="hidden_unit" class="display_none">
    @foreach($units as $unit)
        <option value="{{ $unit->id }}"
            {{ (isset($obj->unit_id) && $obj->unit_id == $unit->id) || old('unit_id') == $unit->id ? 'selected' : '' }}>
            {{ $unit->name }}
        </option>
    @endforeach
</select>
@endsection
@section('script')
    <script type="text/javascript" src="{!! $baseURL . 'frequent_changing/js/inward_outward.js?v=1.0' !!}"></script>
@endsection