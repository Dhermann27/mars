<div class="label"
     style="font-family: {{ $camper->yearattending->fontapply == '2' ? $camper->yearattending->font_value : 'Jost' }};">
    <div class="name"
         style="font-family: {{ $camper->yearattending->font_value }}; font-size: {{ $camper->yearattending->namesize*.5+.3 }}em;">
        {{ $camper->yearattending->name_value }}
    </div>
    <div class="surname">{{ $camper->yearattending->surname_value  }}</div>
    <div class="line1">{{ $camper->yearattending->line1_value }}</div>
    <div class="line2">{{ $camper->yearattending->line2_value }}</div>
    <div class="line3">{{ $camper->yearattending->line3_value }}</div>
    <div class="line4">{{ $camper->yearattending->line4_value }}</div>
    @if($camper->age<18)
        <div class="parent"><i
                class="fa fa-id-card"></i> {{ $camper->parents->first()->firstname }} {{ $camper->parents->first()->lastname }}
        </div>
    @endif
    <div class="pronoun {{ $camper->yearattending->pronoun == "1" ? 'd-none' : '' }}">
        {{ $camper->yearattending->pronoun_value }}
    </div>
</div>
