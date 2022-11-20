<script type="text/javascript" src="/vendor/components/jquery/jquery.js"></script>
<style type="text/css">
.crow {border-bottom:#069 dashed thin; cursor:pointer;}
.pseudolink {border-bottom:#06C solid thin; cursor:pointer;}
#filecmenuclose,  #dircmenuclose
{
	cursor:pointer;
	border-bottom:dashed thin #F00;
	font-size:10px;
	text-align:center;
}
#filecmenu,  #dircmenu
{
	background-color:#FFC;
	color:#333;
	position:absolute;
	width:150px;
	height:auto;
	z-index:155;
	box-shadow:#030 1px 3px 3px;
}
</style>

<div id="filecmenu" style="display:none;">
<span class="pseudolink" onclick="jseditfile('%DESIGN%');">Правка файла</span><br/>
<span class="pseudolink" onclick="jsrename('%DESIGN%');">Переименование</span><br/>
<span class="pseudolink" onclick="jschmod('%DESIGN%');">Смена прав</span><br/>
<span class="pseudolink" onclick="alert('%DESIGN% - Новое действие');">Файл::Новое действие</span>
<hr />
<center><span id="filecmenuclose">[Закрыть]</span></center>
</div>

<div id="dircmenu" style="display:none;">
<span class="pseudolink" onclick="jsviewdir('%DESIGN%');">Перейти в каталог</span><br/>
<span class="pseudolink" onclick="jsrename('%DESIGN%');">Переименование</span><br/>
<span class="pseudolink" onclick="jschmod('%DESIGN%');">Смена прав</span><br/>
<span class="pseudolink" onclick="alert('%DESIGN% - Новое действие');">Каталог::Новое действие</span>
<hr />
<center><span id="dircmenuclose">[Закрыть]</span></center>
</div>

<style>#otherinfo {}</style>
<script language="javascript">
var current_path = "~DIROBJECT~";

function jsviewdir(p)
{
	document.fmjs.action.value="listdir";
	document.fmjs.dirobject.value=p;
	document.forms['fmjs'].submit();
}
function jscopy()
{
	document.fmjs.action.value="copy";
	document.fmjs.dirobject.value=current_path;
	document.fmjs.clipflag.value="copy";
	document.fmjs.clipdir.value=current_path;
	document.fmjs.clipsel.value=document.fmjs.selobjects.value;
	document.forms['fmjs'].submit();
}
function jsdelete()
{
	document.fmjs.action.value="delete";
	document.fmjs.dirobject.value=current_path;
	document.forms['fmjs'].submit();
}
function jsmove()
{
	document.fmjs.action.value="move";
	document.fmjs.dirobject.value=current_path;
	document.fmjs.clipdir.value=current_path;
	document.fmjs.clipsel.value=document.fmjs.selobjects.value;
	document.fmjs.clipflag.value="move";
	document.forms['fmjs'].submit();
}

function jsrename(f)
{
	document.fmjs.action.value="rename";
	document.fmjs.dirobject.value=current_path;
	document.fmjs.clipdir.value=current_path;
	document.fmjs.clipsel.value=f;
	document.fmjs.clipflag.value="move";
	document.forms['fmjs'].submit();
}


function jschmod(f)
{
	document.fmjs.action.value="chmod";
	document.fmjs.dirobject.value=current_path;
	document.fmjs.clipdir.value=current_path;
	document.fmjs.clipsel.value=f;
	document.fmjs.clipflag.value="move";
	document.forms['fmjs'].submit();
}

function jsupload()
{
	document.fmjs.action.value="upload";
	document.fmjs.dirobject.value=current_path;
	document.forms['fmjs'].submit();
}
function jsmkdir()
{
	document.fmjs.action.value="mkdir";
	document.fmjs.dirobject.value=current_path;
	document.forms['fmjs'].submit();
}
function jspaste()
{
	document.fmjs.action.value="paste";
	document.fmjs.dirobject.value=current_path;
	document.forms['fmjs'].submit();
}
function jseditfile(f)
{
	document.fmjs.action.value="editfile";
	document.fmjs.dirobject.value=current_path;
	document.fmjs.selobjects.value=f;
	document.forms['fmjs'].submit();
}
function jsnewfile()
{
	document.fmjs.action.value="newfile";
	document.fmjs.dirobject.value=current_path;
	document.forms['fmjs'].submit();
}
function jstounzip()
{
	document.fmjs.action.value="tounzip";
	document.fmjs.dirobject.value=current_path;
	document.forms['fmjs'].submit();
}
</script>
<h3><img src='/images/ferret.jpg' />&nbsp;Файловый менеджер Хорёк</h3>
<style>
.dir {background-color:#CCC};
.file {background-color:#AAA};
</style>
<div style="background-color:#003; color:#0FF"><span style="color:#FF0">Каталог:</span> ~DIROBJECT~</div>
<table border="1" align="center" width="100%">
<tr>
<td valign="top" width="60%" align="left">Имя файла</td>
<td valign="top" align="left">Права</td>
<td valign="top" align="left">Размер файла</td>
<td valign="top" align="left">Выбран</td>
</tr>

~LIST~

</table>

<form name="fmjs" action="/admin/filemanager" method="post" style="display:none">
<input type="hidden" name="action" value="~ACTION~" />
<input type="file" name="fupload" style="display:none" />
<input type="hidden" name="dirobject" value="~DIROBJECT~" />
<input type="hidden" name="selobjects" value="~SELOBJECTS~" />
<input type="hidden" name="clipdir" value="~CLIPDIR~" />
<input type="hidden" name="clipsel" value="~CLIPSEL~" />
<input type="hidden" name="clipflag" value="~CLIPFLAG~" />
</form>

<div style="margin:10px; padding:10px">
<span class="btn" onclick="jsmkdir();">Нов. каталог </span> &nbsp; <span class="btn" onclick="jsnewfile();">Нов. файл </span> &nbsp; <span class="btn" onclick="jsupload();">Загрузить файл</span> &nbsp; <span class="btn" onclick="jscopy();">Копировать</span> &nbsp; <span class="btn" onclick="jsmove();">Переместить</span> &nbsp; <span class="btn" onclick="jspaste();">Вставить</span> &nbsp; <span class="btn" onclick="jsdelete();">Удалить</span>
<br/><br/><hr/>
<span class="btn" onclick="jstounzip();"> Распаковать... </span>
</div>
<div style="background-color:#000; color:#eee">
Действие: ~ACTION~ <br/>
<span style="color:#00ff00">Буфер-каталог: ~CLIPDIR~ <br/>
Буфер-файлы: ~CLIPSEL~ <br/>
Буфер-флаг: ~CLIPFLAG~ <br/>  </span>
</div>
<script language="javascript">

document.fmjs.clipdir.value = "~CLIPDIR~";
document.fmjs.clipsel.value = "~CLIPSEL~";
document.fmjs.clipflag.value = "~CLIPFLAG~";

var sel_objects = "";


function selectfiles(control, fname)
{
	if (sel_objects.indexOf(fname)==-1)
	{
		sel_objects = sel_objects+fname+'; ';
		mystatus('Добавили '+fname);
	}
	else
	{
		sel_objects = sel_objects.replace(fname+'; ', '');
		mystatus('Убрали '+fname);
	};
selectedtoform(sel_objects);
}
function mystatus(msg)
{
document.getElementById("otherinfo").innerHTML = msg;
	}
function selectedtoform(line)
{
document.fmjs.selobjects.value=line ;
document.getElementById("otherinfo").innerHTML = 'Выбрано: '+line ;
}
</script>

<script type="text/javascript">
var filecpattern = $("#filecmenu").html();
var dircpattern = $("#dircmenu").html();

function dirCMenu(parent, elem)
{
$("#dircmenu").hide();	
st1 = dircpattern.replace(/%DESIGN%/g, elem);
	$("#dircmenu").html(st1);
	$("#dircmenu").css("left",  $(parent).offset().left+15);
	$("#dircmenu").css("top",  $(parent).offset().top+15);
	$("#dircmenu").css("display", "block");	
		$("#dircmenu").show();	
		$('#dircmenuclose').click(
			function()
				{
					
				$("#dircmenu").hide();
				$("#dircmenu").html(dircpattern);
   			}
			);
	
}

function fileCMenu(parent, elem)
{
$("#filecmenu").hide();	
st1 = filecpattern.replace(/%DESIGN%/g, elem);
	$("#filecmenu").html(st1);
	$("#filecmenu").css("left",  $(parent).offset().left+15);
	$("#filecmenu").css("top",  $(parent).offset().top+15);
	$("#filecmenu").css("display", "block");	
		$("#filecmenu").show();	
		$('#filecmenuclose').click(
			function()
				{
					
				$("#filecmenu").hide();
				$("#filecmenu").html(filecpattern);
   			}
			);
	
}

</script>

~INDICATOR~
<span id="otherinfo"></span>