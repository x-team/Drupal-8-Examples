<?php

namespace Drupal\event_subscriber_demo\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\event_subscriber_demo\Event\NodeInsertDemoEvent;

/**
 * Logs the creation of a new node.
 */
class NodeInsertDemoSubscriber implements EventSubscriberInterface {

  /**
   * Log the creation of a new node.
   *
   * @param \Drupal\event_subscriber_demo\Event\NodeInsertDemoEvent $event
   */
  public function onDemoNodeInsert(NodeInsertDemoEvent $event) {
    $entity = $event->getEntity();
    \Drupal::logger('event_subscriber_demo')->notice('New @type: @title. Created by: @owner',
      array(
        '@type' => $entity->getType(),
        '@title' => $entity->label(),
        '@owner' => $entity->getOwner()->getDisplayName()
        ));
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[NodeInsertDemoEvent::DEMO_NODE_INSERT][] = ['onDemoNodeInsert'];
    return $events;
  }
}
