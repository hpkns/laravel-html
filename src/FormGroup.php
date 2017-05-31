<?php

namespace Hpkns\Html;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Session\SessionManager;

class FormGroup implements Htmlable
{
    /**
     * The id of the field.
     *
     * @var string
     */
    protected $name;

    /**
     * The label to the field.
     *
     * @var string
     */
    protected $label;

    /**
     * The field at the heart of the content.
     *
     * @var FormField
     */
    protected $field;

    /**
     * If the field required?
     *
     * @var bool
     */
    protected $required = false;

    /**
     * The fields error.
     *
     * @var string|null
     */
    protected $error;

    /**
     * Session used to find errors.
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * Initialize the group.
     *
     * @param string $name
     * @param string $label
     * @param mixed  $content
     * @param array  $attributes
     */
    public function __construct($name, $label, $content, $default = null, array $attributes = [], SessionManager $session = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->field = (new FormField($name, $content, $default, $attributes))
            ->addClass(config('control_class', 'form__control'));
        $this->session = $session ?: app('session.store');

        $this->checkForError();
    }

    /**
     * It there an error with the field?
     *
     * @return string|null
     */
    public function getError($name)
    {
        if ($error = session('errors')) {
            return $error->first($name);
        }
    }

    /**
     * Check if the fields contains error.
     *
     * @return void
     */
    public function checkForError()
    {
        $this->error = $this->getError($this->name);
    }

    /**
     * Set the field label.
     *
     * @param  string $label
     * @return void
     */
    public function label($label)
    {
        $this->label = $label;
    }

    /**
     * Return the HTML version of the group.
     *
     * @return string
     */
    public function toHtml()
    {
        $base_class = config('html.base_class', 'form__group');
        $group_class = $this->getGroupClass($base_class);

        return view('html::group', array_merge(compact('base_class', 'group_class'), [
            'label' => $this->label,
            'name'  => $this->name,
            'field' => $this->field,
            'error' => $this->error,
        ]));
    }

    /**
     * Get the class group.
     *
     * @param  string $base
     * @return string
     */
    public function getGroupClass($base)
    {
        $class = [$base];

        if (! empty($this->error)) {
            $class[] = "{$base}--with-error";
        }

        if ($this->required) {
            $class[] = "{$base}--required";
        }

        return implode(' ', $class);
    }

    /**
     * Mark the field as required.
     *
     * @return void
     */
    public function required()
    {
        $this->required = true;
        $this->field->required();

        return $this;
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
     * Pass everything unknown to the underlying field.
     *
     * @param  string $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $this->field->{$name}(...$arguments);

        return $this;
    }
}
