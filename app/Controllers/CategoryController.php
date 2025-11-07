<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Category;

class CategoryController extends BaseController
{
    /**
     * To find top level categories
     */
    public function index()
    {
        try {
            $model = new Category();

            $parent_id = request()->getGet('parent_id');
            if (empty($parent_id)) $parent_id = 0;
            $model->where('parent_id', $parent_id);

            $categories = $model->findAll();
            return response()->setJSON([
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function all()
    {
        try {
            $model = new Category();
            $categories = $model->findAll();
            return response()->setJSON([
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function save($id = null)
    {
        try {
            $jsonData = json_decode(request()->getBody(), true);

            $model = new Category();

            $category = $model->where('category_id', $id)->first();
            if (empty($category)) {
                return response()->setJSON([
                    'message' => "No found Category: id=$id"
                ])->setStatusCode(400);
            }

            $category = [
                ...$category,
                ...$jsonData,
            ];
            $result = $model->save($category);

            return response()->setJSON([
                'message' => 'Operation Success'
            ]);
        } catch (\Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function checkSequence() {
        try {
            $model = new Category();
            $count = $model->groupStart()
                    ->where('sequence', '')
                    ->orWhere('sequence IS NULL', null, false) 
                ->groupEnd()
                ->where('parent_id', '0')
                ->countAllResults(false);
            return response()->setJSON([
                'initialized_sequence' => $count > 0 ? false : true
            ]);
        } catch (\Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function initializeSequence() {
        try {
            $model = new Category();
            $top_categories = $model->where('parent_id', 0)->orderBy('category_name', 'asc')->findAll();
            foreach($top_categories as $index => $top_category) {
                $top_category['sequence'] = $index + 1;
                $model->save($top_category);

                $sub_categories = $model->where('parent_id', $top_category['category_id'])->orderBy('category_name', 'asc')->findAll();
                foreach($sub_categories as $j => $sub_category) {
                    $sub_category['sequence'] = $j + 1;
                    $model->save($sub_category);
                }
            }

            return response()->setJSON([
                'result' => true
            ]);
        } catch (\Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function move($id, $direction) {
        try {
            $model = new Category();

            $category = $model->find($id);
            if(empty($category)) throw new \Exception("No found category: category_id=$id");

            if($direction == "up") {
                $swap_category = $model->where('parent_id', $category['parent_id'])
                    ->where("sequence < " . $category['sequence'])
                    ->orderBy("sequence", "desc")
                    ->limit(1)
                    ->get()->getRowArray();
            } else if($direction == "down") {
                $swap_category = $model->where('parent_id', $category['parent_id'])
                    ->where("sequence > " . $category['sequence'])
                    ->orderBy("sequence", "asc")
                    ->limit(1)
                    ->get()->getRowArray();
            }

            if(!empty($swap_category)) {
                $temp = $category['sequence'];
                $category['sequence'] = $swap_category['sequence'];
                $swap_category['sequence'] = $temp;
                $model->save($category);
                $model->save($swap_category);                

                return response()->setJSON([
                    'swappedCategories' => [$category, $swap_category]
                ]);
            } else {
                return response()->setJSON([]);
            }
        } catch (\Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage()
            ])->setStatusCode(400);
        }
    }
}