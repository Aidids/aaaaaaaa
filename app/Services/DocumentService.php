<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;


class DocumentService
{
    public function __construct()
    {
        $this->profile = new ProfileServices();
    }

    public function get(int $id, $query)
    {
        if (is_null($query)) {
            return Document::where('user_id', '=', $id)->paginate(5);
        }

        return Document::where('user_id', '=', $id)
            ->where('name', 'like', "%{$query}%")
            ->paginate(5);
    }

    public function upload(int $id, Array $data)
    {
        $user = $this->profile->getProfile($id);

        $file = file_get_contents($data['file']);
        $fileName = $user->id.'_'.time().'.'.$data['file']->getClientOriginalExtension();
        Storage::disk('profile')->put($fileName, $file);

        return Document::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'path' => $fileName
        ]);
    }

    public function edit(int $id, Array $data)
    {
        $user = $this->profile->getProfile($id);

        $document = Document::findOrFail($data['id']);

        $path = $document->path;

        if ( is_null($data['file'] ?? null) ) {
            $document->update($data);
            return $document;
        }

        Storage::disk('profile')->delete($path);

        $file = file_get_contents($data['file']);
        $fileName = $user->id.'_'.time().'.'.$data['file']->getClientOriginalExtension();
        Storage::disk('profile')->put($fileName, $file);

        $document->update([
            'name' => $data['name'],
            'path' => $fileName
        ]);

        return $document;
    }

    public function delete(int $id)
    {
        $document = Document::findOrFail($id);
        $name = $document->name;
        Storage::disk('profile')->delete($document->path);
        $document->delete();

        return 'successfully deleted '.$name;
    }
}
