<!DOCTYPE html>
<html>
<head>    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>~SITE_NAME~ / ~TITLE~</title>
    <link href="/vendors/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
	<link href="/vendors/plugins/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
</head>   
<body>
<div class="container">

   <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="/content/mainpage">~SITE_NAME~</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li class="active"><a href="/content/mainpage">Главная страница</a></li>              
			  <li><a href="/shop/view">Пример магазина</a></li>              
              <li><a href="/feedback/writemail">Обратная связь</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

<br/>	  
<br/>
<br/>
	  
     
	 <div class="row">
        <h3>~SPEC_LINK~ ~TITLE~</h3>
		
		~BREADCRUMBS~
		
		~BODY~
				
		~ATTRS~
		
		<!-- ~DEBUG_INFO~ -->
	</div>	
		
    <div class="row">
			~COM_BODY~	
			~COM_FORM~				
	</div>
	
	
	
	      
      <div class="row">
        <div class="span4">
			<h2>Обновления</h2>
			~LATEST_ARTICLES~
        </div>
        <div class="span4">
			~BLOCK1~
       </div>
        <div class="span4">
          <h2>Пользователь</h2>
          ~USER_AREA~	
        </div>
      </div>

      <hr>

      <footer>
        <p>&copy; ~SITE_NAME~ ~YEAR~</p>
      </footer>

    </div> <!-- /container -->

	
    
    <script src="/vendors/plugins/jquery/jquery.js"></script>    
    <script src="/vendors/plugins/bootstrap/assets/bootstrap-transition.js"></script>
    <script src="/vendors/plugins/bootstrap/assets/bootstrap-alert.js"></script>
    <script src="/vendors/plugins/bootstrap/assets/bootstrap-modal.js"></script>
    <script src="/vendors/plugins/bootstrap/assets/bootstrap-dropdown.js"></script>
    <script src="/vendors/plugins/bootstrap/assets/bootstrap-scrollspy.js"></script>
    <script src="/vendors/plugins/bootstrap/assets/bootstrap-tab.js"></script>
    <script src="/vendors/plugins/bootstrap/assets/bootstrap-tooltip.js"></script>
    <script src="/vendors/plugins/bootstrap/assets/bootstrap-popover.js"></script>
    <script src="/vendors/plugins/bootstrap/assets/bootstrap-button.js"></script>
    <script src="/vendors/plugins/bootstrap/assets/bootstrap-collapse.js"></script>
    <script src="/vendors/plugins/bootstrap/assets/bootstrap-carousel.js"></script>
    <script src="/vendors/plugins/bootstrap/assets/bootstrap-typeahead.js"></script>   
</body>
</html>