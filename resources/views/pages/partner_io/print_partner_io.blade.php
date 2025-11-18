<?php
$setting = getSettingsInfo();
$tax_setting = getTaxInfo();
$baseURL = getBaseURL();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    {{-- <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> --}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $partner_io->po_no }}</title>
    <link rel="stylesheet" href="{{ getBaseURL() }}frequent_changing/css/pdf_common.css">
</head>

<body>
    <section class="content" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <div style="width: 98%; max-width: 1200px; margin: 30px auto;">
            <div style="padding: 0px 0; border: 1px solid #000; background: #fff;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #000;">
                    <div style="flex: 1; text-align: center;  padding: 5px 0px">
                        <h5 style="font-size: 18px; font-weight: 600; letter-spacing: 1px; margin-bottom: 3px ">
                            {{ $partner_io_detail->outward_type=="RGP" ? 'Returnable Delivery Challan' : 'Non Returnable Delivery Challan' }}
                        </h5>
                        <p style="font-size: 18px; font-weight: 600;">
                            (Rule 55 of CGST Rules 2017)
                        </p>
                    </div>
                </div>
                <div style="display: flex; width: 100%;">
                    <div style="width: 50%; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 8px 10px; font-size: 14px; ">  
                         <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 20%;">GSTIN</span><span style="margin: 0 8px;">:</span><span style="width: 79%">{{ $partner_io->partner->gst_no }} </span>
                        </div>
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 20%;">Name</span><span style="margin: 0 8px;">:</span><span style="width: 79%">{{ $partner_io->partner->name }}</span>
                        </div>
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 20%;">Address</span><span style="margin: 0 8px;">:</span><span style="width: 79%">{{ $partner_io->d_address }}</span>
                        </div>
                    </div>
                    <div style="width: 50%; border-bottom: 1px solid #000; padding: 8px 10px; font-size: 14px; display: grid; grid-template-columns: 50% 50%; grid-auto-rows: min-content; row-gap: 4px; align-items: start; word-break: break-word;">
                        <span>Reference PO Number</span>
                        : {{ $partner_io->reference_no . '/' . $partner_io_detail->line_item_no }}
                        <span>Delivery Challan Number</span>
                        : {{ $partner_io->del_challan_no }}
                        <span>Delivery Challan Date</span>
                        : {{ date('d-m-Y', strtotime($partner_io->io_date)) }}
                        <span>Place of Supply</span>
                        <b>: </b>
                        <span>Region of Consignee</span>
                        <b>: </b>
                    </div>
                </div>  
                <div style="display: flex;">
                    <div style="width: 60%; padding: 8px 10px; font-size: 14px;">
                        <div style="display: flex; margin-bottom: 15px;">
                            <span><b>Consignee Address </b> </span>
                        </div> 
                        <div style="display: flex; margin-bottom: 0px;">
                            <span>{{ strtoupper(getCompanyInfo()->company_name) }}</span>
                        </div>  
                        <div style="display: flex; margin-bottom: 0px;">
                            <span style="width: 65%">{{ safe(getCompanyInfo()->address) }}</span>
                        </div>    
                        <div style="display: flex; margin-bottom: 0px;">
                            <span>GST Number: {{ safe(getCompanyInfo()->gst_no) }}</span>
                        </div>                    
                    </div>
                    <div style="width: 40%; padding: 8px 10px; font-size: 14px;">
                        <div style="display: flex; justify-content:end; margin-bottom: 4px;">
                            <span> Return Due Date: {{ !empty($partner_io->return_due_date) ? date('d.m.Y', strtotime($partner_io->return_due_date)) : '' }} </span>
                        </div>                        
                    </div>
                </div>
                <table style="width:100%; border-collapse:collapse; font-size:14px;">
                    <!-- Header Row 1: Main headings with CGST/SGST spanning 2 columns each -->
                    @php 
                    $totalCGST = 0;
                    $totalSGST = 0;
                    $totalIGST = 0;
                    $totalTax = 0;
                    $finalAmt = 0;
                    $showIColumns = false;
                    $showCSColumns = false;
                    if (isset($partner_io_detail)) {
                        if ($partner_io_detail['inter_state'] === 'Y') {
                            $showIColumns = true;
                        }
                        if ($partner_io_detail['inter_state'] === 'N') {
                            $showCSColumns = true;
                        }
                        $totalCGST = $totalSGST = $totalIGST = $totalTax = $finalAmt = 0;
                    }
                    @endphp
                    <tr style="text-align: center;">
                        <th style="border:1px solid #000; padding:3px; border-left: none;  font-size: 14px" rowspan="2">Sr. No.</th>
                        <th style="border:1px solid #000; padding:3px; font-size: 14px" rowspan="2">Description</th>
                        <th style="border:1px solid #000; padding:3px; font-size: 14px" rowspan="2">HSN Number</th>
                        <th style="border:1px solid #000; padding:3px; font-size: 14px" rowspan="2">Quantity</th>
                        <th style="border:1px solid #000; padding:3px; font-size: 14px" rowspan="2">Unit (UOM)</th>
                        <th style="border:1px solid #000; padding:3px; font-size: 14px" rowspan="2">Rate (Rs. Per UOM)</th>
                        <th style="border:1px solid #000; padding:3px; font-size: 14px" rowspan="2">Total (Rs.)</th>
                        <th style="border:1px solid #000; padding:3px; font-size: 14px" rowspan="2">Taxable Value (Rs.)</th>
                        @if($showCSColumns)
                            <th style="border:1px solid #000; padding:3px; font-size: 14px" colspan="2">CGST</th>
                            <th style="border:1px solid #000; padding:3px; border-right:none; font-size: 14px" colspan="2">SGST</th>
                        @endif
                        @if($showIColumns)
                            <th style="border:1px solid #000; padding:3px; border-right:none; font-size: 14px" colspan="2">IGST</th>
                        @endif
                    </tr>
                    <tr style="text-align: center;">
                        @if($showCSColumns)
                            <th style="border:1px solid #000; padding:3px; font-size: 14px">Rate<br>%</th>
                            <th style="border:1px solid #000; padding:3px; font-size: 14px">Amount<br>(Rs.)</th>
                            <th style="border:1px solid #000; padding:3px; font-size: 14px">Rate<br>%</th>
                            <th style="border:1px solid #000; padding:3px; font-size: 14px; border-right: none">Amount<br>(Rs.)</th>
                        @endif
                        @if($showIColumns)
                            <th style="border:1px solid #000; padding:3px; font-size: 14px">Rate<br>%</th>
                            <th style="border:1px solid #000; padding:3px; font-size: 14px; border-right: none">Amount<br>(Rs.)</th>
                        @endif
                    </tr>
                        @php
                        $i = 1;
                            $total = $partner_io_detail->qty * $partner_io_detail->rate;
                            $cgstRate = $showCSColumns ? $partner_io_detail->cgst : 0;
                            $cgstAmt = $showCSColumns ? $total * $cgstRate/100 : 0;
                            $sgstRate = $showCSColumns ? $partner_io_detail->sgst : 0;
                            $sgstAmt = $showCSColumns ? $total * $sgstRate/100 : 0;
                            $igstRate = $showIColumns ? $partner_io_detail->igst : 0;
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
                            <td style="border:1px solid #000; padding:3px 1px; text-align:center; border-left: none;" >{{ $i++ }}</td>
                            <td style="border:1px solid #000; padding:3px 1px; text-align:left;">{{ $partner_io_detail->instrument->code.'_'.$partner_io_detail->instrument->instrument_name.'_'.$partner_io_detail->instrument->range.'_'.getDMYDateFormat($partner_io_detail->instrument->due_date) }}</td>
                            <td style="border:1px solid #000;  padding:3px 1px; text-align:left;">&nbsp;&nbsp;</td>
                            <td style="border:1px solid #000;  padding:3px 1px; text-align:right;">{{ $partner_io_detail->qty }}</td>
                            <td style="border:1px solid #000;  padding:3px 1px; text-align:right;">EA</td>
                            <td style="border:1px solid #000;  padding:3px 1px; text-align:right;">{{ number_format($partner_io_detail->rate, 2, '.', '') }}</td>
                            <td style="border:1px solid #000;  padding:3px 1px; text-align:right;">{{ number_format($total, 2, '.', '') }}</td>
                            <td style="border:1px solid #000;  padding:3px 1px; text-align:right;">{{ number_format($total, 2, '.', '') }}</td>
                            @if($showCSColumns)
                                <td style="border:1px solid #000;  padding:3px 1px; text-align:center;">{{ $cgstRate }}</td>
                                <td style="border:1px solid #000;  padding:3px 1px; text-align:right;">{{ number_format($cgstAmt, 2) }}</td>
                                <td style="border:1px solid #000;  padding:3px 1px; text-align:center;">{{ $sgstRate }}</td>
                                <td style="border:1px solid #000; padding:3px 1px; border-right:none; text-align:right;">
                                    {{ number_format($sgstAmt, 2) }}
                                </td>
                            @endif
                            @if($showIColumns)
                                <td style="border:1px solid #000;  padding:3px 1px; text-align:center;">{{ $igstRate }}</td>
                                <td style="border:1px solid #000; padding:3px 1px; border-right:none; text-align:right;">
                                    {{ number_format($igstAmt, 2) }}
                                </td>
                            @endif
                        </tr>
                        <tr style=" text-align:right;">
                            <td colspan="7" style="border:1px solid #000; border-left:none; padding:3px 1px; text-align:left;">Total</td>
                            <td style="border:1px solid #000; padding:1px 1px;">{{ number_format($total, 2) }}</td>
                            @if($showCSColumns)
                            <td style="border:1px solid #000; padding:3px 1px;"></td>
                            <td style="border:1px solid #000; padding:3px 1px; border-right: none ">{{ number_format($totalCGST, 2) }}</td>
                            <td style="border:1px solid #000; padding:3px 1px;"></td>
                            <td  style="border:1px solid #000;  padding:3px 1px;">{{ number_format($totalSGST, 2) }}</td>
                            @endif
                            @if($showIColumns)
                            <td style="border:1px solid #000; padding:3px;"></td>
                                <td  style="border:1px solid #000;  padding:3px;">{{ number_format($totalIGST, 2) }}</td>
                            @endif
                        </tr>
                        @php
                            $taxTotal = $totalCGST + $totalSGST + $totalIGST;
                            $grandTotal = $taxTotal + $finalAmt;
                        @endphp
                        <tr>
                            <td colspan="7" style="border:none;font-size:14px;padding:0px 0px;">Amount of Tax (Rs.)</td>
                            <td style="border:1px solid #000;text-align:right;  padding: 2px 1px">{{ number_format($taxTotal,2) }}</td>  
                        </tr>
                        <tr>
                            <td colspan="7" style="border:none;font-size:14px;padding:0px 0px;">Total Invoice Value (Rs.)</td>
                            <td style="border:1px solid #000;text-align:right; padding: 2px 1px">{{ number_format($grandTotal,2) }}</td> 
                        </tr>
                </table>
                <div style="font-size:14px; line-height:1.4;border-bottom:1px solid #000; ">                   
                    <div style="padding-bottom: 5px;padding-top: 5px;">
                        <span>Total Invoice Value (in words) :</span>
                        {{ showAmount($grandTotal) }}
                    </div>
                    <div>
                        <span>Amount of Tax (in words) :</span>
                        {{ showAmount($taxTotal) }}
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; margin: 0px 0px; font-size: 14px; font-family: Arial, sans-serif;">
                    <!-- Left Column: Transporter Details -->
                    <div style="flex: 1; padding: 4px 2px; border-bottom: 1px solid #000; display: flex; flex-direction: column; justify-content: space-between;">
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 120px; font-weight: bold;"></span>
                        </div>
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 120px;">Transporter Name</span><span style="margin: 0 8px;">:</span><span> </span>
                        </div>
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 120px;">Vehicle Details</span><span style="margin: 0 8px;">:</span><span></span>
                        </div>
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 120px;">LR Number</span><span style="margin: 0 8px;">:</span><span></span>
                        </div>
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 120px;">Gross Weight</span><span style="margin: 0 8px;">:</span><span></span>
                        </div>
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 120px;">Net Weight</span><span style="margin: 0 8px;">:</span><span></span>
                        </div>
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 120px;">Tare Weight</span><span style="margin: 0 8px;">:</span><span></span>
                        </div>
                    </div>

                    <!-- Right Column: Purpose Checkboxes -->
                    <div style="flex: 1.5; padding: 8px 10px; font-size: 14px; border-bottom: 1px solid #000; display: flex; flex-direction: column;">
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
                <div style="display: flex; font-size: 14px">
                   <div style="width: 60%; display: flex; margin-top: 0px; border-right:1px solid #000;">
                        <span class="text-start" style="flex: 1;"><b>Notes: </b> FOR INSPECTION</span>
                        <span style="flex: 1; text-align: start;">Not For Sale</span>
                    </div>
                    <div style="width: 40%; text-align: center; font-size: 14px">
                        <b>ANDERSON GREENWOOD CROSBY SANMAR LIMITED(Formerly Pentair Sanmar Ltd)</b><br> <br><br>
                        <b>Authorised Signatory</b>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: start; margin: 0px 0px 40px; font-size: 13px;border-top:1px solid #000;">
                    <div style="width: 60%; display: flex; margin-top: 0px;">
                        <span style="width: 40%;">BN8</span>
                        <span style="width: 40%;">BN8</span>
                    </div>
                </div>
                <div style="display: flex; align-items: center;  font-size: 11px;border-top:1px solid #000; text-align: center;"> 
                    <span>Registered Office: No.9 Cathedral Road, Chennai - 600 089, Tamil nadu - Phone - 91 44 2812 8500 - CIN : U24230TN1985PLC011637</span>  
                </div>
            </div>
            <div style="text-align: end;">
                <span style="font-size: 11px;">DAN/STR/SF/01</span>
            </div>
        </div>
    </section>
    <script src="{{ $baseURL . ('assets/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ $baseURL . ('frequent_changing/js/onload_print.js') }}"></script>
</body>

</html>