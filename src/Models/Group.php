<?php
/**
 * The file is part of Notadd
 *
 * @author: AllenGu<674397601@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime: 17-7-24 下午5:08
 */
namespace Notadd\Slide\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Group.
 */
class Group extends Model
{
    /**
     * @var string
     */
    protected $table = 'ext_slide_groups';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pictures()
    {
        return $this->hasMany('Notadd\Slide\Models\Picture');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('Notadd\Slide\Models\Category');
    }
}