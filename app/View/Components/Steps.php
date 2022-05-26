<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Steps extends Component
{

    /**
     * Array containing data for each step of registration
     *
     * @var mixed
     */
    public $stepdata;

    /**
     * If the stepper should be large
     *
     * @var bool
     */
    public $isLarge;

    /**
     * If the stepper should be vertical
     *
     * @var bool
     */
    public $isVertical;

    /**
     * Create a new component instance.
     *
     * @param  mixed  $stepdata
     * @param  bool   $isVertical
     * @param  bool   $isLarge
     * @return void
     */
    public function __construct($stepdata, $isLarge = 1, $isVertical = 1)
    {
        $this->stepdata = $stepdata;
        $this->isLarge = $isLarge;
        $this->isVertical = $isVertical;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.steps');
    }
}
