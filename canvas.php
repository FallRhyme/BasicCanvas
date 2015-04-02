<?php 
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>HTML5 Canvas塗鴉板</title>
	<link rel="stylesheet" href="./jquery-ui.css" />
	<script src="./jquery-1.10.2.js"></script>
	<script src="./jquery-ui.js"></script>
<script type="text/javascript" src="./jquery.ajaxfileupload.js"></script>
<script type="text/javascript" src="./html2canvas.js"></script>
    <style>
        body,input { font-size: 9pt; }
        #dCanvas,#dLine { clear: both; }
        .option
        {
            float: left; width: 20px; height: 20px; border: 2px solid #cccccc;
            margin-right: 4px; margin-bottom: 4px;
        }
        .active { border: 2px solid black; }
        .lw { text-align: center; vertical-align: middle; }
        img.output { border: 1px solid green; }
        #cSketchPad { cursor: arrow; }
		
		 #keyin {
	z-index:5;
 	border: none;
 	display: none;
 	position: absolute;
 	vertical-align: middle;
 	padding: 0 0 0 0;
 	margin: 0 0 0 0;
 	line-height: 24px;
 }
    </style>
    <script>
        $(function () {
			data = [];
			start = [];
			type="drew";
			setother=new Array();//設定參數
			setdrewtype=new Array();//類型
			setstep=new Array(); //步驟
		    //setcolor=new Array(); //顏色
			settext=new Array(); //文字內容
		    //settextwidth=new Array(); //文字寬度
			nowsetother=["","",4,1,"",""]; //類型 顏色 寬度 橫直 粗細 傾斜
			step=1;
            //產生不同顏色的div方格當作調色盤選項
            var colors =
            "red;orange;yellow;green;blue;indigo;purple;black;white".split(';');
            var sb = [];
            $.each(colors, function (i, v) {
                sb.push("<div class='option' style='background-color:" + v + "'></div>");
            });
            $("#dPallete").html(sb.join("\n"));
            //產生不同尺寸的方格當作線條粗細選項
            sb = [];
            for (var i = 1; i <= 9; i++)
                sb.push("<div class='option lw'>" +
         "<div style='margin-top:#px;margin-left:#px;width:%px;height:%px'></div></div>"
                .replace(/%/g, i).replace(/#/g, 10 - i / 2));
            $("#dLine").html(sb.join('\n'));
            var $clrs = $("#dPallete .option");
            var $lws = $("#dLine .option");
            //點選調色盤時切換焦點並取得顏色存入p_color，
            //同時變更線條粗細選項的方格的顏色
            $clrs.click(function () {
                $clrs.removeClass("active");
                $(this).addClass("active");
                p_color = this.style.backgroundColor;
                $lws.children("div").css("background-color", p_color);
            }).first().click();
            //點選線條粗細選項時切換焦點並取得寬度存入p_width
            $lws.click(function () {
                $lws.removeClass("active");
                $(this).addClass("active");
                p_width =
                    $(this).children("div").css("width").replace("px", "");
 
            }).eq(3).click();
            //取得canvas context
            var $canvas = $("#cSketchPad");
            var ctx = $canvas[0].getContext("2d");
            ctx.lineCap = "round";
            //ctx.fillStyle = "white"; //整個canvas塗上白色背景避免PNG的透明底色效果
            //ctx.fillRect(0, 0, $canvas.width(), $canvas.height());
            var drawMode = false;
            //canvas點選、移動、放開按鍵事件時進行繪圖動作
            $canvas.mousedown(function (e) {
                ctx.beginPath();
                ctx.strokeStyle = p_color;
                ctx.lineWidth = p_width;
                ctx.moveTo(e.pageX - $canvas.position().left, e.pageY - $canvas.position().top);
                drawMode = true;
				if(type=="inserttext"){
			t = $('#keyin');
 			t.css('left', e.pageX+1+'px');
 			t.css('top', e.pageY-t.outerHeight()/2+1+'px');
			start[0]=e.pageX - $canvas.position().left; start[1]=e.pageY - $canvas.position().top; //文字起點
 			t.css('display', 'block');
			t.css('border','3px green dotted');
 			t.focus();
				}
            })
            $canvas.mousemove(function (e) {
				if(type!="inserttext"&&type=="drew"){
                if (drawMode) {
                    ctx.lineTo(e.pageX - $canvas.position().left, e.pageY - $canvas.position().top);
					var x=e.pageX - $canvas.position().left;
					var y=e.pageY - $canvas.position().top;
                    ctx.stroke(); 
//記錄節點
data.push(x+"|"+y);
          }
				}
            })
            $canvas.mouseup(function (e) {
				if(type=="drew"&&type!="inserttext"){
					if (drawMode) {
                drawMode = false;
				setstep[step]=data;
				setother[step]="drew|"+p_color+"|"+p_width; //類型 顏色 寬度 橫直 粗細
				step++;
				data = new Array();
					}
				}else{
					
				}
				
            });
			$canvas.mouseleave(function (e) {
				if(type=="drew"&&type!="inserttext"){
					if (drawMode) {
                drawMode = false;
				setstep[step]=data;
				setother[step]="drew|"+p_color+"|"+p_width; //類型 顏色 寬度 橫直 粗細
				step++;
				data = new Array();
					}
				}else{
					
				}
				
            });
            //利用.toDataqURL()將繪圖結果轉成圖檔
            $("#bGenImage").click(function () {
                $("#dOutput").html(
                $("<img />", { src: $canvas[0].toDataURL(),
                    "class": "output"
                }));
            });
			$("#lookallstep").click(function () {
             //document.write(step);
			 alert(step);
			 //document.write(setstep);
            });
			$("#redrew").click(function () { //重繪
			ctx.fillStyle = "white"; //整個canvas塗上白色背景避免PNG的透明底色效果
            ctx.fillRect(0, 0, $canvas.width(), $canvas.height());
			for(x=1;x<step;x++){
			var aaa=setstep[x].length; //步驟內含所有節點
		     ctx.beginPath();
			 var other = setother[x].split("|"); //類型 顏色 寬度 橫直 粗細
			if(other[0]=="inserttext"){ //寫字
			var kkk = setstep[x][0].split("|"); 
			if(other[3]==1){
			//分割出 起始X座標,起始Y座標,最後X座標,最後Y座標
			ctx.moveTo(kkk[0],kkk[1]);
			ctx.font=""+other[5]+" normal "+other[4]+" "+other[2]*5+"px Arial";
			ctx.fillStyle=other[1];
			ctx.fillText(settext[x],kkk[0],kkk[1]); 
			}else if(other[3]==2){
			//分割出 起始X座標,起始Y座標,最後X座標,最後Y座標
			ctx.moveTo(kkk[0],kkk[1]);
			ctx.font=""+other[5]+" normal "+other[4]+" "+other[2]*5+"px Arial";
			ctx.fillStyle=other[1];
			var textlength=settext[x].length;
			var textarray =settext[x].split(""); 
            for(i=0;i<textlength;i++){
			ctx.fillText(textarray[i],kkk[0],kkk[1]*1+i*other[2]*5);
			}}}
			else{ //畫線
			ctx.strokeStyle = other[1];
			ctx.lineWidth = other[2];
			for(i=0;i<aaa;i++){
			var kkk = setstep[x][i].split("|"); 
			//分割出 起始X座標,起始Y座標,最後X座標,最後Y座標
			if(i<(aaa-1)){	
			var kkk2 = setstep[x][i+1].split("|");
			}
			ctx.moveTo(kkk[0],kkk[1]);
if(i<(aaa-1)){			
			ctx.lineTo(kkk2[0],kkk2[1]);
}else{ctx.lineTo(kkk[0],kkk[1]);}
                    ctx.stroke(); 
			}
			 }
			}
			 drawMode = false;
            });
			$("#reset").click(function () { //重畫
			 //drawMode = false;
			 //document.write(step);
			 ctx.fillStyle = "white"; //整個canvas塗上白色背景避免PNG的透明底色效果
             ctx.fillRect(0, 0, $canvas.width(), $canvas.height());
             step=1;
            });
			$("#backstep").click(function () { //上一步
			ctx.fillStyle = "white"; //整個canvas塗上白色背景避免PNG的透明底色效果
            ctx.fillRect(0, 0, $canvas.width(), $canvas.height());
			if(step>1){
			step = step-1; //總共有幾個已畫步驟
			}
			for(x=1;x<step;x++){
			var aaa=setstep[x].length; //步驟內含所有節點
		     ctx.beginPath();
			var other = setother[x].split("|"); //類型 顏色 寬度 橫直 粗細
			if(other[0]=="inserttext"){ //寫字
			var kkk = setstep[x][0].split("|"); 
			if(other[3]==1){
			//分割出 起始X座標,起始Y座標,最後X座標,最後Y座標
			ctx.moveTo(kkk[0],kkk[1]);
			ctx.font=""+other[5]+" normal "+other[4]+" "+other[2]*5+"px Arial";
			ctx.fillStyle=other[1];
			ctx.fillText(settext[x],kkk[0],kkk[1]);
			}else if(other[3]==2){
			//分割出 起始X座標,起始Y座標,最後X座標,最後Y座標
			ctx.moveTo(kkk[0],kkk[1]);
			ctx.font=""+other[5]+" normal "+other[4]+" "+other[2]*5+"px Arial";
			ctx.fillStyle=other[1];
			var textlength=settext[x].length;
			var textarray =settext[x].split(""); 
            for(i=0;i<textlength;i++){
			ctx.fillText(textarray[i],kkk[0],kkk[1]*1+i*other[2]*5);
			}}}else{ //畫線
			ctx.lineWidth = other[2];
			ctx.strokeStyle = other[1];
			for(i=0;i<aaa;i++){
			var kkk = setstep[x][i].split("|"); 
			//分割出 起始X座標,起始Y座標,最後X座標,最後Y座標
			if(i<(aaa-1)){	
			var kkk2 = setstep[x][i+1].split("|");
			}
			ctx.moveTo(kkk[0],kkk[1]);
if(i<(aaa-1)){			
			ctx.lineTo(kkk2[0],kkk2[1]);
}else{ctx.lineTo(kkk[0],kkk[1]);}
                    ctx.stroke(); 
			}
			 }
			}
			 drawMode = false;
            });
		$('#keyin').bind('keypress', function(e) { //輸入中文  
			var x=start[0];
			var y=start[1];
 		if(e.which=='13') {
			if(nowsetother[3]==1){
			ctx.fillStyle=p_color;
			ctx.font=""+nowsetother[5]+" normal "+nowsetother[4]+" "+p_width*5+"px Arial";
			var nowtext=$(this).val();
			ctx.fillText($(this).val(),x,y);
 			$(this).css('display', 'none');
 			$(this).val('');
			//記錄節點
data.push(x+"|"+y);
drawMode = false;
				setstep[step]=data;
				setother[step]="inserttext|"+p_color+"|"+p_width+"|"+nowsetother[3]+"|"+nowsetother[4]+"|"+nowsetother[5]; //類型 顏色 寬度 橫直 粗細 斜體
				
				settext[step]=nowtext;//文字內容
				//alert(settext[step]);
				step++;
				data = new Array();
			}else if(nowsetother[3]==2){
			ctx.fillStyle=p_color;
			ctx.font=""+nowsetother[5]+" normal "+nowsetother[4]+" "+p_width*5+"px Arial";
			var nowtext=$(this).val();
			var textlength=nowtext.length;
			var textarray = nowtext.split(""); 
            for(i=0;i<textlength;i++){
			ctx.fillText(textarray[i],x,y*1+i*p_width*5);
			}
 			$(this).css('display', 'none');
 			$(this).val('');
			//記錄節點
data.push(x+"|"+y);
drawMode = false;
				setstep[step]=data;
				setother[step]="inserttext|"+p_color+"|"+p_width+"|"+nowsetother[3]+"|"+nowsetother[4]+"|"+nowsetother[5]; //類型 顏色 寬度 橫直 粗細 斜體
				settext[step]=nowtext;//文字內容
				//alert(settext[step]);
				step++;
				data = new Array();
			}
 		}
 	});	
	$("#writetext").click(function (){
			if(type=="inserttext"&&nowsetother[3]==1){type="drew"; drawMode = false; $('#keyin').css('display', 'none');
 			$('#keyin').val(''); nowsetother[3]=1;}else{type="inserttext"; nowsetother[3]=1;}
		});	
	$("#writetext2").click(function (){
			if(type=="inserttext"&&nowsetother[3]==2){type="drew"; drawMode = false; $('#keyin').css('display', 'none');
 			$('#keyin').val(''); nowsetother[3]=1;}else{type="inserttext"; nowsetother[3]=2;}
		});	
    $("#boldtext").click(function (){
			if(nowsetother[4]=="bold"){  nowsetother[4]="";}else{ type="inserttext";nowsetother[4]="bold";}
		});	
    $("#italictext").click(function (){
			if(nowsetother[5]=="italic"){ nowsetother[5]="";}else{ type="inserttext"; nowsetother[5]="italic";}
		});	
	
$("#picurl").AjaxFileUpload({
				onComplete: function(filename, response) {
					var c=document.getElementById("cSketchPad");
                    var ctx=c.getContext("2d");
					var img=new Image();
					img.onload = function() {
                    var w = 800,h = 600;
                    ctx.drawImage(this, 0, 0, w, h);
  }
img.src="./images/"+filename+"";
				}
			})			
        });
	function uploadpic(){
	$("#picurl").trigger("click");
}
	//儲存圖片
function savenowimage() {
	html2canvas($("#cSketchPad"), {
        onrendered: function(canvas) {
	   var myImage = canvas.toDataURL();
	$.ajax({
	type: "POST",
    url: "storepic.php",
	data: {keyNum:"1",tables:"storepic",imgsrc:myImage},
    success: function(response) {
    window.open('./download.php?filename='+response);		
    }
 });
       }
		});
}	
   
    </script>
	
</head>
<body>
<div id="dPallete"></div>
<div id="dLine"></div>
<div id="dCanvas">
<canvas id="cSketchPad" width="800" height="600" style="border: 2px solid gray" />
</div>
<input type="button" id="bGenImage" value="轉圖片檔" />
<?php /*?>
<input type="button" id="lookallstep" value="lookallstep" />
<?php */?>
<input type="button" id="redrew" value="重繪" />
<input type="button" id="reset" value="重置" />
<input type="button" id="backstep" value="回上一步" />
<input type="button" id="writetext" value="橫式文字" />
<input type="button" id="writetext2" value="直式文字" />
<input type="button" id="boldtext" value="粗體文字" />
<input type="button" id="italictext" value="斜體文字" />
<input type="button" id="savenowimage" onclick="savenowimage()" value="存圖" />
<input type="button" id="showpic" value="秀圖" onclick="uploadpic()" />
<form  method="post" enctype="multipart/form-data" id="form" action="./uploadimg.php">
 <input type="file" name="file" id="picurl" accept="image/png,image/gif,image/jpeg,image/jpg"  style="display:none;" >
<img id="scream" src="1.jpg" alt="The Scream" width="800" height="600" style="display:none">
</form>
<input id="keyin" size="18" />

<div id="dOutput"></div>
</body>
</html>