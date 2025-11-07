<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Traits\ModelTrait;

class Organization extends Model
{
    use ModelTrait;

    protected $table            = 'epos_organizations';
    protected $primaryKey       = 'id';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['name','email','telephone','address','post_code','logo_web','logo_mobile','domain'];

    private static function _treatMedia($media) {
        $jsonData = json_decode($media ?? '', true);
        if($jsonData['active_link'] == 'external' && !empty($jsonData['external_link'])) {
            $jsonData['url'] = $jsonData['external_link'];
        }
        if($jsonData['active_link'] == 'upload' && !empty($jsonData['upload_file'])) {
            $jsonData['url'] = env('app.uploads_baseurl') . "/" .$jsonData['upload_file'];
        }
        return $jsonData;
    }

    public function isInUse($id) {
        $cmsModel = new Cms();
        $rows = $cmsModel->where('organization_id', $id)->findAll();
        if(!empty($rows)) return true;

        $managerModel = new Manager();
        $rows = $managerModel->where("organization_id", $id)->findAll();
        if(!empty($rows)) return true;
        
        return false;
    }

    public static function populate(&$row) {
        if(!empty($row['logo_web'])) $row['logo_web'] = Organization::_treatMedia($row['logo_web']);
        if(!empty($row['logo_mobile'])) $row['logo_mobile'] = Organization::_treatMedia($row['logo_mobile']);
    }
}