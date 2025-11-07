<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Traits\ModelTrait;

class Category extends Model
{
    use ModelTrait;

    protected $table = 'epos_categories';
    protected $primaryKey = 'category_id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'category_name',
        'filter_desc',
        'alias',
        'type',
        'parent_id',
        'import',
        'display',
        'c_or_s_b',
        'branches',
        'logo_web',
        'logo_mobile',
        'sequence',
    ];

}