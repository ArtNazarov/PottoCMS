<script src="/CodeMirror-2.24/lib/codemirror.js"></script>
<link rel="stylesheet" href="/CodeMirror-2.24/lib/codemirror.css">
<script src="/CodeMirror-2.24/mode/javascript/javascript.js"></script>
Каталог: ~DIROBJECT~ <br/>
<form name="fmjs" enctype="multipart/form-data" action="/admin/filemanager" method="post">
<input type="hidden" name="action" value="~ACTION~" />
Имя файла:<br/>
<input type="text" name="filename" value="~FILENAME~" /><br/>
Текст файла:<br/>
<textarea name="lines" id='lines' cols="80" rows="40">~LINES~</textarea><br/>
<input type="submit" value="Сохранить файл" /><br/>
<input type="hidden" name="dirobject" value="~DIROBJECT~" />
<input type="hidden" name="selobjects" value="~SELOBJECTS~" />
<input type="hidden" name="clipdir" value="~CLIPDIR~" />
<input type="hidden" name="clipsel" value="~CLIPSEL~" />
<input type="hidden" name="clipflag" value="~CLIPFLAG~" />
</form>
<script>
    var myCodeMirror = CodeMirror.fromTextArea(document.getElementById('lines'));                
</script>    

