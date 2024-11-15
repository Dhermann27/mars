<?php

namespace App\View\Components\Steps;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class Header extends Component
{
    /**
     * URL of the link
     *
     * @var string
     */
    public $url;

    /**
     * Mixed type containing data to compare for evaluation
     *
     * @var mixed
     */
    public $stepdata;

    /**
     * String containing code representing an operation to perform
     *
     * @var string
     */
    public $operation;

    /**
     * Value to compare against using operation
     *
     * @var mixed
     */
    public $comparator;

    /**
     * If the stepper should be large
     *
     * @var bool
     */
    public $isLarge;

    /**
     * String containing FontAwesome icon to use
     *
     * @var string
     */
    public $icon;

    /**
     * Tooltip to be used if step is not available
     *
     * @var string
     */
    public $tooltip;

    /**
     * Create a new component instance.
     *
     * @param string $url
     * @param mixed $stepdata
     * @param string $operation
     * @param mixed $comparator
     * @param bool $isLarge
     * @param string $icon
     * @param string $tooltip
     * @return void
     */
    public function __construct($url, $stepdata, $icon, $isLarge = false, $operation = 'eq', $comparator = true, $tooltip = '')
    {
        $this->url = $url;
        $this->stepdata = $stepdata;
        $this->icon = $icon;
        $this->isLarge = $isLarge;
        $this->operation = $operation;
        $this->comparator = $comparator;
        $this->tooltip = $tooltip;
    }

    /**
     * Determine state of data and return state
     *
     * @return string
     */
    public function getDataState()
    {
        if (Route::currentRouteName() == $this->url . '.index') return 'stepper-active';
        if ($this->stepdata !== null || (request()->route()->hasParameter('id') && Gate::allows('is-council'))) {
            if ($this->dynamicCompare()) return 'stepper-success';
            else return 'stepper-warning';
        }
        return 'stepper-blocked';
    }

    /**
     * Determine state of icon and return FontAwesome icon
     *
     * @return string
     */
    public function getIconState()
    {
        if ($this->stepdata !== null || (request()->route()->hasParameter('id') && Gate::allows('is-council'))) {
            if ($this->dynamicCompare()) return 'fa-square-check';
            else return 'fa-diamond-exclamation';
        }
        return 'fa-do-not-enter';
    }

    /**
     * If the link should be active
     *
     * @return string
     */
    public function isLinkActive()
    {
        return $this->stepdata !== null || (request()->route()->hasParameter('id') && Gate::allows('is-council'));
    }

    /**
     *
     * @return bool
     */
    public function dynamicCompare()
    {
        switch ($this->operation) {
            case 'eq':
                return strval($this->stepdata) === strval($this->comparator);
            case 'gt':
                return $this->stepdata > $this->comparator;
            case 'gte':
                return $this->stepdata >= $this->comparator;
            case 'lt':
                return $this->stepdata < $this->comparator;
            case 'lte':
                return $this->stepdata <= $this->comparator;
            default:
                throw new \ErrorException('Bad operator: ' . $this->operation);
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.steps.header');
    }
}
