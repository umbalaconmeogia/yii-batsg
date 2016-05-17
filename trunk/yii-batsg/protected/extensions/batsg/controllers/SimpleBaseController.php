<?php
/**
 * SimpleBaseController define a simple work flow for managing data of
 * specific model.
 * <p>
 * It helps you to quickly create a set of actions to manage a DB table (model)
 * such as (index, view, new->confirm->create/back to new,
 * edit->confirm->update/back to edit, delete) or more simple one
 * (index, view, new->create, edit->update, delete). Sub class
 * (controller) has to do nothing to archieve methods (actions) to do these job.
 * <p>
 * With this work flow, a set of view template structure are also defined
 * (index, view, create, createConfirm, update, updateConfirm,
 * _view (included in view, createConfirm, updateConfirm),
 * _form (included in create, update),
 * _confirm (included in createConfirm, updateConfirm) to help you standardize
 * the ordinary jobs.
 * @author umbalaconmeogia
 */
trait SimpleBaseController // extends BaseController
{
  /**
   * Get the name of the model class used in this controller.
   * @return string The model class name.
   */
  abstract protected function modelClassName();

  /**
   * Get the flash message displayed when the model is created.
   * @return string
   */
  abstract protected function messageModelCreated();

  /**
   * Get the flash message displayed when the model is updated.
   * @return string
   */
  abstract protected function messageModelUpdated();

  /**
   * Get the flash message displayed when the model is deleted.
   * @return string
   */
  abstract protected function messageModelDeleted();

  /**
   * Get view name for index.
   * @return string
   */
  protected function viewIndex()
  {
    return 'index';
  }

  /**
   * Get view name for create.
   * @return string
   */
  protected function viewCreate()
  {
    return 'create';
  }

  /**
   * Get view name for createConfirm.
   * @return string
   */
  protected function viewCreateConfirm()
  {
    return 'createConfirm';
  }

  /**
   * Get view name for update.
   * @return string
   */
  protected function viewUpdate()
  {
    return 'update';
  }

  /**
   * Get view name for updateConfirm.
   * @return string
   */
  protected function viewUpdateConfirm()
  {
    return 'updateConfirm';
  }

  /**
   * Get view name for view.
   * @return string
   */
  protected function viewView()
  {
    return 'view';
  }

  /**
   * List all records of this table.
   */
  public function actionIndex()
  {
    $model = $this->dataProviderModelForIndex($this->modelClassName());
    $this->render($this->viewIndex(), array('model' => $model));
  }

  /**
   * Create model used in CActiveDataProvider()
   * @param string $modelClassName
   * @return CActiveDataProvider
   */
  protected function dataProviderModelForIndex($modelClassName)
  {
    $model = new $modelClassName('search');
    $model->unsetAttributes();  // clear any default values
    if (isset($_REQUEST[$modelClassName])) {
      $model->attributes = $_REQUEST[$modelClassName];
    }
    return $model;
  }

  /**
   * Display form to input new record,
   * or process when user submit data to create new record.
   */
  public function actionCreate() {
    $this->simpleCreateOrUpdate($this->modelClassName(), NULL, $this->viewCreate(),
        $this->messageModelCreated(), NULL, $this->viewView());
  }

  /**
   * Process when user click back button on createConfirm page.
   */
  public function actionCreateBack() {
    $this->simpleCreateOrUpdate($this->modelClassName(), NULL, $this->viewCreate(),
        NULL, FALSE, $this->viewView());
  }

  /**
   * Display confirmation page when creating new record.
   */
  public function actionCreateConfirm() {
    $this->simpleConfirm($this->modelClassName(), NULL, $this->viewCreateConfirm(), $this->viewCreate());
  }


  /**
   * Display form to edit a record,
   * or process when user submit data to update a record.
   */
  public function actionUpdate($id) {
    $this->simpleCreateOrUpdate($this->modelClassName(), $id, $this->viewUpdate(),
        $this->messageModelUpdated(), NULL, $this->viewView());
  }

  /**
   * Process when user click back button on updateConfirm page.
   */
  public function actionUpdateBack($id) {
    $this->simpleCreateOrUpdate($this->modelClassName(), $id, $this->viewUpdate(),
        NULL, FALSE, $this->viewView());
  }

