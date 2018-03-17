<div>
    @if($post->zan(\Auth::id())->exists())
        <button class="btn btn-default btn-lg" zan-value="1" zan-post="{{$target_post->id}}" type="button">取消赞</button>
    @else
        <button class="btn btn-primary btn-lg" zan-value="0" zan-post="{{$target_post->id}}" type="button">赞</button>
    @endif

</div>