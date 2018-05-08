<?php

namespace Drupal\trailing_slash\Helper\Settings;

use Drupal\Core\Entity\ContentEntityType;

class TrailingSlashSettingsHelper {

  /**
   * @return array
   */
  public static function getActiveBundles() {
    $bundles = [];
    $config = \Drupal::config('trailing_slash.settings');
    $enabled_entity_types = unserialize($config->get('enabled_entity_types'));
    foreach ($enabled_entity_types as $entity_type_key => $entity_type) {
      $isBundleTrailingSlash = array_filter($entity_type);
      if (!empty($isBundleTrailingSlash)) {
        $bundles[$entity_type_key] = $isBundleTrailingSlash;
      }
    }
    return $bundles;
  }

  /**
   * @return array
   */
  public static function getActiveRoutes() {
    static $active_routes;
    if (!$active_routes) {
      $config = \Drupal::config('trailing_slash.settings');
      $list_of_routes = $config->get('list_of_routes');
      $active_routes = explode("\r\n", $list_of_routes);
    }
    return $active_routes;
  }

  /**
   * @return array
   */
  public static function getContentEntityType() {
    static $contentEntityType;
    if (!$contentEntityType) {
      $entities = \Drupal::entityTypeManager()->getDefinitions();
      $contentEntityType = [];
      foreach ($entities as $entity_type_id => $entity_type) {
        if ($entity_type instanceof ContentEntityType) {
          $contentEntityType[$entity_type_id] = $entity_type;
        }
      }
    }
    return $contentEntityType;
  }
}