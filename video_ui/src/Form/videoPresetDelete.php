<?php

/**
 * @file
 * Contains \Drupal\system\Form\RssFeedsForm.
 */

namespace Drupal\video_ui\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;


class videoPresetDelete extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'video_preset_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    if (in_array($preset['name'], variable_get('video_preset', array()))) {
    drupal_access_denied();
    exit;
  }
  $form['name'] = array('#type' => 'value', '#value' => $preset['name']);
  return confirm_form($form, t('Are you sure you want to delete the preset %name?', array('%name' => $preset['name'])), 'admin/config/media/video/presets', '<p>' . t('This action cannot be undone.') . '</p>', t('Delete'));
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  	if ($form_state['values']['confirm']) {
	    Preset::deletePreset($form_state['values']['name']);
	  }

	  $form_state['redirect'] = 'admin/config/media/video/presets';
  }
}