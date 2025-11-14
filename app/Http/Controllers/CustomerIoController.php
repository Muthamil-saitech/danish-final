<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CustomerOrder;
use App\Customer;
use App\CustomerIO;
use App\CustomerIoDetail;
use App\InstrumentCategory;
use App\Instrument;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class CustomerIoController extends Controller
{
    public function index(Request $request) {
        $title = __('index.customer_io');
        $total_customer_ios = CustomerIO::where('del_status', 'Live')->count();
        $startDate = '';
        $endDate = '';
        $customer_id = escape_output($request->get('customer_id'));
        unset($request->_token);
        $customer_io = CustomerIO::where('del_status', "Live");
        if (isset($request->startDate) && $request->startDate != '') {
            $startDate = date('Y-m-d 00:00:00', strtotime($request->startDate));
            $customer_io->where('created_at', '>=', $startDate);
        }
        if (isset($request->endDate) && $request->endDate != '') {
            $endDate = date('Y-m-d 23:59:59', strtotime($request->endDate));
            $customer_io->where('created_at', '<=', $endDate);
        }
        if (isset($customer_id) && $customer_id != '') {
            $customer_io->where('customer_id', $customer_id);
        }
        $obj = $customer_io->with('details')->orderBy('id', 'DESC')->get();
        foreach ($obj as $value) {
            $order = DB::table('tbl_customer_orders')
                ->where('reference_no', $value->po_no)
                ->first();
            $order_detail = DB::table('tbl_customer_order_details')
                ->where('customer_order_id', $order ? $order->id : 0)
                ->where('line_item_no', $value->line_item_no)
                ->first();
            $value->order_id = $order_detail ? $order_detail->id : null;
        }
        $customers = Customer::where('del_status', 'Live')->orderBy('id', 'DESC')->get();
        return view('pages.customer_io.index', compact('title','obj', 'customers', 'startDate', 'endDate', 'customer_id','total_customer_ios'));
    }
    public function create() {
        $title = __('index.add_customer');
        $customer_orders = CustomerOrder::join('tbl_customer_order_details as cod','cod.customer_order_id','=','tbl_customer_orders.id')
        ->where('tbl_customer_orders.del_status', 'Live')
        ->where('cod.del_status', 'Live')
        ->where('cod.order_status', 1)
        ->select('tbl_customer_orders.*','cod.line_item_no', 'cod.id as codid', 'cod.product_id')
        ->orderBy('tbl_customer_orders.id', 'DESC')
        ->whereNotExists(function($q) {
            $q->selectRaw('1')
            ->from('tbl_customer_ios as cio')
            ->where('cio.del_status','Live')
            ->whereColumn('cio.po_no', 'tbl_customer_orders.reference_no') 
            ->whereColumn('cio.line_item_no', 'cod.line_item_no');               
        })->get();
        return view('pages.customer_io.addEditCustomerIO', compact('title','customer_orders'));
    }
    public function store(Request $request){
        // dd($request->all());
        request()->validate([
            'po_no' => 'required|max:50',
            'del_challan_no' => 'required',
            'date' => 'required',
            'phn_no' => 'required',
            'd_address' => 'required'
        ]);

        $customer_io = new \App\CustomerIO();
        if ($request->hasFile('file_button')) {
            $uploadedFiles = $request->file('file_button');
            $storedFiles = [];
            $uploadPath = base_path('uploads/customer_io');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            foreach ($uploadedFiles as $file) {
                $filename = time() . "_" . $file->getClientOriginalName();
                $file->move(base_path('uploads/customer_io'), $filename);
                $storedFiles[] = $filename;
            }
            $customer_io->file = json_encode($storedFiles);
        }
        $customer_io->del_challan_no = null_check(escape_output($request->get('del_challan_no')));
        $customer_io->po_no = null_check(escape_output($request->get('po_no')));
        $customer_io->line_item_no = null_check(escape_output($request->get('line_item_no')));
        $customer_io->customer_id = null_check(escape_output($request->get('customer_id')));
        $customer_io->date =  date('Y-m-d', strtotime($request->get('date')));
        $customer_io->return_due_date =  date('Y-m-d', strtotime($request->get('return_due_date')));
        $customer_io->total_amount = null_check(escape_output($request->get('total_amount')));
        $customer_io->d_address = escape_output($request->get('d_address'));
        $customer_io->save();

        if(isset($_POST['type']) && is_array($_POST['type'])) {
            foreach($_POST['type'] as $row => $type){
                $obj = new \App\CustomerIoDetail();
                $obj->customer_io_id = $customer_io->id;
                $obj->type = null_check(escape_output($type));
                $obj->ins_category = null_check(escape_output($_POST['ins_category'][$row] ?? '0')); 
                $obj->ins_name = null_check(escape_output($_POST['ins_name'][$row] ?? '0')); 
                $obj->qty = null_check(escape_output($_POST['qty'][$row] ?? ''));
                $obj->rate = null_check(escape_output($_POST['rate'][$row] ?? 0));
                $obj->inter_state = null_check(escape_output($_POST['inter_state'][$row] ?? 'N'));
                $obj->cgst = null_check(escape_output($_POST['cgst'][$row] ?? 0));
                $obj->sgst = null_check(escape_output($_POST['sgst'][$row] ?? 0));
                $obj->igst = null_check(escape_output($_POST['igst'][$row] ?? 0));
                $obj->total = null_check(escape_output($_POST['total'][$row] ?? 0));
                $obj->tax_rate = null_check(escape_output($_POST['tax_rate'][$row] ?? 0));
                $obj->tax_amount = null_check(escape_output($_POST['tax_amount'][$row] ?? 0));
                $obj->subtotal = null_check(escape_output($_POST['subtotal'][$row] ?? 0));
                $obj->remarks = escape_output($_POST['remarks'][$row] ?? ''); 
                $obj->save();
            }
        }
        return redirect('customer_io')->with(saveMessage());
    }
    public function edit($id){
        $order_id = encrypt_decrypt($id, 'decrypt');
        $customer_orders = CustomerOrder::join('tbl_customer_order_details as cod','cod.customer_order_id','=','tbl_customer_orders.id')->where('tbl_customer_orders.del_status', 'Live')->where('cod.del_status', 'Live') ->where('cod.order_status', 1)->select('tbl_customer_orders.*','cod.line_item_no', 'cod.id as codid', 'cod.product_id') ->orderBy('tbl_customer_orders.id', 'DESC')->get();
        $order_io = CustomerIO::where('id', $order_id)->where('del_status', "Live")->where('del_status','Live')->first();
        $customer = Customer::where('id',$order_io->customer_id)->where('del_status',"Live")->first();
        $customer_io_details = CustomerIoDetail::where('customer_io_id',$order_io->id)->where('del_status','Live')->get();
        $types = $customer_io_details->pluck('type')->unique();
        $instrument_categories = InstrumentCategory::whereIn('type', $types)->orderBy('id','desc')->get();
        $categories = $customer_io_details->pluck('ins_category')->unique();
        $instruments = Instrument::whereIn('type',$types)->whereIn('category',$categories)->orderBy('id','desc')->get();
        $title = __('index.edit_customer_order_io');
        return view('pages.customer_io.addEditCustomerIO', compact('title', 'customer_orders', 'order_io', 'customer', 'customer_io_details', 'instrument_categories', 'instruments'));
    }
    public function update(Request $request, CustomerIO $customer_io) {
        // dd($request->all());
        request()->validate([
            'del_challan_no' => 'required',
            'po_no' => 'nullable',
            'date' => 'required',
            'return_due_date' => 'required',
            'phn_no' => 'required',
            'd_address' => 'required'
        ]);
        if ($request->hasFile('file_button')) {
            $uploadedFiles = $request->file('file_button');
            $storedFiles = [];
            if (!empty($customer_io->file)) {
                $storedFiles = json_decode($customer_io->file, true);
            }
            $uploadPath = base_path('uploads/customer_io');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            foreach ($uploadedFiles as $file) {
                $filename = time() . "_" . $file->getClientOriginalName();
                $file->move(base_path('uploads/customer_io'), $filename);
                $storedFiles[] = $filename;
            }
            $customer_io->file = json_encode($storedFiles);
        }
        $customer_io->del_challan_no = null_check(escape_output($request->get('del_challan_no')));
        $customer_io->po_no = null_check(escape_output($request->get('po_no')));
        $customer_io->line_item_no = null_check(escape_output($request->get('line_item_no')));
        $customer_io->customer_id = null_check(escape_output($request->get('customer_id')));
        $customer_io->date =  date('Y-m-d', strtotime($request->get('date')));
        $customer_io->return_due_date =  date('Y-m-d', strtotime($request->get('return_due_date')));
        $customer_io->total_amount = null_check(escape_output($request->get('total_amount')));
        $customer_io->d_address = escape_output($request->get('d_address'));
        $customer_io->save();
        $last_id = $customer_io->id;
        $detail_id = $request->get('detail_id');
        CustomerIoDetail::where('customer_io_id', $last_id)->whereIn('id',$detail_id)->update(['del_status' => "Deleted"]);
        if(isset($_POST['type']) && is_array($_POST['type'])) {
            foreach($_POST['type'] as $row => $type){
                $obj = new \App\CustomerIoDetail();
                $obj->customer_io_id = $customer_io->id;
                $obj->type = null_check(escape_output($type));
                $obj->ins_category = null_check(escape_output($_POST['ins_category'][$row] ?? '0')); 
                $obj->ins_name = null_check(escape_output($_POST['ins_name'][$row] ?? '0')); 
                $obj->qty = null_check(escape_output($_POST['qty'][$row] ?? ''));
                $obj->rate = null_check(escape_output($_POST['rate'][$row] ?? 0));
                $obj->inter_state = null_check(escape_output($_POST['inter_state'][$row] ?? 'N'));
                $obj->cgst = null_check(escape_output($_POST['cgst'][$row] ?? 0));
                $obj->sgst = null_check(escape_output($_POST['sgst'][$row] ?? 0));
                $obj->igst = null_check(escape_output($_POST['igst'][$row] ?? 0));
                $obj->total = null_check(escape_output($_POST['total'][$row] ?? 0));
                $obj->tax_rate = null_check(escape_output($_POST['tax_rate'][$row] ?? 0));
                $obj->tax_amount = null_check(escape_output($_POST['tax_amount'][$row] ?? 0));
                $obj->subtotal = null_check(escape_output($_POST['subtotal'][$row] ?? 0));
                $obj->remarks = escape_output($_POST['remarks'][$row] ?? ''); 
                $obj->save();
            }
        }
        return redirect('customer_io')->with(updateMessage());
    }
    public function inward_to_outward(Request $request){
        // dd($request->all());
        $prefix = $request->outward_type === 'RGP' ? 'RGP' : 'NRGP';
        $latestChallan = DB::table('tbl_customer_ios')
            ->where('outward_type', $request->outward_type)
            ->orderBy('id', 'desc')
            ->value('outward_challan_no');
        if ($latestChallan) {
            $lastNumber = (int) str_replace($prefix, '', $latestChallan);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        $outwardChallanNo = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        $customer_io_id = $request->get('customer_io_id');
        $customer_io = CustomerIO::where('id',$customer_io_id)->first();
        $customer_io->inward_date = date('Y-m-d',strtotime($request->get('inward_date')));
        $customer_io->notes = $request->notes ?? null;
        $customer_io->outward_type = $request->outward_type ?? null;
        $customer_io->outward_challan_no = $outwardChallanNo ?? null;
        $customer_io->status = 'Outward';
        $customer_io->save();
        return response()->json(['success' => true]);
    }
    /* public function show($id) {
        $id = encrypt_decrypt($id, 'decrypt');
        $customer_io = CustomerIO::where('id',$id)->where('del_status', "Live")->first();
        $customer_io_details = CustomerIoDetail::where('customer_io_id',$customer_io->id)->where('del_status', "Live")->get();
        $title = __('index.view_customer_io');
        return view('pages.customer_io.view', compact('title','customer_io','customer_io_details'));
    } */
    public function inwardIO($id) {
        $id = encrypt_decrypt($id, 'decrypt');
        $customer_io = CustomerIO::where('id',$id)->where('del_status', "Live")->first();
        $customer_io_details = CustomerIoDetail::where('customer_io_id',$customer_io->id)->where('del_status', "Live")->get();
        $title = "View Customer Inward I/O";
        $status = 'Inward';
        return view('pages.customer_io.view', compact('title','customer_io','customer_io_details','status'));
    }
    public function outwardIO($id) {
        $id = encrypt_decrypt($id, 'decrypt');
        $customer_io = CustomerIO::where('id',$id)->where('del_status', "Live")->first();
        $customer_io_details = CustomerIoDetail::where('customer_io_id',$customer_io->id)->where('del_status', "Live")->get();
        $title = "View Customer Outward I/O";
        $status = 'Outward';
        return view('pages.customer_io.view', compact('title','customer_io','customer_io_details','status'));
    }
    public function destroy(CustomerIO $customer_io) {
        CustomerIoDetail::where('customer_io_id', $customer_io->id)->update(['del_status' => 'Deleted']);
        $customer_io->del_status = 'Deleted';
        $customer_io->save();
        return redirect('customer_io')->with(deleteMessage());
    }
    public function downloadInwardIo($id)
    {
        $detail_id = encrypt_decrypt($id, 'decrypt');
        $customer_io = CustomerIO::where('id',$detail_id)->where('del_status', "Live")->first();
        $customer_io_details = CustomerIoDetail::where('customer_io_id',$customer_io->id)->where('del_status', "Live")->get();
        $status = "Inward";
        $pdf = PDF::loadView('pages.customer_io.print_customer_io', compact('customer_io', 'customer_io_details','status'))->setPaper('a4', 'landscape');
        return $pdf->download($customer_io->po_no . '.pdf');
    }
    public function downloadOutwardIo($id)
    {
        $detail_id = encrypt_decrypt($id, 'decrypt');
        $customer_io = CustomerIO::where('id',$detail_id)->where('del_status', "Live")->first();
        $customer_io_details = CustomerIoDetail::where('customer_io_id',$customer_io->id)->where('del_status', "Live")->get();
        $status = "Outward";
        $pdf = PDF::loadView('pages.customer_io.print_customer_io', compact('customer_io', 'customer_io_details','status'))->setPaper('a4', 'landscape');
        return $pdf->download($customer_io->po_no . '.pdf');
    }
    public function printInward($id)
    {
        $customer_io = CustomerIO::where('id',$id)->where('del_status', "Live")->first();
        $customer_io_details = CustomerIoDetail::where('customer_io_id',$customer_io->id)->where('del_status', "Live")->get();
        $status = "Inward";
        return view('pages.customer_io.print_customer_io', compact('customer_io','customer_io_details','status'));
    }
    public function printOutward($id)
    {
        $customer_io = CustomerIO::where('id',$id)->where('del_status', "Live")->first();
        $customer_io_details = CustomerIoDetail::where('customer_io_id',$customer_io->id)->where('del_status', "Live")->get();
        $status = "Outward";
        return view('pages.customer_io.print_customer_io', compact('customer_io','customer_io_details','status'));
    }
}
