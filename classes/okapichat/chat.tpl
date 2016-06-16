<script src="http://www.google.com/jsapi"></script>  
<script type="text/javascript">    
    google.load("jquery", "1.2.6");  
</script>	
<style>
#chatwnd
{ display:block; height:300px; width:100%; overflow-x:hidden;
overflow-y:scroll;
word-break: break-all}
</style>

<div id="chatwnd"></div>  
      
    <script> 
	
     function sendtoserv()
	 {
		 $.post("/chat", 
				 $("#chatform").serialize()
		 );
		 outchat();
		 document.getElementById("message").value = "";
	 }
	 
        function outchat()  
        {  
            $.ajax({  
                url: "/classes/okapichat/okapichat.frame.php",  
                cache: false,  
                success: function(html){  
                    $("#chatwnd").html(html);  
                }  
            });  
        }  
      
        $(document).ready(function(){  
            outchat();  
            setInterval('outchat()',5000);  
        });  
    </script>  

<!-- <iframe src="/classes/okapichat/okapichat.frame.php" width="100%" height="200"></iframe> -->

<form name="chatform" id="chatform" style="width:100%; overflow:hidden";>
<input type="hidden" name="refresh" value="OK" />
<label for="message">Сообщение:</label><br/>
<textarea name="message" id="message" rows="3" cols="40"></textarea><br/>
<input class="btn" type="button" value="Отправить сообщение" onclick="javascript:sendtoserv();" />
</form>