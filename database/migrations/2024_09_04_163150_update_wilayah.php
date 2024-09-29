<?php

use App\Models\Region;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        // sumber data dari https://github.com/cahyadsn/wilayah/blob/master/db/wilayah.sql
        $sql = file_get_contents('database/file/wilayah.sql');
        DB::beginTransaction();
        try {
            DB::statement($sql);
            // data yang sebelumnya diupdate oleh user
            $updateDataRegions = $this->getUpdatedOldRegion();
            $this->generateRegionTable();
            if($updateDataRegions){
                foreach ($updateDataRegions as $item) {
                    Region::where('region_code', $item->region_code)->update(['new_region_name' => $item->new_region_name]);
                }
            }
            $this->updateDesaId();
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
        }                
    }

    private function getUpdatedOldRegion(){
        $sql = "select tr.*, w.nama from tbl_regions tr 
                    left join wilayah w on w.kode = tr.region_code 
                    where tr.new_region_name is not null
                    and tr.new_region_name != w.nama";
        return DB::select($sql);
    }

    private function generateRegionTable(){
        Region::truncate();
        $sql = "INSERT INTO tbl_regions (region_code,region_name,parent_code,created_by,updated_by,created_at,updated_at)
                select 0 as region_code,'Nasional' as region_name,NULL as parent_code,1 as created_by,1 as updated_by, now() as created_at, now() as updated_at
                union
                select kode as region_code, nama as region_name,
                case 
                when length(kode) = 13 then substring(kode,1,8)
                when length(kode) = 8 then substring(kode,1,5)
                when length(kode) = 5 then substring(kode,1,2)
                else 0	
                end as parent_code,
                1 as created_by,
                1 as updated_by,
                now() as created_at,
                now() as updated_at
                from wilayah
                order by region_code
            ";
        DB::statement($sql);
    }

    private function updateDesaId(){
        $sql = "UPDATE tbl_regions 
            join desa on desa.kode_desa = tbl_regions.region_code
            SET tbl_regions.desa_id = desa.id";
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
