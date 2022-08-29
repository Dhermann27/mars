<?php

namespace App\View\Components;

use App\Models\Camper;
use Illuminate\View\Component;

class CamperForm extends Component
{
    /**
     * The camper
     *
     * @var mixed
     */
    public $camper;

    /**
     * Loop variable
     *
     * @var mixed
     */
    public $looper;

    /**
     * Pronoun Collection
     *
     * @var mixed
     */
    public $pronouns;


    /**
     * Program Collection
     *
     * @var mixed
     */
    public $programs;

    /**
     * Food Option Collection
     *
     * @var mixed
     */
    public $foodoptions;

    /**
     * Tab to show (in case of error)
     *
     * @var int
     */
    public $activeTab;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Camper $camper, $looper, $pronouns, $programs, $foodoptions, $activeTab = 0)
    {
        $this->camper = $camper;
        $this->looper = $looper;
        $this->pronouns = $pronouns;
        $this->programs = $programs;
        $this->foodoptions = $foodoptions;
        $this->activeTab = $activeTab;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.camper');
    }
}
