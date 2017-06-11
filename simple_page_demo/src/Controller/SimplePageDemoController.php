<?php

namespace Drupal\simple_page_demo\Controller;

/**
 * Defines a controller to render a simple page.
 */
class SimplePageDemoController {

  /**
   * Build page output.
   *
   * @return array
   *   A renderable array.
   */
  public function page() {
    return array(
      '#markup' => "This is a simple page",
    );
  }

  /**
   * Page title callback.
   *
   * @return string
   *  The title of the page.
   */
  public function title() {
    return "Simple page title";
  }

}
