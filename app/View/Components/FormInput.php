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

    /**
     * Create a new component instance.
     *
     * @param string $type
     * @param string $name
     * @param string $placeholder
     * @param string $label
     * @param string|null $value
     * @return void
     */
    public function __construct($type, $name, $placeholder, $label, $value = null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->placeholder = $placeholder;
        $this->label = $label;
        $this->value = $value;
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