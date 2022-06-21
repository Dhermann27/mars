<?php

namespace App\View\Components;

use App\Models\Workshop;
use Illuminate\View\Component;

class WorkshopButton extends Component
{

    /**
     * The id for the button.
     *
     * @var string
     */
    public $id;

    /**
     * The workshop for which to create a button.
     *
     * @var Workshop
     */
    public $workshop;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id, $workshop)
    {
        $this->id = $id;
        $this->workshop = $workshop;
    }

    /**
     * Return the formatted blurb to be displayed.
     * @return string
     */
    public function getBlurb()
    {
        $blurb = '';
        if($this->workshop->enrolled >= $this->workshop->capacity) {
            $blurb = '<span class="shop-note-full">Workshop Full</span>';
        } elseif($this->workshop->enrolled >= ($this->workshop->capacity * .75)) {
            $blurb = '<span class="shop-note-fill">Filling Fast</span>';
        }
        return $blurb . 'Led by: ' . $this->workshop->led_by . '<br /><hr />' . $this->workshop->blurb;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.workshop-button');
    }
}
