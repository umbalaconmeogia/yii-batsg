<?php
  // Display view template for the user to confirm the input.
  $this->renderPartial('_view', array('model' => $model));

  // Display the hidden form to submit the input.

  // Action create or update based on the model id.
  if ($model->isNewRecord) {
    $submitAction = 'create';
  	$backAction = 'createBack';
  } else {
    $submitAction = 'update';
    $backAction = 'updateBack';
  }
  // Start form
  $form = $this->beginWidget('CActiveForm', array(
      'action' => array($submitAction, 'id' => $model->id),
  ));
  // Hidden fields
  echo $form->hiddenField($model, "name");
  echo $form->hiddenField($model, "address");
  // Submit button.
  echo CHtml::submitButton(Y::t($submitAction));
  // Back button.
  echo CHtml::button(Y::t('back'), array('submit' => array($backAction, 'id' => $model->id)));
  // End form
  $this->endWidget();
?>