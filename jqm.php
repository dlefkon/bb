<!DOCTYPE html> 
<html> 
	<head> 
		<title>Page Title</title> 
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0b1/jquery.mobile-1.0b1.min.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.1.min.js"></script>
		<script type="text/javascript" src="http://code.jquery.com/mobile/1.0b1/jquery.mobile-1.0b1.min.js"></script>
	</head> 
	<body> 

<!-- Start of first page -->
<div data-role="page" id="settings">

	<div data-role="header">
		<h1>Settings</h1>
	</div>

	<div data-role="content">	
		<p>I'm first in the source order so I'm shown as the page.</p>		
		<p>View internal page called <a href="#bar" data-transition="fade">bar</a></p>
	</div>

	<div data-role="footer">
		<h4>Page Footer</h4>
	</div>
</div>


<!-- Start of second page -->
<div data-role="page" id="bar">

	<div data-role="header">
		<h1>Bar</h1>
	</div>
	
	<div class="content-primary">
	
	
				
<div data-role="fieldcontain">
    <label for="search" class="ui-hide-label">Search Input:</label>
    <input type="search" name="password" id="search" value="wef" class="ui-hide-label" /> 
</div>

	<div data-role="fieldcontain" class="ui-hide-label">
	<label for="username">Username:</label>
	<input type="text" name="username" id="username" value="efefefefefefe" placeholder="Username"/>
</div>
	
		<ul data-role="listview" class=" ui-listview "> 
			<li data-theme="c" class="ui-btn ui-btn-icon-right ui-li ui-btn-up-c"><div class="ui-btn-inner ui-li"><div class="ui-btn-text"><a href="index.html" class="ui-link-inherit">Inbox <span class="ui-li-count ui-btn-up-c ui-btn-corner-all">12</span></a></div><span class="ui-icon ui-icon-arrow-r"></span></div></li>
			<li data-theme="c" class="ui-btn ui-btn-icon-right ui-li ui-btn-up-c"><div class="ui-btn-inner ui-li"><div class="ui-btn-text"><a href="index.html" class="ui-link-inherit">Outbox <span class="ui-li-count ui-btn-up-c ui-btn-corner-all">0</span></a></div><span class="ui-icon ui-icon-arrow-r"></span></div></li>
			<li data-theme="c" class="ui-btn ui-btn-icon-right ui-li ui-btn-up-c"><div class="ui-btn-inner ui-li"><div class="ui-btn-text"><a href="index.html" class="ui-link-inherit">Drafts <span class="ui-li-count ui-btn-up-c ui-btn-corner-all">4</span></a></div><span class="ui-icon ui-icon-arrow-r"></span></div></li>
			<li data-theme="c" class="ui-btn ui-btn-icon-right ui-li ui-btn-up-c"><div class="ui-btn-inner ui-li"><div class="ui-btn-text"><a href="index.html" class="ui-link-inherit">Sent <span class="ui-li-count ui-btn-up-c ui-btn-corner-all">328</span></a></div><span class="ui-icon ui-icon-arrow-r"></span></div></li>
			<li data-theme="c" class="ui-btn ui-btn-icon-right ui-li ui-btn-up-c"><div class="ui-btn-inner ui-li"><div class="ui-btn-text"><a href="index.html" class="ui-link-inherit">Trash <span class="ui-li-count ui-btn-up-c ui-btn-corner-all">62</span></a></div><span class="ui-icon ui-icon-arrow-r"></span></div></li>
		</ul>
		
	</div>
	
	
	<div data-role="footer">
		<h4>Page Footer</h4>
	</div>
</div>
</body>

</html> 