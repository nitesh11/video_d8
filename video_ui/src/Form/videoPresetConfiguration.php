<?php

/**
 * @file
 * Contains \Drupal\system\Form\RssFeedsForm.
 */

namespace Drupal\video_ui\Form;

use Drupal\video\Preset;
use Drupal\Core\Url;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\String;

class videoPresetConfiguration extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'video_preset_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    // $transcoder = new Transcoder();
    $presets = Preset::getAllPresets();

    $form['video_use_preset_wxh'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use preset dimensions for video conversion.'),
      '#default_value' => \Drupal::config('video_ui.settings')->get('video_use_preset_wxh') ?: FALSE,
      '#description' => t('Override the user selected dimensions with the value from the presets (recommended).')
    );

    if (!empty($presets)) {
      $selected = array_filter(\Drupal::config('video_ui.settings')->get('video_preset') ?: array());

      $form['video_preset'] = array(
        '#tree' => TRUE,
      );

      foreach ($presets as $preset) {
        // dsm($preset);  
        $delete = NULL;
        if (empty($preset['module']) && !in_array($preset['name'], $selected)) {
          $delete = array('#type' => 'link', '#title' => \Drupal::l(t('delete'), Url::fromRoute('video_ui.preset_setting') . '/preset/' . $preset['name'] . '/delete'));
        }
        elseif ($preset['overridden']) {
          $delete = array('#type' => 'link', '#title' => \Drupal::l(t('revert'), Url::fromRoute('video_ui.preset_setting') . '/preset/' . $preset['name'] . '/revert'));
        }

        $form['video_preset'][$preset['name']] = array(
          'status' => array(
            '#type' => 'checkbox',
            '#title' => String::checkPlain($preset['name']),
            '#default_value' => in_array($preset['name'], $selected),
          ),
          'description' => array('#markup' => !empty($preset['description']) ? String::checkPlain($preset['description']) : ''),
          'edit' => array('#type' => 'link', '#title' => \Drupal::l(t('edit'), Url::fromRoute('video_ui.preset_setting') . '/preset/' . $preset['name'])),
          'delete' => $delete,
          'export' => array('#type' => 'link', '#title' => \Drupal::l(t('export'), Url::fromRoute('video_ui.preset_setting') . '/preset/' . $preset['name'] . '/export')),
        );
      }
    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = \Drupal::config('video_ui.settings');
    $userInputValues = $form_state->getUserInput();
    $userInputValues['video_use_preset_wxh'] = (bool)$userInputValues['video_use_preset_wxh'];

    $selected = array();

    if (isset($form['video_preset'])) {
      foreach ($userInputValues['video_preset'] as $name => $values) {
        if ($values['status'] == 1) {
          $selected[] = $name;
        }
      }
    }

    // Issue
    $userInputValues['video_preset'] = $selected;
  }
}