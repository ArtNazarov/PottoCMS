<form name='flng' id='flng'>    
    ~LANGS~
    <input type="button" name="chgLang" value='Применить' onclick="setlng();">
</form>

<script language='javascript' type='text/javascript'>
    function setlng()
    {
    url = document.location.href;
    lang = $("#lang :selected").val();   
     
    $.ajax({
            type: "POST",
            url: "/chglang.php",
            data: $('#flng').serialize()
            }).done(function( msg ) {            
  alert( "Язык установлен. " + msg +" Страница будет перезагружена");
  document.location.reload();
  
});
    }
    
    function reloadlng(id)
    {
    $("#lang [value='"+id+"']").attr("selected", "selected");
    setlng();
    }
</script>