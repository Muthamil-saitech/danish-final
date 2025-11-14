<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\InstrumentPaymentEntry;
use App\Partner;
use App\PartnerInstrumentPayment;
use App\PartnerIo;
use App\PartnerIoDetail;
use Illuminate\Http\Request;

class InstrumentPaymentController extends Controller
{
    public function index(Request $request)
    {
        $partner_io = PartnerIo::where('del_status',"Live");
        $obj = $partner_io->with('details')->orderBy('id', 'DESC')->get();
        $total_io = PartnerIoDetail::where('del_status', 'Live')->count();
        $title =  'Instruments Payment List';
        $partners = Partner::where('del_status','Live')->orderBy('id','DESC')->get();
        return view('pages.instruments_payment.index',compact('title','obj','partners','total_io'));
    }
    public function instrumentPayEntry(Request $request) {
        // dd($request->all());
        $io_detail_id = $request->io_detail_id;
        $total_amount = $request->total_amount;
        $balance_amount = $request->balance_amount;
        $pay_amount = $request->pay_amount;
        $payment_type = $request->payment_type;
        $note = $request->note;
        $partnerDetails = PartnerIoDetail::where('id',$io_detail_id)->where('del_status','Live')->first();
        $partnerIO = PartnerIo::where('id',$partnerDetails->partner_io_id)->where('del_status','Live')->first();
        $ins_pay = new InstrumentPaymentEntry();
        $ins_pay->io_detail_id = $io_detail_id;
        $ins_pay->reference_no = $partnerIO->reference_no.'/'.$partnerDetails->line_item_no;
        $ins_pay->io_date = date('Y-m-d', strtotime($partnerIO->created_at));
        $ins_pay->partner_id = $partnerIO->partner_id;
        $ins_pay->total_amount = $total_amount;
        $ins_pay->pay_amount = $pay_amount;
        $ins_pay->balance_amount = $balance_amount - $pay_amount;
        $ins_pay->payment_type = $payment_type;
        $ins_pay->note = $note;
        $proofName = '';
        if ($request->hasFile('payment_img')) {
            if ($request->hasFile('payment_img')) {
                $payment_img = $request->file('payment_img');
                $filename = $payment_img->getClientOriginalName();
                $proofName = time() . "_" . $filename;
                $payment_img->move(base_path() . '/uploads/instrument_payments/', $proofName);
            }
            $ins_pay->payment_proof = $proofName;
        }
        $ins_pay->user_id = auth()->user()->id;
        $ins_pay->save();
        $instrument_inv = PartnerInstrumentPayment::where('partner_io_detail_id',$io_detail_id)->where('del_status','Live')->first();
        $instrument_inv->paid_amount = $instrument_inv->paid_amount + $pay_amount;
        $instrument_inv->due_amount = $instrument_inv->amount - $instrument_inv->paid_amount;
        $instrument_inv->save();
        return redirect('instruments-payment')->with(saveMessage());
    }
    public function paymentEntry($id) {
        $title = 'Instrument Payment Invoice';
        $partnerDetails = PartnerIoDetail::find(encrypt_decrypt($id, 'decrypt'));
        $partnerIO = PartnerIo::find($partnerDetails->partner_io_id);
        $insInvoice = PartnerInstrumentPayment::where('partner_io_detail_id',$partnerDetails->id)->first();
        $obj = $partnerIO;
        $ins_pay_entries = InstrumentPaymentEntry::where('io_detail_id',$partnerDetails->id)->orderBy('id','DESC')->get();
        return view('pages.instruments_payment.invoice', compact('title', 'obj', 'ins_pay_entries','insInvoice','partnerDetails'));
    }
    public function print($id) {
        $title = 'Instrument Payment Invoice';
        $partnerDetails = PartnerIoDetail::find($id);
        $partnerIO = PartnerIo::find($partnerDetails->partner_io_id);
        $insInvoice = PartnerInstrumentPayment::where('partner_io_detail_id',$partnerDetails->id)->first();
        $obj = $partnerIO;
        $ins_pay_entries = InstrumentPaymentEntry::where('io_detail_id',$partnerDetails->id)->orderBy('id','DESC')->get();
        // dd($partnerDetails->id);
        return view('pages.instruments_payment.print_invoice', compact('title', 'obj', 'insInvoice', 'ins_pay_entries','partnerDetails'));
    }
}
