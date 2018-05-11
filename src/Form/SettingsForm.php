<?php
/**
 * @file
 * Contains Drupal\trailing_slash\Form\SettingsForm.
 */

namespace Drupal\trailing_slash\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\trailing_slash\Helper\Settings\TrailingSlashSettingsHelper;

/**
 * Class SettingsForm
 *
 * @package Drupal\trailing_slash\Form
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'trailing_slash.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'trailing_slash_admin_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('trailing_slash.settings');

    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $config->get('enabled')
    ];

    $form['paths'] = [
      '#type' => 'textarea',
      '#title' => $this->t('List of paths'),
      '#description' => $this->t("Write per line a path where you want a trailing slash. Paths start with slash. (e.g., '/book')"),
      '#default_value' => $config->get('paths'),
    ];

    $form['enabled_entity_types'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('Enabled entity types'),
      '#description' => $this->t('Enable to add a trailing slash for the given type.'),
      '#tree' => TRUE,
    ];

    $entities = TrailingSlashSettingsHelper::getContentEntityType();
    $bundle_info = \Drupal::service('entity_type.bundle.info')->getAllBundleInfo();
    $enabled_entity_types = unserialize($config->get('enabled_entity_types'));
    foreach ($entities as $entity_type_id => $entity_type) {
      $entity_type_bundles = $bundle_info[$entity_type_id];
      $form['enabled_entity_types'][$entity_type_id] = [
        '#type' => 'details',
        '#open' => TRUE,
        '#title' => $entity_type->getLabel(),
        '#tree' => TRUE,
      ];
      foreach ($entity_type_bundles as $bundle_id => $bundle) {
        $form['enabled_entity_types'][$entity_type_id][$bundle_id] = [
          '#type' => 'checkbox',
          '#title' => $bundle['label'],
          '#default_value' => $enabled_entity_types[$entity_type_id][$bundle_id],
        ];
      }
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('trailing_slash.settings')
      ->set('enabled', $form_state->getValue('enabled'))
      ->set('paths', $form_state->getValue('paths'))
      ->set('enabled_entity_types', serialize($form_state->getValue('enabled_entity_types')))
      ->save();
  }

}
