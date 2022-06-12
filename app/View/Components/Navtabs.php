<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Navtabs extends Component
{
    /**
     * The tabs to be displayed.
     *
     * @var mixed
     */
    public $tabs;

    /**
     * The attribute of the tabs to be used as an identifier.
     *
     * @var string
     */
    public $id;

    /**
     * The attribute of the tabs to be used as an value.
     *
     * @var string
     */
    public $option;

    /**
     * The index of the tab to be displayed.
     *
     * @var int
     */
    public $activeTab;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($tabs, $activeTab = 0, $id = 'id', $option = 'value')
    {
        $this->tabs = $tabs;
        $this->id = $id;
        $this->option = $option;
        $this->activeTab = $activeTab;
    }

    public function getValue($i)
    {
        $option = $this->option;
        return $this->option == 'fullname' ? $this->tabs[$i]->firstname . ' ' . $this->tabs[$i]->lastname :
            ($this->tabs[$i]->$option ?? $this->tabs[$i]);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.navtabs');
    }
}
