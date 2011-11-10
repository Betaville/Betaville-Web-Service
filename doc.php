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
							<h4>startsession</h4><p>logs a user in<br /><em>Deprecated (use "auth")</em></p>
							<p><strong>Returns</strong> - Session token</p>
							<h5>parameters</h5>
							<ul>
								<li><strong>username</strong> - The username</li>
								<li><strong>password</strong> - The password</li>
							</ul>
						</li>
						<li>
							<h4>endsession</h4><p>logs a user out, invalidating the session token</p>
							<p><strong>Returns</strong> - true or false, depending on success</p>
							<h5>parameters</h5>
							<ul>
								<li><strong>token</strong> - The session token</li>
							</ul>
						</li>
						<li>
							<h4>add</h4><p>Adds a new user</p>
							<p><strong>Returns</strong> - true or false, depending on success</p>
							<h5>parameters</h5>
							<ul>
								<li><strong>username</strong> - new username</li>
								<li><strong>password</strong> - new user's password</li>
								<li><strong>email</strong> - email address of the user where validation mail will be sent</li>
							</ul>
						</li>
						<li>
							<h4>activateuser</h4><p>Activates a user account</p>
							<p><strong>Returns</strong> - true or false, depending on success</p>
							<h5>parameters</h5>
							<ul>
								<li><strong>code</strong> The user's activation code</li>
							</ul>
						</li>
						<li>
							<h4>activated</h4><p>Checks if a user has already been activated</p>
							<p><strong>Returns</strong> - true or false, depending on whether or not the user has been activated</p>
							<h5>parameters</h5>
							<ul>
								<li><strong>username</strong> The user name to check the activation state of</li>
							</ul>
						</li>
						<li>
							<h4>available</h4><p>Checks if a username is available for registration</p>
							<p><strong>Returns</strong> - true or false, depending on whether or not the username can be registered</p>
							<h5>parameters</h5>
							<ul>
								<li><strong>username</strong> The user name to check the availability of</li>
							</ul>
						</li>
						<li>
							<h4>changepass</h4><p>Changes a user's password</p>
							<p><strong>Returns</strong> - true or a response indicating the lack of authenticated credentials</p>
							<h5>parameters</h5>
							<ul>
								<li><strong>oldpass</strong> The user's previous password</li>
								<li><strong>newpass</strong> The user's new password</li>
							</ul>
						</li>
						<li>
							<h4>changebio</h4><p>Changes a user's biographic entry</p>
							<p><strong>Returns</strong> - true or a response indicating the lack of authenticated credentials</p>
							<h5>parameters</h5>
							<ul>
								<li><strong>bio</strong> The user's new biographical information</li>
							</ul>
						</li>
						<li>
							<h4>updateavatar</h4><p>Changes a user's avatar image</p>
							<p><strong>Returns</strong> - true or a response indicating the lack of authenticated credentials</p>
							<h5>parameters</h5>
							<ul>
								<li><strong>FILE DATA</strong> The user's new avatar image</li>
							</ul>
						</li>
						<li>
							<h4>getmail</h4><p>Gets a user's email address if it is available</p>
							<p><strong>Returns</strong> - The user's email address</p>
							<h5>parameters</h5>
							<ul>
							</ul>
						</li>
						<li>
							<h4>checklevel</h4><p>Gets the user's user type</p>
							<p><strong>Returns</strong> - <strong>member</strong>, or <strong>base_committer</strong>, or <strong>data_searcher</strong>, or <strong>moderator</strong>, or <strong>admin</strong></p>
							<h5>parameters</h5>
							<ul>
								<li><strong>username</strong> - The username to get the user type of</li>
							</ul>
						</li>
						<li>
							<h4>getpublicinfo</h4><p>Gets the user's publicly available details</p>
							<p><strong>Returns</strong> - The user's publicly available information</p>
							<h5>parameters</h5>
							<ul>
								<li><strong>username</strong> - The username to get the details of</li>
							</ul>
						</li>
					</ul>
				</li>
				<li>
					<h3>coordinate</h3>
					<ul>
						<li>
							<h4>getutm</h4><p>Gets a coordinate as a UTM coordinate</p>
							<p><strong>Returns</strong> - A UTM coordinate</p>
							<h5>parameters</h5>
							<ul>
								<li><strong>id</strong> - The id of the coordinate to retrieve</li>
							</ul>
						</li>
						<li>
							<h4>getlatlon</h4><p>Gets a coordinate as a lat/lon coordinate</p>
							<p><strong>Returns</strong> - A lat/lon coordinate</p>
							<h5>parameters</h5>
							<ul>
								<li><strong>id</strong> - The id of the coordinate to retrieve</li>
							</ul>
						</li>
					</ul>
				</li>
				<li>
					<h3>design</h3>
					<ul>
						<li>
							<h4>synchronizedata</h4><p><em>unimplemented</em></p>
							<p><strong>Returns</strong> - </p>
							<h5>parameters</h5>
							<ul>
							</ul>
						</li>
						<li>
							<h4>addempty</h4><p><em>unimplemented</em></p>
							<p><strong>Returns</strong> - </p>
							<h5>parameters</h5>
							<ul>
							</ul>
						</li>
						<li>
							<h4>addbase</h4><p><em>unimplemented</em></p>
							<p><strong>Returns</strong> - </p>
							<h5>parameters</h5>
							<ul>
							</ul>
						</li>
						<li>
							<h4>addbasethumbnail</h4><p><em>Adds a thumbnail for a model</em></p>
							<p><strong>Returns</strong> - </p>
							<h5>parameters</h5>
							<ul>
								<li><strong>FILE DATA</strong> The new thumbnail image</li>
							</ul>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</body>
</html>