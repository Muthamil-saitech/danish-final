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
<link rel="stylesheet" href="{{ getBaseURL() }}frequent_changing/css/pdf_common.css">
<section class="main-content-wrapper">
    @include('utilities.messages')
    <section class="content-header">
        <div class="row">
            <div class="col-md-6">
                <h2 class="top-left-header">{{ isset($title) ? $title : '' }}</h2>
            </div>
            <div class="col-md-6">
                <a href="javascript:void();" target="_blank" class="btn bg-second-btn print_invoice"
                    data-id="1"><iconify-icon icon="solar:printer-broken"></iconify-icon>
                    @lang('index.print')</a>
                <a class="btn bg-second-btn" href="#"><iconify-icon
                        icon="solar:round-arrow-left-broken"></iconify-icon>@lang('index.back')</a>
            </div>
        </div>
    </section>
    <section class="content" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <div style="width: 98%; max-width: 1200px; margin: 30px auto;">
            <div style="padding: 0px 0; border: 1px solid #000; background: #fff;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #000;">
                    <div style="flex: 1; text-align: center; line-height: 1.6;">
                        <h5 style="font-size: 18px; font-weight: 600; letter-spacing: 1px; margin: 5px 0px 0px 0px;">
                            {{ $customer_io->outward_type.' '.'Returnable Delivery Challan' }}
                        </h5>
                        <p style="font-size: 15px; margin-bottom: 10px; font-weight: 600;">
                            (Rule 55 of CGST Rules 2017)
                        </p>
                    </div>
                </div>                
                <div style="display: flex;">
                    <div style="width: 50%; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 8px 10px; font-size: 16px;">
                        <div style="display: flex; margin-bottom: 4px;">
                            <span style="width: 40%;">GSTIN </span> <b>: {{ $customer_io->customer->gst_no }}</b>
                        </div>
                        <div style="display: flex;margin-bottom: 4px;">
                            <span style="width: 40%;">Name </span> <b> : {{ $customer_io->customer->name }}</b>
                        </div>
                        <div style="display: flex; margin-bottom: 4px;">
                            <span style="width: 40%;">Address</span> <b> : {{ $customer_io->d_address }}</b>
                        </div>
                    </div>
                    <div style="width: 50%; border-bottom: 1px solid #000; padding: 8px 10px; font-size: 16px;">
                        <div style="display: flex; margin-bottom: 4px;">
                            <span style="width: 40%;">Reference PO Number </span> <b>: {{ $customer_io->po_no .'/'. $customer_io->line_item_no }}</b>
                        </div>
                        <div style="display: flex;margin-bottom: 4px;">
                            <span style="width: 40%;">Delivery Challan Number</span> <b> : {{ $customer_io->del_challan_no }}</b>
                        </div>
                        <div style="display: flex; margin-bottom: 4px;">
                            <span style="width: 40%;">Delivery Challan Date</span> <b> : {{ date('d-m-Y', strtotime($customer_io->date)) }}</b>
                        </div>
                        <div style="display: flex; margin-bottom: 4px;">
                            <span style="width: 40%;">Place of Supply</span> <b> : </b>
                        </div>
                        <div style="display: flex; margin-bottom: 4px;">
                            <span style="width: 40%;">Region of Consignee</span> <b> : </b>
                        </div>
                    </div>
                </div>
                <div style="display: flex;">
                    <div style="width: 50%; padding: 8px 10px; font-size: 16px;">
                        <div style="display: flex; margin-bottom: 15px;">
                            <span style="width: 40%;"><b>Consignee Address </b> </span>
                        </div> 
                        <div style="display: flex; margin-bottom: 0px;">
                            <span style="width: 40%;">{{ strtoupper(getCompanyInfo()->company_name) }}</span>
                        </div>  
                        <div style="display: flex; margin-bottom: 0px;">
                            <span style="width: 40%;">{{ safe(getCompanyInfo()->address) }}</span>
                        </div>    
                        <div style="display: flex; margin-bottom: 0px;">
                            <span style="width: 60%;">GST Number: {{ safe(getCompanyInfo()->gst_no) }}</span>
                        </div>                    
                    </div>
                    <div style="width: 50%; padding: 8px 10px; font-size: 16px;">
                        <div style="display: flex; justify-content:end; margin-bottom: 4px;">
                            <span style="width: 50%;"> Return Due Date: {{ !empty($customer_io->return_due_date) ? date('d.m.Y', strtotime($customer_io->return_due_date)) : '' }} </span>
                        </div>                        
                    </div>
                </div>
                <table style="width:100%; border-collapse:collapse; font-size:16px;">
                    <!-- Header Row 1: Main headings with CGST/SGST spanning 2 columns each -->
                    @php 
                    $showIColumns = false;
                    $showCSColumns = false;
                    if (isset($customer_io_details)) {
                    // foreach ($orderDetails as $od) {
                    if ($customer_io_details[0]['inter_state'] === 'Y') {
                        $showIColumns = true;
                    }
                    if ($customer_io_details[0]['inter_state'] === 'N') {
                        $showCSColumns = true;
                    }
                    $totalCGST = $totalSGST = $totalIGST = $totalTax = $finalAmt = 0;
                    }
                    @endphp
                    <tr style="text-align: center;">
                        <th style="border:1px solid #000; padding:4px; border-left: none;" rowspan="2">Sr. No.</th>
                        <th style="border:1px solid #000; padding:4px;" rowspan="2">Description</th>
                        <th style="border:1px solid #000; padding:4px;" rowspan="2">HSN Number</th>
                        <th style="border:1px solid #000; padding:4px;" rowspan="2">Quantity</th>
                        <th style="border:1px solid #000; padding:4px;" rowspan="2">Unit (UOM)</th>
                        <th style="border:1px solid #000; padding:4px;" rowspan="2">Rate (Rs. Per UOM)</th>
                        <th style="border:1px solid #000; padding:4px;" rowspan="2">Total (Rs.)</th>
                        <th style="border:1px solid #000; padding:4px;" rowspan="2">Taxable Value (Rs.)</th>
                        @if($showCSColumns)
                            <th style="border:1px solid #000; padding:4px;" colspan="2">CGST</th>
                            <th style="border:1px solid #000; padding:4px; border-right:none;" colspan="2">SGST</th>
                        @endif
                        @if($showIColumns)
                            <th style="border:1px solid #000; padding:4px; border-right:none;" colspan="2">IGST</th>
                        @endif
                    </tr>
                    <tr style="text-align: center;">
                        @if($showCSColumns)
                            <th style="border:1px solid #000; padding:4px;">Rate<br>%</th>
                            <th style="border:1px solid #000; padding:4px;">Amount<br>(Rs.)</th>
                            <th style="border:1px solid #000; padding:4px;">Rate<br>%</th>
                            <th style="border:1px solid #000; padding:4px; border-right:none;">Amount<br>(Rs.)</th>
                        @endif
                        @if($showIColumns)
                            <th style="border:1px solid #000; padding:4px;">Rate<br>%</th>
                            <th style="border:1px solid #000; padding:4px; border-right:none;">Amount<br>(Rs.)</th>
                        @endif
                    </tr>
                    @foreach($customer_io_details as $detail)
                    @php
                        $total = $detail->qty * $detail->rate;
                        $cgstRate = $showCSColumns ? $detail->cgst : 0;
                        $cgstAmt = $showCSColumns ? $total * $cgstRate/100 : 0;
                        $sgstRate = $showCSColumns ? $detail->sgst : 0;
                        $sgstAmt = $showCSColumns ? $total * $sgstRate/100 : 0;
                        $igstRate = $showIColumns ? $detail->igst : 0;
                        $igstAmt = $showIColumns ? $total * $igstRate/100 : 0;
                        $totalCGST += $cgstAmt;
                        $totalSGST += $sgstAmt;
                        $totalIGST += $igstAmt;
                        $finalAmt += $total;
                        if($showCSColumns)
                            $totalTax = $totalCGST + $totalSGST;
                        else
                            $totalTax = $totalIGST;
                    @endphp
                    <tr>
                        <td style="border:1px solid #000; padding:4px; text-align:center; border-left: none;" >{{ $loop->iteration }}</td>
                        <td style="border:1px solid #000; padding:4px; text-align:center;">{{ $detail->instrument->code.'_'.$detail->instrument->instrument_name.'_'.$detail->instrument->range.'_'.getDMYDateFormat($detail->instrument->due_date) }}</td>
                        <td style="border:1px solid #000; padding:4px;">&nbsp;&nbsp;</td>
                        <td style="border:1px solid #000; padding:4px; text-align:right;">{{ $detail->qty }}</td>
                        <td style="border:1px solid #000; padding:4px; text-align:right;">EA</td>
                        <td style="border:1px solid #000; padding:4px; text-align:right;">{{ number_format($detail->rate, 2, '.', '') }}</td>
                        <td style="border:1px solid #000; padding:4px; text-align:right;">{{ number_format($total, 2, '.', '') }}</td>
                        <td style="border:1px solid #000; padding:4px; text-align:right;">{{ number_format($total, 2, '.', '') }}</td>
                        @if($showCSColumns)
                            <td style="border:1px solid #000; padding:4px; text-align:center;">{{ $cgstRate }}</td>
                            <td style="border:1px solid #000; padding:4px; text-align:right;">{{ number_format($cgstAmt, 2) }}</td>
                            <td style="border:1px solid #000; padding:4px; text-align:center;">{{ $sgstRate }}</td>
                            <td style="border:1px solid #000; padding:4px; border-right:none; text-align:right;">
                                {{ number_format($sgstAmt, 2) }}
                            </td>
                        @endif
                        @if($showIColumns)
                            <td style="border:1px solid #000; padding:4px; text-align:center;">{{ $igstRate }}</td>
                            <td style="border:1px solid #000; padding:4px; border-right:none; text-align:right;">
                                {{ number_format($igstAmt, 2) }}
                            </td>
                        @endif
                    </tr>
                    @endforeach
                    <tr style="font-weight:bold; text-align:right;">
                        <td colspan="7" style="border:1px solid #000; border-left:none; padding:4px;">Total</td>
                        <td style="border:1px solid #000; padding:4px;">{{ number_format($total, 2) }}</td>
                        @if($showCSColumns)
                            <td colspan="2" style="border:1px solid #000; padding:4px;">{{ number_format($totalCGST, 2) }}</td>
                            <td colspan="2" style="border:1px solid #000; border-right:none; padding:4px;">{{ number_format($totalSGST, 2) }}</td>
                        @endif
                        @if($showIColumns)
                            <td colspan="2" style="border:1px solid #000; border-right:none; padding:4px;">{{ number_format($totalIGST, 2) }}</td>
                        @endif
                    </tr>
                    @php
                        $taxTotal = $totalCGST + $totalSGST + $totalIGST;
                        $grandTotal = $taxTotal + $finalAmt;
                    @endphp
                    <tr style="font-weight:bold;">
                        <td colspan="7" style="border:none;font-size:14px;padding:0px 0px;">Amount of Tax (Rs.)</td>
                        <td style="border:1px solid #000;text-align:right;">{{ number_format($taxTotal,2) }}</td>
                        <td colspan="2" style="border:none;"></td>
                        <td colspan="2" style="border:none;"></td>
                    </tr>
                    <tr style="font-weight:bold;">
                        <td colspan="7" style="border:none;font-size:14px;padding:0px 0px;">Total Invoice Value (Rs.)</td>
                        <td style="border:1px solid #000;text-align:right;">{{ number_format($grandTotal,2) }}</td>
                        <td colspan="2" style="border:none;"></td>
                        <td colspan="2" style="border:none;"></td>
                    </tr>
                </table>
                <div style="font-size:14px; line-height:1.4;border-bottom:1px solid #000;">                   
                    <div style="padding-bottom: 5px;padding-top: 5px;">
                        <strong>Total Invoice Value (in words) :</strong>
                        {{ showAmount($grandTotal) }}
                    </div>
                    <div>
                        <strong>Amount of Tax (in words) :</strong>
                        {{ showAmount($taxTotal) }}
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; margin: 0px 0px; font-size: 16px; font-family: Arial, sans-serif;">
                    <!-- Left Column: Transporter Details -->
                    <div style="flex: 1; padding: 8px 2px; border-bottom: 1px solid #000; display: flex; flex-direction: column; justify-content: space-between;">
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 120px; font-weight: bold;">Transporter Name</span><span style="margin: 0 8px;">:</span><span></span>
                        </div>
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 120px; font-weight: bold;">Vehicle Details</span><span style="margin: 0 8px;">:</span><span></span>
                        </div>
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 120px; font-weight: bold;">LR Number</span><span style="margin: 0 8px;">:</span><span></span>
                        </div>
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 120px; font-weight: bold;">Gross Weight</span><span style="margin: 0 8px;">:</span><span></span>
                        </div>
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 120px; font-weight: bold;">Net Weight</span><span style="margin: 0 8px;">:</span><span></span>
                        </div>
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 120px; font-weight: bold;">Tare Weight</span><span style="margin: 0 8px;">:</span><span></span>
                        </div>
                    </div>

                    <!-- Right Column: Purpose Checkboxes -->
                    <div style="flex: 1.5; padding: 8px 10px; border-bottom: 1px solid #000; display: flex; flex-direction: column;">
                        <div style="margin-bottom: 8px; font-weight: bold;">For the Purpose of -</div>
                        <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            <label style="display:flex;align-items:flex-start;margin-bottom:6px;cursor:default;">
                                <span style="display:inline-block;width:18px;height:18px;border:1px solid #333;border-radius:3px;margin-right:10px;flex-shrink:0;background:#fff;" aria-hidden="true"></span>
                                <span>(a) Supply of liquid gas where the quantity at the time of removal from the place of business of the supplier is not known.</span>
                            </label>
                            <label style="display:flex;align-items:flex-start;margin-bottom:6px;cursor:default;">
                                <span style="display:inline-block;width:18px;height:18px;border:1px solid #333;border-radius:3px;margin-right:10px;flex-shrink:0;background:#fff;" aria-hidden="true"></span>
                                <span>(b) Transportation of goods for job work.</span>
                            </label>
                            <label style="display:flex;align-items:flex-start;margin-bottom:6px;cursor:default;">
                                <span style="display:inline-block;width:18px;height:18px;border:1px solid #333;border-radius:3px;margin-right:10px;flex-shrink:0;background:#fff;" aria-hidden="true"></span>
                                <span>(c) Transportation of goods for reasons other than by way of supply.</span>
                            </label>
                            <label style="display:flex;align-items:flex-start;margin-bottom:6px;cursor:default;">
                                <span style="display:inline-block;width:18px;height:18px;border:1px solid #333;border-radius:3px;margin-right:10px;flex-shrink:0;background:#fff;" aria-hidden="true"></span>
                                <span>(d) Such other supplies as may be notified by the board.</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div style="display: flex;">
                    <div style="width: 60%; display: flex; margin-top: 0px; border-right:1px solid #000;">
                        <span style="width: 40%;"><b>Notes: </b> FOR INSPECTION</span>
                    </div>
                    <div style="width: 40%; text-align: center;">
                        <b>ANDERSON GREENWOOD CROSBY SANMAR LIMITED(Formerly Pentair Sanmar Ltd)</b><br><br><br>
                        <p>Authorised Signatory</p>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: start; margin: 0px 0px 80px; font-size: 16px;border-top:1px solid #000;">
                    <div style="width: 60%; display: flex; margin-top: 0px;">
                        <span style="width: 40%;"><b>BN8 </b></span>
                        <span style="width: 40%;"><b>BN8 </b></span>
                    </div>
                </div>
            </div>
            <div style="text-align: end;">
                <span style="font-size: 12px;">DAN/STR/SF/01</span>
            </div>
        </div>
    </section>
</section>
@endsection
@section('script')
<?php
$baseURL = getBaseURL();
?>
<script src="{!! $baseURL . 'assets/datatable_custom/jquery-3.3.1.js' !!}"></script>
<script>
$(document).ready(function () {
    $(document).on("click", ".print_invoice", function () {
        let status = $(this).attr("data-status");
        if(status == 'Inward'){
            viewInChallan($(this).attr("data-id"));
        } else {
            viewOutChallan($(this).attr("data-id"));
        }
    });
    function viewInChallan(id) {
        let base_url = $("#hidden_base_url").val();
        open(
            base_url + "customer-inward-io-print/" + id,
            "Print Customer Inward IO",
            "width=1600,height=550"
        );
        newWindow.focus();
        newWindow.onload = function () {
            newWindow.document.body.insertAdjacentHTML("afterbegin");
        };
    }
    function viewOutChallan(id) {
        let base_url = $("#hidden_base_url").val();
        open(
            base_url + "customer-outward-io-print/" + id,
            "Print Customer Outward IO",
            "width=1600,height=550"
        );
        newWindow.focus();
        newWindow.onload = function () {
            newWindow.document.body.insertAdjacentHTML("afterbegin");
        };
    }
});
</script>
@endsection
