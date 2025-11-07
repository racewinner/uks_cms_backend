<?php
namespace App\Controllers;

use App\Controllers\BaseController;

use App\Traits\ControllerTrait;

class MediaController extends BaseController
{
    use ControllerTrait;

    public function uploadFile() {
        $uploadFolder = FCPATH . "uploads";
        $file = request()->getFile('file');
        $folder = request()->getVar('folder') ?? "";

        if(!empty($folder)) {
            $uploadFolder .= "/$folder";
            if(!file_exists($uploadFolder)) {
                mkdir($uploadFolder, 0777, true);
            }
        }

        if($file->isValid() && !$file->hasMoved()) {
            try {
                $newFileName = time() . "." . $file->getExtension();
                $file->move($uploadFolder, $newFileName);

                return response()->setJSON([
                    'message' => 'File upload successfully',
                    'file' => (!empty($folder) ? $folder . "/" : "") . $file->getName()
                ]);
            } catch(\Exception $e) {
                return response()->setJSON([
                    'message' => $e->getMessage()
                ])->setStatusCode(500);
            }
        }
    }

    public function deleteUploadedFile() {
        $file = request()->getVar('file');
        $fullPath = FCPATH . "uploads/" . $file;
        if(file_exists($fullPath)) {
            if(unlink($fullPath)) {
                return response()->setJSON([
                    'message' => 'File deleted successfully.'
                ]);
            } else {
                return response()->setJSON([
                    'message' => 'Failed to delete file'
                ])->setStatusCode(400);
            }
        } else {
            return response()->setJSON([
                'message' => "No Fould file"
            ]);
        }
    }

}