<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    private $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = new Category();
    }

    public function testFillable()
    {
        $fillable = ['name','description','is_active'];
        $this->assertEquals($fillable, $this->category->getFillable());
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];

        $categoryTraits = array_keys(class_uses(Category::class));

        $this->assertEquals($traits, $categoryTraits);
    }

    public function testCastsAttributes()
    {
        $casts = ['id' => 'string', 'is_active' => 'bool'];
        $this->assertEquals($casts, $this->category->getCasts());
    }

    public function testIncrementingAttributes()
    {
        $this->assertFalse($this->category->incrementing);
    }

    public function testDatesAttributes()
    {
        $dates = ['created_at', 'updated_at', 'deleted_at'];
//        dd(['Esperado' => $dates, 'Atual' => $category->getDates()]);
        foreach ($dates as $date) {
            $this->assertContains($date, $this->category->getDates());
        }
        $this->assertCount(count($dates), $this->category->getDates());
    }
}
