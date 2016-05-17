<table>
  <tr>
    <td><?php echo CHtml::activeLabel($model, "name"); ?></td>
    <td><?php echo CHtml::encode($model->name); ?></td>
  </tr>
  <tr>
    <td><?php echo CHtml::activeLabel($model, "address"); ?></td>
    <td><?php echo CHtml::encode($model->address); ?></td>
  </tr>
</table>