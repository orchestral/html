<?php namespace Orchestra\Html\Form;

use Closure;
use Illuminate\Container\Container;

class Fieldset extends \Orchestra\Html\Abstractable\Grid
{
    /**
     * Fieldset name.
     *
     * @var string
     */
    protected $name = null;

    /**
     * Control group.
     *
     * @var array
     */
    protected $controls = array();

    /**
     * Field control instance.
     *
     * @var Control
     */
    protected $control = null;

    /**
     * {@inheritdoc}
     */
    protected $definition = array(
        'name'    => 'controls',
        '__call'  => array('controls', 'name'),
        '__get'   => array('attributes', 'name', 'controls'),
        '__set'   => array('attributes'),
        '__isset' => array('attributes', 'name', 'controls'),
    );

    /**
     * Create a new Fieldset instance.
     *
     * @param  \Illuminate\Container\Container  $app
     * @param  string                           $name
     * @param  \Closure                         $callback
     */
    public function __construct(Container $app, $name, Closure $callback = null)
    {
        $this->control = $app['orchestra.form.control'];

        parent::__construct($app);

        $this->buildBasic($name, $callback);
    }

    /**
     * {@inheritdoc}
     */
    protected function initiate()
    {
        $this->control->setTemplate(
            $this->app['config']->get('orchestra/html::form.fieldset', array())
        );
    }

    /**
     * Build basic fieldset.
     *
     * @param  string   $name
     * @param  \Closure $callback
     * @return void
     */
    protected function buildBasic($name, Closure $callback = null)
    {
        if ($name instanceof Closure) {
            $callback = $name;
            $name     = null;
        } elseif (! empty($name)) {
            $this->legend($name);
        }

        call_user_func($callback, $this);
    }

    /**
     * Append a new control to the form.
     *
     * <code>
     *      // add a new control using just field name
     *      $fieldset->control('input:text', 'username');
     *
     *      // add a new control using a label (header title) and field name
     *      $fieldset->control('input:email', 'E-mail Address', 'email');
     *
     *      // add a new control by using a field name and closure
     *      $fieldset->control('input:text', 'fullname', function ($control)
     *      {
     *          $control->label = 'User Name';
     *
     *          // this would output a read-only output instead of form.
     *          $control->field = function ($row) {
     *              return $row->first_name.' '.$row->last_name;
     *          };
     *      });
     * </code>
     *
     * @param  string   $type
     * @param  mixed    $name
     * @param  mixed    $callback
     * @return \Illuminate\Support\Fluent
     */
    public function control($type, $name, $callback = null)
    {
        list($name, $control) = $this->buildControl($name, $callback);

        if (is_null($control->field)) {
            $control->field = $this->control->generate($type);
        }

        $this->controls[]    = $control;
        $this->keyMap[$name] = count($this->controls) - 1;

        return $control;
    }

    /**
     * Build control.
     *
     * @param  mixed    $name
     * @param  mixed    $callback
     */
    protected function buildControl($name, $callback = null)
    {
        list($label, $name, $callback) = $this->buildFluentAttributes($name, $callback);

        $control = new Field(array(
            'id'         => $name,
            'name'       => $name,
            'value'      => null,
            'label'      => $label,
            'attributes' => array(),
            'options'    => array(),
            'checked'    => false,
            'field'      => null,
        ));

        is_callable($callback) && call_user_func($callback, $control);

        return array($name, $control);
    }

    /**
     * Set Fieldset Legend name
     *
     * <code>
     *     $fieldset->legend('User Information');
     * </code>
     *
     * @param  string $name
     * @return mixed
     */
    public function legend($name = null)
    {
        if (is_null($name)) {
            return $this->name;
        }

        $this->name = $name;
    }

    /**
     * Get fieldset name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
