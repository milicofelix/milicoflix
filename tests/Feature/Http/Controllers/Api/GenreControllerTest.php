<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $genres = factory(Genre::class)->create();
        $genres->refresh();

        $response = $this->get(route('genres.index'));

        $response
            ->assertStatus(200)
            ->assertSee(json_encode($genres->toArray()));
    }

    public function testShow()
    {
        $genre = factory(Genre::class)->create();
        $genre->refresh();
        $response = $this->get(route('genres.show', ['genre' => $genre->id]));

        $response
            ->assertStatus(200)
            ->assertSee(json_encode($genre->toArray()));
    }

    public function testInvalidationData()
    {
        $response = $this->json(
            'POST',
            route('genres.store'),
            []
        );

        $this->assertInvalidationRequired($response);

        $response = $this->json(
            'POST',
            route('genres.store'), ['name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]
        );

        $this->assertInvalidationMax($response);

        $genre = factory(Genre::class)->create();
        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $genre->id]),
            []
        );

        $this->assertInvalidationRequired($response);

        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $genre->id]), ['name' => str_repeat('a', 256),
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
            route('genres.store'), ['name' => 'Teste 1']
        );

        $id = $response->json('id');

        $genre = Genre::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray());
        $this->assertTrue($response->json('is_active'));

        $response = $this->json(
            'POST',
            route('genres.store'), [
                'name' => 'Teste',
                'is_active' => false
            ]
        );

        $response
            ->assertJsonFragment([
                'is_active' => false,
            ]);
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'is_active' => false
        ]);

        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $genre->id]),
            [
                'name' => 'Test update',
                'is_active' => true
            ]
        );

        $id = $response->json('id');

        $genre = Genre::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());

        $response->assertJsonFragment([
            'is_active' => true
        ]);
    }

    public function testDestroy()
    {
        $genre = factory(Genre::class)->create();

        $response = $this->json(
            'DELETE',
            route('genres.destroy', ['genre' => $genre->id]), []
        );

        $response
            ->assertStatus(204);

    }
}
