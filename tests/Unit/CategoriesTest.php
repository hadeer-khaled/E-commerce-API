<?php

namespace Tests\Unit;
use Tests\TestCase;

use Maatwebsite\Excel\Excel as ExcelFormat; 

use Maatwebsite\Excel\Facades\Excel;
use \Maatwebsite\Excel\Concerns\FromArray ;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Category ;
use App\Models\Attachment ;
class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_categories_successfully()
    {
        $category1 = Category::create(['title' => 'Category 1']);
        $category2 = Category::create(['title' => 'Category 2']);
        Attachment::create(['filename' => 'file1.jpg', 'filetype' => 'image/jpeg', 'attachable_id' => $category1->id, 'attachable_type' => Category::class]);
        Attachment::create(['filename' => 'file1.jpg', 'filetype' => 'image/jpeg', 'attachable_id' => $category2->id, 'attachable_type' => Category::class]);

        $response = $this->getJson(route('categories.index'));

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'image'
                        ]
                    ],
                    'message',
                ]);
    }
    public function test_show_category_successfully()
    {
        $category = Category::create(['title' => 'Category 1']);

        $response = $this->getJson(route('categories.show', $category->id));

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $category->id,
                         'title' => $category->title,
                         'image' => $category->image,
                     ],
                     'message' => 'Category retrieved successfully',
                 ]);
    }

    public function test_store_category_successfully()
    {
        Storage::fake('public');
    
        $fakeImage = UploadedFile::fake()->image('category_image.jpg');
    
        $requestData = [
            'title' => 'New Category',
            'image' => $fakeImage,
        ];
    
        $response = $this->postJson(route('categories.store'), $requestData);
    
        $category = Category::first();
        $attachment = Attachment::first();
    
        $response->assertStatus(201)
                 ->assertJson([
                     'data' => [
                         'id' => $category->id,
                         'title' => 'New Category',
                         'image' => "http://localhost/storage/".$attachment->filename,
                     ],
                     'message' => 'Category created successfully',
                 ]);
    
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'title' => 'New Category',
        ]);
    
        $this->assertDatabaseHas('attachments', [
            'filename' => $attachment->filename,
            'attachable_id' => $category->id,
            'attachable_type' => Category::class,
        ]);
    
        Storage::disk('public')->assertExists($attachment->filename);
    }

    public function test_updates_category_successfully()
    {
        Storage::fake('public');
    
        $category = Category::create(['title' => 'old Category']);
        $attachment = Attachment::create([
            'filename' => 'images/old.jpg',
            'attachable_id' => $category->id,
            'attachable_type' => Category::class,
        ]);
    
        $newTitle = 'new Category';
        $response = $this->patchJson(route('categories.update', $category->id), [
            'title' => $newTitle,
            'image' => UploadedFile::fake()->image('new.png'),
        ]);
    
        $storedAttachment = Attachment::where('attachable_id', $category->id)
                                      ->where('attachable_type', Category::class)
                                      ->first();
        
        $storedFilename = $storedAttachment->filename;
    
        $response->assertStatus(201)
                 ->assertJson([
                     'data' => [
                         'id' => $category->id,
                         'title' => $newTitle,
                         'image' => "http://localhost/storage/".$storedFilename, 
                     ],
                     'message' => 'Category updated successfully',
                 ]);
    
        
        Storage::disk('public')->assertExists($storedFilename); // Check existence of the new file
        Storage::disk('public')->assertMissing('images/old.jpg'); // Check old file removal
    
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'title' => $newTitle]);
    
        $this->assertDatabaseHas('attachments', [
            'filename' => $storedFilename,
            'attachable_id' => $category->id,
            'attachable_type' => Category::class,
        ]);
    
        $this->assertDatabaseMissing('attachments', ['filename' => 'images/old.jpg']);
    }

    public function test_destroy_category_successfully()
    {
        Storage::fake('public');

        $category = Category::create(['title' => 'Category to be deleted']);
        $attachment = Attachment::create([
            'filename' => 'images/category_image.jpg',
            'attachable_id' => $category->id,
            'attachable_type' => Category::class,
        ]);

        Storage::disk('public')->put($attachment->filename, 'fake category image');

        $response = $this->deleteJson(route('categories.destroy', $category->id));

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Category deleted successfully',
                ]);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('attachments', ['attachable_id' => $category->id]);

        Storage::disk('public')->assertMissing($attachment->filename);
    }

    public function test_imports_categories_excel_file_successfully()
    {
        Excel::fake();

        $file = UploadedFile::fake()->create('categories.xlsx', 100);

        $response = $this->postJson(route('categories.import'), [
            'categories' => $file,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Import Categories successful',
                 ]);

        Excel::assertImported('categories.xlsx'); 

        $this->assertDatabaseCount('categories', 0); 
    }

}










