<?php

namespace Drupal\custom_module\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "my_block_example_block",
 *   admin_label = @Translation("My Custom block"),
 * )
 */
class Block extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $arr = date("Y");
    return [
      '#theme' => 'block_copyright',
      '#abc' => $arr,
    ];
  }
}