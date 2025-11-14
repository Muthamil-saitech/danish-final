<?php
$setting = getSettingsInfo();
$tax_setting = getTaxInfo();
$baseURL = getBaseURL();
$whiteLabelInfo = App\WhiteLabelSettings::first();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $obj->reference_no }}</title>
    <link rel="stylesheet" href="{{ getBaseURL() }}frequent_changing/css/pdf_common.css">
</head>

<body>
    @if(isset($latest_form) && $latest_form!='')
    @php
    $imagePath = base_path('uploads/route_card_form/'.$latest_form);
    $imageData = base64_encode(file_get_contents($imagePath));
    $mimeType = mime_content_type($imagePath);
    @endphp
    <div style="width: 98%; max-width: 1100px; margin: 30px auto;">
        <div style="display: flex; align-items: center; justify-content: center; position: relative; margin-bottom: 10px;">
            <div style="position: absolute; left: 0;">
                <img src="{!! getBaseURL() .
                            (isset(getWhiteLabelInfo()->logo) ? 'uploads/white_label/' . getWhiteLabelInfo()->logo : 'images/logo.png') !!}" 
                    alt="Logo Image" 
                    class="img-fluid mb-2" 
                    style="max-height: 70px;">
            </div>
            <div style="text-align: center;">
                <h3 style="font-weight: bold; font-size: 22px; margin-bottom: 5px;">
                    {{ strtoupper($setting->name_company_name) }}
                </h3>
                <p style="font-size: 16px; font-weight: bold; margin: 0;">
                    {{ isset($title) && $title ? strtoupper($title) : '' }}
                </p>
            </div>
        </div>
        <img src="data:{{ $mimeType }};base64,{{ $imageData }}" alt="route card form" style="display: block; margin: 0 auto; width: 100%;">
    </div>
    @else
    <div style="width: 98%; max-width: 1100px; margin: 30px auto;">
        <div style="display: flex; align-items: center; justify-content: center; position: relative; margin-bottom: 10px;">
            <div style="position: absolute; left: 0;">
                <img src="{!! getBaseURL() .
                            (isset(getWhiteLabelInfo()->logo) ? 'uploads/white_label/' . getWhiteLabelInfo()->logo : 'images/logo.png') !!}" 
                    alt="Logo Image" 
                    class="img-fluid" 
                    style="max-height: 70px;">
            </div>
            <div style="text-align: center;">
                <h3 style="font-weight: bold; font-size: 22px; margin: 0;">{{ strtoupper($setting->name_company_name) }}</h3>
                <p style="font-size: 16px; font-weight: bold; margin: 5px 0 0;">
                    {{ isset($title) && $title ? strtoupper($title) : '' }}
                </p>
            </div>
        </div>
        <div style="border: 1px solid #222; border-top:none; margin-top: 20px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 15px; ">
                <tr>
                    <td style="width: 50%; border: 1px solid #222; padding: 4px;border-left:none;">
                        <b>PPCRC No :</b> {{ isset($obj) ? $obj->reference_no : "-" }}
                    </td>
                    <td style="width: 50%; border: 1px solid #222; padding: 4px;border-right:none;">
                        <b>PPCRC Date :</b> {{ isset($obj) ? getDateFormat($obj->start_date) : "-" }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; padding: 7px 5px;">
                        Customer Name : <b>{{ isset($obj) ? getCustomerNameById($obj->customer_id) : '-' }}</b>
                    </td>
                    <td style="width: 50%; padding: 7px 5px;">
                        Item : <b>{{ isset($product) ? $product->name : '-' }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; padding: 7px 5px;">
                        Part Number : <b>{{ isset($product) ? $product->code : '-' }}</b>
                    </td>
                    <td style="width: 50%; padding: 7px 5px;">
                        Drg No. : <b>{{ isset($drawer) ? $drawer->drawer_no : '-' }}</b>
                        &nbsp;&nbsp; Rev No. : <b>{{ isset($product) ? $product->rev : '-' }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; padding: 7px 5px;">
                        P.O No. : <b>{{ isset($order) ? $order->reference_no.'/'.$order->line_item_no.'_'.date('d-M-y',strtotime($order->po_date)) : '-' }}</b>
                    </td>
                    <td style="width: 50%; padding: 7px 5px;">
                        Size : 
                        &nbsp;&nbsp; Drg Location: <b> {{ isset($drawer) ? $drawer->drawer_loc : '-' }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; padding: 7px 5px;">
                        Delivery Date : <b>{{ isset($obj) && $obj->complete_date!='' ? getDateFormat($obj->complete_date) : "-" }}</b>
                    </td>
                    <td style="width: 50%; padding: 7px 5px;">
                        PO Quantity : <b>{{ isset($obj) ? $obj->product_quantity : "-" }} {{ getStockUnitById($m_rmaterial->stock_id) }}</b>
                    </td>
                </tr>
            </table>
            <table style="width: 100%; border-collapse: collapse; font-size: 15px; margin-bottom: 10px;">
                <tr style="text-align: center;">
                    <th style="border: 1px solid #222; padding: 4px; border-left: none;">Sl.No</th>
                    <th style="border: 1px solid #222; padding: 4px;">Scope</th>
                    <th style="border: 1px solid #222; padding: 4px;">Specification</th>
                    <th style="border: 1px solid #222; padding: 4px;">Your DC &amp; Challan No.</th>
                    <th style="border: 1px solid #222; padding: 4px;">H.No./R.No.</th>
                    <th style="border: 1px solid #222; padding: 4px;">Batch No</th>
                    <th style="border: 1px solid #222; padding: 4px;">Material Quantity</th>
                    <th style="border: 1px solid #222; padding: 4px;">Checked By</th>
                    <th style="border: 1px solid #222; padding: 4px;">Remarks</th>
                </tr>
                @if(isset($rmaterial))
                <tr style="text-align: left;">
                    <td style="border: 1px solid #222; padding: 4px; text-align: center;  border-left: none;">1</td>
                    <td style="border: 1px solid #222; padding: 4px;">{{ isset($product) ? $product->scope : '-' }}</td>
                    <td style="border: 1px solid #222; padding: 4px;">{{ $rmaterial->code.' '.$rmaterial->name }}{{ $rmaterial->diameter!='' ? '_DIA'.$rmaterial->diameter : '' }}{{ isset($product) ? '_'.$product->name : ' ' }}{{ isset($rmaterial->remarks) && $rmaterial->remarks!='' ? '_'.$rmaterial->remarks : ' ' }}{{ isset($rmaterial->old_mat_no) && $rmaterial->old_mat_no!='' ? '_'.$rmaterial->old_mat_no : '' }}{{ '-'.$rmaterial->categoryInfo->name }}</td>
                    <td style="border: 1px solid #222; padding: 4px;">{{ isset($m_rmaterial->materialStock)  ? $m_rmaterial->materialStock->dc_no.'/'.date('d-M-Y',strtotime($m_rmaterial->materialStock->dc_date)) : ' ' }}</td>
                    <td style="border: 1px solid #222; padding: 4px;">{{ isset($m_rmaterial->materialStock) ? $m_rmaterial->materialStock->heat_no : '&nbsp;' }}</td>
                    <td style="border: 1px solid #222; padding: 4px;">&nbsp;</td>
                    <td style="border: 1px solid #222; padding: 4px;">{{ isset($m_rmaterial) ? $m_rmaterial->consumption : '' }}</td>
                    <td style="border: 1px solid #222; padding: 4px;"></td>
                    <td style="border: 1px solid #222; padding: 4px;">
                    </td>
                </tr>
                @endif
            </table>
            <div style="margin-bottom: 5px; padding: 5px 10px; border-bottom: 1px solid #000; line-height: 25px; display: flex; justify-content: space-between;">
                <div>
                    <b>Tools/Gauges List : </b><br>
                    @if(isset($drawer) && $drawer->tools_id!='')
                    @php $tools = explode(',',$drawer->tools_id); $j = 0; @endphp
                        @foreach($tools as $key => $tool)
                        {{ ++$j.'. '.getToolName($tool) }}<br>
                        @endforeach
                    @endif
                </div>
                {{-- <p>{{ isset($drawer) && $drawer->notes!='' ? 'Yes' : 'No' }}</p> --}}
            </div>
            <div style="margin-bottom: 5px; padding: 5px 10px; line-height: 25px;">
                <b>Program List :</b><br>
                @if(isset($drawer) && $drawer->program_code!='')
                    @php $program_codes = json_decode($drawer->program_code,true); $i = 0; @endphp
                    @foreach($program_codes as $key => $code)
                    {{ ++$i.'. '.$code }}<br>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <div style="display: flex; gap: 20px; width: 98%; max-width: 1100px; margin: 30px auto;">
        <div style="width: 50%;">
            <h3 style="text-align: center; font-weight: bold; font-size: 22px; margin-bottom: 2px;">Production Process Route</h3>
            <p style="text-align: center;margin-bottom: 5px;"><small>(Filled By Operator)</small></p>
            <table style="border-collapse: collapse; font-size: 15px;">
                <tr style="text-align: center;">
                    <th style="border: 1px solid #222; padding: 10px 5px; width: 8%;">OPR No</th>
                    <th style="border: 1px solid #222; padding: 10px 5px; width: 20%;">Operations</th>
                    <th style="border: 1px solid #222; padding: 10px 5px; width: 8%;">Cycle Time</th>
                    <th style="border: 1px solid #222; padding: 10px 5px; width: 15%;">Date</th>
                    <th style="border: 1px solid #222; padding: 10px 5px; width: 15%;">Opr Code</th>
                    <th style="border: 1px solid #222; padding: 10px 5px; width: 15%;">M/C Code</th>
                    <th style="border: 1px solid #222; padding: 10px 5px; width: 15%;">Qty</th>
                </tr>
                <tbody style="text-align: center;">
                    @if(isset($m_stages))
                    <?php $total_mimute = 0; ?>
                    @foreach ($m_stages as $value)
                    <?php
                        $total_mimute = $value->stage_minute + $value->stage_set_minute; 
                    ?>
                    <tr>
                        <td style="border: 1px solid #222; padding:  10px 5px;" rowspan="3">{{ $loop->iteration }}</td>
                        <td style="border: 1px solid #222; padding:  10px 5px;" rowspan="3">{{ getProductionStage($value->productionstage_id) }}</td>
                        <td style="border: 1px solid #222; padding:  10px 5px;" rowspan="3">{{ $total_mimute }}</td>
                        <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                        <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                        <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                        <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                        <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                        <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                        <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                        <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                        <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                        <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <div style="width: 50%;">
            <h3 style="text-align: center; font-weight: bold; font-size: 22px; margin-bottom: 2px;">Quality Control</h3>
            <p style="text-align: center;margin-bottom: 5px;"><small>(Filled By Quality Inspector)</small></p>
            <table style="text-align: center; border-collapse: collapse; font-size: 15px; width: 100%;">
                <tr>
                    <th style="border: 1px solid #222; padding:  10px 5px; width: 20%;">Date</th>
                    <th style="border: 1px solid #222; padding:  10px 5px; width: 20%;">Accepted</th>
                    <th style="border: 1px solid #222; padding:  10px 5px; width: 20%;">Blow Hole</th>
                    <th style="border: 1px solid #222; padding:  10px 5px; width: 20%;">RMI</th>
                    <th style="border: 1px solid #222; padding:  10px 5px; width: 20%;">Rejected</th>
                    <th style="border: 1px solid #222; padding:  20px 25px; width: 20%;">RMR</th>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                    <td style="border: 1px solid #222; padding:  10px 5px;"></td>
                </tr>
            </table>
        </div>
    </div>
    <div style="display: flex; align-items: start; flex-direction: column; gap: 20px; width: 98%; max-width: 1100px; margin: 30px auto;">
        <div style="display: flex; justify-content: center; align-items: center; ">
            <h3 style="font-weight: bold; font-size: 22px; margin: 0;">Dispatch</h3>
            <small style="margin-left: 6px; font-size: 14px;">(Filled By Delivery Person)</small>
        </div>
        <table style="text-align:center; width: 100%; border-collapse: collapse; font-size: 15px; margin-bottom: 10px;">
            <tr>
                <th style="border: 1px solid #222; padding: 4px;">Date</th>
                <th style="border: 1px solid #222; padding: 4px;">Our DC No</th>
                <th style="border: 1px solid #222; padding: 4px;">Quantity</th>
                <th style="border: 1px solid #222; padding: 4px;">Signature</th>
                <th style="border: 1px solid #222; padding: 4px;">Vehicle No</th>
                <th style="border: 1px solid #222; padding: 4px;">Remarks</th>
            </tr>
            <tr>
                <td style="border: 1px solid #222; padding: 4px; text-align: center;">1</td>
                <td style="border: 1px solid #222; padding: 4px;"></td>
                <td style="border: 1px solid #222; padding: 4px;"> </td>
                <td style="border: 1px solid #222; padding: 4px;"> </td>
                <td style="border: 1px solid #222; padding: 4px;"></td>
                <td style="border: 1px solid #222; padding: 4px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #222; padding: 4px; text-align: center;">2</td>
                <td style="border: 1px solid #222; padding: 4px;"></td>
                <td style="border: 1px solid #222; padding: 4px;"> </td>
                <td style="border: 1px solid #222; padding: 4px;"> </td>
                <td style="border: 1px solid #222; padding: 4px;"></td>
                <td style="border: 1px solid #222; padding: 4px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #222; padding: 4px; text-align: center;">3</td>
                <td style="border: 1px solid #222; padding: 4px;"></td>
                <td style="border: 1px solid #222; padding: 4px;"> </td>
                <td style="border: 1px solid #222; padding: 4px;"> </td>
                <td style="border: 1px solid #222; padding: 4px;"></td>
                <td style="border: 1px solid #222; padding: 4px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #222; padding: 4px; text-align: center;">4</td>
                <td style="border: 1px solid #222; padding: 4px;"></td>
                <td style="border: 1px solid #222; padding: 4px;"> </td>
                <td style="border: 1px solid #222; padding: 4px;"> </td>
                <td style="border: 1px solid #222; padding: 4px;"></td>
                <td style="border: 1px solid #222; padding: 4px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #222; padding: 4px; text-align: center;">5</td>
                <td style="border: 1px solid #222; padding: 4px;"></td>
                <td style="border: 1px solid #222; padding: 4px;"> </td>
                <td style="border: 1px solid #222; padding: 4px;"> </td>
                <td style="border: 1px solid #222; padding: 4px;"></td>
                <td style="border: 1px solid #222; padding: 4px;"></td>
            </tr>
        </table>
        <div style="border: 1px solid #000; width: 100%; min-height: 100px; padding: 8px; font-size: 14px;">
            Remark:
        </div>
    </div>
    @endif
    <script src="{{ asset('assets/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('frequent_changing/js/onload_print.js') }}"></script>
</body>

</html>