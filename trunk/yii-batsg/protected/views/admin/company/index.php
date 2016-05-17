<h1><?php Y::et('company'); ?></h1>

<?php echo CHtml::link(Y::t('create'), array('create')); ?>

<?php
if ($dataProvider->itemCount) {
  $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'article-grid',
    'dataProvider' => $dataProvider,
    'columns'=>array(
      'id',
      'name',
      array(
        'class'=>'CButtonColumn',
      ),
    ),
  ));
}
?>