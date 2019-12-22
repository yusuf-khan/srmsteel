<?php

namespace Drupal\video_field\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\ElementInfoManagerInterface;
use Drupal\file\Element\ManagedFile;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
/**
 * Plugin implementation of the 'TestimonialDefaultWidget' widget.
 *
 * @FieldWidget(
 *   id = "VideoDefaultWidget",
 *   label = @Translation("Testimonial Information"),
 *   field_types = {
 *     "video"
 *   }
 * )
 *
 * Class TestimonialDefaultWidget
 * @package Drupal\video_field\Plugin\Field\FieldWidget
 */
class VideoDefaultWidget extends WidgetBase implements ContainerFactoryPluginInterface { 

/**
 * Define the form for the field type.
 *
 * Inside this method we can define the form used to edit the field type.
 *
 * @param FieldItemListInterface $items
 * @param int $delta
 * @param array $element
 * @param array $form
 * @param FormStateInterface $formState
 * @return array
 */
public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $formState)
{
$field_settings = $this->getFieldSettings();

    // The field settings include defaults for the field type. However, this
    // widget is a base class for other widgets (e.g., ImageWidget) that may act
    // on field types without these expected settings.
    $field_settings += [
      'display_default' => NULL,
      'display_field' => NULL,
      'description_field' => NULL,
    ];

    $cardinality = $this->fieldDefinition->getFieldStorageDefinition()->getCardinality();
    $defaults = [
      'fids' => [],
      'display' => (bool) $field_settings['display_default'],
      'description' => '',
    ];

    $element['testimonial_headshot'] = array(     
                '#type' => 'managed_file',
                '#upload_location' => 'public://images',
                '#title' => t('Video'),
                '#upload_validators' => array(
                'file_validate_extensions' => array('gif png jpg jpeg'),
                 )
            );
$element['testimonial_headshot']['#weight'] = $delta;

    // Field stores FID value in a single mode, so we need to transform it for
    // form element to recognize it correctly.
    if (!isset($items[$delta]->fids) && isset($items[$delta]->target_id)) {
      $items[$delta]->fids = [$items[$delta]->target_id];
    }
    $element['testimonial_headshot']['#default_value'] = $items[$delta]->getValue() + $defaults;

    $default_fids = $element['testimonial_headshot'] ? $element['testimonial_headshot']['#default_value']['fids'] : $element['testimonial_headshot']['#default_value'];

$element['testimonial_headshot_revision'] = [
        '#type' => 'hidden',
        '#default_value' => isset($items[$delta]->testimonial_headshot) ? $items[$delta]->testimonial_headshot : null
    ];

    return $element;
}

/**
 * {@inheritdoc}
 */
public function massageFormValues(array $values, array $form, FormStateInterface $form_state)
{
    foreach ($values as &$value) {
        if (count($value['testimonial_headshot'])) {
            foreach ($value['testimonial_headshot'] as $fid) {
                $value['testimonial_headshot'] = $fid;
            }
        } else {
            $value['testimonial_headshot'] = $value['testimonial_headshot_revision'] !== '' ? $value['testimonial_headshot_revision'] : '0';
        }

    }

    return $values;
}

}