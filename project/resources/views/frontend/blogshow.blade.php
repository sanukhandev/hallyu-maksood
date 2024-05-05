@extends('layouts.front')
@section('content')
@include('partials.global.common-header')
<!-- breadcrumb -->
<div class="full-row bg-light overlay-dark py-5" style="background-image: url({{ $gs->breadcrumb_banner ? asset('assets/images/'.$gs->breadcrumb_banner):asset('assets/images/noimage.png') }}); background-position: center center; background-size: cover;">
   <div class="container">
      <div class="row text-center text-white">
         <div class="col-12">
            <h3 class="mb-2 text-white">{{ __('Blog') }}</h3>
         </div>
         <div class="col-12">
            <nav aria-label="breadcrumb">
               <ol class="breadcrumb mb-0 d-inline-flex bg-transparent p-0">
                  <li class="breadcrumb-item"><a href="{{ route('front.index') }}">{{ __('Home') }}</a></li>
                  <li class="breadcrumb-item active" aria-current="page">{{ __('Blog') }}</li>
               </ol>
            </nav>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb -->
<!--==================== Blog Section Start ====================-->
<div class="full-row">
   <div class="container">
      <div class="row">
         <div class="col-lg-4 md-mb-50 order-lg-2">
            <div id="sidebar" class="sidebar-blog bg-light p-30">
               <div class="widget border-0 py-0 search-widget">
                  <form action="#" method="post">
                     <input type="text" class="form-control bg-light" name="search" placeholder="Search">
                     <button type="submit" name="submit" class="bg-light"><i class="flaticon-search flat-mini text-secondary"></i></button>
                  </form>
               </div>
               <div class="widget border-0 py-0 widget_categories">
                  <h4 class="widget-title down-line">{{ __('Categories') }}</h4>
                  <ul>
                     @foreach ($bcats as $cat)
                     <li><a class="{{ isset($bcat) ? ($bcat->id == $cat->id ? 'active' : '') : '' }}" href="{{ route('front.blogcategory',$cat->slug) }}">{{ $cat->name }}  ({{ $cat->blogs()->count() }}) </a></li>
                     @endforeach
                  </ul>
               </div>
               <div class="widget border-0 py-0 widget_recent_entries">
                  <h4 class="widget-title down-line">{{ __('Recent Post') }}</h4>
                  <ul>
                     @foreach (App\Models\Blog::latest()->where('language_id',$langg->id)->limit(4)->get() as $reblog)
                     <li>
                        <a href="{{ route('front.blogshow',$reblog->slug) }}">{{ mb_strlen($reblog->title,'UTF-8') > 45 ? mb_substr($reblog->title,0,45,'UTF-8')."..":$reblog->title }}</a>
                        <span class="post-date">{{ date('M d - Y',(strtotime($reblog->created_at))) }}</span>
                     </li>
                     @endforeach
                  </ul>
               </div>
               <div class="widget border-0 py-0 widget_tag_cloud">
                  <h4 class="widget-title down-line">{{ __('Tags') }}</h4>
                  <div class="tagcloud">
                     <ul>
                        @foreach($tags as $tag)
                        @if(!empty($tag))
                        <li>
                           <a class="{{ isset($slug) ? ($slug == $tag ? 'active' : '') : '' }}" href="{{ route('front.blogtags',$tag) }}">
                           {{ $tag }}
                           </a>
                        </li>
                        @endif
                        @endforeach
                     </ul>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-lg-8 order-lg-1">
            <div class="single-post">
               <div class="single-post-title">
                  <h3 class="mb-2 text-secondary">{{ $blog->title }}</h3>
                  <div class="post-meta mb-4">
                     <a href="#"><i class="flaticon-user-silhouette flat-mini"></i> <span>{{ __('By Admin') }}</span></a>
                     <a href="#"><i class="flaticon-calendar flat-mini"></i> <span>{{ date('M d - Y',(strtotime($blog->created_at))) }}</span></a>
                     <a href="#"><i class="flaticon-like flat-mini"></i> <span>{{ $blog->views }} {{ __('View(s)') }}</span></a>
                     <span><i class="flaticon-document flat-mini text-primary"></i> <a href="#"><span>{{ __('Source') }} : </span></a><a href="#"><span>{{ $blog->source }}</span></a></span>
                  </div>
               </div>
               <div class="img">
                  <img data-src="{{ asset('assets/images/blogs/'.$blog->photo) }}" class="img-fluid lazy" alt="">
               </div>
               <div class="post-content pt-4 mb-5">
                  <p>{!! clean($blog->details , array('Attr.EnableID' => true)) !!}</p>
               </div>
               <div class="share-post mt-5">
                  <span><b>{{ __('Share This Post:') }}</b></span>

                  <a class="a2a_dd plus" href="https://www.addtoany.com/share">
                  <i class="fas fa-plus"></i>
                  </a>
               </div>
               <script async src="https://static.addtoany.com/menu/page.js"></script>
               {{-- DISQUS START --}}
               @if($gs->is_disqus == 1)
               <div class="comments">
                  <div id="disqus_thread">
                     <script>
                        (function() {
                        var d = document, s = d.createElement('script');
                        s.src = 'https://{{ $gs->disqus }}.disqus.com/embed.js';
                        s.setAttribute('data-timestamp', +new Date());
                        (d.head || d.body).appendChild(s);
                        })();
                     </script>
                     <noscript>{{ __('Please enable JavaScript to view the') }} <a href="https://disqus.com/?ref_noscript">{{ __('comments powered by Disqus.') }}</a></noscript>
                  </div>
               </div>
               @endif
               {{-- DISQUS ENDS --}}
            </div>
         </div>
      </div>
   </div>
</div>
<!--==================== Blog Section End ====================-->
@includeIf('partials.global.common-footer')
@endsection
