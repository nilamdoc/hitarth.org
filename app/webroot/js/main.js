//JS Document
function SendPassword(){
	$.getJSON('/Users/SendPassword/'+$("#Email").val(),
		function(ReturnValues){
			if(ReturnValues['Password']=="Password Not sent"){
				$("#emailIcon").attr("class", "glyphicon glyphicon-remove");
				$("#LoginEmailPassword").hide();
				return false;
			}
			$("#LoginEmailPassword").show();
			$("#emailIcon").attr("class", "glyphicon glyphicon-ok");
		}
	);
}
function CheckEmail(value,email){
	if(value==""){
		$("#emailIcon").attr("class", "glyphicon glyphicon-remove");
		$("#EmailError").html("Cannot be blank!");		
		$("#Error").val("Yes");		
		return false;
	}
	if(value.toLowerCase()==email.toLowerCase()){
		$("#emailIcon").attr("class", "glyphicon glyphicon-remove");
		$("#EmailError").html("Cannot send to self!");
		$("#Error").val("Yes");		
		return false;
	}else{
		$("#emailIcon").attr("class", "glyphicon glyphicon-ok");		
		$("#EmailError").html("If the user does not claim the amount within a week, then the amount will be returned (credited) back to your account.");
		$("#Error").val("No");
	}
}
function CheckAmount(value,amount){
	if(value>=amount)	{
		$("#amountIcon").attr("class", "glyphicon glyphicon-remove");
		$("#amountError").html("Amount does not match!")
		return false;
	}else{
		$("#amountIcon").attr("class", "glyphicon glyphicon-ok");		
		if($("#Error").val()=="No"){
			$("#SendButton").removeAttr('disabled');
		}else{
			$("#EmailError").html("Cannot send to self! or email blank!");			
			$("#SendButton").attr("disabled", "disabled");			
		}
	}
}
function initialAmount(value){
	$("#amountError").html("Amount should be less than "+ value);
}
function coingreenAddress(){}
function SuccessButtonDisable(){
	$("#SendXGCSuccessButton").attr("disabled", "disabled");
	}
function CheckXGCPayment(){
	$("#XGCAlert").hide();
	address = $("#coingreenaddress").val();
	if(address==""){
	$("#XGCAlert").html("Address incorrect"); 	
	$("#XGCAlert").show();
	return false;}
	amount = $("#Amount").val();
	if(amount==""){
	$("#XGCAlert").html("Not sufficient balance"); 	
	$("#XGCAlert").show();
	return false;}

	
	maxValue = $("#maxValue").val();
	if(parseFloat(amount)>parseFloat(maxValue)){
		$("#XGCAlert").html("Not sufficient balance"); 	
		$("#XGCAlert").show();
		return false;
		}
	$("#SendXGCFees").html($("#txFee").val());

	$("#SendXGCAmount").html(amount);	
	$("#SendXGCTotal").html(parseFloat(amount)-parseFloat($("#txFee").val()));	
	$("#TransferAmount").val(parseFloat(amount)-parseFloat($("#txFee").val()));

	$.getJSON('/withdraw/XGCAddress/'+address,
		function(ReturnValues){
			if(ReturnValues['verify']['isvalid']==true){
			address = "<a href='http://coingreen.com/address/"+ address +"' target='_blank'>"+ address +"</a> <i class='icon-ok'></i>";
			$("#SendXGCAddress").html(address); 	
			$("#SendXGCSuccessButton").removeAttr('disabled');				
				}
		});
	return true;
}
