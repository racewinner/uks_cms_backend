<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FooterConfig;
use App\Models\TopRibbonConfig;
use App\Traits\ControllerTrait;

class SiteConfigController extends BaseController
{
    use ControllerTrait;

    public function indexFooterConfig() {
        try {
            $model = new FooterConfig();
            $row = $model->first();
            return response()->setJSON([
                'row' => $row
            ]);
        } catch(\Exception $e) {

        }
    }

    public function saveFooterConfig() {
        $authUser = request()->fetchGlobal('user');

        try {
            $model = new FooterConfig();
            $jsonData = json_decode(request()->getBody(), true);

            $row = $model->first();
            if(empty($row)) {
                $model->save([
                    ...$jsonData,
                    'editor' => $authUser['id'],
                ]);
            } else {
                $row = [
                    ...$row,
                    ...$jsonData,
                    "editor" => $authUser['id']
                ];
                $model->save($row);
            }

            return response()->setJSON([
                'message' => 'Operation Success'
            ]);
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function indexTopRibbonConfig() {
        try {
            $model = new TopRibbonConfig();
            $row = $model->first();
            return response()->setJSON([
                'row' => $row
            ]);
        } catch(\Exception $e) {

        }
    }

    public function saveTopRibbonConfig() {
        $authUser = request()->fetchGlobal('user');

        try {
            $model = new TopRibbonConfig();
            $jsonData = json_decode(request()->getBody(), true);

            $row = $model->first();
            if(empty($row)) {
                $model->save([
                    ...$jsonData,
                    'editor' => $authUser['id'],
                ]);
            } else {
                $row = [
                    ...$row,
                    ...$jsonData,
                    "editor" => $authUser['id']
                ];
                $model->save($row);
            }

            return response()->setJSON([
                'message' => 'Operation Success'
            ]);
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }
}