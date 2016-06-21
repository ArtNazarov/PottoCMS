
<form name="fmjs" action="/admin/filemanager" method="post">
    <label for='ziparchive'>Путь к архиву</label>
    <input type='text' name='ziparchive' value='./install.zip' />    
    <label for='folderextract'>Куда распаковывать</label>
    <input type='text' name='folderextract' value='./' />    
<input type="hidden" name="action" value="~ACTION~" />
<input type="file" name="fupload" style="display:none" />
<input type="hidden" name="dirobject" value="~DIROBJECT~" />
<input type="hidden" name="selobjects" value="~SELOBJECTS~" />
<input type="hidden" name="clipdir" value="~CLIPDIR~" />
<input type="hidden" name="clipsel" value="~CLIPSEL~" />
<input type="hidden" name="clipflag" value="~CLIPFLAG~" />
<input type="hidden" name="clipflag" value="~CLIPFLAG~" />
<input type="submit" value="Перейти к каталогу" />
</form>