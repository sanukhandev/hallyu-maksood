<style>
    .maintance img{
        width: 100%;
        height: 100vh;
    }
</style>
<div class="maintance">
    {!! clean($gs->maintain_text , array('Attr.EnableID' => true)) !!}

</div>