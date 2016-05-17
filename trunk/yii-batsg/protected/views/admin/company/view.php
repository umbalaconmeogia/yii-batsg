<h1><?php Y::et('company_detail'); ?></h1>

<?php
  $this->renderPartial('_view', array('model' => $model));
  echo CHtml::button(Y::t('edit'), array('submit' => array('update', 'id' => $model->id)));
  echo ' ';
  echo CHtml::button(Y::t('delete'), array(
    'submit' => array('delete', 'id' => $model->id),
    'confirm' => Y::t('confirm_delete'),
  ));
?>
