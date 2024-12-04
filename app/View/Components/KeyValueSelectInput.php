<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Database\Eloquent\Collection;

class KeyValueSelectInput extends Component
{
    public $options;
    public $label;
    public $name;
    public $id;

    /**
     * Create a new component instance.
     *
     * @param Collection $options
     * @param string $label
     * @param string $name
     * @param string $id
     */
    public function __construct(iterable $options, $label = 'Выберите опцию', $name = 'select', $id = 'select')
    {
        $this->options = $options;
        $this->label = $label;
        $this->name = $name;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.key-value-select-input');
    }
}