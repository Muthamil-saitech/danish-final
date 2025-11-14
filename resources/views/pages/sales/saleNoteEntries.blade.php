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
            </div>
            <div class="col-md-6">
                <a href="javascript:void();" target="_blank" class="btn bg-second-btn print_sales_note" data-id="{{ $obj->id }}"><iconify-icon icon="solar:printer-broken"></iconify-icon>@lang('index.print')</a>
                {{-- <a href="{{ route('download-material-return', encrypt_decrypt($obj->id, 'encrypt')) }}" target="_blank"
                class="btn bg-second-btn print_btn"><iconify-icon icon="solar:cloud-download-broken"></iconify-icon>
                @lang('index.download')</a> --}}
                <a class="btn bg-second-btn" href="{{ url('sales') }}/{{ encrypt_decrypt($obj->id, 'encrypt') }}/sales_items_list"><iconify-icon icon="solar:round-arrow-left-broken"></iconify-icon>@lang('index.back')</a>
            </div>
        </div>
    </section>
    @if(isset($obj) && isset($sale_note_entry))
        <section class="content" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
            <div style="width: 98%; max-width: 1200px; margin: 30px auto;">
                <div style="padding: 18px 0; border: 1px solid #000; background: #fff;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <div style="flex: 1; text-align: center;">
                            <img src="{!! getBaseURL() .
                            (isset(getWhiteLabelInfo()->logo) ? 'uploads/white_label/' . getWhiteLabelInfo()->logo : 'images/logo.png') !!}" alt="Logo Image" class="img-fluid my-2">
                            <!-- <h3 style="font-size: 25px; font-weight: 500; margin: 2px 0;">{{ strtoupper(getCompanyInfo()->company_name) }}</h3> -->
                            <p style="font-size: 15px; margin-top: 0px; margin-bottom: 1px;">An ISO 9001:2008 Certified Company</p>
                            <p style="font-size: 16px; margin: 0;">{{ safe(getCompanyInfo()->address) }}
                            </p>
                        </div>
                    </div>
                    <div
                        style="display: flex; align-items: start; justify-content: space-between; font-size: 16px; margin:0px 25px  2px 10px; flex-wrap: wrap">
                        <div style="display: flex; align-items: center;">
                            <p>GSTIN No.: </p><b style="margin-left: 10px">{{ safe(getCompanyInfo()->gst_no) }}</b>
                        </div>
                        <div>
                            <span style="display: flex; align-items: center;">
                                <p style="margin: 5px; display: flex; justify-content: space-between; width: 30%; white-space: nowrap">SSl. No
                                    <span>:</span>
                                </p> <b style="margin-left: 10px">{{ safe(getCompanyInfo()->ssi_no) }}</b>
                            </span>
                            <span style="display: flex; align-items: center;">
                                <p style="margin: 5px;  display: flex; justify-content: space-between; width: 30%; white-space: nowrap">
                            </span>
                        </div>
                        <div>
                            <span style="display: flex; align-items: center;">
                                <p style="margin: 5px;  display: flex; justify-content: space-between; width: 30%; white-space: nowrap">Phone No<span>:</span> </p><b style="margin-left: 10px">{{ safe(getCompanyInfo()->phone) }}</b>
                            </span>
                            <span style="display: flex; align-items: center;">
                                <p style="margin: 5px;  display: flex; justify-content: space-between; width: 30%; white-space: nowrap">E-Mail
                                    <span>:</span>
                                </p><b style=" width: 50%;margin-left: 10px">{{ safe(getCompanyInfo()->email) }}</b>
                            </span>
                        </div>
                    </div>
                    <div style="display: flex; margin: 12px 0 0;">
                        <div
                            style="width: 50%; border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px 10px; font-size: 16px;">
                            <b>To</b><br>
                            <div style="padding-left: 30px; padding-bottom:35px; display: flex;"> <span>Mr./Ms.</span> <b
                                    style="padding-left: 10px; font-weight: 700;"> {{ $obj->customer->name }}<br>
                                    {{ $obj->customer->address }}<br>
                                    {{ $obj->customer->pan_no!='' ? 'PAN: '.$obj->customer->pan_no : '' }}</b>
                            </div>
                            <p>Party GSTIN No. : {{ $obj->customer->gst_no }}</p>
                        </div>
                        <div style="width: 50%; border-top: 1px solid #000; padding: 8px 10px; font-size: 16px;">
                            <div style="display: flex; margin-bottom: 4px;">
                                <span style="width: 40%;">Invoice No </span> <b>: {{ isset($sale_note_entry) ? $sale_note_entry->invoice_no : '' }}</b>
                            </div>
                            <div style="display: flex;margin-bottom: 4px;">
                                <span style="width: 40%;">Invoice Date </span> <b> : {{ isset($sale_note_entry) ? date('d-M-y',strtotime($sale_note_entry->invoice_date)) : '' }}</b>
                            </div>
                            <div style="display: flex; margin-bottom: 4px;">
                                <span style="width: 40%;">Customer Code</span> <b> : {{ $obj->customer->customer_id }}</b>
                            </div>
                        </div>
                    </div>
                    <table style="width:100%; border-collapse:collapse; font-size:16px;">
                        <tr style="text-align: center;">
                            <th style="border:1px solid #000; padding:4px; border-left: none;">S.No</th>
                            <th style="border:1px solid #000; padding:4px;">Part No.</th>
                            <th style="border:1px solid #000; padding:4px;">Description</th>
                            <th style="border:1px solid #000; padding:4px;">HSN/SAC</th>
                            <th style="border:1px solid #000; padding:4px;">DC.No & Date</th>
                            <th style="border:1px solid #000; padding:4px;">PO No</th>
                            <th style="border:1px solid #000; padding:4px;">Invoice No</th>
                            <th style="border:1px solid #000; padding:4px;">Qty</th>
                            <th style="border:1px solid #000; padding:4px;">Rate</th>
                            <th style="border:1px solid #000; padding:4px; border-right: none;">Total Amount</th>
                        </tr>
                        @if (isset($sale_note_entry) && $sale_note_entry)
                            <?php
                            $productInfo = getFinishedProductInfo($sale_note_entry->product_id);
                            $order = getOrderInfo($sale_detail->order_id);
                            ?>
                            <tr>
                                <td style="border:1px solid #000; padding:4px; text-align:center;  border-left: none;">1</td>
                                <td style="border:1px solid #000; padding:4px; text-align: center;">{{ $productInfo->code }}</td>
                                <td style="border:1px solid #000; padding:4px; ">{{ $productInfo->name }} </td>
                                <td style="border:1px solid #000; padding:4px; ">{{ $productInfo->hsn_sac_no }} </td>
                                <td style="border:1px solid #000; padding:4px; ">{{ isset($challanInfo) ? $challanInfo->challan_no.'/'.date('d-m-Y',strtotime($challanInfo->challan_date)) : ' ' }}</td>
                                <td style="border:1px solid #000; padding:4px; text-align:center;">{{ isset($sale_detail) ? getPoNo($sale_detail->order_id).'/'.getLineItemNo($sale_detail->order_id) : '' }}</td>
                                <td style="border:1px solid #000; padding:4px; text-align:start;">{{ $obj->reference_no }}</td>
                                <td style="border:1px solid #000; padding:4px; text-align:start;">{{ $sale_note_entry->qty }}</td>
                                <td style="border:1px solid #000; padding:4px; text-align:start;">{{ number_format($sale_note_entry->price,2) }}</td>
                                <td style="border:1px solid #000; padding:4px; text-align:end; border-right: none;">{{ number_format($sale_note_entry->price,2) }}</td>
                            </tr>
                        @endif
                    </table>
                    @php
                    if(isset($sale_note_entry)){
                        $sum = $sale_note_entry->price;
                        $otherState = ($order->inter_state == 'N');
                        $tax_row = getTaxItems($order->tax_type == 1 ? 'Labor' : 'Sales');
                        if ($otherState) {
                        // CGST + SGST
                        $taxAmount = ($sum * ($tax_row->tax_value / 2) / 100) * 2;
                        } else {
                        // IGST
                        $taxAmount = ($sum * $tax_row->tax_value) / 100;
                        }

                        $grandTotal = $sum + $taxAmount;
                    }
                    @endphp
                    <div style="display: flex; border-bottom:1px solid #000;">
                        <div style="width: 50%; border-right: 1px solid #000; padding: 8px 10px; font-size: 16px;">
                            
                        </div>
                        <div style="width: 50%; padding: 8px 10px; font-size: 16px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                <span style="width: 40%;"><b>Goods Value</b></span>
                                {{ isset($sale_note_entry) ? number_format($sale_note_entry->price, 2) : '' }}
                            </div>
                            @if(isset($otherState))
                                @if(isset($tax_row))
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                        <span style="width: 40%;">CGST</span>
                                        <span style="width: 30%;">{{ number_format($tax_row->tax_value / 2, 2) }} %</span>
                                        <span style="text-align: right; width: 30%;">{{ number_format(($sum * ($tax_row->tax_value / 2)) / 100,2) }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                        <span style="width: 40%;">SGST</span>
                                        <span style="width: 30%;">{{ number_format($tax_row->tax_value / 2, 2) }} %</span>
                                        <span style="text-align: right; width: 30%;">{{ number_format(($sum * ($tax_row->tax_value / 2)) / 100,2) }}</span>
                                    </div>
                                @else
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                        <span style="width: 40%;">IGST</span>
                                        <span style="width: 30%;">{{ number_format($tax_row->tax_value, 2) }} %</span>
                                        <span style="text-align: right; width: 30%;">{{ number_format(($sum * ($tax_row->tax_value / 2)) / 100,2) }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div style="display: flex; border-bottom:1px solid #000;">
                        <div style="width: 50%; border-right: 1px solid #000; padding: 8px 10px; font-size: 16px;">
                            <strong>Bank Details :</strong>
                            <div>
                                <span>A/C No: 101902000000015</span> &nbsp;&nbsp; 
                                <span>Branch: Viralimalai</span>
                            </div>
                            <div>
                                <span>Bank: Indian Overseas Bank</span> &nbsp;&nbsp; 
                                <span>IFSC Code: IOBA001019</span>
                            </div>
                        </div>
                        <div style="width: 50%; padding: 8px 10px; font-size: 16px;">
                            <div style="display: flex; border-bottom:1px solid #000; padding-bottom:4px; margin-bottom:4px;">
                                <span style="width: 40%;">Total</span>
                                <span style="text-align:right; flex:1;">{{ isset($grandTotal) ? number_format($grandTotal, 2) : '0.00' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                <span style="width: 40%;"><b>Grand Total</b></span>
                                {{ isset($grandTotal) ? number_format($grandTotal, 2) : '0.00' }}
                            </div>
                        </div>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; align-items: start; margin: 30px 10px 0px; font-size: 16px;">
                        <div>
                                {{-- <p style="margin: 0;"> Received the above Goods in good conditions</p>
                                <p style="margin-top: 30px;"> Receiver's Signature with Seal</p> --}}
                        </div>
                        <div style="text-align: center;margin-bottom: 60px;"">
                            For <b>{{ strtoupper(getCompanyInfo()->company_name) }}</b>
                            
                        </div>
                    </div>
                </div>
                <div style="text-align: end;">
                    <span style="font-size: 12px;">DAN/STR/SF/01</span>
                </div>
            </div>
        </section>
    @else
        <section>
            <div class="text-center">
                <p>No Entries Found</p>
            </div>
        </section>
    @endif
</section>
@endsection
@section('script')
<script>
$(document).ready(function () {
    $(document).on("click", ".print_sales_note", function () {
        viewChallan($(this).attr("data-id"));
    });
    function viewChallan(id) {
        let base_url = $("#hidden_base_url").val();
        open(
            base_url + "sales-note-print/" + id,
            "Print Sales Notes",
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