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
    <title>{{ $manufacture->reference_no }}</title>
    <link rel="stylesheet" href="{{ getBaseURL() }}frequent_changing/css/pdf_common.css">
</head>

<body>
    <section class="content">
        <div class="row" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
            <div style="max-width: 1200px; margin: 30px auto; ">
                <div style="text-align: center; border-bottom: 1px solid #000; padding: 10px;">
                    <img src="{!! getBaseURL() .
                        (isset(getWhiteLabelInfo()->logo) ? 'uploads/white_label/' . getWhiteLabelInfo()->logo : 'images/logo.png') !!}" alt="Logo Image" class="img-fluid mb-2">
                    <h3 style="font-weight: 700; text-decoration: underline; font-size: 20px; padding-bottom: 15px;">INSPECTION REPORT</h3>
                    <form style="display: flex; justify-content: center; gap: 30px; align-items: center;">
                        <div style="display: flex; align-items: center;">
                            <input type="checkbox" style="transform: scale(1.8); margin-right: 20px; border-radius: 0px;" disabled {{ $di_inspect_dimensions->count() > 0 && !is_object($inspection_approval) ? 'checked' : '' }}>
                            <label style="font-size: 20px;"> In-Process</label>
                        </div>
                        <div style="display: flex; align-items: center;">
                            <input type="checkbox" style="transform: scale(1.8); margin-right: 20px;" disabled {{ is_object($inspection_approval) && $inspection_approval->status == '2' ? 'checked' : '' }}>
                            <label style="font-size: 20px;"> Final</label>
                        </div>
                    </form>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: start; padding: 10px;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 10px;"> <span style="font-weight: 600; font-size: 16px; display: flex; justify-content: space-between; width: 100px;">CUSTOMER <span>:</span> </span>
                            <p style="margin: 5px; font-weight: 600;">{{ getCustomerNameById($manufacture->customer_id) }}</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;"> <span style="font-weight: 600; font-size: 16px; display: flex; justify-content: space-between; width: 100px;">PART NAME <span>:</span> </span>
                            <p style="margin: 5px; font-weight: 600;">{{ isset($finishProduct) ? $finishProduct->name : '' }}</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;"> <span style="font-weight: 600; font-size: 16px; display: flex; justify-content: space-between; width: 100px;">PART No. <span>:</span> </span>
                            <p style="margin: 5px; font-weight: 600;">{{ isset($finishProduct) ? $finishProduct->code : '' }}</p>
                        </div>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 10px;"> <span style="font-size: 16px; display: flex; justify-content: space-between; width: 100px;">DRG No. <span>:</span> </span>
                            <p style="margin: 5px;">{{ $manufacture->drawer_no }}</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;"> <span style="font-size: 16px; display: flex; justify-content: space-between; width: 100px;">REV <span>:</span> </span>
                            <p style="margin: 5px;"> {{ $manufacture->rev }}</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;"> <span style="font-size: 16px; display: flex; justify-content: space-between; width: 100px;">OPERATION <span>:</span> </span>
                            <p style="margin: 5px;"> {{ $manufacture->operation }}</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;"> <span style="font-size: 16px; display: flex; justify-content: space-between; width: 100px;">PoNo <span>:</span> </span>
                            <p style="margin: 5px;">{{ getPoNo($manufacture->customer_order_id).'/'.getLineItemNo($manufacture->customer_order_id) }}</p>
                        </div>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 10px;"> <span style="font-size: 16px; display: flex; justify-content: space-between; width: 150px;">MATERIAL <span>:</span> </span>
                            <p style="margin: 5px;">{{ materialName($manufacture->rawMaterials[0]->rmaterials_id).'-'.($material->code) }} {{ $material->diameter!='' ? 'DIA '.$material->diameter : '' }}</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;"> <span style="font-size: 16px; display: flex; justify-content: space-between; width: 150px;">Total Quantity <span>:</span> </span>
                            <p style="margin: 5px;">{{ $manufacture->product_quantity }}</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;"> <span style="font-size: 16px; display: flex; justify-content: space-between; width: 150px;">Sample Quantity <span>:</span> </span>
                            <p style="margin: 5px;">{{ $manufacture->product_quantity }}</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;"> <span style="font-size: 16px; display: flex; justify-content: space-between; width: 150px;">Heat No. <span>:</span> </span>
                            <p style="margin: 5px;">{{ getheatNo($manufacture->rawMaterials[0]->rmaterials_id) }}</p>
                        </div>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 10px;"> <span style="font-size: 16px; display: flex; justify-content: space-between; width: 100px;">Report No. <span>:</span> </span>
                            <p style="margin: 5px;">{{ 'IR' . str_pad($manufacture->id, 4, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;"> <span style="font-size: 16px; display: flex; justify-content: space-between; width: 100px;">Date <span>:</span> </span>
                            <p style="margin: 5px;"> {{ date('d/m/Y') }}</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;"> <span style="font-size: 16px; display: flex; justify-content: space-between; width: 100px;">DC No. <span>:</span> </span>
                            <p style="margin: 5px;"> {{ $material_stock->dc_no }}</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;"> <span style="font-size: 16px; display: flex; justify-content: space-between; width: 100px;">PPCRCNo. <span>:</span> </span>
                            <p style="margin: 5px;">{{ $manufacture->reference_no }}</p>
                        </div>
                    </div>
                </div>
                @php
$columnsPerPage = $columnsPerPage ?? 13;
$totalPages = $totalPages ?? 1;
@endphp

@for ($page = 1; $page <= $totalPages; $page++)
    @php
        $startIndex = ($page - 1) * $columnsPerPage + 1;
        $endIndex = min($startIndex + $columnsPerPage - 1, $manufacture->product_quantity);
    @endphp

    <div style="page-break-after: always; border:1px solid #000; border-top: none; margin-bottom: 20px;">
        <table style="width:100%; border-collapse:collapse; font-size:16px;">
            <tr style="text-align:center;">
                <th style='border: 1px solid #000; border-left: none;'>Sl.No</th>
                <th style='border: 1px solid #000; padding: 10px;'>PARAMETER</th>
                <th style='border: 1px solid #000; padding: 10px;'>DRAWING <br> SPEC.</th>
                <th style='border: 1px solid #000; padding: 10px;'>INSP. <br>METHOD</th>
                <th style='border: 1px solid #000; border-right: none;' colspan="{{ $columnsPerPage }}">
                    OBSERVED DIMENSIONS SL.No.
                </th>
            </tr>

            <tr>
                <td style="border: 1px solid #000; padding: 5px; border-left: none;"></td>
                <td style="border: 1px solid #000; padding: 5px;">&nbsp;</td>
                <td style="border: 1px solid #000; padding: 5px;">&nbsp;</td>
                <td style="border: 1px solid #000; padding: 5px;">&nbsp;</td>
                @for ($i = $startIndex; $i <= $endIndex; $i++)
                    <td style="border: 1px solid #000; padding: 5px; border-right:none;">
                        DBF{{ $i }}
                    </td>
                @endfor
            </tr>

            {{-- Dimension Inspection --}}
            @if(isset($inspection) && count($inspection) > 0)
                <tr>
                    <th></th>
                    <th colspan="{{ 4 + $columnsPerPage }}" style="text-align: start; padding: 10px 7px;">Dimension Inspection</th>
                </tr>
                @foreach($inspection as $key => $value)
                    @if($value->di_param!='')
                        <tr>
                            <td style="border: 1px solid #000; padding: 10px 7px; border-left: none;">{{ $loop->iteration }}</td>
                            <td style="border: 1px solid #000; padding: 10px 7px;">{{ $value->di_param }}</td>
                            <td style="border: 1px solid #000; padding: 10px 7px;">{{ $value->di_spec }}</td>
                            <td style="border: 1px solid #000; padding: 10px 7px; text-align:center;">{{ $value->di_method ?? '-' }}</td>

                            @php
                            $matchedDimensions = $di_inspect_dimensions->where('inspect_id', $value->id)->values();
                            @endphp
                            @for ($i = $startIndex; $i <= $endIndex; $i++)
                                @php $index = $i - 1; @endphp
                                <td style="border: 1px solid #000; border-right:none;">
                                    {{ $matchedDimensions[$index]->di_observed_dimension ?? '' }}
                                </td>
                            @endfor
                        </tr>
                    @endif
                @endforeach
            @endif

            {{-- Appearance Inspection --}}
            @if(isset($inspection) && count($inspection) > 0)
                <tr>
                    <th></th>
                    <th colspan="{{ 4 + $columnsPerPage }}" style="text-align: start; padding: 10px 7px;">Appearance Inspection</th>
                </tr>
                @foreach($inspection as $key => $value)
                    @if($value->ap_param!='')
                        <tr>
                            <td style="border: 1px solid #000; padding: 10px 7px; border-left: none;">{{ $loop->iteration }}</td>
                            <td style="border: 1px solid #000; padding: 10px 7px;">{{ $value->ap_param }}</td>
                            <td style="border: 1px solid #000; padding: 10px 7px;">{{ $value->ap_spec }}</td>
                            <td style="border: 1px solid #000; padding: 10px 7px; text-align:center;">{{ $value->ap_method ?? '-' }}</td>

                            @php
                            $matchedApDimensions = $ap_inspect_dimensions->where('inspect_id', $value->id)->values();
                            @endphp
                            @for ($i = $startIndex; $i <= $endIndex; $i++)
                                @php $index = $i - 1; @endphp
                                <td style="border: 1px solid #000; border-right:none;">
                                    {{ $matchedApDimensions[$index]->ap_observed_dimension ?? '' }}
                                </td>
                            @endfor
                        </tr>
                    @endif
                @endforeach
            @endif
        </table>

        {{-- Footer --}}
        <div style="display: flex; justify-content: space-around; margin-top: 20px;">
            <div>
                <p>INSPECTED BY</p>
                <h5>{{ !empty($inspection_approval) && is_object($inspection_approval) ? getEmpCode($inspection_approval->inspected_by) : '' }}</h5>
            </div>
            <div>
                <p>CHECKED BY</p>
                <h5>{{ !empty($inspection_approval) && is_object($inspection_approval) ? getEmpCode($inspection_approval->checked_by) : '' }}</h5>
            </div>
        </div>

        <div style="text-align:center; margin-top:10px;">
            <strong>Page {{ $page }} of {{ $totalPages }}</strong>
        </div>
    </div>
@endfor
                <p style="padding-top: 5px; padding-bottom: 100px; font-size: 13px;"><b>Remarks</b>&nbsp;&nbsp;{{ !empty($inspection_approval) && is_object($inspection_approval) ? $inspection_approval->remarks : '' }}</p>
            </div>
        </div>
    </section>
    <script src="{{ asset('assets/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('frequent_changing/js/onload_print.js') }}"></script>
</body>

</html>