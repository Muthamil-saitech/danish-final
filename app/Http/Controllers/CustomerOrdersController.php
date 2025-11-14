<?php
/*
##############################################################################
# iProduction - Production and Manufacture Management Software
##############################################################################
# AUTHOR:        Door Soft
##############################################################################
# EMAIL:        info@doorsoft.co
##############################################################################
# COPYRIGHT:        RESERVED BY Door Soft
##############################################################################
# WEBSITE:        https://www.doorsoft.co
##############################################################################
# This is CustomerOrdersController Controller
##############################################################################
 */

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerOrder;
use App\CustomerOrderDelivery;
use App\CustomerOrderDetails;
use App\CustomerOrderInvoice;
use App\CustomerPoReorder;
use App\FinishedProduct;
use App\FPrmitem;
use App\MaterialStock;
use App\Unit;
use App\TaxItems;
use App\RawMaterial;
use App\RawMaterialCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerOrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $total_orders = CustomerOrderDetails::where('del_status', 'Live')->count();
        $startDate = '';
        $endDate = '';
        $customer_id = escape_output($request->get('customer_id'));
        unset($request->_token);
        $order = CustomerOrder::where('del_status', "Live");
        if (isset($request->startDate) && $request->startDate != '') {
            $startDate = date('Y-m-d 00:00:00', strtotime($request->startDate));
            $order->where('created_at', '>=', $startDate);
        }
        if (isset($request->endDate) && $request->endDate != '') {
            $endDate = date('Y-m-d 23:59:59', strtotime($request->endDate));
            $order->where('created_at', '<=', $endDate);
        }
        if (isset($customer_id) && $customer_id != '') {
            $order->where('customer_id', $customer_id);
        }
        $obj = $order->with('details')->orderBy('id', 'DESC')->get()->map(function ($customer_order) {
            foreach ($customer_order->details as $detail) {
                $detail->reorder_total = DB::table('tbl_customer_po_reorders')
                ->where('customer_order_detail_id', $detail->id)
                ->value('subtotal');
                /* $exists = MaterialStock::where('reference_no', $customer_order->reference_no)
                    ->where('line_item_no', $detail->line_item_no)
                    ->where('mat_id', $detail->raw_material_id)
                    ->where('del_status', 'Live')
                    ->exists();
                    dd($customer_order->reference_no, $detail->line_item_no, $detail->raw_material_id, $exists);
                $detail->used_in_stock = $exists; */
            }
            return $customer_order;
        });
        // dd($obj);
        $customers = Customer::where('del_status', 'Live')->orderBy('id', 'DESC')->get();
        $title = __('index.customer_order');
        return view('pages.customer_order.index', compact('title', 'obj', 'customers', 'startDate', 'endDate', 'customer_id', 'total_orders'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = __('index.add_customer_order');
        // $total_customer = CustomerOrder::count();
        // $ref_no = "CO-" . str_pad($total_customer + 1, 6, '0', STR_PAD_LEFT);
        $customers = Customer::orderBy('id', 'DESC')->where('del_status', "Live")->get()
            ->mapWithKeys(function ($customer) {
                return [$customer->id => $customer->name . ' (' . $customer->customer_id . ')'];
            });
        $stock_customers = Customer::where('del_status', 'Live')->orderBy('id', 'DESC')->get();
        $orderTypes = ['Quotation' => 'Labor', 'Work Order' => 'Sales'];
        $tax_types = TaxItems::where('del_status', 'Live')->where('collect_tax', 'Yes')->get();
        $units = Unit::orderBy('name', 'ASC')->where('del_status', "Live")->get();
        $rmaterialcats = RawMaterialCategory::where('del_status', "Live")
            ->where('mat_type_id', 1)
            ->orderBy('name', 'ASC')
            ->get();
        $rawMaterialIds = RawMaterial::whereIn('category', $rmaterialcats->pluck('id'))
            ->where('del_status', 'Live')
            ->pluck('id');
        $productIds = FPrmitem::whereIn('rmaterials_id', $rawMaterialIds)
            ->pluck('finish_product_id')
            ->unique();
        $productList = FinishedProduct::orderBy('name', 'ASC')->where('del_status', "Live")->whereIn('id', $productIds)->get();
        $product = $productList->pluck('name', 'id');
        return view('pages.customer_order.create', compact('title', 'customers', 'orderTypes', 'units', 'productList', 'product', 'tax_types', 'stock_customers'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // dd($request->all());
        request()->validate([
            'reference_no' => [
                'required',
                'max:50',
                /* Rule::unique('tbl_customer_orders', 'reference_no')->where(function ($query) {
                    return $query->where('del_status', 'Live');
                }), */
            ],
            'customer_id' => 'required',
            'order_type' => 'required',
            'po_date' => 'required',
        ],[
            'reference_no.unique' => 'The po number field already exists'
        ]);
        try {
            $productList = $request->get('product');
            $customerOrder = new \App\CustomerOrder();
            $file = '';
            if ($request->hasFile('file_button')) {
                $file = $request->file('file_button');
                $filename = $file->getClientOriginalName();
                $fileName = time() . "_" . $filename;
                $file->move(base_path('uploads/order'), $fileName);
                $customerOrder->file = $fileName;
            }
            $customerOrder->reference_no = null_check(escape_output($request->get('reference_no')));
            $customerOrder->customer_id = null_check(escape_output($request->get('customer_id')));
            $customerOrder->order_type = escape_output($request->get('order_type'));
            $customerOrder->po_date = date('Y-m-d', strtotime($request->get('po_date')));
            $customerOrder->delivery_address = escape_output($request->get('delivery_address'));
            $customerOrder->total_product = null_check(sizeof($productList));
            $customerOrder->total_amount = null_check(escape_output($request->get('total_subtotal')));
            $customerOrder->quotation_note = html_entity_decode($request->get('quotation_note'));
            $customerOrder->internal_note = html_entity_decode($request->get('internal_note'));
            $customerOrder->order_status = '0';
            $customerOrder->created_by = auth()->user()->id;
            $customerOrder->created_at = date('Y-m-d h:i:s');
            $customerOrder->save();
            $inter_state = array_values($_POST['inter_state']);
            if (isset($_POST['product']) && is_array($_POST['product'])) {
                foreach ($_POST['product'] as $row => $productId) {
                    $obj = new \App\CustomerOrderDetails();
                    $obj->customer_order_id = $customerOrder->id;
                    $lastEntry = \App\CustomerOrderDetails::orderBy('id', 'desc')
                        ->value('so_entry_no');
                    if ($lastEntry) {
                        $nextNumber = (int)$lastEntry + 1;
                    } else {
                        $nextNumber = 1;
                    }
                    $obj->so_entry_no = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                    $obj->product_id = null_check(escape_output($productId));
                    $obj->raw_material_id = null_check(escape_output($_POST['raw_material'][$row] ?? 0));
                    $obj->raw_qty = null_check(escape_output($_POST['raw_quantity'][$row] ?? 0));
                    $obj->quantity = null_check(escape_output($_POST['prod_quantity'][$row] ?? 0));
                    $obj->sale_price = null_check(escape_output($_POST['sale_price'][$row] ?? 0));
                    $obj->price = null_check(escape_output($_POST['price'][$row] ?? 0));
                    $obj->tax_type = escape_output($_POST['tax_type'][$row] ?? '');
                    $obj->inter_state = escape_output($inter_state[$row] ?? '');
                    $obj->cgst = escape_output($_POST['cgst'][$row] ?? '');
                    $obj->sgst = escape_output($_POST['sgst'][$row] ?? '');
                    $obj->igst = escape_output($_POST['igst'][$row] ?? '');
                    $obj->discount_percent = 0;
                    $obj->tax_amount = null_check(escape_output($_POST['tax_amount'][$row] ?? 0));
                    $obj->sub_total = null_check(escape_output($_POST['sub_total'][$row] ?? 0));
                    $obj->delivery_date = $_POST['delivery_date_product'][$row] != '' ? date('Y-m-d', strtotime(escape_output($_POST['delivery_date_product'][$row] ?? ''))) : null;
                    $obj->line_item_no = escape_output($_POST['line_item_no'][$row] ?? '');
                    $obj->order_status = '0';
                    $obj->production_status = 0;
                    $obj->delivered_qty = 0;
                    $obj->save();
                    $inv_obj = new \App\CustomerOrderInvoice();
                    $inv_obj->customer_order_id = null_check($obj->id);
                    $inv_obj->invoice_type = 'Quotation';
                    $inv_obj->amount = null_check(escape_output($_POST['sub_total'][$row] ?? 0));
                    $inv_obj->invoice_date = null_check(date('Y-m-d', strtotime($request->get('po_date'))));
                    $inv_obj->paid_amount = 0.00;
                    $inv_obj->due_amount = null_check(escape_output($_POST['sub_total'][$row] ?? 0));
                    // $inv_obj->order_due_amount = null_check($request->invoice_order_due[$key]);
                    $inv_obj->save();
                }
            }
            // if (!empty($request->invoice_type)) {
            //     foreach ($request->invoice_type as $key => $value) {
            
            //     }
            // }
            return redirect('customer-orders')->with(saveMessage());
        } catch (\Exception $e) {
            return redirect()->back()->withInput($request->all())->with(dangerMessage($e->getMessage()));
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = encrypt_decrypt($id, 'decrypt');
        $orderDetails = CustomerOrderDetails::where('id', $id)->where('del_status', "Live")->first();
        $customerOrder = CustomerOrder::find($orderDetails->customer_order_id);
        $title = __('index.customer_order_details');
        $orderInvoice = CustomerOrderInvoice::where('customer_order_id', $customerOrder->id)->where('del_status', "Live")->orderBy('id', 'desc')->first();
        $orderDeliveries = CustomerOrderDelivery::where('customer_order_id', $customerOrder->id)->where('del_status', "Live")->orderBy('id', 'desc')->get();
        $obj = $customerOrder;
        return view('pages.customer_order.view', compact('title', 'obj', 'orderDetails', 'orderInvoice', 'orderDeliveries'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $detail_id = encrypt_decrypt($id, 'decrypt');
        $orderDetails = CustomerOrderDetails::where('id', $detail_id)->where('del_status', "Live")->first();
        $customerOrder = CustomerOrder::find($orderDetails->customer_order_id);
        $title = __('index.edit_customer_order');
        $customers = Customer::orderBy('id', 'DESC')->where('del_status', "Live")->pluck('name', 'id');
        $orderTypes = ['Quotation' => 'Labor', 'Work Order' => 'Sales'];
        $units = Unit::orderBy('name', 'ASC')->where('del_status', "Live")->get();
        $rawMaterialList = RawMaterial::orderBy('name', 'ASC')->where('del_status', "Live")->where('category', '!=', 1)->get();
        $rmaterialcats = RawMaterialCategory::where('del_status', "Live")
            ->where('mat_type_id', 1)
            ->orderBy('name', 'ASC')
            ->get();
        $rawMaterialIds = RawMaterial::whereIn('category', $rmaterialcats->pluck('id'))
            ->where('del_status', 'Live')
            ->pluck('id');
        $productIds = FPrmitem::whereIn('rmaterials_id', $rawMaterialIds)
            ->pluck('finish_product_id')
            ->unique();
        $productList = FinishedProduct::orderBy('name', 'ASC')->where('del_status', "Live")->whereIn('id', $productIds)->get();
        $product = $productList->pluck('name', 'id');
        $orderInvoice = CustomerOrderInvoice::where('customer_order_id', $customerOrder->id)->where('del_status', "Live")->get();
        $tax_types = TaxItems::where('del_status', 'Live')->where('collect_tax', 'Yes')->get();
        $orderDeliveries = CustomerOrderDelivery::where('customer_order_id', $customerOrder->id)->where('del_status', "Live")->orderBy('id', 'desc')->get();
        return view('pages.customer_order.edit', compact('title', 'product', 'customerOrder', 'customers', 'orderTypes', 'units', 'productList', 'orderDetails', 'orderInvoice', 'orderDeliveries', 'tax_types', 'rawMaterialList'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerOrder $customerOrder)
    {
        // dd($request->all());
        request()->validate([
            'reference_no' => [
                'required',
                'max:50',
                /* Rule::unique('tbl_customer_orders', 'reference_no')->ignore($customerOrder->id, 'id')->where(function ($query) {
                    return $query->where('del_status', 'Live');
                }), */
            ],
            'customer_id' => 'required',
            'order_type' => 'required',
            'delivery_address' => 'required',
            'po_date' => 'required',
        ]);
        $productList = $request->get('product');
        $file = $request->get('file_old');
        if ($request->hasFile('file_button')) {
            $uploadedFile = $request->file('file_button');
            if (!empty($file)) {
                @unlink(base_path('uploads/order/' . $file));
            }
            $filename = time() . "_" . $uploadedFile->getClientOriginalName();
            $uploadedFile->move(base_path('uploads/order'), $filename);
            $customerOrder->file = $filename;
        } else {
            if (!empty($file)) {
                $customerOrder->file = $file;
            }
        }
        $customerOrder->reference_no = null_check(escape_output($request->get('reference_no')));
        $customerOrder->customer_id = null_check(escape_output($request->get('customer_id')));
        $customerOrder->po_type = escape_output($request->get('po_type_hidden'));
        $customerOrder->order_type = escape_output($request->get('order_type'));
        $customerOrder->po_date = date('Y-m-d', strtotime($request->get('po_date')));
        $customerOrder->delivery_address = escape_output($request->get('delivery_address'));
        $customerOrder->total_product = null_check(sizeof($productList));
        $customerOrder->total_amount = $customerOrder->total_amount + $request->get('total_subtotal');
        $customerOrder->quotation_note = html_entity_decode($request->get('quotation_note'));
        $customerOrder->internal_note = html_entity_decode($request->get('internal_note'));
        $customerOrder->save();
        $last_id = $customerOrder->id;
        $detail_id = $request->get('detail_id');
        // CustomerOrderDetails::where('customer_order_id', $last_id)->where('id',$detail_id)->update(['del_status' => "Deleted"]);
        // CustomerOrderInvoice::where('customer_order_id', $detail_id)->update(['del_status' => "Deleted"]);
        if (isset($_POST['product']) && is_array($_POST['product'])) {
            $interStateList = array_values($_POST['inter_state'] ?? []);
            foreach ($_POST['product'] as $row => $productId) {
                $existingDetail = CustomerOrderDetails::where('customer_order_id', $last_id)->where('product_id', $productId)->first();
                $newData = [
                    'raw_material_id' => null_check(escape_output($_POST['raw_material'][$row] ?? 0)),
                    'raw_qty'         => null_check(escape_output($_POST['raw_quantity'][$row] ?? 0)),
                    'quantity'        => null_check(escape_output($_POST['prod_quantity'][$row] ?? 0)),
                    'sale_price'      => null_check(escape_output($_POST['sale_price'][$row] ?? 0)),
                    'price'           => null_check(escape_output($_POST['price'][$row] ?? 0)),
                    'tax_amount'      => null_check(escape_output($_POST['tax_amount'][$row] ?? 0)),
                    'sub_total'       => null_check(escape_output($_POST['sub_total'][$row] ?? 0)),
                    'tax_type'        => escape_output($_POST['tax_type'][$row] ?? ''),
                    'inter_state'     => escape_output($interStateList[$row] ?? ''),
                    'cgst'            => escape_output($_POST['cgst'][$row] ?? ''),
                    'sgst'            => escape_output($_POST['sgst'][$row] ?? ''),
                    'igst'            => escape_output($_POST['igst'][$row] ?? ''),
                    'delivery_date'   => $_POST['delivery_date_product'][$row] != '' ? date('Y-m-d', strtotime(escape_output($_POST['delivery_date_product'][$row] ?? ''))) : null,
                    'line_item_no'    => escape_output($_POST['line_item_no'][$row] ?? '')
                ];
                if ($existingDetail) {
                    $changesDetected = false;
                    foreach ($newData as $key => $val) {
                        if ($existingDetail->$key != $val) {
                            $changesDetected = true;
                            break;
                        }
                    }
                    if ($request->get('po_type_hidden') == "same_line" && $changesDetected) {
                        $re_order = new CustomerPoReorder();
                        $re_order->customer_order_id = $last_id;
                        $re_order->customer_order_detail_id = $existingDetail->id;
                        $re_order->product_id = $existingDetail->product_id;
                        $re_order->mat_id = $existingDetail->raw_material_id;
                        $re_order->mat_qty = $existingDetail->raw_qty;
                        $re_order->prod_qty = $existingDetail->quantity;
                        $re_order->unit_price = $existingDetail->sale_price;
                        $re_order->price = $existingDetail->price;
                        $re_order->tax_type = $existingDetail->tax_type;
                        $re_order->inter_state = $existingDetail->inter_state;
                        $re_order->cgst = $existingDetail->cgst;
                        $re_order->sgst = $existingDetail->sgst;
                        $re_order->igst = $existingDetail->igst;
                        $re_order->tax_amount = $existingDetail->tax_amount;
                        $re_order->subtotal = $existingDetail->sub_total;
                        $re_order->save();
                        $customer_reorder_id = $re_order->id;
                        $reference_no = $customerOrder->reference_no;
                        $line_item_no = $existingDetail->line_item_no;

                        $materialStock = \App\MaterialStock::where('reference_no', $reference_no)
                            ->where('line_item_no', $line_item_no)
                            ->where('mat_id', $existingDetail->raw_material_id)
                            ->where('customer_id', $customerOrder->customer_id)
                            ->where('del_status', 'Live')
                            ->first();

                        if ($materialStock) {
                            $saleExists = DB::table('tbl_sale_details')
                                ->where('order_id', $existingDetail->id)
                                ->where('del_status', 'Live')
                                ->exists();
                            if (!$saleExists) {
                                $stock_qty = $newData['raw_qty'] ?? 0;
                                $log = new \App\StockAdjustLog();
                                $log->mat_stock_id = $materialStock->id;
                                $log->customer_reorder_id = $customer_reorder_id;
                                $log->type = 'addition';
                                $log->quantity = $stock_qty;
                                $log->stock_type = 'customer';
                                $log->reference_no = $reference_no;
                                $log->line_item_no = $line_item_no;
                                $log->dc_no = $materialStock->dc_no ?? '';
                                $log->mat_doc_no = $materialStock->mat_doc_no ?? '';
                                $log->heat_no = $materialStock->heat_no ?? '';
                                $log->dc_date = $materialStock->dc_date ?? null;
                                $log->dc_inward_price = $materialStock->dc_inward_price ?? 0;
                                $log->material_price = $materialStock->material_price ?? 0;
                                $log->hsn_no = $materialStock->hsn_no ?? '';
                                $log->added_by = auth()->user()->id;
                                $log->save();

                                $materialStock->current_stock = $materialStock->current_stock + $stock_qty;
                                $materialStock->save();
                            }
                        }
                    }
                    $existingDetail->update($newData);
                } else {
                    $lastEntry = \App\CustomerOrderDetails::orderBy('id', 'desc')->value('so_entry_no');
                    $nextNumber = $lastEntry ? (int)$lastEntry + 1 : 1;
                    $obj = new CustomerOrderDetails();
                    $obj->so_entry_no = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                    $obj->customer_order_id = $customerOrder->id;
                    $obj->product_id = null_check(escape_output($productId));
                    foreach ($newData as $key => $val) {
                        $obj->$key = $val;
                    }
                    $obj->discount_percent = 0;
                    $obj->order_status = '0';
                    $obj->production_status = 0;
                    $obj->delivered_qty = 0;
                    $obj->save();
                }
            }
            $currentDetailId = $existingDetail->id ?? $obj->id;
            $invoiceExists = CustomerOrderInvoice::where('customer_order_id', $currentDetailId)->exists();
            if (!$invoiceExists) {
                $total_amount = ($existingDetail->sub_total ?? $obj->sub_total)
                            + $request->get('total_subtotal');

                $inv_obj = new CustomerOrderInvoice();
                $inv_obj->customer_order_id = $currentDetailId;
                $inv_obj->invoice_type = 'Quotation';
                $inv_obj->amount = $total_amount;
                $inv_obj->invoice_date = date('Y-m-d', strtotime($request->get('po_date')));
                $inv_obj->paid_amount = 0.00;
                $inv_obj->due_amount = $total_amount;
                $inv_obj->save();
            }
        }
        return redirect('customer-orders')->with(updateMessage());
    }
    /**
     * Store/Update a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeUpdateInvoice(Request $request)
    {
        request()->validate([
            'invoice_type' => 'required|max:150',
            'paid_amount' => 'required',
            'order_due_amount' => 'required',
            'customer_order_id' => 'required',
        ]);

        $orderInvoice = new CustomerOrderInvoice;
        $orderInvoice->customer_order_id = $request->customer_order_id;
        $orderInvoice->invoice_type = $request->invoice_type;
        $orderInvoice->paid_amount = $request->paid_amount;
        $orderInvoice->due_amount = $request->due_amount;
        $orderInvoice->order_due_amount = $request->order_due_amount;
        $orderInvoice->invoice_date = date('Y-m-d');
        $orderInvoice->save();

        return redirect('customer-orders/' . $request->customer_order_id . '/edit')->with(updateMessage());
    }

    /**
     * Store/Update a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeUpdateDelivery(Request $request)
    {
        request()->validate([
            'product_id' => 'required',
            'quantity' => 'required',
            'delivery_date' => 'required',
            'delivery_note' => 'required',
            'delivery_status' => 'required',
            'customer_order_id' => 'required',
        ]);

        $orderDelivery = new CustomerOrderDelivery;
        $orderDelivery->customer_order_id = $request->customer_order_id;
        $orderDelivery->product_id = $request->product_id;
        $orderDelivery->quantity = null_check($request->quantity);
        $orderDelivery->delivery_date = string_date_null_check(escape_output($request->delivery_date));
        $orderDelivery->delivery_note = escape_output($request->delivery_note) ?? null;
        $orderDelivery->delivery_status = escape_output($request->delivery_status) ?? null;
        $orderDelivery->save();

        return redirect('customer-orders/' . $request->customer_order_id . '/edit')->with(updateMessage());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RawMaterialPurchase  $rawmaterialpurchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerOrderDetails $customerOrder)
    {
        /* CustomerOrderInvoice::where('customer_order_id', $customerOrder->id)->update(['del_status' => 'Deleted']);
        CustomerOrder::where('id',$customerOrder->customer_order_id)->update(['del_status' => 'Deleted']);
        CustomerOrderDelivery::where('customer_order_id', $customerOrder->customer_order_id)->update(['del_status' => 'Deleted']);
        $customerOrder->update(['del_status' => 'Deleted']);
        return redirect('customer-orders')->with(deleteMessage()); */
        return DB::transaction(function () use ($customerOrder) {
            $customerOrderId = $customerOrder->customer_order_id;
            $remainingProducts = CustomerOrderDetails::where('customer_order_id', $customerOrderId)
            ->where('del_status', '!=', 'Deleted')
            ->count();
            // dd($customerOrder->id);
            // $customerOrder = CustomerOrder::find($customerOrderId);
            if ($remainingProducts === 1) {
                CustomerOrder::where('id', $customerOrderId)->update(['del_status' => 'Deleted']);
                CustomerOrderInvoice::where('customer_order_id', $customerOrder->id)->update(['del_status' => 'Deleted']);
                CustomerPoReorder::where('customer_order_detail_id', $customerOrder->id)->update(['del_status' => 'Deleted']);
                $customerOrder->update(['del_status' => 'Deleted']);
            } else {
                CustomerOrderInvoice::where('customer_order_id', $customerOrder->id)->update(['del_status' => 'Deleted']);
                CustomerPoReorder::where('customer_order_detail_id', $customerOrder->id)->update(['del_status' => 'Deleted']);
                $customerOrder->update(['del_status' => 'Deleted']);
            }
            return redirect('customer-orders')->with(deleteMessage());
        });
    }

    /**
     * Download customer order invoice
     */

    public function downloadInvoice($id)
    {
        $detail_id = encrypt_decrypt($id, 'decrypt');
        $orderDetails = CustomerOrderDetails::where('id', $detail_id)->where('del_status', "Live")->first();
        $obj = CustomerOrder::find($orderDetails->customer_order_id);
        $orderInvoice = CustomerOrderInvoice::where('customer_order_id', $obj->id)->where('del_status', "Live")->orderBy('id', 'desc')->get();
        $orderDeliveries = CustomerOrderDelivery::where('customer_order_id', $obj->id)->where('del_status', "Live")->orderBy('id', 'desc')->get();
        $pdf = PDF::loadView('pages.customer_order.invoice', compact('obj', 'orderDetails', 'orderInvoice', 'orderDeliveries'))->setPaper('a4', 'landscape');
        return $pdf->download($obj->reference_no . '.pdf');
    }

    /**
     * Print
     */

    public function print($id)
    {
        $orderDetails = CustomerOrderDetails::where('id', $id)->where('del_status', "Live")->first();
        $obj = CustomerOrder::find($orderDetails->customer_order_id);
        $orderInvoice = CustomerOrderInvoice::where('customer_order_id', $obj->id)->where('del_status', "Live")->orderBy('id', 'desc')->get();
        $orderDeliveries = CustomerOrderDelivery::where('customer_order_id', $obj->id)->where('del_status', "Live")->orderBy('id', 'desc')->get();
        return view('pages.customer_order.invoice', compact('obj', 'orderDetails', 'orderInvoice', 'orderDeliveries'));
    }
    public function order_edit_logs($id){
        $id = encrypt_decrypt($id, 'decrypt');
        $order_edit_logs =  CustomerPoReorder::where('customer_order_detail_id',$id)->orderBy('id','desc')->get();
        // dd($order_edit_logs);
        $orderDetails = CustomerOrderDetails::where('id', $id)->where('del_status', "Live")->first();
        $customerOrder = CustomerOrder::find($orderDetails->customer_order_id);
        $title = __('index.customer_order_details');
        $obj = $customerOrder;
        return view('pages.customer_order.view_order_edit_logs', compact('title', 'obj', 'orderDetails','order_edit_logs'));
    }
    public function downloadEditLog($id){
        $id = encrypt_decrypt($id, 'decrypt');
        $order_edit_logs =  CustomerPoReorder::where('customer_order_detail_id',$id)->orderBy('id','desc')->get();
        $orderDetails = CustomerOrderDetails::where('id', $id)->where('del_status', "Live")->first();
        $customerOrder = CustomerOrder::find($orderDetails->customer_order_id);
        $title = __('index.customer_order_details');
        $obj = $customerOrder;
        $pdf = PDF::loadView('pages.customer_order.order_edit_logs_invoice', compact('title', 'obj', 'orderDetails','order_edit_logs'))->setPaper('a4', 'landscape');
        return $pdf->stream('order_edit_logs_'.$orderDetails->po_no . '.pdf');
    }
    public function printEditLog($id)
    {
       $id = encrypt_decrypt($id, 'decrypt');
        $order_edit_logs =  CustomerPoReorder::where('customer_order_detail_id',$id)->orderBy('id','desc')->get();
        $orderDetails = CustomerOrderDetails::where('id', $id)->where('del_status', "Live")->first();
        $customerOrder = CustomerOrder::find($orderDetails->customer_order_id);
        $title = __('index.customer_order_details');
        $obj = $customerOrder;
        return view('pages.customer_order.order_edit_logs_invoice', compact('title', 'obj', 'orderDetails','order_edit_logs'))->setPaper('a4', 'landscape');
    }
}
