<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Category::class,1)->create();

        $categories = Category::all();
        $this->assertCount(1, $categories);
        $categoryKey = array_keys($categories->first()->getAttributes());
//        var_dump(['LISTAGEM' => $categories, 'CAMPOS' => $categoryKey]);
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $categoryKey
        );
    }

    public function testCreate()
    {
        $category = Category::create([
            'name' => 'teste1'
        ]);
        $category->refresh();

        $this->assertEquals(36, strlen($category->id));
        $this->assertEquals('teste1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create([
            'name' => 'teste2',
            'description' => null
        ]);
        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'teste2',
            'description' => 'description_test'
        ]);
        $this->assertEquals('description_test', $category->description);

        $category = Category::create([
            'name' => 'teste2',
            'is_active' => true
        ]);
        $this->assertTrue($category->is_active);
    }

    public function testUpdate()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create(
            [
                'description' => 'description_CREATE',
                'is_active' => false
            ]
        );

        $data =
            [
                'name' => 'name_updated',
                'description' => 'description_update',
                'is_active' => false
            ];

        $category->update($data);

        foreach ($data as $key => $value) {
            //var_dump(['KEY => ' => $key, 'VALUE =>' => $value]);
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create();
        $category->delete();
        $result = Category::find($category->id);
        $this->assertNull($result);
    }
}
