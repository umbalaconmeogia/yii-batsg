<?php
trait BaseAjaxController
{

  /**
   * Wrap renderPartial by <tr>
   * This is used for updating screen partialy by <td> elements.
   * @param string $view
   * @param array $params
   * @param string $tag
   */
  protected function wrapRenderPartial($view, $params, $tag = 'tr')
  {
    return $this->wrapInTag($this->renderPartial($view, $params, TRUE), $tag);
  }

  /**
   * Wrap text in <tr>
   * @param string $text
   * @param string $tag
   */
  protected function wrapInTag($text, $tag)
  {
    return "<$tag>$text</$tag>";
  }

  /**
   * Wrap renderPartial result by json.
   * @param string $view
   * @param array $params
   * @param number $code
   */
  protected function renderPartialJson($view, $params, $code = 0)
  {
    $this->renderJson($this->renderPartial($view, $params, TRUE), $code);
  }

  protected function renderPartialWrapJson($view, $params, $tag)
  {
    $this->renderJson($this->wrapRenderPartial($view, $params, $tag));
  }

  /**
   * Wrap a data (may be a html string or any thing) into a json wrapper.
   * @param mixed $data
   * @param number $code
   */
  protected function renderJson($data, $code = 0)
  {
    $result = array(
        'code' => $code,
        'data' => $data,
    );
    echo json_encode($result);
  }
}
?>