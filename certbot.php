<?php
//Config
DEFINE('CERTBOT_PATH',"/root/");
if (file_exists(CERTBOT_PATH."/certbot-auto")) {
	if (isset($_POST["createCert"])) {
		$domain = $_POST["domain"];
		$acc = $_POST["acc"];
		$email = $_POST["email"];
		$error = false;

		if (strlen($domain)==0) {
			echo "<pre>Invalid domain name</pre>";
			$error = true;
		}
		if (strlen($acc)==0) {
			echo "<pre>Please enter an account</pre>";
			$error = true;
		}
		if(strlen($email)==0 || filter_var($email,FILTER_VALIDATE_EMAIL)===false){
			echo "<pre>Please enter an email</pre>";
			$error = true;
		}

		if (!$error) {
			$command = "cd ".CERTBOT_PATH." && ./certbot-auto certonly --email $email --agree-tos --renew-by-default --webroot  -w /home/$acc/public_html/ -d $domain && cp -f /etc/letsencrypt/live/$domain/fullchain.pem /etc/pki/tls/certs/$domain.crt && cp -f /etc/letsencrypt/live/$domain/privkey.pem /etc/pki/tls/private/$domain.key && cp -f /etc/letsencrypt/live/$domain/chain.pem /etc/pki/tls/certs/$domain.bundle";
			echo "<pre>";
			echo shell_exec($command);
			echo "</pre>";
		}
	}
}else{
	echo "<pre> CERTBOT not installed in '".CERTBOT_PATH."' please configure file</pre><br>
				This relies on CERTBOT already being installed on your system, please configure this file by changing:
				<code>DEFINE('CERTBOT_PATH','/root/')</code>
				to where Certbot is installed";
}

$conn = new mysqli($db_host,$db_user,$db_pass,$db_name);

if ($conn->connect_error) {
	echo "<pre>Error Connecting to database</pre>";
}

$sql = "SELECT username FROM user;";
$result = $conn->query($sql);
$drop_down= $result->fetch_all(MYSQLI_NUM);
$conn->close();
?>
<h3>Certbot Module</h3>
<p>
	Welcome to the certbot module for CWP
	<br>
	Fill in the information below, e.g.:
	<br>
	<br>
	Domain: example.com<br>
	Account: example<br>
	Email: john.smith@example.com<br>
	<br>
	Once SSL has been recieved from letsencrypt and there has been a success message given, install the SSL through the SSL Cert Manager
	located under 'Apache Settings'
</p>
<div style="width: 200px;">
	<form method="post">
		<label for="domain">Domain Name:</label>
		<br>
		<input type="text" name="domain" placeholder="Domain Name... ">
		<br>
		<label for="acc">Account Name:</label>
		<select class="" name="acc">
			<?php foreach ($drop_down as $user): ?>
				<option value="<? echo $user[0] ?>"><? echo $user[0] ?></option>
			<?php endforeach; ?>
		</select>
		<br>
		<label for="email">Email:</label>
		<br>
		<input type="text" name="email" placeholder="Email...">
		<br>
		<button name="createCert" style="margin-top: 10px;">Create</button>
	</form>
</div>
