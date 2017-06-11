<?php

namespace Drupal\simple_page\Controller;

/**
 * Defines a controller to render a simple page.
 */
class SimplePageController {

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

}
