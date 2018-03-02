/**
 * Created by Administrator on 15-6-29.
 */
var canvas_width=500,canvas_height=500;
var mycanvas, context;
window.onload=function(){
    createCanvas();
    //drawRect();
   drawImage();
}
function createCanvas(){

    document.body.innerHTML="<canvas id=\"mycanvas\" width=\""+canvas_width+"\" height=\""+canvas_height+"\"></canvas>";
        mycanvas=document.getElementById("mycanvas");
        context=mycanvas.getContext('2d');
}
function drawRect(){
    context.fillStyle="#ff0000";
    context.ratate(45);
    context.translate(200,200);
    context.scale(2,0.5);
 //   context.fillRect(0,0,200,200);
}
function drawImage(){
    var image=new Image();
    image.onload=function(){
        context.drawImage(image,0,0);
    }
    image.src=pen.png;
}