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
<!--==================== Blog Section Start ====================-->
<div class="full-row">
   <div class="container">
      <div id="ajaxContent">
         <div class="row">
            <div class="col-lg-4 md-mb-50">
               <div id="sidebar" class="sidebar-blog bg-light p-30">
                  <div class="widget border-0 py-0 search-widget">
                     <form action="{{ route('front.blogsearch') }}" method="GET">
                        <input type="text" class="form-control bg-light" name="search" placeholder="{{ __('Search Here') }}" value="{{ isset($_GET['search']) ? $_GET['search'] : '' }}" required>
                        <button type="submit" name="submit" ><i class="flaticon-search flat-mini text-red"></i></button>
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
                        @foreach (App\Models\Blog::latest()->where('language_id',$langg->id)->limit(4)->get() as $blog)
                        <li>
                           <a href="{{ route('front.blogshow',$blog->slug) }}">{{ mb_strlen($blog->title,'UTF-8') > 45 ? mb_substr($blog->title,0,45,'UTF-8')."..":$blog->title }}</a>
                           <span class="post-date">{{ date('M d - Y',(strtotime($blog->created_at))) }}</span>
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
            <div class="col-lg-8">
               <div class="row">
                  @foreach ($blogs as $blog)
                  <div class="col-md-12">
                     <div class="thumb-blog-horizontal clearfix hover-img-zoom transation mb-30">
                        <div class="post-image overflow-hidden">
                           <img class="lazy" data-src="{{ $blog->photo ? asset('assets/images/blogs/'.$blog->photo):asset('assets/images/noimage.png')}}" alt="Image not found!">
                        </div>
                        <div class="post-content ps-3">
                           <div class="post-meta font-mini text-uppercase list-color-light mb-1">
                           </div>
                           <h4 class="mb-2"><a href="{{ route('front.blogshow',$blog->slug) }}" class="transation text-dark hover-text-primary d-block">{{ mb_strlen($blog->title,'UTF-8') > 45 ? mb_substr($blog->title,0,45,'UTF-8')."..":$blog->title }}</a></h4>
                           <p>{!! mb_strlen($blog->details,'UTF-8') > 200 ? mb_substr($blog->details,0,200,'UTF-8')."..":$blog->details !!}</p>
                           <div class="date text-primary font-small text-uppercase"><span>{{ date('M d - Y',(strtotime($blog->created_at))) }}</span></div>
                        </div>
                     </div>
                  </div>
               </div>
               @endforeach
               <div class="col-lg-12 mt-3">
                  <div class="d-flex justify-content-center align-items-center pt-3" id="custom-pagination">
                     <div class="pagination-style-one">
                        <nav aria-label="Page navigation example">
                           <ul class="pagination">
                              {{ $blogs->links() }}
                           </ul>
                        </nav>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
<!--==================== Blog Section End ====================-->
@includeIf('partials.global.common-footer')
@endsection
