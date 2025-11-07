<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Product;

class ProductController extends BaseController
{
    public function getBrands() {
        try {
            $model = new Product();
            $products = $model->groupBy('brand')->findAll();
            
            $brands = array_map(function($product) {
                return $product['brand'];
            }, $products);

            $brands = array_filter($brands, function($brand) {
                return !empty($brand);
            });
            
            return response()->setJSON([
                'brands' => array_values($brands)
            ]);
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }
}