<div class="d-flex bg-light mb-3">
    <div class="col-md-3 border-right d-none d-md-flex">
        <x-steps :stepdata="$stepdata" :is-large="false"/>
    </div>
    <div class="container-md p-3">
        {{ $slot }}

        <x-steps.progress :width="$step/8" :previous="$previous" :next="$next"/>
    </div>
</div>
