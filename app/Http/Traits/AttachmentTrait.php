<?php

namespace App\Http\Traits;

use App\Models\EFormAttachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait AttachmentTrait
{
    public function addMultipleAttachment(Array $data, String $storage_name, String $main_model_id, Model $model)
    {
        foreach ($data['files'] as $attachment) {
            if(! ($attachment->isValid()))
            {
                return "Attachment Origins Path not found";
            }

            $name = $attachment->getClientOriginalName();

            // added UniqueID() to prevent file overwriting
            $file = file_get_contents($attachment);
            $file_name = $data[$main_model_id]. '_' . uniqid() . '.' .$attachment->getClientOriginalExtension();

            Storage::disk($storage_name)->put($file_name, $file);

            $model = $model::create([
                $main_model_id => $data[$main_model_id],
                'name' => $name,
                'path' => $file_name,
            ]);

            if (!$model) {
                return 'Error uploading attachment';
            }
        }

        return 'Attachment Upload Successful';
    }

    public function deleteAttachment(int $attachment_id, Model $model, String $storage_name)
    {
        if ($attachment_id) {
            $attachment = $model::find($attachment_id);
            Storage::disk($storage_name)->delete($attachment->path);

            $attachment->delete($attachment_id);

            return response()->json([
                'message' => 'Attachment Deleted.',
                'status' => 200,
            ]);
        }

        return response()->json([
            'message' => 'Attachment not found.',
            'status' => 404,
        ]);
    }

    public function eFormAddMultipleAttachment(Array $data, String $storage_name, Model $model, bool $hrUpload = false)
    {
        foreach ($data['files'] as $attachment) {
            if(! ($attachment->isValid()))
            {
                return "Attachment Origins Path not Found";
            }

            //insert record first, to retrieve attachment_id so that it can be saved in the file path
            $EFormAttachmentID = DB::table('e_form_attachments')
                ->insertGetId([
                    'e_form_id' => $model->id,
                    'created_at' => now()->toDateTimeString(),
                ]);

            $file_original_name = $attachment->getClientOriginalName();
            $file = file_get_contents($attachment);
            // added UniqueID() to prevent file overwriting
            $file_name = $EFormAttachmentID . '_' . uniqid() . '.' .$attachment->getClientOriginalExtension();

            Storage::disk($storage_name)->put($file_name, $file);

            $EFormAttachment = EFormAttachment::findOrFail($EFormAttachmentID)
                ->update([
                    'name' => $file_original_name,
                    'path' => $file_name,
                    'hr_upload' => $hrUpload, //cast from eformAttachment model
                ]);

            if(! $EFormAttachment) {
                return 'Error Uploading Attachment';
            }
        }
        return 'Attachment Upload Successful';
    }

    public function addLiveAttachment(Model $model, UploadedFile $file, String $directory)
    {
        if (! is_null($model->path))
        {
            Storage::disk('travel-claim-attachment')
                ->delete('/'.$model->travel_id."/{$directory}/".$model->path);
        }

        $fileName = str()->uuid() . '.' . $file->extension();
        Storage::disk('travel-claim-attachment')->putFileAs(
            '/'.$model->travel_id."/{$directory}",
            $file,
            $fileName
        );

        return $model->update([
            'path' => $fileName
        ]);
    }

    public function deleteLiveAttachment(Model $model, String $directory)
    {
        Storage::disk('travel-claim-attachment')
            ->delete('/'.$model->travel_id."/{$directory}/".$model->path);

        return $model->update([
            'path' => null
        ]);
    }

    // any new module that has seperate table for attachment, can re-use this.
    public function addMultipleFileV2(Model $main_model, Model $attachment_model, Array $data, String $directory)
    {
        foreach($data['files'] as $attachment) {
            if (! ($attachment->isValid())) {
                return 'INVALID FILE'; // LATER DECIDE WHAT TO RETURN IF FILE IS INVALID
            }

            $file_name = $attachment->getClientOriginalName();
            $file_path = str()->uuid() .'.'. $attachment->getClientOriginalExtension();
            Storage::disk($directory)->putFileAs(
                path: '/' . $main_model->id,
                file: $attachment,
                name: $file_path
            );

            // Attachment Model (dynamic)
            $attachment_model->store(
                main_model_id: $main_model->id,
                path: $file_path,
                name: $file_name,
            );
        }
    }

    // any new module that has seperate table for attachment, can re-use this.
    public function deleteAttachmentV2(Model $attachment_model, String $directory, String $full_path)
    {
        if (! is_null($attachment_model->id)) {
           Storage::disk($directory)->delete($full_path);

           $attachment_model->delete(); //delete record inside DB

            return 'Attachment delete successful';
        }

        return 'Attachment delete failed.';
    }
}
