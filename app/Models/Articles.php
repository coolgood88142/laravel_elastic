<?php

namespace App\Models;

use App\Tools\Markdowner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Articles extends Model
{
    use Searchable;
    protected  $table = 'articles';

    protected $dates = ['cearteDate'];

    protected $fillable = [
        'title',
        'author',
        'content',
    ];

    /**
     * Set the content attribute.
     *
     * @param $value
     */
    public function setContentAttribute($value)
    {
        $data = [
            'raw'  => $value,
            'html' => (new Markdowner)->convertMarkdownToHtml($value)
        ];

        $this->attributes['content'] = json_encode($data);
    }

    public function searchableAs()
    {
        return 'elastic';
    }

    public function toSearchableArray()
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'content' => $this->content,
            'createDate' => $this->create_date,
        ];

        return $data;
    }

}
