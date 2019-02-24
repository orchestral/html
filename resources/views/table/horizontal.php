<table<?php echo HTML::attributable($grid->attributes(), ['class' => 'table table-striped']); ?>>
    <thead>
        <tr>
        <?php foreach ($grid->columns() as $column): ?>
            <th<?php echo HTML::attributes($column->headers ?: []); ?>><?php echo $column->label; ?></th>
        <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($grid->data() as $row): ?>
        <tr<?php echo HTML::attributes(\call_user_func($grid->header(), $row) ?: []); ?>>
        <?php foreach ($grid->columns() as $column): ?>
            <td<?php echo HTML::attributes(\call_user_func($column->attributes, $row)); ?>>
                <?php echo $column->getValue($row); ?>
            </td>
        <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    <?php if (! \count($grid->data()) && $empty) : ?>
        <tr class="no-records">
            <td colspan="<?php echo count($grid->columns()); ?>"><?php echo $empty; ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<?php echo $pagination ?: ''; ?>