  /**
   * Display confirmation page when updating a record.
   */
  public function actionUpdateConfirm($id) {
    $this->simpleConfirm($this->modelClassName(), $id, $this->viewUpdateConfirm(), $this->viewUpdate());
  }

  /**
   * Displays a particular model.
   * @param integer $id the ID of the model to be displayed
   */
  public function actionView($id)
  {
    $this->render($this->viewView(),
        array('model' => BaseModel::loadModel($this->modelClassName(), $id)));
  }

  /**
   * Deletes a particular model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param string $modelClassName
   * @param mixed $id the primary key (ID) of the model to be deleted
   * @param string $indexView
   */
  public function actionDelete($id)
  {
    if(Yii::app()->request->isPostRequest) {
      // we only allow deletion via POST request
      BaseModel::loadModel($this->modelClassName(), $id)->delete();

      // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
      if(!isset($_GET['ajax'])) {
        Y::setFlashSuccess($this->messageModelDeleted());
        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
      }
    } else {
      throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }
  }

  /**
   * Process action create, update, createConfirm, updateConfirm,
   * backFromCreate, backFromUpdate.
   * @param string $modelClassName
   * @param mixed $id the primary key
   * @param string $formView
   * @param boolean $saveDb Save DB or not. If $saveDb is TRUE, then the model will be saved.
   *        If $saveDb is FALSE, then the model will not be saved.
   *        If $saveDb is NULL, then the model will be saved if $_POST[$modelClassName] is set.
   *        Usage example: $saveDb = FALSE for back, NULL for create/update
   *        (first time or after confirm).
   * @param string $viewView View template after saving the model.
   * @param string $pkName primary key name. Default to "id".
   */
  protected function simpleCreateOrUpdate(
      $modelClassName,
      $id,
      $formView,
      $successFlashMessage,
      $saveDb = NULL,
      $viewView = 'view',
      $pkName = 'id')
  {
    // Create model to display in form.
    $model = BaseModel::loadModel($modelClassName, $id);

    // Get model data from URL parameter if set.
    if(isset($_POST[$modelClassName]))
    {
      $model->attributes = $_POST[$modelClassName];
    }
    if ($saveDb == TRUE || ($saveDb === NULL && isset($_POST[$modelClassName]))) {
      // Redirect to view page if save successfully.
      if($this->simpleCreateOrUpdateSaveModel($model)) {
          Y::setFlashSuccess($successFlashMessage);
          $this->redirect(array('view', $pkName => $model->$pkName));
      }
    }

    // Render form view (in the first time request or when data is invalid).
    $this->render($formView, array('model' => $model));
  }

  /**
   * Save a model object in the simpleCreateOrUpdate work flow.
   * Override this when neccessary.
   * @param CActiveRecord $model
   * @return boolean
   */
  protected function simpleCreateOrUpdateSaveModel($model)
  {
    return $model->save();
  }

  /**
   * @param string $modelClassName
   * @param mixed $id the primary key
   * @param string $confirmView View template to display confirm.
   *     For example: 'createConfirm' or 'updateConfirm'.
   * @param string $formView View template to display when data is invalid.
   *     For example: 'create' or 'update'.
   */
  protected function simpleConfirm($modelClassName, $id, $confirmView, $formView)
  {
    // Create model to display in form.
    $model = BaseModel::loadModel($modelClassName, $id);

    // Get model data from URL parameter if set.
    if(isset($_POST[$modelClassName]))
    {
      $model->attributes = $_POST[$modelClassName];
      // Render confirm view if data is valid.
      if($this->simpleConfirmValidate($model)) {
        return $this->render($confirmView, array('model' => $model));
      }
    }

    // Render form view (incase of invalid data).
    $this->render($formView, array('model' => $model));
  }

  /**
   * Validate a model object in the simpleConfirm work flow.
   * Override this when neccessary.
   * @param CActiveRecord $model
   * @return boolean
   */
  protected function simpleConfirmValidate($model)
  {
    return $model->validate();
  }
}
?>