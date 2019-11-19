<?php

namespace Drupal\commerce_quantity_options\Plugin\views\field;

use Drupal\commerce_quantity_options\Plugin\Field\FieldWidget\QuantityWidgetOptions;
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
 * Defines a form element for editing the order item quantity by provided options.
 *
 * @ViewsField("edit_quantity_options")
 */
class EditQuantityOptions extends EditQuantity {

  use UncacheableFieldHandlerTrait;

  /**
   * Constructs a new EditQuantityOptions object.
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
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['allowed_options'] = ['default' => ""];
    $options['options_type'] = ['default' => 'radios'];
    $options['specify_product_types'] = ['default' => FALSE];
    $options['allowed_product_types'] = ['default' => []];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
  
    $form['specify_product_types'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Specify product types'),
      '#description' => $this->t('Allows specify product types for which field must work.'),
      '#default_value' => $this->options['specify_product_types'],
    ];

    // Collect all product types and build options.
    $types_options = [];
    $product_types = \Drupal\commerce_product\Entity\ProductType::loadMultiple();
    foreach ($product_types as $type_id => $type) {
      $types_options[$type_id] = $type->label();
    }

    $form['allowed_product_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Product types'),
      '#default_value' => $this->options['allowed_product_types'],
      '#options' => $types_options,
      '#states' => [
        'visible' => [
          ':input[name="options[specify_product_types]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $type = $this->options['options_type'];
    $form['options_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Options type'),
      '#description' => $this->t('Type of the options.'),
      '#default_value' => $type,
      '#options' => [
        'radios' => $this->t('Radio button'),
        'select' => $this->t('Select button'),
      ],
      '#required' => TRUE,
    ];

    $form['allowed_options'] = [
      '#type' => 'textarea',
      '#rows' => 10,
      '#title' => $this->t('Options'),
      '#default_value' => $this->options['allowed_options'],
      '#description' => QuantityWidgetOptions::optionsDescription(),
    ];

  }

  /**
   * {@inheritdoc}
   */
  public function viewsForm(array &$form, FormStateInterface $form_state) {
    // Build the parent views form for field edit_quantity_options.
    parent::viewsForm($form, $form_state);
    $options = QuantityWidgetOptions::extractValues($this->options['allowed_options']);
    $specify_product_types = $this->options['specify_product_types'];
    if ($specify_product_types) {
      $allowed_product_types = array_filter(array_values($this->options['allowed_product_types']));
    }
    // Alter parent form.
    foreach ($this->view->result as $row_index => $row) {
      if (($specify_product_types && in_array($this->getEntity($row)->bundle(), $allowed_product_types)) || !$specify_product_types) {
        $form[$this->options['id']][$row_index]['#type'] = $this->options['options_type'];
        $form[$this->options['id']][$row_index]['#options'] = !empty($options) ? $options : [1];
      }
      else {
        $form[$this->options['id']][$row_index] = ['#markup' => '<--->'];
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function viewsFormSubmit(array &$form, FormStateInterface $form_state) {
    if (isset($form['edit_quantity'])) {
      $triggering_element = $form_state->getTriggeringElement();
      if (empty($triggering_element['#update_cart'])) {
        // Don't run when the "Remove" or "Empty cart" buttons are pressed.
        return;
      }

      $order_storage = $this->entityTypeManager->getStorage('commerce_order');
      // @var \Drupal\commerce_order\Entity\OrderInterface $cart
      $cart = $order_storage->load($this->view->argument['order_id']->getValue());
      $options[] = $form_state->getValue('edit_quantity', []);
      $options[] = $form_state->getValue($this->options['id'], []);
      $save_cart = FALSE;
      $updated_rows = [];
      foreach ($options as $option) {
        foreach ($option as $row_index => $quantity) {
          // Do not update order item if it was already updated.
          if (in_array($row_index, $updated_rows)) {
            continue;
          }
          if (!is_numeric($quantity) || $quantity < 0) {
            // The input might be invalid if the #required or #min attributes
            // were removed by an alter hook.
            continue;
          }
          // @var \Drupal\commerce_order\Entity\OrderItemInterface $order_item 
          $order_item = $this->getEntity($this->view->result[$row_index]);
          if ($order_item->getQuantity() == $quantity) {
            // The quantity hasn't changed.
            continue;
          }

          if ($quantity > 0) {
            $order_item->setQuantity($quantity);
            $this->cartManager->updateOrderItem($cart, $order_item, FALSE);
          }
          else {
            // Treat quantity "0" as a request for deletion.
            $this->cartManager->removeOrderItem($cart, $order_item, FALSE);
          }
          $save_cart = TRUE;
          // Remember which row was updated by any of the field.
          $updated_rows[] = $row_index;
        }
      }
      if ($save_cart) {
        $cart->save();
        if (!empty($triggering_element['#show_update_message'])) {
          $this->messenger->addMessage($this->t('Your shopping cart has been updated.'));
        }
      }
    }
    else {
      parent::viewsFormSubmit($form, $form_state);
    }
  }

}
