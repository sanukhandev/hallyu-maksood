    <div class="row">
        @foreach($blogs as $blogg)

        <div class="col-lg-6 col-md-6 mycol">
            <div class="single-blog">
                <div class="img">
                <img src="{{  $blogg->photo ? asset('assets/images/blogs/'.$blogg->photo):asset('assets/images/noimage.png') }}" alt="">
                <div class="date">
                {{ date('d M, Y',strtotime($blogg->created_at)) }}
                </div>
                </div>
                <div class="content">
                <a href="{{ route('front.blogshow',$blogg->id) }}">
                    <h4 class="title">
                        {{ mb_strlen($blogg->title,'UTF-8') > 200 ? mb_substr($blogg->title,0,200,'UTF-8')."...":$blogg->title }}
                    </h4>
                </a>
                <ul class="top-meta">
                    <li>
                    <a href="javascript:;"><i class="far fa-comments"></i> {{ $blogg->source }} </a>
                    </li>
                    <li>
                    <a href="javascript:;">
                        <i class="far fa-eye"></i> {{ $blogg->views }} 
                    </a>
                    </li>
                </ul>
                </div>
            </div>
        </div>

        @endforeach

    </div>

    <div class="page-center">

        {!! $blogs->links() !!}   
            
    </div>