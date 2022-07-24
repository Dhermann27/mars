<div class="label" dusk="label-{{ $index }}"
     style="font-family: {{ $camper->yearattending->fontapply == '2' ? $camper->yearattending->font_value : 'Jost' }};">
    <div class="label-container">
        <div class="name"
             style="font-weight: bold; font-family: {{ $camper->nametag->font }}; font-size: {{ $camper->yearattending->namesize*.5+.8 }}em;">
            {{ $camper->nametag->name }}
        </div>
        <div class="surname">{{ $camper->nametag->surname  }}</div>
        <div class="line1">{{ $camper->nametag->line1 }}</div>
        <div class="line2">{{ $camper->nametag->line2 }}</div>
        <div class="line3">{{ $camper->nametag->line3 }}</div>
        <div class="line4">{{ $camper->nametag->line4 }}</div>
        @if($camper->age<18)
            <div class="parent" dusk="parent-{{ $index }}">
                <i class="fas fa-{{ $camper->nametag->icon }}" dusk="icon-{{ $index }}"></i>
                {{ $camper->nametag->parent }}
            </div>
        @endif
        <div class="pronoun">{{ $camper->nametag->pronoun }}</div>
    </div>
</div>
