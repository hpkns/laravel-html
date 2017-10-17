<?php

namespace Hpkns\Html;

use Illuminate\Support\Traits\Macroable;

class FormBuilder extends \Collective\Html\FormBuilder
{
    use Macroable;

    /**
     * Create a form group.
     *
     * @param  string   $name
     * @param  string   $label
     * @param  \Closure $content
     * @param  array    $attributes
     * @return \Illuminate\Support\HtmlString
     */
    public function group($name, $label, $content, $default, $attributes = [])
    {
        return new FormGroup($name, $label, $content, $default, $attributes);
    }

    /**
     * Create an input group.
     *
     * @param  string $type
     * @param  string $name
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $attributes
     * @return \Illuminate\Support\HtmlString
     */
    public function inputGroup($type, $name, $label, $default = null, $attributes = [])
    {
        return $this->group($name, $label, function ($name, $default, $attributes) use ($type) {
            return $this->input($type, $name, $default, $attributes);
        }, $default, $attributes);
    }

    /**
     * Create an input group.
     *
     * @param  string $name
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $attributes
     * @return \Illuminate\Support\HtmlString
     */
    public function checkboxGroup($name, $label, $default, $attributes = [])
    {
        return $this->group($name, $label, function ($name, $default, $attributes) {
            return $this->checkbox($name, '1', $default, []);
        }, $default, $attributes)->checkbox();
    }

    /**
     * Create a text group.
     *
     * @param  string $name
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $attributes
     * @return \Illuminate\Support\HtmlString
     */
    public function textGroup($name, $label, $default = null, $attributes = [])
    {
        return $this->inputGroup('text', $name, $label, $default, $attributes);
    }

    /**
     * Create an email group.
     *
     * @param  string $name
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $attributes
     * @return \Illuminate\Support\HtmlString
     */
    public function emailGroup($name, $label, $default = null, $attributes = [])
    {
        return $this->inputGroup('email', $name, $label, $default, $attributes);
    }

    /**
     * Create an date group.
     *
     * @param  string $name
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $attributes
     * @return \Illuminate\Support\HtmlString
     */
    public function dateGroup($name, $label, $default = null, $attributes = [])
    {
        return $this->inputGroup('date', $name, $label, $default, $attributes);
    }

    /**
     * Create a text group.
     *
     * @param  string $name
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $attributes
     * @return \Illuminate\Support\HtmlString
     */
    public function textareaGroup($name, $label, $default = null, $attributes = [])
    {
        return $this->group($name, $label, function ($name, $default, $attributes) {
            return $this->textarea($name, $default, $attributes);
        }, $default, $attributes);
    }

    /**
     * Create a select group.
     *
     * @param  string $name
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $attributes
     * @return \Illuminate\Support\HtmlString
     */
    public function selectGroup($name, $label, $values, $default = null, $attributes = [])
    {
        return $this->group($name, $label, function ($name, $default, $attributes) use ($values) {
            return $this->select($name, $values, $default, $attributes);
        }, $default, $attributes);
    }

    /**
     * Create a select group.
     *
     * @param  string $name
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $attributes
     * @return \Illuminate\Support\HtmlString
     */
    public function radioGroup($name, $label, $values, $default = null, $attributes = [])
    {
        return $this->group($name, $label, function ($name, $default, $attributes) use ($values) {
            $old = $this->getValueAttribute($name, $default);
            return view('html::radio-group', ['builder' => $this] + compact('builder', 'old', 'name', 'attributes', 'values'));
        }, $default, $attributes);
    }
}
