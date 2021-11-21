<?php

namespace App\Models;

use App\Tools\Markdowner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class FakeArticles extends Model
{
    use Searchable;
    public function toSearchableArray()
    {
        return $this->only('title', 'author', 'create_date', 'content');
    }
}
