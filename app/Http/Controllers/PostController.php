<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Post;
use \App\Comment;
use  \App\Zan;
use Visitor;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //laravel队列突然不能用了  记得php artisan queue:work
        //todo 2.  CACHE_DRIVER=file SESSION_DRIVER=file QUEUE_DRIVER=database改成   redis缓存
        //todo laravel延迟服务提供者怎么做
        //todo 整理一下Chrome的书签
        //todo:优化查询 如何建立mysql索引等等
        //todo 找出所有性能可以优化的部分
        //todo: 学习使用Swoole2.0
        //Appserverceprovider和config/app.php有啥区别
        //laravel 文档序列化
        //laravel 文档序列化
        ///dblisten 是什么
        //laravel预加载问题with('user')有什么用，查看laravel官方文档
        //使用预加载提升性能。一种是with一种是load
        /* //第一种办法
         $posts = Post::orderBy('created_at', 'desc')->withCount(['comments', 'zans'])->with('user')->paginate(6);*/
        $posts = Post::orderBy('created_at', 'desc')->withCount(['comments', 'zans'])->paginate(6);
        //第二种办法
        $posts->load('user');
        return view('post/index', compact('posts'));
    }

    public function create()
    {

        return view('post/create');

    }

    public function store(Request $request)
    {
        //验证
        $this->validate($request, [
            'title' => 'required|string|max:100|min:5',
            'content' => 'required|string|min:10'
        ]);
        //逻辑
        $user_id = \Auth::id();
        $params = array_merge(request(['title', 'content']), compact('user_id'));
        //渲染
        if (Post::create($params))
            return redirect('/posts');

    }

    public function show(Post $post)
    {
        //todo:浏览量出现问题，统计不准确  屏蔽了127.0.0.1
        /*$post_id = $post->id;
        Visitor::log($post_id);*/
        $post->load('comments');//预加载
        $post->load('user');//预加载
        return view('post/show', compact('post'));
    }

    public function edit(Post $post)
    {
        return view("post/edit", compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        //权限判定
        $this->authorize('update', $post);
        //验证
        $this->validate($request, [
            'title' => 'required|string|max:100|min:5',
            'content' => 'required|string|min:10',
        ]);

        //逻辑
        $post->title = request('post');
        $post->content = request('content');
        $post->save();

        //渲染
        return redirect("/posts/{$post->id}");

    }

    public function delete(Post $post)
    {
        //权限判定
        $this->authorize('delete', $post);
        $post->delete();
        //return redirect("/posts")->withErrors('删除成功');
        //return redirect()->back()->withInput()->withErrors('删除成功！');
        return redirect("/posts")->withErrors('删除成功！');
    }

    //上传图片
    public function imageUpload(Request $request)
    {
        $path = $request->file('wangEditorH5File')->storePublicly(md5(time()));
        return asset('storage/' . $path);
        // dd(request()->all());
    }

    //提交评论
    public function comment(Post $post)
    {
        //验证
        $this->validate(request(), [
            'content' => 'required|min:3'
        ]);
        //逻辑
        $comment = new Comment();

        $comment->user_id = \Auth::id();
        $comment->content = request('content');
        $post->comments()->save($comment);
        //渲染
        return back();
    }

    //赞
    public function zan(Post $post, Zan $zan)
    {
        $param = [
            'user_id' => \Auth::id(),
            'post_id' => $post->id,
        ];

        $zan::firstOrCreate($param);

//        return back();  //原先版本  现在改成ajax版本

        return [
            'error' => 0,
            'msg' => ''
        ];
    }

    //取消赞
    public function unzan(Post $post)
    {
        $post->zan(\Auth::id())->delete();
//        return back(); //原先版本  现在改成ajax版本

        return [
            'error' => 0,
            'msg' => ''
        ];
    }

    //搜索结果页
    public function search()
    {
        //验证
        $this->validate(request(), [
                'query' => 'required'
            ]
        );
        //逻辑
        $query = request('query');
        $posts = \App\Post::search($query)->paginate(2);
        //渲染
        return view('post/search', compact('posts', 'query'));
    }
}
