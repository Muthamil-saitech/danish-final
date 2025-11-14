<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $obj->reference_no }}</title>
</head>
<body>
    <section class="content" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <div style="width: 98%; max-width: 1200px; margin: 30px auto;">
            <div style="padding: 18px 0; border: 1px solid #000; background: #fff;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <div style="flex: 1; text-align: center;">
                        <img src="{!! getBaseURL() .
                        (isset(getWhiteLabelInfo()->logo) ? 'uploads/white_label/' . getWhiteLabelInfo()->logo : 'images/logo.png') !!}" alt="Logo Image" class="img-fluid my-2">
                        <!-- <h3 style="font-size: 25px; font-weight: 500; margin: 2px 0;">{{ strtoupper(getCompanyInfo()->company_name) }}</h3> -->
                        <p style="font-size: 15px;margin-top: 0px; margin-bottom: 1px;">An ISO 9001:2008 Certified Company</p>
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
                            <p style="margin: 5px;  display: flex; justify-content: space-between; width: 30%; white-space: nowrap">DELIVERY CHALLAN NO SALES INVOLVED</b>
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
                        <div style="padding-left: 30px; display: flex;"> <span>Mr./Ms.</span> <b
                                style="padding-left: 10px; font-weight: 700;"> {{ $obj->customer->name }}<br>
                                {{ $obj->customer->address }}<br>
                                {{ $obj->customer->pan_no!='' ? 'PAN: '.$obj->customer->pan_no : '' }}</b>
                        </div>
                        <p>Party GSTIN No. : {{ $obj->customer->gst_no }}</p>
                    </div>
                    <div style="width: 50%; border-top: 1px solid #000; padding: 8px 10px; font-size: 16px;">
                        <div style="display: flex; margin-bottom: 4px;">
                            <span style="width: 40%;">DC No </span> <b>: {{ $obj->dc_no }}</b>
                        </div>
                        <div style="display: flex;margin-bottom: 4px;">
                            <span style="width: 40%;">DC Date</span> <b> : {{ date('d-M-y',strtotime($obj->dc_date)) }}</b>
                        </div>
                        <div style="display: flex; margin-bottom: 4px;">
                            <span style="width: 40%;">Customer Code</span> <b> : {{ $obj->customer->customer_id }}</b>
                        </div>
                    </div>
                </div>
                <table style="width:100%; border-collapse:collapse; font-size:16px;">
                    <tr>
                        <th style="border:1px solid #000; padding:4px; border-left: none;">S.No</th>
                        <th style="border:1px solid #000; padding:4px;">Part No.</th>
                        <th style="border:1px solid #000; padding:4px;">Description</th>
                        <th style="border:1px solid #000; padding:4px;">Qty</th>
                        <th style="border:1px solid #000; padding:4px;">UOM</th>
                        <th style="border:1px solid #000; padding:4px; border-right: none;">Remarks</th>
                    </tr>
                    @if (isset($obj) && $obj)
                        <?php
                        $productId = getStockProduct($obj->reference_no,$obj->line_item_no);
                        $productInfo = getFinishedProductInfo($productId);
                        ?>
                        <tr>
                            <td style="border:1px solid #000; padding:4px; text-align:center;  border-left: none;">1</td>
                            <td style="border:1px solid #000; padding:4px; text-align: center;">{{ $productInfo->code }}</td>
                            <td style="border:1px solid #000; padding:4px; ">{{ $productInfo->name }} </td>
                            <td style="border:1px solid #000; padding:4px; text-align:center;">{{ isset($stock_return) ? $stock_return->qty : $obj->current_stock }}</td>
                            <td style="border:1px solid #000; padding:4px; text-align:start;">{{ getRMUnitById($obj->unit_id) }}</td>
                            <td style="border:1px solid #000; padding:4px; text-align:start; border-right: none;">Return</td>
                        </tr>
                    @endif
                </table>
                <div
                    style="display: flex; justify-content: space-between; align-items: start; margin: 30px 10px 0px; font-size: 16px;">
                    <div>
                        <p style="margin: 0;"> Received the above Goods in good conditions</p>
                        <p style="margin-top: 30px;"> Receiver's Signature with Seal</p>
                    </div>
                    <div style="text-align: right;">
                        For <b>{{ strtoupper(getCompanyInfo()->company_name) }}</b>

                    </div>
                </div>
            </div>
            <div style="text-align: end;">
                <span style="font-size: 12px;">DAN/STR/SF/01</span>
            </div>
        </div>
    </section>
</body>