<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
  'action' => array($model->isNewRecord ? 'createConfirm' : 'updateConfirm',
                    'id' => $model->id),
)); ?>

<p class="note"><span class="required">*</span> <?php Y::et('is_required')?></p>

<table>
  <tr>
    <td><?php echo $form->labelEx($model, "name"); ?></td>
    <td><?php echo $form->textField($model, "name"); ?></td>
  </tr>
  <tr>
    <td><?php echo $form->labelEx($model, "address"); ?></td>
    <td><?php echo $form->textField($model, "address"); ?></td>
  </tr>
</table>

<div class="row buttons">
  <?php echo CHtml::submitButton(Y::t('confirm')); ?>
</div>

<p class="note">
Notice: You can make it submit directly, without confirmation by
just changing the form request's action to <tt>create/update</tt> instead of
<tt>createConfirm/updateConfirm</tt>.
</p>

<?php $this->endWidget(); ?>

</div><!-- form -->