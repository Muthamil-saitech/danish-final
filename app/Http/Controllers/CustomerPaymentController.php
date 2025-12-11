<?php
/*
  ##############################################################################
  # iProduction - Production and Manufacture Management Software
  ##############################################################################
  # AUTHOR:		Door Soft
  ##############################################################################
  # EMAIL:		info@doorsoft.co
  ##############################################################################
  # COPYRIGHT:		RESERVED BY Door Soft
  ##############################################################################
  # WEBSITE:		https://www.doorsoft.co
  ##############################################################################
  # This is CustomerPaymentController Controller
  ##############################################################################
 */

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerDueReceive;
use App\SaleNoteEntry;
use App\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CustomerPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $startDate = '';
        $endDate = '';
        $customer_id = escape_output($request->get('customer_id'));
        unset($request->_token);
        $salesQuery = DB::table('tbl_sales as s')
            ->leftJoin('tbl_quotations as q', 'q.id', '=', 's.challan_id')
            ->leftJoin('tbl_customer_due_receives as cp', 'cp.sale_id', '=', 's.id')
            ->leftJoin('tbl_sale_note_entries as sn', 'sn.sale_id', '=', 's.id')
            ->where('s.del_status', 'Live');
            // ->leftJoin('tbl_sale_details as sd', 's.id', '=', 'sd.sale_id')
            // ->where('sd.del_status', 'Live');
        if (!empty($customer_id)) {
            $salesQuery->where('s.customer_id', $customer_id);
        }
        if (isset($request->startDate) && $request->startDate != '') {
            $startDate = $request->startDate;
            $salesQuery->where('sale_date', '>=', date('Y-m-d', strtotime($request->startDate)));
        }
        if (isset($request->endDate) && $request->endDate != '') {
            $endDate = $request->endDate;
            $salesQuery->where('sale_date', '<=', date('Y-m-d', strtotime($request->endDate)));
        }
        $obj = $salesQuery
            ->select('s.*', 'q.challan_no', 'sn.invoice_no as sale_entry_inv_no','sn.type','sn.price','sn.grand_total as note_grand_total')
            ->orderBy('s.id', 'DESC')
            ->get()
            ->unique();
        // dd($obj);
        $title = __('index.customer_due_receives');
        $customers = Customer::where('del_status', 'Live')->orderBy('id', 'DESC')->get();
        return view('pages.customer_payment.index', compact('title', 'obj', 'customers', 'startDate', 'endDate', 'customer_id'));
    }
    public function customerDueEntry(Request $request) {
        // dd($request->all());
        $sale_id = $request->sale_id;
        $total_amount = $request->total_amount;
        $balance_amount = $request->balance_amount;
        $pay_amount = $request->pay_amount;
        $tds_amount = $request->tds_amount ?? 0.00;
        $payment_type = $request->payment_type;
        $note = $request->note;
        $sale = Sales::find($sale_id);
        $customer_due = new CustomerDueReceive();
        $customer_due->sale_id = $sale_id;
        $customer_due->customer_id = $sale->customer_id;
        $customer_due->total_amount = $total_amount;
        $customer_due->pay_amount = $pay_amount;
        $customer_due->balance_amount = $balance_amount - $pay_amount;
        $customer_due->payment_type = $payment_type;
        $customer_due->note = $note;
        $proofName = '';
        if ($request->hasFile('payment_img')) {
            if ($request->hasFile('payment_img')) {
                $payment_img = $request->file('payment_img');
                $filename = $payment_img->getClientOriginalName();
                $proofName = time() . "_" . $filename;
                $payment_img->move(base_path() . '/uploads/customer_due/', $proofName);
            }
            $customer_due->payment_proof = $proofName;
        }
        $customer_due->user_id = auth()->user()->id;
        $customer_due->save();
        $sale->paid = $sale->paid + $pay_amount;
        // $sale->due = (float) $sale->grand_total - (float) $sale->paid - (float) $tds_amount;
        $sale->due = $balance_amount - $pay_amount;
        $sale->tds_amount = $tds_amount === "" ? 0 : $tds_amount;
        $sale->save();
        return redirect('customer-payment')->with(saveMessage());
    }
    public function dueEntry($id) {
        $sale = Sales::find(encrypt_decrypt($id, 'decrypt'));
        $title = __('index.customer_payment_invoice');
        $obj = $sale;
        $customer_due_entries = CustomerDueReceive::where('sale_id',$sale->id)->get();
        $sale_note_entry = SaleNoteEntry::where('sale_id',$sale->id)->first();
        return view('pages.customer_payment.invoice', compact('title', 'obj', 'customer_due_entries', 'sale_note_entry'));
    }
}
