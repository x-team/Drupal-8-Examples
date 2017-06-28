<?php

namespace Drupal\custom_hooks_demo\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ModuleHandler;
use Psr\Log\LoggerInterface;


class CustomHooksDemoController implements ContainerInjectionInterface {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a new ModuleHandler object.
   *
   * @param \Drupal\Core\Extension\ModuleHandler $module_handler
   *   The module handler.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance
   */
  public function __construct(ModuleHandlerInterface $module_handler, LoggerInterface $logger) {
    $this->moduleHandler = $module_handler;
    $this->logger = $logger;
  }

  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container) {
    return new static (
      $container->get('module_handler'),
      $container->get('logger.factory')->get('custom_hooks_demo')
    );
  }

  /**
   * Run all hook_cron implementations and log each run.
   */
  public function customRunAllCrons() {
    foreach ($this->moduleHandler->getImplementations('cron') as $module) {
      try {
        $this->moduleHandler->invoke($module, 'cron');
        $this->logger->notice('Ran hook_cron from @name module.', array('@name' => $module));
      } catch (\Exception $e) {
        watchdog_exception('cron', $e);
      }
    }

    drupal_set_message(t('Ran all crons'));
    return new RedirectResponse(\Drupal::url('<front>', [], ['absolute' => TRUE]));
  }
}
