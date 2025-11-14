<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Drawer extends Model
{
    use HasFactory;
    protected $table = "tbl_drawers";
    protected $guarded = [];

    public function getDrawerStages($drawer_id,$fproduct_id){
        $stageIdsString = DB::table('tbl_drawers')
        ->where('id', $drawer_id)
        ->where('del_status', 'Live')
        ->value('stage_id');
        $stageIds = explode(',', $stageIdsString);
        $result = DB::table('tbl_production_stages as ps')
            ->leftJoin('tbl_finished_products_productionstage as fps', function ($join) use ($fproduct_id) {
                $join->on('fps.productionstage_id', '=', 'ps.id')
                    ->where('fps.finish_product_id', '=', $fproduct_id);
            })
            ->whereIn('ps.id', $stageIds)
            ->where('ps.del_status', 'Live')
            ->where('fps.del_status', 'Live')
            ->orderByRaw('FIELD(ps.id, ' . implode(',', $stageIds) . ')')
            ->select(
                'ps.id',
                'ps.name',
                DB::raw('fps.stage_minute as stage_minute'),
                DB::raw('fps.stage_set_minute as stage_set_minute')
            )
            ->get();
        return $result;
    }
    public function getDrawerProductStages($drawer_no){
        $stageIdsString = DB::table('tbl_drawers')
            ->where('drawer_no', $drawer_no)
            ->where('del_status', 'Live')
            ->value('stage_id');
        $stageIds = explode(',', $stageIdsString);
        $result = DB::table('tbl_production_stages')
            ->whereIn('id', $stageIds)
            ->where('del_status', 'Live')
            ->orderByRaw('FIELD(id, ' . implode(',', $stageIds) . ')')
            ->get();

        return $result;
    }
}
