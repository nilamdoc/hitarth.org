<?php 
use lithium\storage\Session;
use app\extensions\action\Functions;
use lithium\util\String;
use li3_qrcode\extensions\action\QRcode;
$qrcode = new QRcode();

$user = Session::read('default');
$function = new Functions();
if($user['email']!=''){
?>
<div class="container">
<h2>Hi <?=$user['email']?></h2>
<div style="text-align:center ">
<script type="text/javascript" src="/js/qrcode/grid.js"></script>
<script type="text/javascript" src="/js/qrcode/version.js"></script>
<script type="text/javascript" src="/js/qrcode/detector.js"></script>
<script type="text/javascript" src="/js/qrcode/formatinf.js"></script>
<script type="text/javascript" src="/js/qrcode/errorlevel.js"></script>
<script type="text/javascript" src="/js/qrcode/bitmat.js"></script>
<script type="text/javascript" src="/js/qrcode/datablock.js"></script>
<script type="text/javascript" src="/js/qrcode/bmparser.js"></script>
<script type="text/javascript" src="/js/qrcode/datamask.js"></script>
<script type="text/javascript" src="/js/qrcode/rsdecoder.js"></script>
<script type="text/javascript" src="/js/qrcode/gf256poly.js"></script>
<script type="text/javascript" src="/js/qrcode/gf256.js"></script>
<script type="text/javascript" src="/js/qrcode/decoder.js"></script>
<script type="text/javascript" src="/js/qrcode/qrcode.js"></script>
<script type="text/javascript" src="/js/qrcode/findpat.js"></script>
<script type="text/javascript" src="/js/qrcode/alignpat.js"></script>
<script type="text/javascript" src="/js/qrcode/databr.js"></script>
<style>
.Address_success{background-color: #9FFF9F;font-weight:bold}
</style>
<script type="text/javascript">
var gCtx = null;
	var gCanvas = null;

	var imageData = null;
	var ii=0;
	var jj=0;
	var c=0;
	
	
function dragenter(e) {
  e.stopPropagation();
  e.preventDefault();
}

function dragover(e) {
  e.stopPropagation();
  e.preventDefault();
}
function drop(e) {
  e.stopPropagation();
  e.preventDefault();

  var dt = e.dataTransfer;
  var files = dt.files;

  handleFiles(files);
}

function handleFiles(f)
{
	var o=[];
	for(var i =0;i<f.length;i++)
	{
	  var reader = new FileReader();

      reader.onload = (function(theFile) {
        return function(e) {
          qrcode.decode(e.target.result);
        };
      })(f[i]);

      // Read in the image file as a data URL.
      reader.readAsDataURL(f[i]);	}
}
	
function read(a)
{
 $("#coingreenaddress").val(a);
 $("#SendAddress").html(a); 
 $("#coingreenaddress").addClass("Address_success");
 $("#coingreenaddressWindow").hide();
}	
	
function loadDiv()
{
	$("#coingreenaddressWindow").show();
	initCanvas(300,200);
	qrcode.callback = read;
	qrcode.decode("");
}

function initCanvas(ww,hh)
	{
		gCanvas = document.getElementById("qr-canvas");
		gCanvas.addEventListener("dragenter", dragenter, false);  
		gCanvas.addEventListener("dragover", dragover, false);  
		gCanvas.addEventListener("drop", drop, false);
		var w = ww;
		var h = hh;
		gCanvas.style.width = w + "px";
		gCanvas.style.height = h + "px";
		gCanvas.width = w;
		gCanvas.height = h;
		gCtx = gCanvas.getContext("2d");
		gCtx.clearRect(0, 0, w, h);
		imageData = gCtx.getImageData( 0,0,320,240);
	}

	function passLine(stringPixels) { 
		//a = (intVal >> 24) & 0xff;

		var coll = stringPixels.split("-");
	
		for(var i=0;i<320;i++) { 
			var intVal = parseInt(coll[i]);
			r = (intVal >> 16) & 0xff;
			g = (intVal >> 8) & 0xff;
			b = (intVal ) & 0xff;
			imageData.data[c+0]=r;
			imageData.data[c+1]=g;
			imageData.data[c+2]=b;
			imageData.data[c+3]=255;
			c+=4;
		} 

		if(c>=320*240*4) { 
			c=0;
      			gCtx.putImageData(imageData, 0,0);
		} 
 	} 

 function captureToCanvas() {
		flash = document.getElementById("embedflash");
		flash.ccCapture();
		qrcode.decode();

 }

</script>
<div class="panel panel-info">
  <div class="panel-heading"><strong>Withdraw from <?=number_format($details['balance.XGC'],8)?> XGC</strong></div>
  <div class="panel-body">
			<form action="/withdraw/payment/" method="post">
			<div class="input-group">										
				<input type="text" name="coingreenaddress" id="coingreenaddress" placeholder="15AXfnf7hshkwgzA8UKvSyjpQdtz34H9LE" class="form-control" title="To Address" data-content="This is the GreenCoin Address of the recipient." value="" onblur="coingreenAddress();"/>
				<span class="input-group-addon">
					<a href="#" onclick="loadDiv();"><i class="glyphicon glyphicon-qrcode tooltip-x" rel="tooltip-x" data-placement="top" title="Scan using your webcam"></i></a>
				</span>
			</div>
			<p class="help-block">Enter The GreenCoin Address of the Recipient</p>
	</div>
	<div id="coingreenaddressWindow" style="display:none;margin:auto;border:1px solid gray;padding:2px;width:406px;text-align:center ">
	<object  id="iembedflash" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="400" height="300">
	<param name="movie" value="/js/qrcode/camcanvas.swf" />
	<param name="quality" value="high" />
	<param name="allowScriptAccess" value="always" />
	<embed  allowScriptAccess="always"  id="embedflash" src="/js/qrcode/camcanvas.swf" quality="high" width="400" height="300" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" mayscript="true"  />
	</object><br>
	<a onclick="captureToCanvas();" class="btn btn-primary">Capture</a>
	<canvas id="qr-canvas" width="400" height="300" style="display:none"></canvas>
	</div>
	<?php
	$max = (float)$details['balance.XGC'];
	?>
	<div style="padding:15px " class="">
		<?=$this->form->field('amount', array('label'=>'Amount', 'placeholder'=>'0.0', 'class'=>'form-control', 'max'=>$max,'min'=>'0.001','onFocus'=>'SuccessButtonDisable();','maxlenght'=>10)); ?>
	</div>
			<input type="hidden" id="maxValue" value="<?=$max?>" name="maxValue">
			<input type="hidden" id="txFee" value="<?=$txfee?>" name="txFee">							<br>
			<input type="hidden" id="TransferAmount" value="0" name="TransferAmount" onFocus="SuccessButtonDisable()">											
			<div class="alert alert-warning" id="XGCAlert" style="display:none"></div>
			<input type="button" value="Calculate" class="btn btn-primary" onclick="return CheckXGCPayment();">
			<input type="submit" value="Send" class="btn btn-success" onclick="return CheckXGCPayment();" disabled="disabled" id="SendXGCSuccessButton"> <br>
<br>
</div>
		<!-- Withdraw -->					
</div>
<?php }?>