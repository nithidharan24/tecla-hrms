<?php

namespace App\Traits;

use Illuminate\Http\Request;
use File;

trait ImageUploadTrait {


    public function uploadImage(Request $request, $inputName, $path)
    {
        if ($request->hasFile($inputName)) {
            $image = $request->file($inputName);
            $extension = $image->getClientOriginalExtension();
            $fileName = 'media_' . uniqid() . '.' . $extension;

            $image->move(public_path($path), $fileName);

            return $path . '/' . $fileName; // Return the path for saving in the database
        }

        return null; // Handle cases where no file is uploaded
    }




    public function uploadMultiImage(Request $request, $inputName, $path)
    {
        $imagePaths = [];

        if($request->hasFile($inputName)){

            $images = $request->{$inputName};

            foreach($images as $image){

                $ext = $image->getClientOriginalExtension();
                $imageName = 'media_'.uniqid().'.'.$ext;

                $image->move(public_path($path), $imageName);

                $imagePaths[] =  $path.'/'.$imageName;
            }

            return $imagePaths;
       }
    }


    public function updateImage(Request $request, $inputName, $path, $oldPath = null)
{
    if ($request->hasFile($inputName)) {
        if ($oldPath && File::exists(public_path($oldPath))) {
            File::delete(public_path($oldPath));
        }

        $image = $request->{$inputName};
        $ext = $image->getClientOriginalExtension();
        $imageName = 'media_' . uniqid() . '.' . $ext;

        $image->move(public_path($path), $imageName);

        return $path . '/' . $imageName;
    }

    return $oldPath; // Return the old path if no new file is uploaded
}

    /** Handle Delte File */
    public function deleteImage(string $path)
    {
        if(File::exists(public_path($path))){
            File::delete(public_path($path));
        }
    }
}

