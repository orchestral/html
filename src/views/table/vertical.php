<?php $attributes['table'] = Html::decorate($attributes['table'], array('class' => 'table table-bordered table-striped')); ?>
<table<?php echo Html::attributes($attributes['table']); ?>>
	<tbody>
<?php foreach ($columns as $col): ?>
		<tr>
			<th<?php echo Html::attributes($col->labelAttributes ?: array()); ?>><?php echo $col->label; ?></th>
<?php foreach ($rows as $row): ?>
			<td<?php echo Html::attributes(call_user_func($col->attributes, $row)); ?>><?php 

				$columnValue = call_user_func($col->value, $row);
				echo ( !! $col->escape ? e($columnValue) : $columnValue); ?></td>
<?php endforeach; ?>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo $pagination ?: ''; ?>