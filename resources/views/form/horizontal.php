<?php

echo Form::open(array_merge($grid->attributes(), ['class' => 'form-horizontal']));

if ($token) echo csrf_field();

foreach ($grid->hiddens() as $hidden) echo $hidden;

foreach ($grid->fieldsets() as $fieldset) : ?>

    <fieldset<?php echo HTML::attributes($fieldset->attributes() ?: []); ?>>

        <?php if ($fieldset->name) : ?><legend><?php echo e($fieldset->name) ?: '' ?></legend><?php endif; ?>

        <?php foreach ($fieldset->controls() as $control) : ?>

        <div class="form-group<?php echo $errors->has($control->name) ? ' has-error' : '' ?>">
            <?php echo Form::label($control->name, $control->label, ['class' => 'three columns control-label']); ?>

            <div class="nine columns">
                <?php echo $control->getField($grid->data(), $control, []); ?>
                <?php if ($control->inlineHelp) : ?><span class="help-inline"><?php echo $control->inlineHelp; ?></span><?php endif; ?>
                <?php if ($control->help) : ?><p class="help-block"><?php echo $control->help; ?></p><?php endif; ?>
                <?php echo $errors->first($control->name, $format); ?>
            </div>
        </div>

        <?php endforeach; ?>

    </fieldset>
<?php endforeach; ?>

<fieldset>
    <div class="row">
        <?php /* Fixed row issue on Bootstrap 3 */ ?>
    </div>
    <div class="row">
        <div class="nine columns offset-by-three">
            <button type="submit" class="btn btn-primary"><?php echo $submit; ?></button>
        </div>
    </div>
</fieldset>

<?php echo Form::close(); ?>
