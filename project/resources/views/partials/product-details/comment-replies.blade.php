<div class="row">
    <div class="col-8">
        <div id="comments">

            @if (Auth::check())
            <div class="review-area mt-5">
                <h4 class="title">{{ __('Write Comment') }}</h4>
              </div>
              <div class="write-comment-area">
                <form id="comment-form" action="{{ route('product.comment') }}" method="POST">
                  {{csrf_field()}}
                  <input type="hidden" name="product_id" id="product_id" value="{{$productt->id}}">
                  <input type="hidden" name="user_id" id="user_id" value="{{Auth::user()->id}}">
                  <div class="row">
                    <div class="col-lg-12">
                    <textarea class="form-control border" placeholder="{{ __('Write Your Comments Here...') }}" name="text" required></textarea>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-lg-12">
                      <button class="submit-btn mybtn1" type="submit">{{ __('Post Comment') }}</button>
                    </div>
                  </div>
                </form>
              </div>
              <br>

              <ul class="all-comment">
                @if($productt->comments)
                @foreach($productt->comments()->latest()->get() as $comment)
                  <li>
                    <div class="single-comment comment-section">
                      <div class="left-area">
                        <img class="lazy" data-src="{{$comment->user->photo != null ? asset('assets/images/users/'.$comment->user->photo) : asset('assets/images/'.$gs->user_image)}}" alt="">
                        <h5 class="name">{{ $comment->user->name }}</h5>
                        <p class="date">{{ $comment->created_at->diffForHumans() }}</p>
                      </div>
                      <div class="right-area">
                        <div class="comment-body">
                          <p>
                            {{ $comment->text }}
                          </p>
                        </div>
                        <div class="comment-footer">
                          <div class="links">
                            <a href="javascript:;" class="comment-link reply mr-2"><i class="fas fa-reply "></i>{{ __('Reply') }}</a>
                            @if(count($comment->replies) > 0)
                            <a href="javascript:;" class="comment-link view-reply mr-2"><i class="fas fa-eye "></i>{{ __('View ') }} {{ count($comment->replies) == 1 ? __('Reply') : __('Replies')  }}</a>
                            @endif
                          @if(Auth::user()->id == $comment->user->id)
                            <a href="javascript:;" class="comment-link edit mr-2"><i class="fas fa-edit "></i>{{ __('Edit') }}</a>
                            <a href="javascript:;" data-href="{{ route('product.comment.delete',$comment->id) }}" class="comment-link comment-delete mr-2"><i class="fas fa-trash"></i>{{ __('Delete') }}</a>
                          @endif
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="replay-area edit-area d-none">
                      <form class="update" action="{{ route('product.comment.edit',$comment->id) }}" method="POST">
                        {{csrf_field()}}
                        <textarea placeholder="{{ __('Edit Your Comment') }}" name="text" required=""></textarea>
                        <button type="submit">{{ __('Submit') }}</button>
                        <a href="javascript:;" class="remove cmn-rmv">{{ __('Cancel') }}</a>
                      </form>
                    </div>
                @if($comment->replies)
                  @foreach($comment->replies as $reply)
                    <div class="single-comment replay-review d-none">
                      <div class="left-area">
                        <img class="lazy" data-src="{{ $reply->user->photo != null ? asset('assets/images/users/'.$reply->user->photo) : asset('assets/images/'.$gs->user_image) }}" alt="">
                        <h5 class="name">{{ $reply->user->name }}</h5>
                        <p class="date">{{ $reply->created_at->diffForHumans() }}</p>
                      </div>
                      <div class="right-area">
                        <div class="comment-body">
                          <p>
                            {{ $reply->text }}
                          </p>
                        </div>
                        <div class="comment-footer">
                          <div class="links">

                            <a href="javascript:;" class="comment-link reply mr-2"><i class="fas fa-reply "></i>{{ __('Reply') }}</a>
                          @if(Auth::user()->id == $reply->user->id)
                            <a href="javascript:;" class="comment-link edit mr-2"><i class="fas fa-edit "></i>{{ __('Edit') }}</a>
                            <a href="javascript:;" data-href="{{ route('product.reply.delete',$reply->id) }}" class="comment-link reply-delete mr-2"><i class="fas fa-trash"></i>{{ __('Delete') }}</a>
                          @endif
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="replay-area edit-area d-none">
                      <form class="update" action="{{ route('product.reply.edit',$reply->id) }}" method="POST">
                        {{csrf_field()}}
                        <textarea placeholder="{{ __('Edit Your Reply') }}" name="text" required=""></textarea>
                        <button type="submit">{{ __('Submit') }}</button>
                        <a href="javascript:;"  class="remove cmn-rmv">{{ __('Cancel') }}</a>
                      </form>
                    </div>
                  @endforeach
                @endif

                    <div class="replay-area reply-reply-area d-none">
                      <form class="reply-form" action="{{ route('product.reply',$comment->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                        <textarea placeholder="{{ __('Write Your Your Reply') }}" name="text" required=""></textarea>
                        <button type="submit">{{ __('Submit') }}</button>
                        <a href="javascript:;" class="remove cmn-rmv">{{ __('Cancel') }}</a>
                      </form>
                    </div>

                  </li>
                @endforeach
                @endif
                </ul>


                @else
                <div class="row">
                <div class="col-lg-12">
                <br>
                  <h5 class="text-center"><a href="{{ route('user.login') }}"  class="btn login-btn">{{ __('Login') }}</a> {{ __('To Comment') }} </h5>
                <br>
                </div>
                </div>

                @if($productt->comments)
                <ul class="all-comment">

                  @foreach($productt->comments()->latest()->get() as $comment)

                  <li>
                    <div class="single-comment">
                      <div class="left-area">
                        <img class="lazy" data-src="{{$comment->user->photo != null ? asset('assets/images/users/'.$comment->user->photo) : asset('assets/images/'.$gs->user_image)}}" alt="">
                        <h5 class="name">{{ $comment->user->name }}</h5>
                        <p class="date">{{ $comment->created_at->diffForHumans() }}</p>
                      </div>
                      <div class="right-area">
                        <div class="comment-body">
                          <p>
                            {{$comment->text}}
                          </p>
                        </div>

                        @if(count($comment->replies) > 0)
                        <div class="comment-footer">
                          <div class="links">
                            <a href="javascript:;" class="comment-link view-reply mr-2"><i class="fas fa-eye "></i>{{ __('View ') }} {{ count($comment->replies) == 1 ? __('Reply') : __('Replies')  }}</a>
                          </div>
                        </div>
                        @endif

                      </div>
                    </div>

                @if($comment->replies)
                  @foreach($comment->replies()->latest()->get() as $reply)
                    <div class="single-comment replay-review d-none">
                      <div class="left-area">
                        <img class="lazy" data-src="{{ $reply->user->photo != null ? asset('assets/images/users/'.$reply->user->photo) : asset('assets/images/'.$gs->user_image) }}" alt="">
                        <h5 class="name">{{ $reply->user->name }}</h5>
                        <p class="date">{{ $reply->created_at->diffForHumans() }}</p>
                      </div>
                      <div class="right-area">
                        <div class="comment-body">
                          <p>
                            {{ $reply->text }}
                          </p>
                        </div>

                      </div>
                    </div>
                  @endforeach
                @endif
                  </li>
                  @endforeach
                </ul>
                @endif
                @endif
        </div>
    </div>
</div>
