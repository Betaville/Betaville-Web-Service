<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Betaville Web Service</title>
</head>

<body>
	<div>
		<div>
			<h1>Welcome to the Betaville Web Service</h1>
			<div>
				<p>The web service is broken up into <em>sections</em>, each of which has a number of calls available, which are described as <em>requests</em></p>
			</div>
		</div>
		
		<!-- Lists the sections and requests -->
		<div>
			<ul>
				<li>
					<h3>user</h3>
					<ul>
						<li>
							<h4>auth</h4><p>logs a user in</p>
							<p><strong>Returns</strong> - Session token</p>
							<h5>parameters</h5>
							<ul>
								<li><strong>username</strong> - The username</li>
								<li><strong>password</strong> - The password</li>
							</ul>
						</li>
						<li>
							<h4>startsession</h4><p>logs a user in<br/><em>Deprecated (use "auth")</em></p>
							<p><strong>Returns</strong> - Session token</p>
							<h5>parameters</h5>
							<ul>
								<li><strong>username</strong> - The username</li>
								<li><strong>password</strong> - The password</li>
							</ul>
						</li>
						<li>
							<h4>endsession</h4><p></em>logs a user out, invalidating the session token<br/></p>
							<p><strong>Returns</strong> - true or false, depending on success</p>
							<h5>parameters</h5>
							<ul>
								<li><strong>token</strong> - The session token</li>
							</ul>
						</li>
				</li>
			</ul>
		</div>
	</div>
</body>
</html>