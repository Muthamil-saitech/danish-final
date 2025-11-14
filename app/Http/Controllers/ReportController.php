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
# This is ReportController
##############################################################################
 */

namespace App\Http\Controllers;

use App\Account;
use App\Attendance;
use App\Customer;
use App\CustomerDueReceive;
use App\CustomerIO;
use App\CustomerOrder;
use App\CustomerOrderDetails;
use App\Expense;
use App\FinishedProduct;
use App\FPrmitem;
use App\Manufacture;
use App\Pnonitem;
use App\ProductWaste;
use App\RawMaterial;
use App\RawMaterialCategory;
use App\RawMaterialPurchase;
use App\RMWaste;
use App\Salary;
use App\Sales;
use App\Stock;
use App\Partner;
use App\Supplier;
use App\Supplier_payment;
use App\User;
use App\Drawer;
use App\Exports\POReportExport;
use App\MaterialStock;
use App\ProductionStage;
use App\PartnerIo;
use App\PartnerIoDetail;
use App\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
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
    public function rmPurchaseReport(Request $request)
    {
        $startDate = '';
        $endDate = '';

        unset($request->_token);

        $rmPurchase = RawMaterialPurchase::orderBy('id', 'DESC')->where('status', 'Final')->where('del_status', "Live");
        if (isset($request->startDate)) {
            $startDate = $request->startDate;
            $rmPurchase->whereDate('created_at', '>=', $request->startDate);
        }
        if (isset($request->endDate)) {
            $endDate = $request->endDate;
            $rmPurchase->whereDate('created_at', '<=', $request->endDate);
        }

        $obj = $rmPurchase->get();

        $title = __('index.rm_purchase_report');

        return view('pages.report.rm_purchase_report', compact('title', 'obj', 'startDate', 'endDate'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rmItemPurchaseReport(Request $request)
    {
        $startDate = '';
        $endDate = '';

        unset($request->_token);

        $rmPurchase = RawMaterialPurchase::orderBy('id', 'DESC')->where('status', 'Final')->where('del_status', "Live");
        if (isset($request->startDate)) {
            $startDate = $request->startDate;
            $rmPurchase->whereDate('created_at', '>=', $request->startDate);
        }
        if (isset($request->endDate)) {
            $endDate = $request->endDate;
            $rmPurchase->whereDate('created_at', '<=', $request->endDate);
        }

        $obj = $rmPurchase->get();

        $title = __('index.rm_item_purchase_report');

        return view('pages.report.rm_item_purchase_report', compact('title', 'obj', 'startDate', 'endDate'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rmStockReport(Request $request)
    {
        unset($request->_token);

        $category_id = escape_output($request->get('category_id'));
        $product_id = escape_output($request->get('finish_p_id'));
        $obj1 = new Stock();
        $obj = $obj1->getRMStock($category_id);
        if ($product_id != '') {
            $rm = new FPrmitem();
            $rmObj = $rm->getFinishProductRM($product_id);
            $rm_id = $rmObj[0]->rmaterials_id;
            $obj = array_filter($obj, function ($v) use ($rm_id) {
                return $v->id == $rm_id;
            });
        }
        $rmCategory = RawMaterialCategory::orderBy('id', 'DESC')->where('del_status', "Live")->get();
        $finishProduct = FinishedProduct::orderBy('id', 'DESC')->where('del_status', "Live")->get();

        $title = __('index.rm_stock_report');

        return view('pages.report.rm_stock_report', compact('title', 'obj', 'rmCategory', 'finishProduct', 'category_id'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function supplierDueReport(Request $request)
    {
        $type = '';
        if (isset($request->type)) {
            $type = $request->type;
        }

        $supplierDueReport = Supplier::where('del_status', 'Live')->get();

        $title = __('index.supplier_due_report');

        return view('pages.report.supplier_due_report', compact('title', 'type', 'supplierDueReport'));
    }

    /**
     * Supplier Balance Report
     */
    public function supplierBalanceReport(Request $request)
    {
        $type = '';
        if (isset($request->type)) {
            $type = $request->type;
        }

        $supplierDueReport = Supplier::where('del_status', 'Live')->get();

        $title = __('index.supplier_balance_report');

        return view('pages.report.supplier_balance_report', compact('title', 'type', 'supplierDueReport'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function supplierLedger(Request $request)
    {
        $startDate = '';
        $endDate = '';
        $selectString = '';
        $supplier_id = '';
        $type = '';
        $supplierLedger = [];

        if (isset($request->supplier_id)) {
            unset($request->_token);
            $supplier_id = $request->supplier_id;

            $startDate = $request->startDate ?? null;
            $endDate = $request->endDate ?? null;
            $type = $request->type ?? null;

            $s_type = getSupplierOpeningBalanceType($supplier_id);

            if ($s_type == 'Debit') {
                $selectString = "0 as credit, opening_balance as debit";
            } else {
                $selectString = "opening_balance as credit, 0 as debit";
            }

            $purchaseDateRange = '';
            $supplierPaymentDateRange = '';
            $purchaseReturnDateRange = '';

            if (!empty($startDate) && !empty($endDate)) {
                $purchaseDateRange = " AND date BETWEEN '$startDate' AND '$endDate'";
                $supplierPaymentDateRange = " AND date BETWEEN '$startDate' AND '$endDate'";
                $purchaseReturnDateRange = " AND r.date BETWEEN '$startDate' AND '$endDate'";
            }

            $supplierLedger = DB::select("
                SELECT s.* FROM (
                    (SELECT $selectString, '' as date, 'Opening Balance' as type, '' as reference_no FROM tbl_suppliers WHERE id = ?)
                    UNION
                    (SELECT paid as credit, 0 as debit, date, 'Purchase Payment' as type, reference_no FROM tbl_purchase WHERE supplier = ? AND paid != 0 AND del_status = 'Live' $purchaseDateRange)
                    UNION
                    (SELECT amount as credit, 0 as debit, date, 'Supplier Payment' as type, '' as reference_no FROM tbl_supplier_payments WHERE supplier = ? AND del_status = 'Live' $supplierPaymentDateRange)
                    UNION
                    (SELECT 0 as credit, total_return_amount as debit, r.date, 'Purchase Return' as type, r.reference_no FROM tbl_purchase_return r JOIN tbl_purchase p ON r.supplier_id = p.supplier WHERE r.supplier_id = ? AND r.del_status = 'Live' $purchaseReturnDateRange)
                ) as s
                ORDER BY s.date ASC", [$supplier_id, $supplier_id, $supplier_id, $supplier_id]
            );
        } else {
            unset($request->_token);
            $supplierLedger = DB::select("
                SELECT s.* FROM (
                    (SELECT '' as credit, '' as debit, '' as date, 'Opening Balance' as type, '' as reference_no FROM tbl_suppliers WHERE id = ?)
                    UNION
                    (SELECT paid as credit, 0 as debit, date, 'Purchase Payment' as type, reference_no FROM tbl_purchase WHERE supplier = ? AND paid != 0 AND del_status = 'Live')
                    UNION
                    (SELECT amount as credit, 0 as debit, date, 'Supplier Payment' as type, '' as reference_no FROM tbl_supplier_payments WHERE supplier = ? AND del_status = 'Live')
                    UNION
                    (SELECT 0 as credit, total_return_amount as debit, r.date, 'Purchase Return' as type, r.reference_no FROM tbl_purchase_return r JOIN tbl_purchase p ON r.supplier_id = p.supplier WHERE r.supplier_id = ? AND r.del_status = 'Live')
                ) as s
                ORDER BY s.date ASC", [$supplier_id, $supplier_id, $supplier_id, $supplier_id]
            );
        }

        $title = __('index.supplier_ledger');

        $suppliers = Supplier::where('del_status', "Live")->get();
        return view('pages.report.supplier_ledger', compact('title', 'startDate', 'endDate', 'type', 'supplier_id', 'supplierLedger', 'suppliers'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function productionReportOld()
    {
        $obj = Manufacture::with(['customer', 'product'])->where('manufacture_status', 'inProgress')->where('del_status', "Live")->get();

        $title = __('index.production_report');

        return view('pages.report.production_report', compact('title', 'obj'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fpProductionReport()
    {
        $obj = Manufacture::with(['customer', 'product'])->where('manufacture_status', 'done')->orderBy('id', 'desc')->where('del_status', "Live")->get();

        $title = __('index.fp_production_report');

        return view('pages.report.fp_production_report', compact('title', 'obj'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function balanceSheet()
    {
        $obj = Account::where('del_status', "Live")->get();

        $title = __('index.balance_sheet');

        return view('pages.report.balance_sheet', compact('title', 'obj'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trialBalance()
    {

        $date = request()->get('date') ?? '';

        $sales_credit = Sales::singleDate($date)->where('del_status', "Live")->sum('grand_total');
        $customer_due_received_credit = CustomerDueReceive::singleDate($date)->where('del_status', "Live")->sum('amount');
        $supplier_due_paid_debit = Supplier_payment::singleDate($date)->where('del_status', "Live")->sum('amount');
        $purchase_debit = RawMaterialPurchase::singleDate($date)->where('del_status', "Live")->sum('paid');
        $production_non_inventory_cost_debit = Pnonitem::singleDate($date)->where('del_status', "Live")->sum('totalamount');
        $expense_debit = Expense::singleDate($date)->where('del_status', "Live")->sum('amount');
        $payroll_debit = Salary::singleDate($date)->where('del_status', "Live")->sum('total_amount');

        $title = __('index.trial_balance');

        return view('pages.report.trial_balance', compact('title', 'sales_credit', 'customer_due_received_credit', 'supplier_due_paid_debit', 'purchase_debit', 'production_non_inventory_cost_debit', 'expense_debit', 'payroll_debit', 'date'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fpSaleReport()
    {
        $obj = Sales::with('customer')->where('del_status', "Live")->get();
        $title = __('index.fp_sale_report');

        return view('pages.report.fp_sale_report', compact('title', 'obj'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fpItemSaleReport()
    {
        $obj = Sales::with(['customer', 'details'])->where('del_status', "Live")->get();

        $title = __('index.fp_item_sale_report');

        return view('pages.report.fp_item_sale_report', compact('title', 'obj'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customerDueReport(Request $request)
    {
        $type = '';
        if (isset($request->type)) {
            $type = $request->type;
        }
        $customerDueReport = Customer::where('del_status', 'Live')->get();
        $title = __('index.customer_due_report');

        return view('pages.report.customer_due_report', compact('title', 'type', 'customerDueReport'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customerLedger(Request $request)
    {
        $startDate = '';
        $endDate = '';
        $selectString = '';
        $customer_id = '';
        $type = '';
        $customerLedger = [];

        if (isset($request->customer_id)) {
            unset($request->_token);
            $customer_id = $request->customer_id;
            if (isset($request->startDate)) {
                $startDate = $request->startDate;
            }
            if (isset($request->endDate)) {
                $endDate = $request->endDate;
            }

            if (isset($request->type)) {
                $type = $request->type;
            }

            $customer = Customer::find($customer_id);
            if ($startDate != '' && $endDate != '') {
                $customer = Customer::where('id', $customer_id)->whereBetween('created_at', [$startDate, $endDate])->first();
            }
            if ($customer) {
                $customerLedger[0]['date'] = $customer->created_at;
                $customerLedger[0]['type'] = 'Opening Balance';
                $customerLedger[0]['transaction_no'] = '';
                if ($customer->opening_balance_type == 'Debit') {
                    $customerLedger[0]['debit'] = $customer->opening_balance;
                    $customerLedger[0]['credit'] = 0;
                } else {
                    $customerLedger[0]['debit'] = 0;
                    $customerLedger[0]['credit'] = $customer->opening_balance;
                }
            }

            $sales = Sales::where('customer_id', $customer_id)->where('del_status', 'Live')->get();
            if ($startDate != '' && $endDate != '') {
                $sales = Sales::where('customer_id', $customer_id)->where('del_status', 'Live')->whereBetween('sale_date', [$startDate, $endDate])->get();
            }

            $customerDueReceive = CustomerDueReceive::where('customer_id', $customer_id)->where('del_status', 'Live')->get();
            if ($startDate != '' && $endDate != '') {
                $customerDueReceive = CustomerDueReceive::where('customer_id', $customer_id)->where('del_status', 'Live')->whereBetween('only_date', [$startDate, $endDate])->get();
            }

            $i = 1;
            foreach ($sales as $sale) {
                $customerLedger[$i]['date'] = $sale->sale_date;
                $customerLedger[$i]['type'] = 'Sales Due';
                $customerLedger[$i]['transaction_no'] = $sale->reference_no;
                $customerLedger[$i]['debit'] = $sale->due;
                $customerLedger[$i]['credit'] = 0;
                $i++;
            }

            foreach ($customerDueReceive as $dueReceive) {
                $customerLedger[$i]['date'] = $dueReceive->only_date;
                $customerLedger[$i]['type'] = 'Customer Due Receive';
                $customerLedger[$i]['transaction_no'] = $dueReceive->reference_no;
                $customerLedger[$i]['debit'] = 0;
                $customerLedger[$i]['credit'] = $dueReceive->amount;
                $i++;
            }
        }
        // dd($customerLedger);
        $title = __('index.customer_ledger');
        $customers = Customer::orderBy('id', 'DESC')->where('del_status', "Live")->pluck('name', 'id');

        return view('pages.report.customer_ledger', compact('title', 'startDate', 'endDate', 'type', 'customer_id', 'customerLedger', 'customers'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profitLossReport(Request $request)
    {
        $startDate = '';
        $endDate = '';

        unset($request->_token);

        if (isset($request->startDate)) {
            $startDate = $request->startDate;
        }
        if (isset($request->endDate)) {
            $endDate = $request->endDate;
        }

        $totalSales = Sales::where('status', 'Final')->dateFilter($startDate, $endDate)->where('del_status', "Live")->sum('grand_total');
        $costOfGoodsSold = Sales::where('status', 'Final')->dateFilter($startDate, $endDate)->where('del_status', "Live")->get();
        $costOfTransferred = Sales::where('status', 'Final')->dateFilter($startDate, $endDate)->where('del_status', "Live")->get();
        $totalCostOfGoodsSold = 0;
        $totalCostOfTransferred = 0;
        foreach ($costOfGoodsSold as $cost) {
            $totalCostOfGoodsSold += $cost->cost_of_goods;
        }

        foreach ($costOfTransferred as $cost) {
            $totalCostOfTransferred += $cost->cost_of_transferred;
        }

        $grossProfit = $totalSales - $totalCostOfGoodsSold - $totalCostOfTransferred;
        $totalTax = Sales::where('status', 'Final')->dateFilter($startDate, $endDate)->where('del_status', "Live")->get();
        $totalTaxAmount = 0;
        foreach ($totalTax as $tax) {
            $totalTaxAmount += $tax->total_tax;
        }
        $total_waste = ProductWaste::where('del_status', "Live")->sum('total_loss');
        $total_expense = Expense::where('del_status', "Live")->sum('amount');
        $netProfit = $grossProfit - $totalTaxAmount - $total_waste - $total_expense;
        $title = __('index.profit_loss_report');

        return view('pages.report.profit_loss_report', compact('title', 'startDate', 'endDate', 'totalSales', 'totalCostOfGoodsSold', 'totalCostOfTransferred', 'grossProfit', 'totalTaxAmount', 'total_waste', 'total_expense', 'netProfit'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function productProfitReport(Request $request)
    {
        $startDate = '';
        $endDate = '';

        unset($request->_token);

        if (isset($request->startDate)) {
            $startDate = $request->startDate;
        }
        if (isset($request->endDate)) {
            $endDate = $request->endDate;
        }

        $obj = FinishedProduct::where('del_status', "Live")->get();

        $title = __('index.product_profit_report');

        return view('pages.report.product_profit_report', compact('title', 'startDate', 'endDate', 'obj'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function attendanceReport(Request $request)
    {
        $startDate = '';
        $endDate = '';

        unset($request->_token);

        $attendance = Attendance::orderBy('id', 'DESC')->where('del_status', "Live");
        if (isset($request->startDate)) {
            $startDate = $request->startDate;
            $attendance->where('date', '>=', $request->startDate);
        }
        if (isset($request->endDate)) {
            $endDate = $request->endDate;
            $attendance->where('date', '<=', $request->endDate);
        }

        $obj = $attendance->get();

        $company_id = auth()->user()->company_id;
        $employees = User::where('company_id', $company_id)->where('del_status', "Live")->get();

        $title = __('index.attendance_report');

        return view('pages.report.attendance_report', compact('title', 'obj', 'startDate', 'endDate', 'employees'));
    }
    /**
     * RM Waste Report
     */
    public function rmwasteReport(Request $request)
    {
        $startDate = '';
        $endDate = '';

        unset($request->_token);
        $rmwaste = RMWaste::orderBy('id', 'DESC')->where('del_status', "Live");
        if (isset($request->startDate)) {
            $startDate = $request->startDate;
            $rmwaste->where('date', '>=', $request->startDate);
        }
        if (isset($request->endDate)) {
            $endDate = $request->endDate;
            $rmwaste->where('date', '<=', $request->endDate);
        }

        $obj = $rmwaste->get();
        $title = __('index.rmwaste_report');
        return view('pages.report.rmwaste_report', compact('title', 'obj', 'startDate', 'endDate'));
    }

    /**
     * Product Waste Report
     */

    public function fpwasteReport(Request $request)
    {
        $startDate = '';
        $endDate = '';

        unset($request->_token);
        $pw = ProductWaste::orderBy('id', 'DESC')->where('del_status', "Live");
        if (isset($request->startDate)) {
            $startDate = $request->startDate;
            $pw->where('date', '>=', $request->startDate);
        }
        if (isset($request->endDate)) {
            $endDate = $request->endDate;
            $pw->where('date', '<=', $request->endDate);
        }

        $obj = $pw->get();
        $title = __('index.product_waste_report');
        return view('pages.report.productwaste_report', compact('title', 'obj', 'startDate', 'endDate'));
    }

    /**
     * ABC Analysis Report
     */

    public function abcReport()
    {
        $materials = RawMaterial::with(['purchase'])->where('del_status', 'Live')->get();
        $materials->map(function ($material) {
            $material->total_value = $material->purchase->sum(function ($purchase) {
                return $purchase->quantity_amount * $purchase->unit_price;
            });
            return $material;
        });
        $sortedMaterials = $materials->sortByDesc('total_value');
        $totalValue = $sortedMaterials->sum('total_value');
        $cumulativeValue = 0;
        foreach ($sortedMaterials as $material) {
            $cumulativeValue += $material->total_value;
            $material->cumulative_value = $cumulativeValue;
            $material->percentage = ($cumulativeValue / $totalValue) * 100;
        }

        $aMaterials = $sortedMaterials->filter(function ($material) {
            return $material->percentage <= 70;
        });

        $bMaterials = $sortedMaterials->filter(function ($material) {
            return $material->percentage > 70 && $material->percentage <= 90;
        });

        $cMaterials = $sortedMaterials->filter(function ($material) {
            return $material->percentage > 90;
        });

        $obj = [
            'a' => $aMaterials,
            'b' => $bMaterials,
            'c' => $cMaterials,
        ];

        $title = __('index.abc_analysis_report');
        return view('pages.report.abc_report', compact('title', 'obj'));
    }
    public function salesReport(Request $request)
    {
        // dd($request->all());
        $startDate = '';
        $endDate = '';
        $customer_id = escape_output($request->get('customer_id'));
        $search_sale = escape_output($request->get('search_sale'));
        $search_customer = escape_output($request->get('search_customer'));
        $startDate = !empty($request->startDate) ? date('Y-m-d', strtotime($request->startDate)) : null;
        $endDate   = !empty($request->endDate)   ? date('Y-m-d', strtotime($request->endDate))   : null;
        $query = DB::table('tbl_sales as s')
            ->leftJoin('tbl_quotations as q', 'q.id', '=', 's.challan_id')
            ->where('s.del_status', 'Live')
            ->select('s.*', 'q.challan_no');
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw('DATE(s.sale_date)'), [$startDate, $endDate]);
        } elseif (!empty($startDate)) {
            $query->whereDate('s.sale_date', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->whereDate('s.sale_date', '<=', $endDate);
        }
        if (!empty($customer_id)) {
            $query->where('s.customer_id', $customer_id);
        }
        if (!empty($search_sale)) {
           $keyword = strtolower(trim($search_sale));
            if (in_array($keyword, ['sale', 'sales'])) {
                $query->where('s.reference_no', 'like', 'S%');
            } elseif (in_array($keyword, ['labor', 'labour'])) {
                $query->where('s.reference_no', 'like', 'L%');
            } else {
                $query->where(function ($q) use ($search_sale) {
                    $q->where('s.reference_no', 'like', "%{$search_sale}%")
                    ->orWhere('q.challan_no', 'like', "%{$search_sale}%");
                });
            }
        }
        $obj = $query->orderBy('s.id', 'DESC')->get()->unique();
        foreach ($obj as $sale) {
            if (str_starts_with($sale->reference_no, 'S')) {
                $sale->nature_of_business = 'Sales';
            } elseif (str_starts_with($sale->reference_no, 'L')) {
                $sale->nature_of_business = 'Labor';
            } else {
                $sale->nature_of_business = 'Unknown';
            }
        }
        /* foreach ($obj as $sale) {
            $payAmount = DB::table('tbl_customer_due_receives')
                ->where('order_id', $sale->order_id)
                ->where('del_status', 'Live')
                ->sum('pay_amount');

            $balanceAmount = DB::table('tbl_customer_due_receives')
                ->where('order_id', $sale->order_id)
                ->where('del_status', 'Live')
                ->orderBy('id','DESC')
                ->value('balance_amount');

            if ($payAmount == 0) {
                $sale->receive_status = 'Initiated';
            } elseif ($balanceAmount == 0) {
                $sale->receive_status = 'Paid';
            } else {
                $sale->receive_status = 'Partially Paid';
            }

            $sale->pay = $payAmount;
            $sale->bal = $balanceAmount;
        } */
        $customers = Customer::where('del_status','Live')->orderBy('id','DESC')->get();
        $title = __('index.sales_report');
        return view('pages.report.saleReport',compact('title','obj','startDate','endDate','customer_id','customers','search_sale','search_customer'));
    }
    /**
    * Expense Report
    */
    public function expenseReport(Request $request)
    {
        $startDate = '';
        $endDate = '';
        unset($request->_token);
        $expense = Expense::orderBy('id', 'DESC')->where('del_status', "Live");
        if (isset($request->startDate)) {
            $startDate = $request->startDate;
            $expense->where('date', '>=', date('Y-m-d',strtotime($request->startDate)));
        }
        if (isset($request->endDate)) {
            $endDate = $request->endDate;
            $expense->where('date', '<=', date('Y-m-d',strtotime($request->endDate)));
        }
        $obj = $expense->get();
        $title = __('index.expense_report');
        return view('pages.report.expense_report', compact('title', 'obj', 'startDate', 'endDate'));
    }
    /**
     * Salary Report
     */

    public function salaryReport(Request $request)
    {
        $startDate = '';
        $endDate = '';
        unset($request->_token);
        $salary = Salary::orderBy('id', 'DESC')->where('del_status', "Live");
        if (isset($request->startDate)) {
            $startDate = $request->startDate;
            $salary->where('date', '>=', date('Y-m-d',strtotime($request->startDate)));
        }
        if (isset($request->endDate)) {
            $endDate = $request->endDate;
            $salary->where('date', '<=', date('Y-m-d',strtotime($request->endDate)));
        }
        $obj = $salary->get();
        $title = __('index.salary_report');
        return view('pages.report.salary_report', compact('title', 'obj', 'startDate', 'endDate'));
    }
    public function poReport(Request $request)
    {
        // dd($request->all());
        $startDate = '';
        $endDate = '';
        $order_status = '';
        $search_customer = escape_output($request->get('search_customer'));
        $customer_id = escape_output($request->get('customer_id'));
        $search_po = escape_output($request->get('search_po'));
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
        if (isset($search_po) && $search_po != '') {
            $order->where(function ($query) use ($search_po) {
                $query->where('reference_no', 'like', "%{$search_po}%")
                    ->orWhere('order_type', 'like', "%{$search_po}%");
            });
        }
        if ($request->has('order_status') && $request->order_status !== '') {
            $order_status = (int) escape_output($request->get('order_status'));
            $order->whereHas('details', function ($q) use ($order_status) {
                $q->where('order_status', $order_status);
            });
            $order->with(['details' => function ($q) use ($order_status) {
                $q->where('order_status', $order_status);
            }]);
        } else {            
            $order->with('details');
        }
        // dd($order->toSql(), $order->getBindings());
        $obj = $order->orderBy('id', 'DESC')->get();
        $total_orders = CustomerOrderDetails::whereHas('customerOrder', function($q) use ($request, $customer_id, $search_po) {
            $q->where('del_status', 'Live');
            if (!empty($request->startDate)) {
                $q->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($request->startDate)));
            }
            if (!empty($request->endDate)) {
                $q->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($request->endDate)));
            }
            if (!empty($customer_id)) {
                $q->where('customer_id', $customer_id);
            }
            if (!empty($search_po)) {
                $q->where(function($query) use ($search_po) {
                    $query->where('reference_no', 'like', "%{$search_po}%")
                        ->orWhere('order_type', 'like', "%{$search_po}%");
                });
            }
        })->when($request->has('order_status') && $request->order_status !== '', function($q) use ($request) {
            $order_status = (int) escape_output($request->get('order_status'));
            $q->where('order_status', $order_status);
        })->count();
        $orderStatus = [0 => 'Pending', 1 => 'Confirmed', 2 => 'Cancelled'];
        $title = "PO Report";
        return view('pages.report.poReport',compact('title','obj','startDate','endDate','customer_id','total_orders','orderStatus','order_status','search_po','search_customer'));
    }
    public function supplierPurchaseReport(Request $request) {
        $startDate = '';
        $endDate = '';
        $supplier_id = escape_output($request->get('supplier_id'));
        $purchase_status = escape_output($request->get('purchase_status'));
        $purchase = RawMaterialPurchase::where('del_status', "Live");
        if (isset($request->startDate) && $request->startDate != '') {
            $startDate = date('Y-m-d', strtotime($request->startDate));
            $purchase->where('date', '>=', $startDate);
        }
        if (isset($request->endDate) && $request->endDate != '') {
            $endDate = date('Y-m-d', strtotime($request->endDate));
            $purchase->where('date', '<=', $endDate);
        }
        if (isset($supplier_id) && $supplier_id != '') {
            $purchase->where('supplier', $supplier_id);
        }
        if (isset($purchase_status) && $purchase_status != '') {
            $purchase->where('status', $purchase_status);
        }
        // dd($order->toSql(), $order->getBindings());
        $obj = $purchase->orderBy('id', 'DESC')->get();
        $suppliers = Supplier::where('del_status','Live')->orderBy('id','DESC')->get();
        $total_purchase = RawMaterialPurchase::where('del_status', 'Live')->count();
        $title = "Supplier Purchase Report";
        return view('pages.report.supplier_purchase_report',compact('title','obj','startDate','endDate','supplier_id','suppliers','total_purchase','purchase_status'));
    } 
    public function productionReport(Request $request) {
        // dd($request->all());
        $startDate = '';
        $endDate = '';
        $drawer_id = escape_output($request->get('drawer_id'));
        // $customer_id = escape_output($request->get('customer_id'));
        $stage_name = escape_output($request->get('stage_name'));
        $m_status = escape_output($request->get('m_status'));
        $manufacture = Manufacture::where('del_status', "Live");
        if (!empty($request->startDate)) {
            $startDate = date('Y-m-d', strtotime($request->startDate));
            $manufacture->where('start_date', '>=', $startDate);
        }
        if (!empty($request->endDate)) {
            $endDate = date('Y-m-d', strtotime($request->endDate));
            $manufacture->where('complete_date', '<=', $endDate);
        }
        if (isset($drawer_id) && $drawer_id != '') {
            $manufacture->where('drawer_id', $drawer_id);
        }
        if (isset($stage_name) && $stage_name != '') {
            $manufacture->where('stage_name', $stage_name);
        }
        if (isset($m_status) && $m_status != '') {
            $manufacture->where('manufacture_status', $m_status);
        }
        // dd($order->toSql(), $order->getBindings());
        $obj = $manufacture->orderBy('id', 'DESC')->get();
        // $customers = Customer::where('del_status','Live')->orderBy('id','DESC')->get();
        $total_manufactures = Manufacture::where('del_status', 'Live')->count();
        $drawings = Drawer::where('del_status','Live')->get();
        $production_stages = ProductionStage::where('del_status','Live')->get();
        $title = "Production Report";
        return view('pages.report.productionReport',compact('title','obj','startDate','endDate','total_manufactures','drawer_id','stage_name','m_status','drawings','production_stages'));
    }
    public function customerSearchReport(Request $request) {
        $title = "Customer Report";
        $search_customer = escape_output($request->get('search_customer'));
        $customer_id = escape_output($request->get('customer_id'));
        $order = CustomerOrder::where('del_status', 'Live')
        ->whereHas('orderPayment', function ($q) {
            $q->where('order_status', 1)
            ->where('del_status', 'Live');
        });
        if (isset($customer_id) && $customer_id != '') {
            $order->where('customer_id', $customer_id);
        }
        $orders = $order->orderBy('id', 'DESC')->get();
        $manufacture = Manufacture::where('del_status', "Live")->where('manufacture_status', 'done');
        if (isset($customer_id) && $customer_id != '') {
            $manufacture->where('customer_id', $customer_id);
        }
        $productions = $manufacture->orderBy('id', 'DESC')->get();
        $material_stock = MaterialStock::where('del_status','Live')->where('owner_type',2);
        if (isset($customer_id) && $customer_id != '') {
            $material_stock->where('customer_id', $customer_id);
        }
        $material_stock = $material_stock->orderBy('id', 'DESC')->get();
        $inspections = Manufacture::with('inspect_approval')->where('manufacture_status', 'done')->where('del_status', 'Live');
        if (isset($customer_id) && $customer_id != '') {
            $inspections->where('customer_id', $customer_id);
        }
        $inspections = $inspections->orderBy('id','DESC')->get();
        $quotations = Quotation::where('del_status', 'Live')->where('challan_status',3);
        if (isset($customer_id) && $customer_id != '') {
            $quotations->where('customer_id', $customer_id);
        }
        $quotations = $quotations->orderBy('id','DESC')->get();
        $sales = DB::table('tbl_sales as s')->leftJoin('tbl_quotations as q', 'q.id', '=', 's.challan_id')->where('s.del_status', 'Live');
        if (isset($customer_id) && $customer_id != '') {
            $sales->where('s.customer_id', $customer_id);
        }
        $sales = $sales->select('s.*', 'q.challan_no')->orderBy('s.id', 'DESC')->get()->unique();
        $customer_io = CustomerIO::where('del_status', "Live");
        if (isset($customer_id) && $customer_id != '') {
            $customer_io->where('customer_id', $customer_id);
        }
        $customer_io = $customer_io->with('details')->orderBy('id', 'DESC')->get();
        $customer_receives = CustomerOrder::where('del_status', 'Live')
        ->whereHas('details', function ($q) {
            $q->where('order_status', 1)
            ->where('del_status', 'Live')
            ->whereHas('orderInvoice', function ($iq) {
                $iq->where('del_status', 'Live')
                    ->where('due_amount', 0);
            });
        })
        ->with(['details.orderInvoice' => function ($q) {
            $q->where('del_status', 'Live')
            ->where('due_amount', 0);
        }]);
        if (isset($customer_id) && $customer_id != '') {
            $customer_receives->where('customer_id', $customer_id);
        }
        $customer_receives = $customer_receives->orderBy('id', 'DESC')->get();
        // dd($customer_receives);
        return view('pages.report.customer_report',compact('title','orders','search_customer','productions','material_stock','inspections','quotations','sales','customer_io','customer_receives'));
    }
    public function loadCustomerReport(Request $request) {
        $report = [
            'iodetails' => ['total' => 3, 'inward' => 2, 'outward' => 1],
            'quotation' => ['total' => 2, 'quote_amount' => 40000],
            'orders' => ['total_po' => 4, 'confirmed' => 2, 'cancelled' => 1, 'pending' => 1],
            'production' => ['total' => 10, 'draft' => 5, 'inprogress' => 3, 'done' => 2],
            'sales' => ['total_amount' => 120000, 'total' => 10],
            'customer_payment' => ['total_po' => 2, 'total_amount' => 20000, 'paid' => 10000, 'balance' => 10000],
        ];
        return response()->json($report);
    }
    public function instrumentReport(Request $request) {
        $title = "Instrument Report";
        $partners = Partner::where('del_status','Live')->orderBy('id','DESC')->get();
        $customers = Customer::where('del_status','Live')->orderBy('id','DESC')->get();
        $customer_io = DB::table('tbl_customer_ios as cio')
            ->join('tbl_customer_io_details as ciod', 'cio.id', '=', 'ciod.customer_io_id')
            ->leftJoin('tbl_customers as c', 'cio.customer_id', '=', 'c.id')
            ->select(
                'cio.id',
                'cio.po_no',
                'cio.date',
                'cio.status',
                'cio.line_item_no',
                'c.name as customer_name',
                'c.customer_id as customer_id',
                'ciod.ins_name',
                'ciod.qty',
                'ciod.remarks'
            )
            ->where('cio.del_status', 'Live')
            ->where('ciod.del_status', 'Live')
            ->orderBy('cio.date', 'desc')
            ->get();
        $partner_io = DB::table('tbl_partner_ios as pio')
            ->join('tbl_partner_io_details as piod', 'pio.id', '=', 'piod.partner_io_id')
            ->leftJoin('tbl_partners as p', 'pio.partner_id', '=', 'p.id')
            ->select(
                'pio.id',
                'pio.reference_no',
                'pio.io_date',
                'p.name as partner_name',
                'p.partner_id as partner_id',
                'piod.ins_name',
                'piod.line_item_no',
                'piod.qty',
                'piod.status',
                'piod.remarks'
            )
            ->where('pio.del_status', 'Live')
            ->where('piod.del_status', 'Live')
            ->orderBy('pio.io_date', 'desc')
            ->get();
        return view('pages.report.instrument_report',compact('title','partners','customers','customer_io','partner_io'));
    }
    public function loadInstrumentReport(Request $request) {
        $title = "Instrument Report";
        $startDate = '';
        $endDate = '';
        $search_instrument = escape_output($request->get('search_instrument'));
        $customer_id = escape_output($request->get('customer_id'));
        $partner_id = escape_output($request->get('partner_id'));
        $ins_id = escape_output($request->get('ins_id'));
        if (!empty($request->startDate)) {
            $startDate = date('Y-m-d', strtotime($request->startDate));
        }
        if (!empty($request->endDate)) {
            $endDate = date('Y-m-d', strtotime($request->endDate));
        }
        $customer_io = DB::table('tbl_customer_ios as cio')
            ->join('tbl_customer_io_details as ciod', 'cio.id', '=', 'ciod.customer_io_id')
            ->leftJoin('tbl_customers as c', 'cio.customer_id', '=', 'c.id')
            ->select(
                'cio.id',
                'cio.po_no',
                'cio.date',
                'cio.status',
                'cio.line_item_no',
                'c.name as customer_name',
                'c.customer_id as customer_id',
                'ciod.ins_name',
                'ciod.qty',
                'ciod.remarks'
            )
            ->where('cio.del_status', 'Live')
            ->where('ciod.del_status', 'Live')
            ->when(!empty($ins_id), function ($query) use ($ins_id) {
                return $query->where('ciod.ins_name', $ins_id);
            })
            ->when(!empty($startDate), function ($query) use ($startDate) {
                return $query->where('cio.date', '>=', $startDate);
            })
            ->when(!empty($endDate), function ($query) use ($endDate) {
                return $query->where('cio.date', '<=', $endDate);
            })
            ->when(!empty($customer_id), function ($query) use ($customer_id) {
                return $query->where('cio.customer_id',  $customer_id);
            })
            ->orderBy('cio.date', 'desc')
            ->get();
        $partner_io = DB::table('tbl_partner_ios as pio')
            ->join('tbl_partner_io_details as piod', 'pio.id', '=', 'piod.partner_io_id')
            ->leftJoin('tbl_partners as p', 'pio.partner_id', '=', 'p.id')
            ->select(
                'pio.id',
                'pio.reference_no',
                'pio.io_date',
                'p.name as partner_name',
                'p.partner_id as partner_id',
                'piod.ins_name',
                'piod.line_item_no',
                'piod.qty',
                'piod.status',
                'piod.remarks'
            )
            ->where('pio.del_status', 'Live')
            ->where('piod.del_status', 'Live')
            ->when(!empty($ins_id), function ($query) use ($ins_id) {
                return $query->where('piod.ins_name', $ins_id);
            })
            ->when(!empty($startDate), function ($query) use ($startDate) {
                return $query->where('pio.io_date', '>=', $startDate);
            })
            ->when(!empty($endDate), function ($query) use ($endDate) {
                return $query->where('pio.io_date', '<=', $endDate);
            })
            ->when(!empty($partner_id), function ($query) use ($partner_id) {
                return $query->where('pio.partner_id',  $partner_id);
            })
            ->orderBy('pio.io_date', 'desc')
            ->get();
        $partners = Partner::where('del_status','Live')->orderBy('id','DESC')->get();
        $customers = Customer::where('del_status','Live')->orderBy('id','DESC')->get();
        return view('pages.report.instrument_report',compact('title','search_instrument','ins_id', 'customer_io','partner_io','partners','customers','partner_id','customer_id'));
    }
    public function paymentReport(Request $request) {
        // dd($request->all());
        $title = "Payment Report";
        $startDate = '';
        $endDate = '';
        $type = escape_output($request->get('type'));
        $customer_id = escape_output($request->get('customer_id'));
        $supplier_id = escape_output($request->get('supplier_id'));
        $partner_id = escape_output($request->get('partner_id'));
        $search_reference = escape_output($request->get('search_reference'));
        if($search_reference!='') {
            $ref_id = escape_output($request->get('ref_id'));
            $ref_detail_id = escape_output($request->get('ref_detail_id'));
        } else {
            $ref_id = '';
            $ref_detail_id = '';
        }
        $customers = Customer::where('del_status','Live')->orderBy('id','DESC')->get();
        $partners = Partner::where('del_status','Live')->orderBy('id','DESC')->get();
        $suppliers = Supplier::where('del_status','Live')->orderBy('id','DESC')->get();
        $customer_receives = CustomerOrder::where('del_status', 'Live')
            ->whereHas('orderPayment', function ($q) use ($ref_detail_id) {
                $q->where('order_status', 1)
                ->where('del_status', 'Live');

                if (!empty($ref_detail_id)) {
                    $q->where('id', $ref_detail_id);
                }
            });
        // dd($customer_receives->toSql(),$customer_receives->getBindings());
        if (isset($request->startDate) && $request->startDate != '') {
            $startDate = $request->startDate;
            $customer_receives->where('po_date', '>=', date('Y-m-d',strtotime($request->startDate)));
        }
        if (isset($request->endDate) && $request->endDate != '') {
            $endDate = $request->endDate;
            $customer_receives->where('po_date', '<=', date('Y-m-d',strtotime($request->endDate)));
        }
        if (isset($customer_id) && $customer_id != '') {
            $customer_receives->where('customer_id', $customer_id);
        }
        $obj1 = $customer_receives->orderBy('id', 'DESC')->get();
        $total_orders = CustomerOrderDetails::where('order_status',1)->where('del_status', 'Live')->count();
        $purchase = RawMaterialPurchase::with('supplierPayments')->where('status','Completed')->where('del_status','Live');
        if (isset($request->startDate) && $request->startDate != '') {
            $startDate = $request->startDate;
            $purchase->where('date', '>=', date('Y-m-d',strtotime($request->startDate)));
        }
        if (isset($request->endDate) && $request->endDate != '') {
            $endDate = $request->endDate;
            $purchase->where('date', '<=', date('Y-m-d',strtotime($request->endDate)));
        }
        if (isset($supplier_id) && $supplier_id != '') {
            $purchase->where('supplier', $supplier_id);
        }
        if (isset($ref_id) && $ref_id != '') {
            $purchase->where('id', $ref_id);
        }
        $obj2 = $purchase->orderBy('id', 'DESC')->get();
        $partner_io = PartnerIo::where('del_status', 'Live')
            ->with(['details' => function ($q) use ($ref_detail_id) {
                $q->where('del_status', 'Live');
                if (!empty($ref_detail_id)) {
                    $q->where('id', $ref_detail_id);
                }
            }])
            ->whereHas('details', function ($q) use ($ref_detail_id) {
                $q->where('del_status', 'Live');
                if (!empty($ref_detail_id)) {
                    $q->where('id', $ref_detail_id);
                }
        });
        // dd($partner_io->get());
        if (!empty($request->startDate)) {
            $partner_io->where('io_date', '>=', date('Y-m-d', strtotime($request->startDate)));
        }
        if (!empty($request->endDate)) {
            $partner_io->where('io_date', '<=', date('Y-m-d', strtotime($request->endDate)));
        }
        if (!empty($partner_id)) {
            $partner_io->where('partner_id', $partner_id);
        }
        $obj3 = $partner_io->orderBy('id', 'DESC')->get();
        $total_io = PartnerIoDetail::where('del_status', 'Live')->count();
        return view('pages.report.payment_report',compact('title','type','customers','partners','suppliers','customer_id','supplier_id','partner_id','ref_id','ref_detail_id','search_reference','obj1','obj2','obj3','total_orders','total_io','startDate','endDate'));
    }
    public function exportPOReport(Request $request)
    {
        return Excel::download(new POReportExport($request), 'PO_Report_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
}
