{{--<div class="container px-3 py-5 px-lg-4 py-lg-6 bg-grey mb-5">--}}
{{--@foreach($programs as $program)--}}
{{--@component('snippet.blog', ['title' => $program->name])--}}
<article class="mb-5 pr-xl-6" data-animate="fadeIn" data-animate-delay="0.1">
    <h2 class="text-slab">{!!  $title !!}</h2>

    {{ $slot }}

</article>
<hr class="hr-lg my-5"/>
{{--@endcomponent--}}
{{--@endforeach--}}
{{--</div>--}}
