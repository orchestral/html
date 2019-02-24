<?php

echo Form::open(\array_merge($grid->attributes(), ['class' => 'form-horizontal']));

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
      <?php echo Form::label($control->name, $control->label, ['class' => 'col-md-3 control-label']); ?>
      <div class="col-md-9">
        <?php echo $control->attributes(['class' => 'col-md-12'])->getField($grid->data()); ?>
        <?php if ($control->inlineHelp) : ?>
        <span class="help-inline"><?php echo $control->inlineHelp; ?></span>
        <?php endif; ?>
        <?php if ($control->help) : ?>
        <p class="help-block"><?php echo $control->help; ?></p>
        <?php endif; ?>
        <?php echo $errors->first($control->id, $format); ?>
      </div>
    </div>
    <?php endforeach; ?>
  </fieldset>
<?php endforeach; ?>

<div class="row">
  <div<?php echo HTML::attributable(($meta['button'] ?? []), ['class' => 'col-md-9 col-md-offset-3']); ?>>
    <button type="submit" class="btn btn-primary"><?php echo $submit; ?></button>
  </div>
</div>

<?php echo Form::close(); ?>
