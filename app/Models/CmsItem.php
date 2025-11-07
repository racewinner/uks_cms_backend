<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Traits\ModelTrait;

class CmsItem extends Model
{
    use ModelTrait;

	protected $table            = 'epos_cms_items';
    protected $primaryKey       = 'id';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['type', 'cms_id', 'internal_name', 'active', 'editor', 'sequence', 'data',
        'ga_id', 'dwell_time', 'prod_codes', 'link_url', 'media_web', 'media_mobile', 'ribbon', 'deleted'];

    public static function getLastSequence($cms_id) {
        $model = new CmsItem();
        // To get last sequence
        $query = $model
            ->where('cms_id', $cms_id)
            ->selectMax('sequence');

        $last_one = $query->first();
        
        $last_sequence = !empty($last_one) ? intVal($last_one['sequence']) : 0;

        return $last_sequence;
    }
}