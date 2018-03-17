<?php

namespace App;

use App\Model;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    //protected $table="posts2"; 自定义表名，默认为posts
    //protected $fillable=['title','content'];
    //protected $guarded=[];

    /**
     * 模型关联
     */
    use Searchable;

    //覆写内容
    //定义索引里面type
    public function searchable()
    {
        return "posts";
    }

    //定义有哪些字段需要搜索
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
        ];
    }

    //关联用户
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    //评论模型
    public function comments()
    {
        return $this->hasMany('App\Comment')->orderBy('created_at', 'desc');
    }

    //和用户进行关联
    public function zan($user_id)
    {
        return $this->hasOne('App\Zan')->where('user_id', $user_id);
    }

    //文章的所有赞
    public function zans()
    {
        return $this->hasMany('App\Zan');
    }

    /**
     * 文章与访问登记数一对多的关联关系
     */
    public function visitors()
    {
        return $this->hasMany('App\VisitorRegistry');
    }

    //属于某个作者的文章
    public function scopeAuthorBy(Builder $query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    public function postTopics()
    {
        return $this->hasMany(\App\PostTopic::class, 'post_id', 'id');
    }

    //不属于某个专题的文章
    public function scopeTopicNotBy(Builder $query, $topic_id)
    {
        return $query->doesntHave('postTopics', 'and', function ($q) use ($topic_id) {
            $q->where('topic_id', $topic_id);
        });
    }

    //匿名全局scope的方式
    //复写boot函数
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope("post_avaiable",function (Builder $builder){
            $builder->whereIn('status',[0,1]);
        });
    }
}
