<form action="/feedback/~ACTION~" method="post">
<label for="mtheme">~FROM_CAPTION~</label><br />
<input type="text" name="mfrom" id="mfrom" /><br />
<label for="mtheme">~MTHEME_CAPTION~</label><br />
<input type="text" name="mtheme" id="mtheme" /><br />
<label for="mailbody">~MBODY_CAPTION~</label><br />
<textarea cols="50" rows="4" id="mailbody" name="mailbody">~DEF_TEXT~</textarea> <br />
~CAPTCHA~
<input class="btn" type="submit" value="~BUTTON~" />
</form>