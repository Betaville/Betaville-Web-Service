<?php
/* require the user as the parameter */

if(isset($_GET['section']) && isset($_GET['request'])){
	$section = $_GET['section'];
	$request = $_GET['request'];
	if($section=='user'){
		
	}
	else if($section=='design'){
		if($request=='findbyid'){}
		else if($request=='findbyname'){
			
		}
		else if($request=='findbyuser'){}
		else if($request=='findbydate'){}
		else if($request=='findbycity'){}
		else if($request=='findbymodellimitedcity'){}
	}
	else if($section=='proposal'){
		
	}
	else if($section=='version'){
		
	}
	else if($section=='fave'){
		
	}
	else if($section=='comment'){
		
	}
	else if($section=='city'){
		
	}
	else if($section=='wormhole'){
		
	}
	else if($section=='softwareversion'){
		// eh, I don't think we really need this for the web service
	}
}



if(isset($_GET['user']) &amp;&amp; intval($_GET['user'])) {

	/* soak in the passed variable or set our own */
	$number_of_posts = isset($_GET['num']) ? intval($_GET['num']) : 10; //10 is the default
	$format = strtolower($_GET['format']) == 'json' ? 'json' : 'xml'; //xml is the default
	$user_id = intval($_GET['user']); //no default

	/* connect to the db */
	$link = mysql_connect('localhost','username','password') or die('Cannot connect to the DB');
	mysql_select_db('db_name',$link) or die('Cannot select the DB');

	/* grab the posts from the db */
	$query = "SELECT post_title, guid FROM wp_posts WHERE post_author = $user_id AND post_status = 'publish' ORDER BY ID DESC LIMIT $number_of_posts";
	$result = mysql_query($query,$link) or die('Errant query:  '.$query);

	/* create one master array of the records */
	$posts = array();
	if(mysql_num_rows($result)) {
		while($post = mysql_fetch_assoc($result)) {
			$posts[] = array('post'=&gt;$post);
		}
	}

	/* output in necessary format */
	if($format == 'json') {
		header('Content-type: application/json');
		echo json_encode(array('posts'=&gt;$posts));
	}
	else {
		header('Content-type: text/xml');
		echo '&lt;posts&gt;';
		foreach($posts as $index =&gt; $post) {
			if(is_array($post)) {
				foreach($post as $key =&gt; $value) {
					echo '&lt;',$key,'&gt;';
					if(is_array($value)) {
						foreach($value as $tag =&gt; $val) {
							echo '&lt;',$tag,'&gt;',htmlentities($val),'&lt;/',$tag,'&gt;';
						}
					}
					echo '&lt;/',$key,'&gt;';
				}
			}
		}
		echo '&lt;/posts&gt;';
	}

	/* disconnect from the db */
	@mysql_close($link);
}
?>