<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Articles extends Model
{
    use Searchable;
    protected  $table = 'articles';

    protected $dates = ['cearteDate'];

    protected $fillable = [
        'title',
        'auther',
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

    public function toSearchableArray()
    {
        $data = [
            'title' => $this->title,
            'author' => $this->author,
            'content' => $this->content,
            'cearteDate' => $this->cearteDate
        ];

        return $data;
    }

}
