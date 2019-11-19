<?php

namespace Drupal\commerce_quantity_options\Plugin\views\field;

use Drupal\commerce_cart\CartManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\Plugin\views\field\UncacheableFieldHandlerTrait;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\commerce_cart\Plugin\views\field\EditQuantity;

/**
 * Defines a form element for editing the order item quantity.
 *
 * @ViewsField("edit_quantity_altered")
 */
class EditQuantityAltered extends EditQuantity {

  use UncacheableFieldHandlerTrait;

  /**
   * Constructs a new EditQuantityAltered object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\commerce_cart\CartManagerInterface $cart_manager
   *   The cart manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CartManagerInterface $cart_manager, EntityTypeManagerInterface $entity_type_manager, MessengerInterface $messenger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $cart_manager, $entity_type_manager, $messenger);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('commerce_cart.cart_manager'),
      $container->get('entity_type.manager'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewsFormSubmit(array &$form, FormStateInterface $form_state) {
    if (!isset($form['edit_quantity_options'])) {
      parent::viewsFormSubmit($form, $form_state);
    }
    // If field edit_quantity_options is enabled, then submission
    // of the field edit_quantity will be handled there - EditQuantityOptions::viewsFormSubmit.
  }

}
