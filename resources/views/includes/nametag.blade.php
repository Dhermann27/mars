<div class="label{{ $camper->yearattending->fontapply == '2' ? ' ' . $camper->yearattending->font_value : '' }}">
    <div class="name {{ $camper->yearattending->font_value }}"
         style="font-size: {{ $camper->yearattending->namesize+1 }}em;">{{ $camper->yearattending->name_value }}</div>
    <div class="surname">{{ $camper->yearattending->surname_value  }}</div>
    <div class="pronoun">{{ $camper->yearattending->pronoun_value }}</div>
    <div class="line1">{{ $camper->yearattending->line1_value }}</div>
    <div class="line2">{{ $camper->yearattending->line2_value }}</div>
    <div class="line3">{{ $camper->yearattending->line3_value }}</div>
    <div class="line4">{{ $camper->yearattending->line4_value }}</div>
    @if($camper->age<18)
        <div class="parent"><i
                class="fa fa-id-card"></i> {{ $camper->parents->first()->firstname }} {{ $camper->parents->first()->lastname }}
        </div>
    @endif
</div>
