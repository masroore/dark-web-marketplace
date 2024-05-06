<?php

namespace App;

use Cache;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Category.
 *
 * @property int $id
 * @property int $parent_id
 * @property string $title
 * @property int $priority
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category wherePriority($value)
 *
 * @mixin \Eloquent
 */
class Category extends Model
{
    protected $table = 'categories';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public $fillable = [
        'parent_id', 'title', 'priority',
    ];

    /** @var Collection */
    private static $_categories;

    /** @var Collection */
    private static $_parents;

    /** @var Collection */
    private static $_childrens;

    private static function group(): void
    {
        if (self::$_categories == null || self::$_parents == null || self::$_childrens == null) {
            self::$_categories = Cache::remember('categories', 60, fn () => Category::get());

            self::$_parents = (clone self::$_categories)->filter(fn ($item) => $item->parent_id === null);

            self::$_childrens = (clone self::$_categories)->filter(fn ($item) => $item->parent_id !== null);
        }
    }

    /**
     * Return main categories.
     *
     * @return Category[]|Collection
     */
    public static function main()
    {
        self::group();

        return self::$_parents;
    }

    /**
     * @return Category[]|Collection
     */
    public static function allChildren()
    {
        self::group();

        return self::$_childrens;
    }

    public static function getById($categoryId)
    {
        self::group();
        if (is_array($categoryId)) {
            return (clone self::$_categories)->whereIn('id', $categoryId);
        }

        return (clone self::$_categories)->where('id', $categoryId);
    }

    /**
     * Returns children categories.
     */
    public function children()
    {
        self::group();
        if (!$this->isMain()) {
            throw new Exception('This category has no children.');
        }

        return (clone self::$_categories)->where('parent_id', $this->id);
    }

    /**
     * @return Category
     */
    public function parent()
    {
        self::group();

        return self::getById($this->parent_id)->first();
    }

    /**
     * Return true if current category is one of main categories.
     *
     * @return bool
     */
    public function isMain()
    {
        return $this->parent_id === null;
    }
}
