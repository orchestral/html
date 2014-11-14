<?php namespace Orchestra\Html\Form;

use Closure;
use Orchestra\Html\Grid as BaseGrid;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Orchestra\Contracts\Html\Form\Control as ControlContract;
use Orchestra\Contracts\Html\Form\Fieldset as FieldsetContract;

class Fieldset extends BaseGrid implements FieldsetContract
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
    protected $controls = [];

    /**
     * Field control instance.
     *
     * @var \Orchestra\Contracts\Html\Form\Control
     */
    protected $control = null;

    /**
     * {@inheritdoc}
     */
    protected $definition = [
        'name'    => 'controls',
        '__call'  => ['controls', 'name'],
        '__get'   => ['attributes', 'name', 'controls'],
        '__set'   => ['attributes'],
        '__isset' => ['attributes', 'name', 'controls'],
    ];

    /**
     * Create a new Fieldset instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     * @param  string  $name
     * @param  \Closure  $callback
     */
    public function __construct(Container $app, $name, Closure $callback = null)
    {
        parent::__construct($app);

        $this->buildBasic($name, $callback);
    }

    /**
     * Load grid configuration.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @param  \Orchestra\Contracts\Html\Form\Control  $control
     * @return void
     */
    public function initiate(Repository $config, ControlContract $control)
    {
        $control->setTemplate(
            $config->get('orchestra/html::form.fieldset', [])
        );

        $this->control = $control;
    }

    /**
     * Build basic fieldset.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     * @return void
     */
    protected function buildBasic($name, Closure $callback = null)
    {
        if ($name instanceof Closure) {
            $callback = $name;
            $name     = null;
        }

        ! empty($name) && $this->legend($name);

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
     * @param  string  $type
     * @param  mixed   $name
     * @param  mixed   $callback
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
     * @param  mixed  $name
     * @param  mixed  $callback
     * @return array
     */
    protected function buildControl($name, $callback = null)
    {
        list($label, $name, $callback) = $this->buildFluentAttributes($name, $callback);

        $control = new Field([
            'id'         => $name,
            'name'       => $name,
            'value'      => null,
            'label'      => $label,
            'attributes' => [],
            'options'    => [],
            'checked'    => false,
            'field'      => null,
        ]);

        is_callable($callback) && call_user_func($callback, $control);

        return [$name, $control];
    }

    /**
     * Set Fieldset Legend name
     *
     * <code>
     *     $fieldset->legend('User Information');
     * </code>
     *
     * @param  string  $name
     * @return mixed
     */
    public function legend($name = null)
    {
        if (! is_null($name)) {
            $this->name = $name;
        }

        return $this->name;
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
