<?php

namespace Drupal\video_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Field formatter "burrito_default".
 *
 * @FieldFormatter(
 *   id = "TestimonialDefaultFormatter",
 *   label = @Translation("Video formatter"),
 *   field_types = {
 *     "video",
 *   }
 * )
 */
class TestimonialDefaultFormatter extends FormatterBase {

  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Displays the random string.');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      // Render each element as markup.
      $element[$delta] = ['#markup' => $item->value];
    }

    return $element;
  }

}