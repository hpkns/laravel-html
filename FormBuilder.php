<?php

namespace Hpkns\Html;

class FormBuilder extends \Collective\Html\FormBuilder
{
    /**
     * The template used to format form groups.
     *
     * @var string
     */
     protected $groupTemplate = '';

    /**
     * Return the first error (if any) for a given id.
     *
     * @param  string  $id
     * @return \Illuminate\Support\HtmlString
     */
    public function getError($id)
    {
        if (session()->has('errors') && session('errors')->has($id)) {
            return $this->toHtmlString('<span class="form-error">' . session('errors')->first($id) . '</span>');
        }
    }

    /**
     * Create a form group.
     *
     * @param  string   $id
     * @param  string   $label
     * @param  \Closure $content
     * @param  array    $options
     * @return \Illuminate\Support\HtmlString
     */
    public function group($id, $label, $content, $options = [], $checkbox = false)
    {
        $class_base = 'form__group';
        $classes = [$class_base];

        if ($error = $this->getError($id)) {
            $options = $this->addClassToOptions($options, 'form__control form__control--invalid');
            $classes[] = "{$class_base}--with-errors";
        } else {
            $options = $this->addClassToOptions($options, 'form__control');
        }

        if (array_key_exists('required', $options) || in_array('required', $options)) {
            $classes[] = "{$class_base}--required";
        }

        $classes = implode(' ', $classes);

        if ($checkbox) {
            $label = "<label style=\"font-weight:normal\">". $content($this, $id, $options) . " <span>{$label}</span>{$error}</label>";
            $content = '';
        } else {
            $label   = "<div class=\"{$class_base}__label\">" . $this->label($id, $label) . '</div>';
            $content = "<div class=\"{$class_base}__input\">" . $content($this, $id, $options) . "{$error}</div>";
        }

        return $this->toHtmlString("<div class=\"{$classes}\">{$label}{$content}</div>");
    }


    /**
     * Add a class within an attributes array.
     *
     * @param  array $options
     * @return array
     */
    public function addClassToOptions($options, $class)
    {
        return array_set($options, 'class', implode(' ', array_merge(
            explode(' ', array_get($options, 'class', '')),
            explode(' ', $class)
        )));
    }

    /**
     * Create an input group.
     *
     * @param  string $type
     * @param  string $id
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $options
     * @return \Illuminate\Support\HtmlString
     */
    public function inputGroup($type, $id, $label, $default = null, $options = [])
    {
        return $this->group($id, $label, function ($builder, $id, $options) use ($type, $default) {
            return $builder->input($type, $id, $default, $options);
        }, $options);
    }

    /**
     * Create an input group.
     *
     * @param  string $id
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $options
     * @return \Illuminate\Support\HtmlString
     */
    public function checkboxGroup($id, $label, $default, $options = [])
    {
        return $this->group($id, $label, function ($builder, $id, $options) use ($default) {
            return $builder->checkbox($id, '1', $default, []);
        }, $options, true);
    }

    /**
     * Create a text group.
     *
     * @param  string $id
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $options
     * @return \Illuminate\Support\HtmlString
     */
    public function textGroup($id, $label, $default = null, $options = [])
    {
        return $this->inputGroup('text', $id, $label, $default, $options);
    }

    /**
     * Create an email group.
     *
     * @param  string $id
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $options
     * @return \Illuminate\Support\HtmlString
     */
    public function emailGroup($id, $label, $default = null, $options = [])
    {
        return $this->inputGroup('email', $id, $label, $default, $options);
    }

    /**
     * Create an date group.
     *
     * @param  string $id
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $options
     * @return \Illuminate\Support\HtmlString
     */
    public function dateGroup($id, $label, $default = null, $options = [])
    {
        return $this->inputGroup('date', $id, $label, $default, $options);
    }

    /**
     * Create a text group.
     *
     * @param  string $id
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $options
     * @return \Illuminate\Support\HtmlString
     */
    public function textareaGroup($id, $label, $default = null, $options = [])
    {
        return $this->group($id, $label, function ($builder, $id, $options) use ($default) {
            return $builder->textarea($id, $default, $options);
        }, $options);
    }

    /**
     * Create a select group.
     *
     * @param  string $id
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $options
     * @return \Illuminate\Support\HtmlString
     */
    public function selectGroup($id, $label, $values, $default = null, $options = [])
    {
        return $this->group($id, $label, function ($builder, $id, $options) use ($default, $values) {
            return $builder->select($id, $values, $default, $options);
        }, $options);
    }

    /**
     * Create a select group.
     *
     * @param  string $id
     * @param  string $label
     * @param  mixed  $default
     * @param  array  $options
     * @return \Illuminate\Support\HtmlString
     */
    public function radioGroup($id, $label, $values, $default = null, $options = [])
    {
        return $this->group($id, $label, function ($builder, $id, $options) use ($default, $values) {
            $html = '';
            $old_value = old($id, $default);
            foreach ($values as $value => $key) {
                $checked = $old_value == $value ? ' checked' : '';
                $html .= "<label><input type=\"radio\" name=\"{$id}\" value=\"{$value}\"{$checked}>{$key}</label>";
            }
            return $html;
        }, $options);
    }

    /**
     * Display an array of hidden values.
     *
     * @param  string $name
     * @param  array $array
     * @return \Illuminate\Support\HtmlString
     */
    public function hiddenArray($name, array $array = [])
    {
        $hiddens = [];

        foreach ($array as $key => $value) {
            $hiddens[] = (string)$this->input('hidden', "{$name}[{$key}]", $value);
        }

        return $this->toHtmlString(implode('', $hiddens));
    }
}
