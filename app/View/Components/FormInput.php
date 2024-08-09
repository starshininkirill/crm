<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FormInput extends Component
{
    public $type;
    public $name;
    public $placeholder;
    public $label;
    public $value;
    public $required;

    /**
     * Create a new component instance.
     *
     * @param string $type
     * @param string $name
     * @param string $placeholder
     * @param string $label
     * @param string|null $value
     * @param bool $required
     * @return void
     */
    public function __construct($type, $name, $placeholder, $label, $value = null, $required = false)
    {
        $this->type = $type;
        $this->name = $name;
        $this->placeholder = $placeholder;
        $this->label = $label;
        $this->value = $value;
        $this->required = $required;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.form-input');
    }
}