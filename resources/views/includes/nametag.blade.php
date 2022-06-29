<div class="label" dusk="label-{{ $loop->index }}"
     style="font-family: {{ $camper->yearattending->fontapply == '2' ? $camper->yearattending->font_value : 'Jost' }};">
    <div class="name"
         style="font-weight: bold; font-family: {{ $camper->yearattending->font_value }}; font-size: {{ $camper->yearattending->namesize*.5+.8 }}em;">
        {{ $camper->yearattending->name_value }}
    </div>
    <div class="surname">{{ $camper->yearattending->surname_value  }}</div>
    <div class="line1">{{ $camper->yearattending->line1_value }}</div>
    <div class="line2">{{ $camper->yearattending->line2_value }}</div>
    <div class="line3">{{ $camper->yearattending->line3_value }}</div>
    <div class="line4">{{ $camper->yearattending->line4_value }}</div>
    @if($camper->age<18)
        <div class="parent" dusk="parent-{{ $loop->index }}">
            @php
                $parents = ""; // TODO: Gross
                $pyas = $camper->parents->sortBy('camper.birthdate');
                if (count($pyas) == 2) {
                    if (($pyas[0]->camper->pronoun_id == App\Enums\Pronounname::HeHim && $pyas[1]->camper->pronoun_id == App\Enums\Pronounname::SheHer)
                        || ($pyas[1]->camper->pronoun_id == App\Enums\Pronounname::HeHim && $pyas[0]->camper->pronoun_id == App\Enums\Pronounname::SheHer)) {
                        $icon = '<i class="fa-solid fa-family" dusk="icon-' . $loop->index . '"></i>';
                    } elseif ($pyas[0]->camper->pronoun_id == App\Enums\Pronounname::SheHer && $pyas[1]->camper->pronoun_id == App\Enums\Pronounname::SheHer) {
                        $icon = '<i class="fa-solid fa-family-dress" dusk="icon-' . $loop->index . '"></i>';
                    } else {
                        $icon = '<i class="fa-solid fa-family-pants" dusk="icon-' . $loop->index . '"></i>';
                    }
                } elseif (count($pyas) == 1) {
                        $icon = '<span class="fa-layers">
                                <i class="fa-solid fa-person" dusk="icon-' . $loop->index . '" data-fa-transform="grow-10 left-2 down-1" style="color: darkgray"></i>
                                <i class="fa-solid fa-child" data-fa-transform="right-5 down-6"></i>
                            </span>';
                        if ($pyas[0]->camper->pronoun_id == App\Enums\Pronounname::SheHer) {
                            $icon = preg_replace('/fa-person/', 'fa-person-dress', $icon);
                        }
                } elseif (count($pyas) == 0) {
                    $icon = '<span dusk="icon-' . $loop->index . '">SPONSOR NEEDED</span>';
                } else {
                    $icon = '<i class="fa-solid fa-people-group" dusk="icon-' . $loop->index . '"></i>';
                }
            @endphp
            {!! $icon !!} {{ isset($pyas[0]->camper) ? $pyas[0]->camper->firstname . " " . $pyas[0]->camper->lastname : '' }}
        </div>
    @endif
    <div class="pronoun {{ $camper->yearattending->pronoun == "1" ? 'd-none' : '' }}">
        {{ $camper->yearattending->pronoun_value }}
    </div>
</div>
