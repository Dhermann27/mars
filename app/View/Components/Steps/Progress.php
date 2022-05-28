<?php

namespace App\View\Components\Steps;

use Illuminate\View\Component;

class Progress extends Component
{
    /**
     * Previous step route name
     *
     * @var string
     */
    public $previous;

    /**
     * Width of positive progress
     *
     * @var integer
     */
    public $width;

    /**
     * Next step route name
     *
     * @var bool
     */
    public $next;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($width, $previous = '#', $next = '#')
    {
        $this->width = $width * 100;
        $this->previous = $previous;
        $this->next = $next;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.steps.progress');
    }
}
