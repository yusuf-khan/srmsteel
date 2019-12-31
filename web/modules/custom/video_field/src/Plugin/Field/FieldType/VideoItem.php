<?php

namespace Drupal\video_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface as StorageDefinition;

/**
 * Plugin implementation of the 'Testimonial' field type.
 *
 * @FieldType(
 *   id = "video",
 *   label = @Translation("Video"),
 *   description = @Translation("Stores Testimonial Information."),
 *   category = @Translation("Custom"),
 *   default_widget = "VideoDefaultWidget",
 *   default_formatter = "TestimonialDefaultFormatter"
 * )
 *
 * Class Testimonial
 * @package Drupal\video_field\Plugin\Field\FieldType
 */
class VideoItem extends FieldItemBase
{

/**
 * Field type properties definition.
 *
 * Inside this method we defines all the fields (properties) that our
 * custom field type will have.
 *
 * @param StorageDefinition $storage
 * @return array
 */
public static function propertyDefinitions(StorageDefinition $storage)
{
    $properties['testimonial_headshot'] = DataDefinition::create('integer')->setLabel(t('Headshot'));

    return $properties;
}

/**
 * Field type schema definition.
 *
 * Inside this method we defines the database schema used to store data for
 * our field type.
 * @param StorageDefinition $storage
 * @return array
 */
public static function schema(StorageDefinition $storage)
{
    $schema['columns']['testimonial_headshot'] = ['type' => 'int'];

    return $schema;
}

/**
 * Define when the field type is empty.
 *
 * This method is important and used internally by Drupal. Take a moment
 * to define when the field type must be considered empty.
 *
 * @return bool
 */
public function isEmpty()
{
    return empty($this->get('testimonial_headshot')->getValue());
}

}