<?php
  use_helper('I18N', 'Javascript');

  include_partial('install/styles');

	// Result messages
	switch($result){
	  case 0:
	    $message = __('<h1>An error occoured</h1><p>Cannot connect to database with the provided data. Please check your data.</p>');
	    break;
    case 1:      
	    $message = __('<h1>Success!</h1><p>The installation has been correctly completed.</p>');
	    break;
	  case 2:
	    $message = __('<h1>An error occoured</h1><p>The database does not exist. Please create the database on your own or check the Create database checkbox to let W3studioCMS creates it for you.</p>');
	    break;
    case 4:
	    $message = __('<h1>An error occoured</h1><p>Something was wrong during the configurations. Please show details to learn more.</p>');
	    break;
	}
	echo $message;

  if ($form != null) include_partial('renderForm', array('form' => $form));

  if ($responseMessage != '')
  {
    echo '<style>
            #w3s_resp{
              display: none;
            }
          </style>';
    echo '<div id="w3s_resp"><pre>' . $responseMessage . '</pre></div>';
    echo '<p>' . link_to_function(__('Show installation details'), 'document.getElementById(\'w3s_resp\').style.display = \'block\';') . '</p>';
  }
  
  if($result == 1)
  {
    echo '<p>To start using W3studioCMS click link below to go to the home page of your website, then click the "Edit site" button and type admin as username and admin as password.</p>';
    echo '<p>' . link_to('Start using W3studioCMS' , url_for('/english/index.html')) . '</p>';
    echo '<p>Thank you for using W3studioCMS.</p>';
  }