<?php

namespace Drupal\trailing_slash\PathProcessor;

use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Url;
use Drupal\trailing_slash\Helper\Settings\TrailingSlashSettingsHelper;
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
    if ($this->isPathWithTrailingSlash($path, $options, $request, $bubbleable_metadata)) {
      TrailingSlashHelper::add($path);
    }
    return $path;
  }

  /**
   * @param                    $path
   * @param array              $options
   * @param Request            $request
   * @param BubbleableMetadata $bubbleable_metadata
   *
   * @return bool
   */
  public function isPathWithTrailingSlash($path, &$options = [], Request $request = NULL, BubbleableMetadata $bubbleable_metadata = NULL) {
    $isPathWithTrailingSlash = FALSE;
    if (
      \Drupal::state()->get('trailing_slash', TRUE)
      && $path != '<front>'
      && !empty($path)
      && !$this->isAdminPath($path)
      &&
       (
         $this->isRouteWithTrailingSlash($path)
         || $this->isBundleWithTrailingSlash($path)
       )
    ) {
      return TRUE;
    }
    return $isPathWithTrailingSlash;
  }

  /**
   * @param $path
   *
   * @return bool
   */
  public function isAdminPath($path) {
    if (strpos($path, '/admin') === 0 || strpos($path, '/devel') === 0) {
      return TRUE;
    }
    $url = Url::fromUri("internal:" . $path);
    if ($url->isRouted()) {
      $route_name = $url->getRouteName();
      $route = \Drupal::service('router.route_provider')->getRouteByName($route_name);
      $is_admin = \Drupal::service('router.admin_context')->isAdminRoute($route);
      return $is_admin;
    }
    return FALSE;
  }
  /**
   * @param $path
   *
   * @return bool
   */
  public function isRouteWithTrailingSlash($path) {
    $routes = TrailingSlashSettingsHelper::getActiveRoutes();
    if (in_array($path, $routes)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * @param $path
   *
   * @return bool
   */
  public function isBundleWithTrailingSlash($path) {
    $bundles = TrailingSlashSettingsHelper::getActiveBundles();
    $contentEntityType = TrailingSlashSettingsHelper::getContentEntityType();
    $contentEntityTypeKeys = array_keys($contentEntityType);
    if (!empty($bundles)) {
      $url = Url::fromUri("internal:" . $path);
      try {
        if ($url->isRouted() && $params = $url->getRouteParameters()) {
          $entity_type = key($params);
          if (in_array($entity_type, $contentEntityTypeKeys)) {
            $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
            $bundle = $entity->bundle();
            if (isset($bundles[$entity_type][$bundle])) {
              return TRUE;
            }
          }
        }
      }
      catch (Exception $e) {}
    }
    return FALSE;
  }

}
