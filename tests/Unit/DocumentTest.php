<?php

namespace Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\DocumentService;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_empty_document()
    {
        $user = User::factory()->create();
        $service = (new DocumentService())->get($user->id, null);
        $this->assertEmpty($service);
    }

    public function test_get_documents()
    {
        $user = User::factory()->has(Document::factory()->count(3))->create();
        $service = (new DocumentService())->get($user->id, null);
        $this->assertIsObject($service);
    }

/*TODO: Find a way to test document upload without uploading to actual storage*/
//    public function test_upload_document()
//    {
//        $data = [
//            'id' => null,
//            'name' => 'test',
//            'file' => UploadedFile::fake()->create('fakePdf.pdf', '200', 'pdf'),
//        ];
//        $user = User::factory()->create();
//        $service = (new DocumentService())->upload($user->id, $data);
//        $this->assertModelExists($service);
//    }

}
