<?php

namespace Drupal\custom_module\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;


class FormController extends ControllerBase  {


function create_new_user(){
  $user = User::create();

  //Mandatory settings
  $user->setPassword('cmpusdnk17');
  $user->enforceIsNew();
  $user->setEmail('khany7asd62@gmail');

  //This username must be unique and accept only a-Z,0-9, - _ @ .
  $user->setUsername('yusuddf_23');

  // //Optional settings
  // $language = 'en';
  // $user->set("init", 'email');
  // $user->set("langcode", $language);
  // $user->set("preferred_langcode", $language);
  // $user->set("preferred_admin_langcode", $language);
  // $user->activate();

  //Save user
  $user->save();
  drupal_set_message("User with uid " . $user->id() . " saved!\n");
  return $theme = array(
  	'#type' => 'markup', 
  	'#markup'=>'this is test page',

);

}

function create_node(){
  //Mandatory settings
  $node = Node::create([
  'type'        => 'article',
  'title'       => 'Druplicon test',
  'body'=> 'body goes here!',
]);
$node->save();

  drupal_set_message("node with nid " . $node->id() . " saved!\n");
  return $theme = array(
  	'#type' => 'markup', 
  	'#markup'=>'this is test page',

);

}
function create_taxonmy(){
 $term =Term::create([

'name' => 'term 1',

'vid' => 'new',

])

->save();
  return $theme = array(
  	'#type' => 'markup', 
  	'#markup'=>'this is test page',

);
}
}

