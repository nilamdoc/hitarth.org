<?php
namespace app\controllers;
use lithium\storage\Session;

class LogoutController extends \lithium\action\Controller {
	public function index(){
		Session::delete('default');						
		if(in_array('json',$this->request->params['args'])){
			$json = true;
		}
		$title = "Logout";
		if($json == true){
			return $this->render(array('json' =>  
				compact('title'),
			'status'=> 200));
		}
	

		return $this->redirect('/');
	}

}?>