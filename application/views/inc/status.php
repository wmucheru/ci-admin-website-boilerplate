<?php 
	$success = $this->nativesession->get('successMsg');
	$error = $this->nativesession->get('errorMsg');

    if($success || $error){
	 	if($success){
	 	    echo '<div class="alert alert-success">'.$success.'</div>';
        }
        if($error){
		    echo '<div class="alert alert-danger">'.$error.'</div>'; 
        }
		
		//Unset session variable
		$this->nativesession->delete('successMsg');
		$this->nativesession->delete('errorMsg');
    }
?>