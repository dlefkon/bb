<style type="text/css">
// WebKit CSS gradient
-webkit-gradient(linear, left top, right top,
  from(#4b4c4d),
  color-stop(0.249, #4b4c4d),
  color-stop(0.25, #575b5c),
  color-stop(0.329, #575b5c),
  color-stop(0.33, #6b7071),
  color-stop(0.749, #6b7071),
  color-stop(0.75, #575b5c),
  color-stop(0.909, #575b5c),
  color-stop(0.91, #4b4c4d),
  to(#4b4c4d)
);
 </style>






<html>
 <head>
  <script type="application/x-javascript">
// mini-pico-tiny convenience micro-framework, ymmv
function $(id){ return document.getElementById(id); }
function html(id, html){ $(id).innerHTML = html; }
function css(id, style){ $(id).style.cssText += ';'+style; }
function anim(id, transform, opacity, dur){
  css(id, '-webkit-transition:-webkit-transform'+
    ',opacity '+(dur||0.5)+'s,'+(dur||0.5)+'s;-webkit-transform:'+
    transform+';opacity:'+(1||opacity));
}






    function draw() {
      var canvas = document.getElementById("canvas");
      if (canvas.getContext) {
        var ctx = canvas.getContext("2d");

        ctx.fillStyle = "rgb(200,0,0)";
        ctx.fillRect (10, 10, 55, 50);

        ctx.fillStyle = "rgba(0, 0, 200, 0.5)";
        ctx.fillRect (30, 30, 55, 50);




// <canvas> gradient
var gradient = $('canvas').getContext("2d").createLinearGradient(0,0,230,0);
gradient.addColorStop(0,'#4b4c4d');
gradient.addColorStop(0.249,'#4b4c4d');
gradient.addColorStop(0.25,'#575b5c');
gradient.addColorStop(0.329,'#575b5c');
gradient.addColorStop(0.33,'#6b7071');
gradient.addColorStop(0.749,'#6b7071');
gradient.addColorStop(0.75,'#575b5c');
gradient.addColorStop(0.909,'#575b5c');
gradient.addColorStop(0.91,'#4b4c4d');
gradient.addColorStop(1,'#4b4c4d');

      }
    }




  </script>
 </head>
 <body onload="draw();">
   <canvas id="canvas" width="150" height="150">
     <p>This example requires a browser that supports the
     <a href="http://www.w3.org/html/wg/html5/">HTML5</a> 
     &lt;canvas&gt; feature.</p>

   </canvas>
 </body>
</html>






