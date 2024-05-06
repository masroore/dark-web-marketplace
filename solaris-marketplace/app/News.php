<?php
/**
 * File: News.php
 * This file is part of MM2-catalog project.
 * Do not modify if you do not know what to do.
 */

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\News.
 *
 * @property int $id
 * @property string $title
 * @property string $text
 * @property string $author
 * @property null|\Carbon\Carbon $created_at
 * @property null|\Carbon\Carbon $updated_at
 *
 * @method static Builder|News whereAuthor($value)
 * @method static Builder|News whereCreatedAt($value)
 * @method static Builder|News whereId($value)
 * @method static Builder|News whereText($value)
 * @method static Builder|News whereTitle($value)
 * @method static Builder|News whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class News extends Model
{
    protected $table = 'news';

    protected $primaryKey = 'id';

    protected $fillable = [
        'title', 'text', 'author',
    ];

    public function isUnread()
    {
        if (!Auth::check() || !Auth::user()->news_last_read) {
            return false;
        }

        return Auth::user()->news_last_read->lt($this->created_at);
    }
}
