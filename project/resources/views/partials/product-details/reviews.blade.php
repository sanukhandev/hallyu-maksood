<div class="row">
    <div class="col-8">
        <div id="comments">
            @if(count($productt->ratings) > 0)
            <h2 class="woocommerce-Reviews-title my-3"> {{ __('Ratings & Reviews') }}</h2>

            <div class="reating-area">
                <div class="stars"><span id="star-rating">{{ App\Models\Rating::normalRating($productt->id) }}</span> <i class="fas fa-star"></i></div>
              </div>

            <ul class="all-comments">
                @foreach($productt->ratings as $review)
                <li>
                  <div class="single-comment">
                    <div class="left-area">
                      <img class="lazy" data-src="{{ $review->user->photo ? asset('assets/images/users/'.$review->user->photo):asset('assets/images/'.$gs->user_image) }}" alt="">
                      <h5 class="name">
                        {{ $review->user->name }}
                      </h5>
                      <p class="date">
                        {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$review->review_date)->diffForHumans() }}
                      </p>
                    </div>
                    <div class="right-area">
                      <div class="header-area">
                        <div class="stars-area">
                          <ul class="stars">
                            <div class="ratings">
                              <div class="empty-stars"></div>
                              <div class="full-stars" style="width:{{$review->rating*20}}%"></div>
                            </div>
                          </ul>
                        </div>
                      </div>
                      <div class="comment-body">
                        <p>
                          {{ $review->review }}
                        </p>
                      </div>
                    </div>
                  </div>
                </li>
                @endforeach
              </ul>
        </div>
        @else
        <p>{{ __('No Review Found.') }}</p>
        @endif
        <div id="review_form_wrapper">
            @if(Auth::check())
            <div class="review-area">
                <h4 class="title">{{ __('Reviews') }}</h4>
                <div class="star-area">
                  <ul class="star-list">
                    <li class="stars" data-val="1">
                      <i class="fas fa-star"></i>
                    </li>
                    <li class="stars" data-val="2">
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                    </li>
                    <li class="stars" data-val="3">
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                    </li>
                    <li class="stars" data-val="4">
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                    </li>
                    <li class="stars active" data-val="5">
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="write-comment-area">
                  <div class="gocover"
                  style="background: url({{ asset('assets/images/'.$gs->loader) }}) no-repeat scroll center center rgba(45, 45, 45, 0.5);">
                </div>
                <form id="reviewform" action="{{ route('front.review.submit') }}" data-href="{{ route('front.reviews',$productt->id) }}" data-side-href="{{ route('front.side.reviews',$productt->id) }}" method="POST">
                  @csrf
                  <input type="hidden" id="rating" name="rating" value="5">
                  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                  <input type="hidden" name="product_id" value="{{ $productt->id }}">
                  <div class="row">
                    <div class="col-lg-12">
                      <textarea name="review" placeholder="{{ __('Write Your Review *') }}" required></textarea>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-12">
                      <button class="mybtn1" type="submit">{{ __('Submit') }}</button>
                    </div>
                  </div>
                </form>
              </div>
              @else
              <div class="row">
                <div class="col-lg-12">
                  <br>
                  <h5 class="text-center">
                    <a href="{{ route('user.login') }}" class="btn login-btn mr-1">
                      {{ __('Login') }}
                    </a>
                      {{ __('To Review') }}
                  </h5>
                  <br>
                </div>
            </div>
            @endif
    </div>

</div>
</div>

