<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $categories = factory(Category::class)->create();
        $categories->refresh();

        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertSee(json_encode($categories->toArray()));
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $category->refresh();
        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response
            ->assertStatus(200)
            ->assertSee(json_encode($category->toArray()));
    }

    public function testInvalidationData()
    {
        $response = $this->json(
            'POST',
            route('categories.store'),
            []
        );

        $this->assertInvalidationRequired($response);

        $response = $this->json(
            'POST',
            route('categories.store'), ['name' => str_repeat('a', 256),
            'is_active' => 'a'
            ]
        );

        $this->assertInvalidationMax($response);

        $category = factory(Category::class)->create();
        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            []
        );

        $this->assertInvalidationRequired($response);

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]), ['name' => str_repeat('a', 256),
            'is_active' => 'a'
            ]
        );

        $this->assertInvalidationMax($response);
    }

    protected function assertInvalidationRequired(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors('is_active')
            ->assertJsonFragment([
                \Lang::get('validation.required', ['attribute' => 'name'])
            ]);
    }

    protected function assertInvalidationMax(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'is_active'])
            ->assertJsonFragment([
                \Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ])
            ->assertJsonFragment([
                \Lang::get('validation.boolean', ['attribute' => 'is active'])
            ]);
    }

    public function testStore()
    {
        $response = $this->json(
            'POST',
            route('categories.store'), ['name' => 'Teste 1']
        );

        $id = $response->json('id');

        $category = Category::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $response = $this->json(
            'POST',
            route('categories.store'), [
                'name' => 'Teste',
                'description' => 'description test',
                'is_active' => false
            ]
        );

        $response
            ->assertJsonFragment([
                'is_active' => false,
                'description' => 'description test'
            ]);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'description' => 'Description',
            'is_active' => false
        ]);

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            [
                'name' => 'Test update',
                'description' => 'description update',
                'is_active' => true
            ]
        );

        $id = $response->json('id');

        $category = Category::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());

        $response->assertJsonFragment([
            'description' => 'description update',
            'is_active' => true
        ]);

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            [
                'name' => 'Test  2',
                'description' => ''
            ]
        );

        $response->assertJsonFragment([
            'description' => null,
        ]);
    }

    public function testDestroy()
    {
        $category = factory(Category::class)->create();

        $response = $this->json(
            'DELETE',
            route('categories.destroy', ['category' => $category->id]), []
        );

        $response
            ->assertStatus(204);

    }
}
