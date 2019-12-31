<?php

namespace Drupal\commerce_quantity_options\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\NumberWidget;

/**
 * Plugin implementation of the 'commerce_quantity_options' widget.
 *
 * @FieldWidget(
 *   id = "commerce_quantity_options",
 *   label = @Translation("Quantity options"),
 *   field_types = {
 *     "decimal",
 *   }
 * )
 */
class QuantityWidgetOptions extends NumberWidget {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'options' => '',
      'type' => 'radios',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    $type = $this->getSetting('type');
    $element['type'] = [
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

    $element['options'] = [
      '#type' => 'textarea',
      '#rows' => 10,
      '#title' => t('Options'),
      '#default_value' => $this->getSetting('options'),
      '#description' => $this->optionsDescription(),
    ];
  
    return $element;
  }

  /**
   * Description of the options.
   */
  public static function optionsDescription() {
    $description = '<p>' . t('The possible values this field can contain. Enter one value per line, in the format key|label.');
    $description .= '<br/>' . t('The key is the stored value, and must be numeric. The label will be used in displayed values and edit forms.');
    return $description;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $summary[] = $this->t('Options type:') . ' ' . $this->getSetting('type');
    if ($this->getSetting('options') == '') {
      $summary[] = $this->t('There are no any provided options.');
    }
    else {
      $summary[] = $this->t('Provided options:');
      $summary[] = ['#markup' => implode('<br/>', explode("\n", $this->getSetting('options')))];
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $options = $this->extractValues($this->getSetting('options'));
    $element['value']['#type'] = $this->getSetting('type');
    $element['value']['#options'] = !empty($options) ? $options : [1];
    return $element;
  }

  /**
   * Extracts the values array from the string.
   *
   * @param string $string
   *   The string to extract values from.
   *
   * @return array
   *   The array of extracted key/value pairs, or empty array.
   */
  public static function extractValues($string) {
    $values = [];

    $list = explode("\n", $string);
    $list = array_map('trim', $list);
    $list = array_filter($list, 'strlen');

    foreach ($list as $position => $text) {
      // Check for an explicit key.
      $matches = [];
      if (preg_match('/(.*)\|(.*)/', $text, $matches)) {
        // Trim key and value to avoid unwanted spaces issues.
        $key = trim($matches[1]);
        $value = trim($matches[2]);
        $explicit_keys = TRUE;
      }
      $values[$key] = $value;
    }

    return !empty($values) ? $values : [];
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    $entity_type = $field_definition->getTargetEntityTypeId();
    $field_name = $field_definition->getName();
    return $entity_type == 'commerce_order_item' && $field_name == 'quantity';
  }

}
