<?php

echo Form::open($grid->attributes());

if ($token) :
echo \csrf_field();
endif;

foreach ($grid->hiddens() as $hidden) :
echo $hidden;
endforeach;

foreach ($grid->fieldsets() as $fieldset) : ?>
  <fieldset<?php echo HTML::attributes($fieldset->attributes() ?: []); ?>>
    <?php if ($fieldset->name) : ?>
    <legend><?php echo \e($fieldset->name) ?: ''; ?></legend>
    <?php endif; ?>

    <?php foreach ($fieldset->controls() as $control) : ?>
    <div class="form-group<?php echo $errors->has($control->id) ? ' has-error' : ''; ?>">
      <?php echo Form::label($control->name, $control->label, ['class' => 'control-label']); ?>
      <?php echo $control->getField($grid->data()); ?>
      <?php if ($control->inlineHelp) : ?>
      <span class="help-inline"><?php echo $control->inlineHelp; ?></span>
      <?php endif; ?>
      <?php if ($control->help) : ?>
      <p class="help-block"><?php echo $control->help; ?></p>
      <?php endif; ?>
      <?php echo $errors->first($control->id, $errorMessage); ?>
    </div>
    <?php endforeach; ?>
  </fieldset>
<?php endforeach; ?>

<div class="row">
  <div<?php echo HTML::attributable(($meta['button'] ?? []), ['class' => 'col-md-12']); ?>>
    <button type="submit" class="btn btn-primary">
      <?php echo $submit; ?>
    </button>
  </div>
</div>

<?php echo Form::close(); ?>
