<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Traits\ModelTrait;

class CmsTemplate extends Model
{
    use ModelTrait;

    protected $table            = 'epos_cms_templates';
    protected $primaryKey       = 'id';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['internal_name', 'background', 'deleted', 'editor'];


}