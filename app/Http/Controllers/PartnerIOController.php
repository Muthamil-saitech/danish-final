<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Partner;
use App\PartnerIO;
use App\PartnerIoDetail;
use App\InstrumentCategory;
use App\Instrument;
use App\Unit;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PartnerIOController extends Controller
{
    public function index() {
        $title = __('index.partner_io');
        $total_partner_ios = PartnerIoDetail::where('del_status', 'Live')->count();
        $partner_io = PartnerIO::where('del_status', "Live");
        $obj = $partner_io->with('details')->orderBy('id', 'DESC')->get();
        $partners = Partner::where('del_status', 'Live')->orderBy('id', 'DESC')->get();
        return view('pages.partner_io.index', compact('title','total_partner_ios','obj','partners'));
    }
    public function create() {
        $title = __('index.add_partner');
        $partners = Partner::where('del_status','Live')->get();
        $units = Unit::where('del_status', 'Live')->get();
        return view('pages.partner_io.addEditPartner', compact('title','partners','units'));
    }
    public function store(Request $request) {
        request()->validate([
            'reference_no' => [
                'required',
                Rule::unique('tbl_partner_ios', 'reference_no')->where(function ($query) {
                    return $query->where('del_status', 'Live');
                }),
            ],
            'del_challan_no' => 'required',
            'partner_id' => 'required',
            'io_date' => 'required',
            'phn_no' => 'required',
            'd_address' => 'required'
        ],[
            'reference_no.unique' => 'Reference No already exists',
            'del_challan_no.required' => 'The delivery challan number field is required'
        ]);
        
        $partner_io = new \App\PartnerIO();
        if ($request->hasFile('file_button')) {
            $uploadedFiles = $request->file('file_button');
            $storedFiles = [];
            $uploadPath = base_path('uploads/partner_io');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            foreach ($uploadedFiles as $file) {
                $filename = time() . "_" . $file->getClientOriginalName();
                $file->move(base_path('uploads/partner_io'), $filename);
                $storedFiles[] = $filename;
            }
            $partner_io->file = json_encode($storedFiles);
        }

        $partner_io->del_challan_no = null_check(escape_output($request->get('del_challan_no')));
        $partner_io->reference_no = null_check(escape_output($request->get('reference_no')));
        $partner_io->partner_id = null_check(escape_output($request->get('partner_id')));
        $partner_io->total_amount = null_check(escape_output($request->get('total_amount')));
        $partner_io->io_date =  date('Y-m-d', strtotime($request->get('io_date')));
        $partner_io->return_due_date =  date('Y-m-d', strtotime($request->get('return_due_date')));
        $partner_io->d_address = escape_output($request->get('d_address'));
        $partner_io->save();

        if(isset($_POST['type']) && is_array($_POST['type'])) {
            foreach($_POST['type'] as $row => $type){
                $obj = new \App\PartnerIoDetail();
                $obj->partner_io_id = $partner_io->id;
                $obj->type = null_check(escape_output($type));
                $obj->ins_category = null_check(escape_output($_POST['ins_category'][$row] ?? '0')); 
                $obj->ins_name = null_check(escape_output($_POST['ins_name'][$row] ?? '0')); 
                $obj->qty = null_check(escape_output($_POST['qty'][$row] ?? 0));
                // $obj->unit_id = null_check(escape_output($_POST['unit_id'][$row] ?? 0));
                $obj->rate = null_check(escape_output($_POST['rate'][$row] ?? 0));
                $obj->total = null_check(escape_output($_POST['total'][$row] ?? 0));
                $obj->tax_rate = null_check(escape_output($_POST['tax_rate'][$row] ?? 0));
                $obj->inter_state = null_check(escape_output($_POST['inter_state'][$row] ?? 'N'));
                $obj->cgst = null_check(escape_output($_POST['cgst'][$row] ?? 0));
                $obj->sgst = null_check(escape_output($_POST['sgst'][$row] ?? 0));
                $obj->igst = null_check(escape_output($_POST['igst'][$row] ?? 0));
                $obj->tax_amount = null_check(escape_output($_POST['tax_amount'][$row] ?? 0));
                $obj->subtotal = null_check(escape_output($_POST['subtotal'][$row] ?? 0));
                $obj->remarks = escape_output($_POST['remarks'][$row] ?? ''); 
                $obj->line_item_no = escape_output($_POST['line_item_no'][$row] ?? '');
                $obj->save();
                $inv_obj = new \App\PartnerInstrumentPayment();
                $inv_obj->partner_io_detail_id = null_check($obj->id);
                $inv_obj->amount = null_check(escape_output($_POST['subtotal'][$row] ?? 0));
                $inv_obj->paid_amount = 0.00;
                $inv_obj->due_amount = null_check(escape_output($_POST['subtotal'][$row] ?? 0));
                $inv_obj->payment_date = null_check(date('Y-m-d', strtotime($request->get('io_date'))));
                $inv_obj->save();
            }
        }
        return redirect('partner_io')->with(saveMessage());
    }
    public function edit($id){
        $detail_id = encrypt_decrypt($id, 'decrypt');
        $partnerOrderDetails = PartnerIoDetail::where('id', $detail_id)->where('del_status', "Live")->first();
        $partners = Partner::where('del_status','Live')->get();
        $partner_io = PartnerIO::find($partnerOrderDetails->partner_io_id);
        $partner_detail = Partner::where('id',$partner_io->partner_id)->where('del_status','Live')->first();
        $types = $partnerOrderDetails->pluck('type')->unique();
        $instrument_categories = InstrumentCategory::whereIn('type', $types)->orderBy('id','desc')->get();
        $categories = $partnerOrderDetails->pluck('ins_category')->unique();
        $instruments = Instrument::whereIn('type',$types)->whereIn('category',$categories)->orderBy('id','desc')->get();
        $units = Unit::where('del_status', 'Live')->get();
        $title = __('index.edit_partner_io');
        return view('pages.partner_io.addEditPartner', compact('title', 'partnerOrderDetails', 'partners', 'partner_io', 'partner_detail', 'instrument_categories', 'instruments','units'));
    }
    public function update(Request $request, PartnerIO $partner_io) {
        // dd($request->all());
        request()->validate([
            'reference_no' => [
                'required',
                Rule::unique('tbl_partner_ios', 'reference_no')->ignore($partner_io->id, 'id')->where(function ($query) {
                    return $query->where('del_status', 'Live');
                }),
            ],
            'del_challan_no' => 'required',
            'partner_id' => 'required',
            'io_date' => 'required',
            'return_due_date' => 'required',
            'phn_no' => 'required',
            'd_address' => 'required'
        ],[
            'reference_no.unique' => 'Reference No already exists',
            'del_challan_no.required' => 'The delivery challan number field is required'
        ]);
        
        if ($request->hasFile('file_button')) {
            $uploadedFiles = $request->file('file_button');
            $storedFiles = [];
            if (!empty($partner_io->file)) {
                $storedFiles = json_decode($partner_io->file, true);
            }
            $uploadPath = base_path('uploads/partner_io');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            foreach ($uploadedFiles as $file) {
                $filename = time() . "_" . $file->getClientOriginalName();
                $file->move(base_path('uploads/partner_io'), $filename);
                $storedFiles[] = $filename;
            }
            $partner_io->file = json_encode($storedFiles);
        }

        $partner_io->reference_no = null_check(escape_output($request->get('reference_no')));
        $partner_io->del_challan_no = null_check(escape_output($request->get('del_challan_no')));
        $partner_io->partner_id = null_check(escape_output($request->get('partner_id')));
        $partner_io->io_date =  date('Y-m-d', strtotime($request->get('io_date')));
        $partner_io->return_due_date =  date('Y-m-d', strtotime($request->get('return_due_date')));
        $partner_io->d_address = escape_output($request->get('d_address'));
        $partner_io->total_amount = null_check(escape_output($request->get('total_amount')));
        $partner_io->save();

        $last_id = $partner_io->id;
        $detail_id = $request->get('detail_id');
        $partner_detail = PartnerIoDetail::where('partner_io_id', $last_id)->where('id',$detail_id)->first();
        if($partner_detail) {
            $partner_detail->del_status = "Deleted";
            $partner_detail->save(); 
            if($partner_detail->status == 'Outward') {
                if(isset($_POST['type']) && is_array($_POST['type'])) {
                    foreach($_POST['type'] as $row => $type){
                        $obj = new \App\PartnerIoDetail();
                        $obj->partner_io_id = $partner_io->id;
                        $obj->type = null_check(escape_output($type));
                        $obj->ins_category = null_check(escape_output($_POST['ins_category'][$row] ?? '0')); 
                        $obj->ins_name = null_check(escape_output($_POST['ins_name'][$row] ?? '0'));
                        $obj->qty = null_check(escape_output($_POST['qty'][$row] ?? 0));
                        // $obj->unit_id = null_check(escape_output($_POST['unit_id'][$row] ?? 0));
                        $obj->rate = null_check(escape_output($_POST['rate'][$row] ?? 0));
                        $obj->total = null_check(escape_output($_POST['total'][$row] ?? 0));
                        $obj->tax_rate = null_check(escape_output($_POST['tax_rate'][$row] ?? 0));
                        $obj->inter_state = null_check(escape_output($_POST['inter_state'][$row] ?? 0));
                        $obj->cgst = null_check(escape_output($_POST['cgst'][$row] ?? 0));
                        $obj->sgst = null_check(escape_output($_POST['sgst'][$row] ?? 0));
                        $obj->igst = null_check(escape_output($_POST['igst'][$row] ?? 0));
                        $obj->tax_amount = null_check(escape_output($_POST['tax_amount'][$row] ?? 0));
                        $obj->subtotal = null_check(escape_output($_POST['subtotal'][$row] ?? 0));
                        $obj->remarks = escape_output($_POST['remarks'][$row] ?? ''); 
                        $obj->line_item_no = escape_output($_POST['line_item_no'][$row] ?? ''); 
                        $obj->status = 'Outward';
                        $obj->inward_date = $partner_detail->inward_date;
                        $obj->save();
                    }
                }
            } else {
                if(isset($_POST['type']) && is_array($_POST['type'])) {
                    foreach($_POST['type'] as $row => $type){
                        $obj = new \App\PartnerIoDetail();
                        $obj->partner_io_id = $partner_io->id;
                        $obj->type = null_check(escape_output($type));
                        $obj->ins_category = null_check(escape_output($_POST['ins_category'][$row] ?? '0')); 
                        $obj->ins_name = null_check(escape_output($_POST['ins_name'][$row] ?? '0')); 
                        $obj->qty = null_check(escape_output($_POST['qty'][$row] ?? ''));
                        // $obj->unit_id = null_check(escape_output($_POST['unit_id'][$row] ?? 0));
                        $obj->rate = null_check(escape_output($_POST['rate'][$row] ?? 0));
                        $obj->total = null_check(escape_output($_POST['total'][$row] ?? 0));
                        $obj->tax_rate = null_check(escape_output($_POST['tax_rate'][$row] ?? 0));
                        $obj->inter_state = null_check(escape_output($_POST['inter_state'][$row] ?? 'N'));
                        $obj->cgst = null_check(escape_output($_POST['cgst'][$row] ?? 0));
                        $obj->sgst = null_check(escape_output($_POST['sgst'][$row] ?? 0));
                        $obj->igst = null_check(escape_output($_POST['igst'][$row] ?? 0));
                        $obj->tax_amount = null_check(escape_output($_POST['tax_amount'][$row] ?? 0));
                        $obj->subtotal = null_check(escape_output($_POST['subtotal'][$row] ?? 0));
                        $obj->remarks = escape_output($_POST['remarks'][$row] ?? ''); 
                        $obj->line_item_no = escape_output($_POST['line_item_no'][$row] ?? '');
                        $obj->save();
                    }
                }
            }
        }
        return redirect('partner_io')->with(saveMessage());
    }
    public function inward_to_outward(Request $request){
        $prefix = $request->outward_type === 'RGP' ? 'RGP' : 'NRGP';
        $latestChallan = DB::table('tbl_partner_io_details')
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
        $partner_io_id = $request->get('partner_io_id');
        $partner_detail_io = PartnerIoDetail::where('id',$partner_io_id)->first();
        $partner_detail_io->inward_date = date('Y-m-d',strtotime($request->get('inward_date')));
        $partner_detail_io->notes = $request->notes ?? null;
        $partner_detail_io->outward_type = $request->outward_type ?? null;
        $partner_detail_io->outward_challan_no = $outwardChallanNo ?? null;
        $partner_detail_io->status = 'Outward';
        $partner_detail_io->save();
        return response()->json(['success' => true]);
    }
    /* public function show($id) {
        $id = encrypt_decrypt($id, 'decrypt');
        $title = __('index.view_partner_io');
        $partner_io_detail = PartnerIoDetail::where('id',$id)->where('del_status','Live')->first();
        $partner_io = PartnerIO::find($partner_io_detail->partner_io_id)->first();
        return view('pages.partner_io.view', compact('title','partner_io_detail','partner_io'));
    } */
   public function inwardIO($id) {
        $id = encrypt_decrypt($id, 'decrypt');
        $partner_io_detail = PartnerIoDetail::where('id',$id)->where('del_status','Live')->first();
        $partner_io = PartnerIO::find($partner_io_detail->partner_io_id)->first();
        $title = "View Partner Inward I/O";
        $status = "Inward";
        return view('pages.partner_io.view', compact('title','partner_io_detail','partner_io','status'));
    }
    public function outwardIO($id) {
        $id = encrypt_decrypt($id, 'decrypt');
        $partner_io_detail = PartnerIoDetail::where('id',$id)->where('del_status','Live')->first();
        $partner_io = PartnerIO::find($partner_io_detail->partner_io_id)->first();
        $title = "View Partner Outward I/O";
        $status = "Outward";
        return view('pages.partner_io.view', compact('title','partner_io_detail','partner_io','status'));
    }
    public function destroy($id)
    {
        $partner_io_detail = PartnerIoDetail::findOrFail($id);
        // dd($partner_io_detail->partner_io_id);
        return DB::transaction(function () use ($partner_io_detail) {
            $remainingDetails = PartnerIoDetail::where('partner_io_id', $partner_io_detail->partner_io_id)
                ->where('del_status', '!=', 'Deleted')
                ->count();
            if ($remainingDetails === 1) {
                PartnerIO::where('id', $partner_io_detail->partner_io_id)->update(['del_status' => 'Deleted']);
            }
            $partner_io_detail->update(['del_status' => 'Deleted']);
            return redirect('partner_io')->with(deleteMessage());
        });
    }
    public function downloadInward($id)
    {
        $detail_id = encrypt_decrypt($id, 'decrypt');
        $partner_io_detail = PartnerIoDetail::where('id',$detail_id)->where('del_status', "Live")->first();
        $partner_io = PartnerIO::where('id',$partner_io_detail->partner_io_id)->first();
        $status = "Inward";
        $pdf = PDF::loadView('pages.partner_io.print_partner_io', compact('partner_io_detail', 'partner_io','status'))->setPaper('a4', 'landscape');
        return $pdf->download($partner_io->reference_no . '.pdf');
    }
    public function downloadOutward($id)
    {
        $detail_id = encrypt_decrypt($id, 'decrypt');
        $partner_io_detail = PartnerIoDetail::where('id',$detail_id)->where('del_status', "Live")->first();
        $partner_io = PartnerIO::where('id',$partner_io_detail->partner_io_id)->first();
        $status = "Outward";
        $pdf = PDF::loadView('pages.partner_io.print_partner_io', compact('partner_io_detail', 'partner_io','status'))->setPaper('a4', 'landscape');
        return $pdf->download($partner_io->reference_no . '.pdf');
    }
    public function printInward($id)
    {
        $partner_io_detail = PartnerIoDetail::where('id',$id)->where('del_status', "Live")->first();
        $partner_io = PartnerIO::where('id',$partner_io_detail->partner_io_id)->first();
        $status = "Inward";
        return view('pages.partner_io.print_partner_io', compact('partner_io','partner_io_detail','status'));
    }
    public function printOutward($id)
    {
        $partner_io_detail = PartnerIoDetail::where('id',$id)->where('del_status', "Live")->first();
        $partner_io = PartnerIO::where('id',$partner_io_detail->partner_io_id)->first();
        $status = "Outward";
        // dd($partner_io);
        return view('pages.partner_io.print_partner_io', compact('partner_io','partner_io_detail','status'));
    }
}
