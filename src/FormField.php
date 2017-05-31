<?php

namespace Hpkns\Html;

use Illuminate\Contracts\Support\Htmlable;

class FormField implements Htmlable
{
    /**
     * The id of the field.
     *
     * @var string
     */
    protected $name;

    /**
     * The content of the field.
     *
     * @var \Closure
     */
    protected $content;

    /**
     * The $default value for the field.
     *
     * @var mixed
     */
    protected $default;

    /**
     * The attributes.
     *
     * @var array
     */
    public $attributes = [];

    /**
     * Initilize the field.
     *
     * @param string   $name
     * @param \Closure $content
     * @param array    $attributes
     */
    public function __construct($name, $content, $default = null,  array $attributes = [])
    {
        $this->name = $name;
        $this->content = $content;
        $this->default = $default;
        $this->attributes = $attributes;
    }

    /**
     * Return the HTML version of the group.
     *
     * @return string
     */
    public function toHtml()
    {
        $callback = $this->content;
        return $callback(app('form'), $this->name, $this->default, $this->attributes);
    }

    /**
     * Get the HTML string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toHtml();
    }

    /**
     * Add one or more classes.
     *
     * @param  mixed $class
     * @return $this
     */
    public function addClass(...$classes) {
        if (is_array($classes[0])) {
            $classes = $classes[0];
        }

        $list = explode(' ', array_get($this->attributes, 'class', ''));

        foreach ($classes as $class) {
            if (! in_array($class, $list)) {
                $list[] = $class;
            }
        }

        array_set($this->attributes, 'class', trim(implode(' ', $list)));

        return $this;
    }

    /**
     * Convert method calls to arguments.
     *
     * @param  string $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($method, $options)
    {
        if (empty($options)) {
            $this->attributes[] = $method;
        } else {
            $this->attributes[$method] = $options[0];
        }
    }
}
