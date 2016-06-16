<form action='/sklad/addcall' method='post'> 
    <label for='callfrom'>Кто звонил</label></br>
    <input type="text" name='callfrom' /></br>
    <label for='callto'>Кому звонили</label></br>
    <input type="text" name='callto' /></br>
    <label for='callmsg'>Что сказали</label></br>
    <textarea name='callmsg'></textarea></br>
    <label for='calldt'>Дата-время</label></br>
    <input type="text" name='calldt' /></br>
	<input type='submit' value='Зарегистрировать звонок' />
</form>