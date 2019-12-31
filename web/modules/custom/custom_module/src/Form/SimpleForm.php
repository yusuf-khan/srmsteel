<?php 
namespace Drupal\custom_module\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


Class SimpleForm extends FormBase {



public function getFormId(){

return 'simple_form';	
}

public function buildForm(array $form, FormStateInterface $form_state){

$form['name'] = array(
      '#type' => 'textfield',
      '#title' => t('Candidate Name:'),
      '#required' => TRUE,
    );

$form['cell_number'] = array (
      '#type' => 'tel',
      '#title' => t('Mobile no'),
    );
    $form['dob'] = array (
      '#type' => 'date',
      '#title' => t('DOB'),
      '#required' => TRUE,
    );
    $form['candidate_confirmationgender'] = array (
      '#type' => 'select',
      '#title' => ('Gender'),
      '#options' => array(
        'Female' => t('Her'),
        'male' => t('His'),
      ),
    );
    $form['candidate_confirmation'] = array (
      '#type' => 'radios',
      '#title' => ('Are you above 18 years old?'),
      '#options' => array(
        'Yes' =>t('Yes'),
        'No' =>t('No')
      ),
    );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );
    return $form;
}

  public function submitForm(array &$form, FormStateInterface $form_state) {
$values = array(
		'title' => $form_state->getValue('name'),
		'mobilenumber' => $form_state->getValue('cell_number'),
		'description' => $form_state->getValue('dob'),
		'gender' => $form_state->getValue('candidate_confirmationgender'),
		'confirmation' => $form_state->getValue('candidate_confirmation'),
		
	);

$insert = db_insert('form_data')
		-> fields(array(
			'name' => $values['title'],
			'mobilenumber' => $values['mobilenumber'],
			'confirmation' => $values['description'],
			'gender' => $values['gender'],
            'age' => $values['confirmation'],
		))
		->execute();
	
	drupal_set_message(t('Settings have been saved'));

}




}