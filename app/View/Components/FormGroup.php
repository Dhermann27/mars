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
     * The form element value.
     *
     * @var string
     */
    public $value;

    /**
     * The form element name with loop value, for the errors() array .
     *
     * @var string
     */
    public $errorKey;

    /**
     * The Model object that could contain the default value.
     *
     * @var string
     */
    public $formobject;

    /**
     * If the element is only viewable by PC members
     *
     * @var bool
     */
    public $isAdminonly;

    /**
     * If the element should be read-only
     *
     * @var bool
     */
    public $isReadonly;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($label = null, $name = null, $value = null, $type = 'text', $errorKey = null,
                                $formobject = null, $isAdminonly = false, $isReadonly = false)
    {
        $this->name = $name;
        $this->label = $label;
        $this->value = $value;
        $this->type = $type;
        $this->errorKey = $errorKey ? $errorKey : $name;
        $this->formobject = $formobject;
        $this->isAdminonly = $isAdminonly;
        $this->isReadonly = $isReadonly;
    }

    public function getSafeDefault()
    {
        $name = preg_replace('/\\[\\]/', '', $this->name);
        $value = $this->formobject->$name ?? $this->value;
        return old($this->errorKey, $value);
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
