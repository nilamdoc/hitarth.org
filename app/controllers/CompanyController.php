<?php
namespace app\controllers;
class CompanyController extends \lithium\action\Controller {
	public function about(){
		if(in_array('json',$this->request->params['args'])){
			$json = true;
		}
		
		$title = 'About us';
		if($json == true){
			return $this->render(array('json' =>  
				compact('title'),
			'status'=> 200));
		}

		return compact('title');

	}
	public function contact(){
		if(in_array('json',$this->request->params['args'])){
			$json = true;
		}
		
		$title = 'Contact us';
		if($json == true){
			return $this->render(array('json' =>  
				compact('title'),
			'status'=> 200));
		}

		return compact('title');

	}
}
?>