<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Traits\ModelTrait;

class Product extends Model
{
    use ModelTrait;

	protected $table            = 'epos_product';
    protected $primaryKey       = 'id';
    protected $useTimestamps    = true;
    protected $allowedFields    = [];

}