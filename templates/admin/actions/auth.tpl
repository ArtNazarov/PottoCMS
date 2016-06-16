<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Potto CMS :: ~PAGE~ </title>
<style>

</style>
</head>

<body>
<table width="100%" >
  <tr>
  <td colspan="3" align="center"><h1>Potto CMS :: ~PAGE~ </h1>    </tr>
  <tr>
<td width="25%"><td valign="top" align="left" width="50%">
<p>  ~SYS_MESSAGE~ </p>
<form method="post" action="/admin/~ACTION~">
  <p>
    <label for="user">Имя пользователя</label>
    <br />
    <input type="text" name="user" id="user" />
    <label for="password"><br />
      Пароль</label>
    <br />
    <input type="password" name="password" id="password" />
  </p>
  ~CAPTCHA~
  <p>
    <input style="cursor:pointer;padding:5px; background-color: #dcdcdc;  border: 1px solid #666; color:#000;  text-decoration:none;" type="submit" name="button" id="button" value="~BUTTON~" />
  </p>
  <p><a href="/admin/~ACTION2~">~TLINK2~</a></p>
</form></td><td></td></tr></table>
</body>
</html>