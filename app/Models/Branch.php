<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Traits\ModelTrait;

class Branch extends Model
{
    use ModelTrait;

    protected $table            = 'epos_branches';
    protected $primaryKey       = 'id';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['site_name', 'trading_name', 'address1', 'address2','address3' ,'post_code', 
        'telephone', 'fax', 'email', 'image', 'depot_plan', 'depot_location', 'geo_latitude', 'geo_longitude',
        'mo_open', 'mo_close', 'tu_open', 'tu_close', 'we_open', 'we_close', 'th_open', 'th_close',
        'fr_open', 'fr_close', 'sa_open', 'sa_close', 'su_open', 'su_close'];

    public function isInUse($id) {
        $cmsModel = new Cms();
        $rows = $cmsModel->where("FIND_IN_SET($id, branches)")->findAll();
        if(!empty($rows)) return true;

        $employeeModel = new Employee();
        $rows = $employeeModel->where("FIND_IN_SET($id, branches)")->findAll();
        if(!empty($rows)) return true;
        
        return false;
    }
}