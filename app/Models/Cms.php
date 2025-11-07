<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Traits\ModelTrait;

class Cms extends Model
{
    use ModelTrait;

	protected $table            = 'epos_cms';
    protected $primaryKey       = 'id';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['type', 'start_date', 'end_date', 'branches', 'price_list', 'organization_id', 'internal_name', 'active', 
        'editor', 'page_pos', 'ribbon', 'deleted', 'template_id', 'data', 'dwell_time'];

    public static function getMaxPagePosition() {
        $model = new Cms();
        $date = date('Y-m-d H:i:s');
        $query = $model
            ->where('active', 1)
            ->where('start_date<=', $date)
            ->where('end_date>=', $date)
            ->selectMax('page_pos');
        $result = $query->first();
        return !empty($result) ? $result['page_pos'] : 0;
    }

    public static function getDetailTable($type) {
        switch($type) {
        }
        return '';
    }

    public static function getDetailModel($type) {
        switch($type) {
        }
        return null;
    }
}