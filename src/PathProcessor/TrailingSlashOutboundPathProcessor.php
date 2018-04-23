<?php

namespace Drupal\trailing_slash\PathProcessor;

use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\trailing_slash\Helper\Url\TrailingSlashHelper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TrailingSlashOutboundPathProcessor.
 *
 * @package Drupal\trailing_slash\PathProcessor
 */
class TrailingSlashOutboundPathProcessor implements OutboundPathProcessorInterface {

  /**
   * @inheritdoc
   */
  public function processOutbound($path, &$options = [], Request $request = NULL, BubbleableMetadata $bubbleable_metadata = NULL) {
    // @todo do it configurable.
    if (\Drupal::state()->get('trailing_slash', TRUE) && $path != '<front>' && !empty($path)) {
      TrailingSlashHelper::add($path);
    }
    return $path;
  }

}
