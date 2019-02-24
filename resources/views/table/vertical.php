<table<?php echo HTML::attributable($grid->attributes(), ['class' => 'table table-striped']); ?>>
    <tbody>
    <?php foreach ($grid->columns() as $column): ?>
        <tr>
            <th<?php echo HTML::attributes($column->headers ?: []); ?>><?php echo $column->label; ?></th>
            <?php foreach ($grid->data() as $row): ?>
            <td<?php echo HTML::attributes(\call_user_func($column->attributes, $row)); ?>>
                <?php echo $column->getValue($row); ?>
            </td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php echo $pagination ?: ''; ?>
