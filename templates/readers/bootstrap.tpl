<!DOCTYPE html>
<html>
<head>    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>~SITE_NAME~ / ~TITLE~</title>
    
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.2/css/bootstrap.min.css" integrity="sha512-CpIKUSyh9QX2+zSdfGP+eWLx23C8Dj9/XmHjZY2uDtfkdLGo0uY12jgcnkX9vXOgYajEKb/jiw67EYm+kBf+6g==" crossorigin="anonymous" referrerpolicy="no-referrer" /></head>   

<body>
<div class="container">
 <div class="row">
    <nav class="navbar navbar-dark bg-primary">
   <div class="container-fluid">
    <a class="navbar-brand" href="#">~SITE_NAME~</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
         
        <li class="nav-item">
          <a class="nav-link" href="/content/mainpage">Главная страница</a>
        </li>
        <li class="nav-item">
            <li><a class="nav-link" href="/shop/view">Пример магазина</a></li>
        </li>
        <li class="nav-item">
            <li><a class="nav-link" href="/feedback/writemail">Обратная связь</a></li>        </li>
      </ul>
    </div>
  </div>
</nav>
 </div>
 
	  
     
	 <div class="row">
             <div class="col">
        <h3>~SPEC_LINK~ ~TITLE~</h3>
		
		~BREADCRUMBS~
		
		~BODY~
				
		~ATTRS~
             </div>
		<!-- ~DEBUG_INFO~ -->
	</div>	
		
    <div class="row">
        <div class="col">
			~COM_BODY~	
			~COM_FORM~				
             </div>
        </div>
	
	
	
	      
      <div class="row">
        <div class="col span4">
			<h2>Обновления</h2>
			~LATEST_ARTICLES~
        </div>
        <div class="col span4">
			~BLOCK1~
       </div>
        <div class="col span4">
          <h2>Пользователь</h2>
          ~USER_AREA~	
        </div>
      </div>

      <hr>

      <footer>
        <p>&copy; ~SITE_NAME~ ~YEAR~</p>
      </footer>

    </div> <!-- /container -->

	
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.2/js/bootstrap.min.js" integrity="sha512-5BqtYqlWfJemW5+v+TZUs22uigI8tXeVah5S/1Z6qBLVO7gakAOtkOzUtgq6dsIo5c0NJdmGPs0H9I+2OHUHVQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> 
</body>
</html>