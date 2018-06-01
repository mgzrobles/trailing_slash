<?php

namespace Drupal\trailing_slash\Helper\Settings;

use Drupal\Core\Entity\ContentEntityType;

/**
 * Class TrailingSlashSettingsHelper
 *
 * @package Drupal\trailing_slash\Helper\Settings
 */
class TrailingSlashSettingsHelper {

  /**
   * @return bool
   */
  public static function isEnabled() {
    $config = \Drupal::config('trailing_slash.settings');
    $enabled = $config->get('enabled');
    return isset($enabled) ? (bool) $enabled : FALSE;
  }

  /**
   * @return array
   */
  public static function getActiveBundles() {
    static $bundles;
    if (!$bundles) {
      $bundles = [];
      $config = \Drupal::config('trailing_slash.settings');
      $enabled_entity_types = unserialize($config->get('enabled_entity_types'));
      foreach ($enabled_entity_types as $entity_type_key => $entity_type) {
        $isBundleTrailingSlash = array_filter($entity_type);
        if (!empty($isBundleTrailingSlash)) {
          $bundles[$entity_type_key] = $isBundleTrailingSlash;
        }
      }
    }
    return $bundles;
  }

  /**
   * @return array
   */
  public static function getActivePaths() {
    static $active_paths;
    if (!$active_paths) {
      $config = \Drupal::config('trailing_slash.settings');
      $paths = $config->get('paths');
      $active_paths = explode("\n", str_replace("\r\n", "\n", $paths));
    }
    return $active_paths;
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
