<?php
class CompanyController extends SimpleBaseController
{
  public $layout = 'admin';

  protected function modelClassName() {
    return 'Company';
  }

  protected function messageModelCreated() {
    return Y::t('flash_success_company_created');
  }

  protected function messageModelUpdated() {
    return Y::t('flash_success_company_updated');
  }

  /**
   * Get the flash message displayed when the model is deleted.
   * @return string
   */
  protected function messageModelDeleted() {
    return Y::t('flash_success_company_deleted');
  }
}
?>