<?php

use App\Models\Program;
use App\Models\Yearattending;

$factory->define(Yearattending::class, function () {
    return [
        'days' => 6,
        'program_id' => function () {
            return Program::factory()->create()->id;
        }
    ];
}
}


