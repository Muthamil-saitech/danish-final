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
  # This is OrderStatusController
  ##############################################################################
 */

namespace App\Http\Controllers;

use App\CustomerOrder;
use App\CustomerOrderDetails;
use App\Http\Controllers\Controller;
use App\Manufacture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderStatusController extends Controller
{
    public function customerOrderStatus()
    {
        $title = __('index.customer_order_status');
        $order_quotation = DB::table('tbl_customer_order_details as cod')
            ->join('tbl_customer_orders as co', 'cod.customer_order_id', '=', 'co.id')
            ->join('tbl_finish_products as p', 'cod.product_id', '=', 'p.id')
            ->where('cod.del_status', 'Live')
            ->where('co.del_status', 'Live')
            ->where('cod.order_status', 0)
            ->whereNotIn('cod.id', function ($subquery) {
                $subquery->select('m.customer_order_id')
                    ->from('tbl_manufactures as m')
                    ->where('m.del_status', 'Live')
                    ->whereNotNull('m.customer_order_id');
            })
            ->select(
                'co.*',
                'p.name',
                'p.code',
                'cod.id as codid',
                'cod.product_id',
                'cod.line_item_no',
                'cod.sub_total',
                'cod.price',
                'cod.sale_price',
                'cod.quantity',
            )
            ->get();
            // dd($order_quotation);
        $order_quotation_cancelled = DB::table('tbl_customer_order_details as cod')
            ->join('tbl_customer_orders as co', 'cod.customer_order_id', '=', 'co.id')
            ->join('tbl_finish_products as p', 'cod.product_id', '=', 'p.id')
            ->where('cod.del_status', 'Live')
            ->where('co.del_status', 'Live')
            ->where('cod.order_status', 2)
            ->whereNotIn('cod.id', function ($subquery) {
                $subquery->select('m.customer_order_id')
                    ->from('tbl_manufactures as m')
                    ->where('m.del_status', 'Live')
                    ->whereNotNull('m.customer_order_id');
            })
            ->select(
                'co.*',
                'p.name',
                'p.code',
                'cod.id as codid',
                'cod.product_id',
                'cod.line_item_no',
                'cod.sub_total',
                'cod.price',
                'cod.sale_price',
                'cod.quantity',
            )
            ->get();
        $waiting_for_production = DB::table('tbl_customer_order_details as cod')
            ->join('tbl_customer_orders as co', 'cod.customer_order_id', '=', 'co.id')
            ->join('tbl_finish_products as p', 'cod.product_id', '=', 'p.id')
            ->where('cod.del_status', 'Live')
            ->where('co.del_status', 'Live')
            ->where('cod.order_status', 1)
            ->whereNotIn(DB::raw('(cod.id, cod.product_id)'), function ($subquery) {
                $subquery->selectRaw('m.customer_order_id, m.product_id')
                    ->from('tbl_manufactures as m')
                    ->where('m.del_status', 'Live')
                    ->whereNotNull('m.customer_order_id');
            })
            ->select(
                'co.*',
                'p.name',
                'p.code',
                'cod.id as codid',
                'cod.product_id',
                'cod.line_item_no',
                'cod.sub_total',
                'cod.price',
                'cod.sale_price',
                'cod.quantity',
            )
            ->get();
        $yet_to_start = DB::table('tbl_manufactures as m')
            ->join('tbl_customer_order_details as cod', 'm.customer_order_id', '=', 'cod.id')
            ->join('tbl_customer_orders as co', 'cod.customer_order_id', '=', 'co.id')
            ->join('tbl_finish_products as p', 'm.product_id', '=', 'p.id')
            ->where('m.del_status', 'Live')
            ->where('co.del_status', 'Live')
            ->where('cod.order_status', 1)
            ->where('m.manufacture_status', '!=', 'done')
            ->whereDate('m.start_date', '>', date('Y-m-d'))
            ->select(
                'co.*',
                'p.name',
                'p.code',
                'm.start_date',
                'm.complete_date',
                'm.id as mid',
                'cod.id as codid',
                'cod.product_id',
                'cod.line_item_no',
                'cod.sub_total',
                'cod.price',
                'cod.sale_price',
                'cod.quantity',
            )
            ->get();
        $inproduction = DB::table('tbl_manufactures as m')
            ->join('tbl_customer_order_details as cod', 'm.customer_order_id', '=', 'cod.id')
            ->join('tbl_customer_orders as co', 'cod.customer_order_id', '=', 'co.id')
            ->join('tbl_finish_products as p', 'm.product_id', '=', 'p.id')
            ->whereIn('m.manufacture_status', ['inProgress', 'draft'])
            ->whereDate('m.start_date', '<=', date('Y-m-d'))
            ->where('m.del_status', 'Live')
            ->whereNotNull('m.customer_order_id')
            ->where('co.del_status', 'Live')
            ->where('cod.order_status', 1)
            ->select(
                'co.*',
                'p.name',
                'p.code',
                'm.start_date',
                'm.complete_date',
                'm.id as mid',
                'cod.id as codid',
                'cod.product_id',
                'cod.line_item_no',
                'cod.sub_total',
                'cod.price',
                'cod.sale_price',
                'cod.quantity',
            )
            ->get();
        $ready_for_ship = DB::table('tbl_manufactures as m')
            ->join('tbl_customer_order_details as cod', 'm.customer_order_id', '=', 'cod.id')
            ->join('tbl_customer_orders as co', 'cod.customer_order_id', '=', 'co.id')
            ->join('tbl_finish_products as p', 'm.product_id', '=', 'p.id')
            ->where('m.manufacture_status', 'done')
            ->where('m.del_status', 'Live')
            ->whereNotNull('m.customer_order_id')
            ->where('co.del_status', 'Live')
            ->select(
                'co.*',
                'p.name',
                'p.code',
                'm.start_date',
                'm.complete_date',
                'm.id as mid',
                'cod.id as codid',
                'cod.product_id',
                'cod.line_item_no',
                'cod.sub_total',
                'cod.price',
                'cod.sale_price',
                'cod.quantity',
            )
            ->get();
        return view('pages.customer_order.order-status', compact('title', 'order_quotation','order_quotation_cancelled','waiting_for_production','yet_to_start','inproduction','ready_for_ship'));
    }
}
