<?php

namespace Hpkns\Html;

use Illuminate\Contracts\Support\Htmlable;

class FormGroup implements Htmlable
{
    /**
     * The attributes, like in an Eloquent model...
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * A form builder.
     *
     * @var Hpkns\Html\FormBuilder
     */
    protected $form;

    /**
     * Initialize the group.
     *
     * @param Hpkns\Html\FormBuilder $builder
     */
    public function __construct(FormBuilder $builder, $id, $label, $content, $options)
    {
        $this->builder = $builder;
        $this->setAttributes(compact('id', 'label', 'content', 'options'));

        $this->form = app('form');
    }

    /**
     * Return the first error (if any) for a given id.
     *
     * @param  string  $id
     * @return \Illuminate\Support\HtmlString
     */
    public function getError($id)
    {
        if (session()->has('errors') && session('errors')->has($id)) {
            return str_replace(':error', session('errors')->first($id), $this->errorFormat);
        }
    }

    /**
     * Set a bunch of attributes in one batch.
     *
     * @param  array $attributes
     * @return $this
     */
    public function setAttributes($attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }
    /**
     * Set an attribute, à la Eloquent.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function setAttribute($key, $value)
    {
        $method = camel_case("set_{$key}_attribute");

        if (method_exists($this, $method)) {
            $this->$method($value);
        } else {
            array_set($this->attributes, $key, $value);
        }
    }

    /**
     * Mark the field as required.
     *
     * @return $this
     */
    public function required()
    {
        $this->required = true;

        return $this;
    }

    /**
     * Add a legend to the content.
     *
     * @param  string $text
     * @return $this
     */
    public function legend($text)
    {
        $this->attributes['legend'] = $text;
        return $this;
    }

    /**
     * Change to checkbox layout.
     *
     * @return $this
     */
    public function checkbox()
    {
        $this->checkboxLayout = true;

        return $this;
    }

    /**
     * Get an attribute.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getAttribute($key, $default = null)
    {
        $method = camel_case("get_{$key}_attribute");

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return array_get($this->attributes, $key, $default);
    }

    /**
     * Magic getter wrapper.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Set an attribute.
     *
     * @param  string $key
     * @param  mixed  $value
     */
    public function __set($key, $value)
    {
        return $this->setAttribute($key, $value);
    }


    public function getHasErrorAttribute()
    {
        return session()->has('errors')
            && session('errors')->has($this->attributes['id']);
    }

    public function getControllClassAttribute()
    {
        $class = [$this->builder->controlClassBase];

        if ($this->has_error) {
            $class[] = "{$this->builder->controlClassBase}--invalid";
        }

        $class = array_merge($class, $this->attributes['attributes']);

        return $class;
    }

    public function getGroupClassAttribute()
    {
        $class = [$this->builder->classBase];

        if ($this->has_error) {
            $class[] = "{$this->builder->classBase}--with-errors";
        }

        return $class;
    }

    public function getOptionsAttribute()
    {
        $options = $this->attributes['options'];

        $class = [$this->builder->controlClassBase];

        if ($this->has_error) {
            $class[] = "{$this->builder->controlClassBase}--invalid";
        }

        if ($this->required) {
            $options[] = 'required';
        }

        $options['class'] = implode(' ', array_merge(explode(' ', array_get($options, 'class', '')), $class));

        return $options;
    }

    /**
     * Get the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        $attributes = app('html')->attributes(['class' => implode(' ', $this->group_class)]);

        $content = ($this->content)($this->form, $this->id, $this->options);
        $error = $this->getError($this->id);
        $legend = $this->legend ? str_replace(':legend', $this->legend, $this->legendFormat) : '';

        if ($this->checkboxLayout) {
            $html = "<label style=\"font-weight:normal\">{$content} <span>{$this->label}</span>{$error}</label>";
        } else {
            $label = $this->form->label($this->id, $this->label);
            $html  = "<div class=\"{$this->builder->classBase}__label\">{$label}</div>";
            $html .= "<div class=\"{$this->builder->classBase}__input\">{$content}{$legend}{$error}</div>";
        }

        return "<div{$attributes}>{$html}</div>";
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
}
