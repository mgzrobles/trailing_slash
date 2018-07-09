<?php

namespace Drupal\trailing_slash\PathProcessor;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Routing\AdminContext;
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
   * Var to collect which paths I'm checking to prevent maximum function nesting level.
   * @var array
   */
  private $checkingPaths = [];

  /**
   * Var to collect which paths I checked before and prevent maximum function nesting level.
   * @var array
   */
  private $checkedPaths = [];

  /**
   * @var \Drupal\Core\Routing\AdminContext
   */
  protected $adminContext;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * TrailingSlashOutboundPathProcessor constructor.
   * @param AdminContext $admin_context
   * @param EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(
    AdminContext $admin_context,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    $this->adminContext = $admin_context;
    $this->entityTypeManager = $entity_type_manager;
  }

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
   * @param array $options
   * @param Request $request
   * @param BubbleableMetadata $bubbleable_metadata
   *
   * @return bool
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function isPathWithTrailingSlash($path, array &$options = [], Request $request = NULL, BubbleableMetadata $bubbleable_metadata = NULL) {
    $is_path_with_trailing_slash = FALSE;
    if (!in_array($path, $this->checkingPaths, TRUE)) {
      $this->checkingPaths[] = $path;
      if (
        TrailingSlashSettingsHelper::isEnabled()
        && $path !== '<front>'
        && !empty($path)
        && !$this->isAdminPath($path)
        &&
        (
          $this->isPathInListWithTrailingSlash($path)
          || $this->isBundleWithTrailingSlash($path)
        )
      ) {
        $is_path_with_trailing_slash = TRUE;
      }
      $this->checkedPaths[$path] = $is_path_with_trailing_slash;
    }
    if (array_key_exists($path, $this->checkedPaths)) {
      return $this->checkedPaths[$path];
    }
    return FALSE;
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
    $url = Url::fromUri('internal:' . $path);
    if ($url->isRouted()) {
      $route_name = $url->getRouteName();
      // I can't inject the service because it would involve circular dependence.
      $route = \Drupal::service('router.route_provider')->getRouteByName($route_name);
      return $this->adminContext->isAdminRoute($route);
    }
    return FALSE;
  }

  /**
   * @param $path
   *
   * @return bool
   */
  public function isPathInListWithTrailingSlash($path) {
    $paths = TrailingSlashSettingsHelper::getActivePaths();
    return in_array($path, $paths);
  }

  /**
   * @param $path
   *
   * @return bool
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function isBundleWithTrailingSlash($path) {
    $bundles = TrailingSlashSettingsHelper::getActiveBundles();
    if (!empty($bundles)) {
      $contentEntityType = TrailingSlashSettingsHelper::getContentEntityType();
      $contentEntityTypeKeys = array_keys($contentEntityType);
      $url = Url::fromUri('internal:' . $path);
      try {
        if ($url->isRouted() && $params = $url->getRouteParameters()) {
          $entity_type = key($params);
          if (in_array($entity_type, $contentEntityTypeKeys)) {
            $entity = $this->entityTypeManager->getStorage($entity_type)->load($params[$entity_type]);
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
