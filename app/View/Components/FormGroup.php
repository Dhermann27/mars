<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FormGroup extends Component
{

    /**
     * The form element type (text, select, textarea, submit...).
     *
     * @var string
     */
    public $type;

    /**
     * The form element id/name.
     *
     * @var string
     */
    public $name = 'none';

    /**
     * The form element label.
     *
     * @var string
     */
    public $label;

    /**
     * Checkbox or radio selected state
     *
     * @var bool
     */
    public $checked;

    /**
     * The form element readonly placeholder text
     *
     * @var string
     */
    public $readonly;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($label, $name = null, $type = 'text', $checked = false, $readonly = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->checked = $checked;
        $this->readonly = $readonly;
    }

    /**
     * Determine if Dusk is running
     *
     * @return bool
     */
    public function isDusk()
    {
        return config('app.name') == 'MUUSADusk';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form-group');
    }
}
