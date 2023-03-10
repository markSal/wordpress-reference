<?php
  // wp_enqueue_script = I want this script to be inserted now
  // wp_register_script = I want to make a reference to this script but I don't want to insert it yet
  // wp_localize_script = I want to send data to a script that's going to be inserted (used for sending PHP vars, arrays, or objects to JS)
  
  // In the example below there is folder name "Clients" on Google Drive, 
  // within that folder are subfolders for each client, each folder is named using the busienss name
  

  // EXAMPLE: Function to build array containing values for autocomplete dropdown that gets passed to JS script
  // Only works when creating new posts or editing posts with "Clients" custom post type 
  // Array is built from Google Drive API request to grab all subfolders in a parent folder
  // Array data is sent to JS using Wordpress enqueing for scripts
	function enqueue_client_autocomplete_deps($hook_suffix){
    
    // Check if this is 'create new post' or 'edit post' screen in Wordpress Dashboard/Admin
		if(in_array($hook_suffix, array('post.php', 'post-new.php'))){
			$current_screen = get_current_screen();
      
      // Check if current post type is "Clients"
			if(is_object( $current_screen ) && $current_screen->post_type == 'clients'){

				// Prepare list of clients to populat autocomplete drop-down
          // Make sure Google Drive Service API is available
          global $google_service;
          
          // Get Google Drive parent folder name from Wordpress options
				  $clients_folder = get_field('google_clients_folder', 'option');
          
          // Get all client subfolders from parent folder on Google Drive
				  // TODO: Change g_getFolderContents to use folder ID instead of folder NAME
				  $clientsFolders = g_getFolderContents($google_service, $clients_folder['label'], 'folders');
        
        // Loop through response containing client subfolder information from Google Drive API response
				$i = 0;
				$svr_clients = array();
				foreach($clientsFolders->files as $folder){
          // Get client subfolder name (bussines name)
          $svr_clients[$i]['name'] = $folder->name;
					
          // Get folder id of subfolder
          $svr_clients[$i]['id'] = $folder->id;
					$i++;
				}
        
        // Create array to pass data to JS
				$phpData = array();
        
        // Add wordpress endpoint to array going to JS to use in ajax request
				$phpData['ajaxUrl'] = admin_url('admin-ajax.php');
        
        // Add client subfolder information to array going to JS
				$phpData['clients'] = $svr_clients;

				// Queue up javascript and send data from php
				wp_enqueue_script('svr-client-admin', SVR_PLUGIN_URL . 'js/clients.js', false, '0.1.0');
				wp_localize_script('svr-client-admin', 'phpData', $phpData);
			}
		}
	}
  
  // Hook this function the admin admin_enqueue_scripts action so it fires when the admin scripts are loaded
	add_action('admin_enqueue_scripts', 'enqueue_client_autocomplete_deps', 10, 1);
 ?>
