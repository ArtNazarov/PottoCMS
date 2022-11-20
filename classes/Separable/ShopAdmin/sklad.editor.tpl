<div style="-webkit-border-radius: 6px;
		-moz-border-radius: 6px; padding:5px; margin:5px;
		border-radius: 6px; background-color:#ffffff;">
<form method="POST" action="/sklad/~FORM_ACTION~" >
<input type="hidden" name="old_artikul" value="~ARTIKUL~"/>
Артикул:<br/>
<input type="text" name="artikul" value="~ARTIKUL~"/><br/>
Тип товара: ~CATEGORY~<br/>
Фотография:<br/>
~ZPHOTO~ <br/>
<div id='section1' style='display:none;z-index:99;
     position:absolute; top:0;left:0;
     border: #0099CC thin dashed;
     width:100%; height:auto; background-color:#9F9;'>
<input type='button' value="Скрыть селектор изображений"
        onclick='document.getElementById("section1").style.display="none";'></br>    
~PHOTO~
</div>
<input type='button' value="Показать селектор изображений"
        onclick='document.getElementById("section1").style.display="block";'>
<br/>Краткое описание ( название и размер ) :<br/>
<textarea name="captiontxt" cols="50" rows="2">~CAPTIONTXT~</textarea><br/>
Полное описание ( подробная комплектация ) :<br/>
<textarea name="description" cols="50" rows="10">~DESCRIPTION~</textarea><br/>
Цена:<br/>
<input type="text" name="price" value="~PRICE~"/><br/>
Теги (метки через запятую):<br/>
<input type="text" name="tags" value="~TAGS~"/><br/>
Артикулы похожих товаров(через запятую):<br/>
<input type="text" name="see_also" value="~SEE_ALSO~"/><br/>
Количество:<br/>
<input type="text" name="count" value="~COUNT~"/><br/>
<hr/>
<br/>Примечание для служебного пользования:</br>
<textarea name="note" cols="50" rows="10">~NOTE~</textarea><br/>
<input type="submit" value="~BUTTON_NAME~"/>
</form>
</div>
<script type="text/javascript" src="/vendor/ckeditor/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
// CKEDITOR.replace( 'captiontxt');
CKEDITOR.replace( 'description');
</script>