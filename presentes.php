<?php
$valid_passwords = array ('user' => 'password');
$valid_users = array_keys($valid_passwords);

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];

$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

if (!$validated) {
  header('WWW-Authenticate: Basic realm="YourRealm"');
  header('HTTP/1.0 401 Unauthorized');
  die ("Not authorized");
}

session_start();

require('./template/header.php');

// If arrives here, is a valid user.
echo "<p class='text-right' id='userId' data-id='$user'><small>Usuário: $user.</small></p>";
?>

<img src="./images/logo_checkin.png" class="img-responsive center-block" alt="Logo do Congresso Estadual - Sistema de Check-In">
<div class="text-center"><a class="text-center btn btn-link" href="./">« Voltar para o Check-In</a></div>
<p class="text-center"><small>Estes são os presentes no 37º CEOD SP.<br>Até o momento temos <strong><span id="count"></span></strong> membros presentes.</small></p>

<div id="painel">
  

  <div class="col-xs-12">
   <table class="table table-striped">
	<thead>
		<tr>
			<th width="20%">
				CID
			</th>
			<th width="80%">
				Nome
			</th>
		</tr>
	</thead>
	<tbody id="table_body">
		
	</tbody>
</table>
  </div>
</div>

<?php 
require('./template/footer.php');

?>
<script>
    var URL = "http://ceod2016.demolaysp.com.br/checkin/inc/api.php/";
    var linha = "<tr><td>{{cid}}</td><td>{{nome}}</td></tr>";
    var body = $('#table_body');
    var count = $('#count');

    $.ajax({
      url: URL + "checkin?include=inscritos&columns=checkin.id,inscritos.nome,inscritos.cid&transform=1&filter=finalizado,eq,1",
      type: 'GET',
      success: function(response) {
        count.html(response.checkin.length);      
        for(var i = 0; i<response.checkin.length; i++) {
          var rendered = Mustache.render(linha, response.checkin[i].inscritos[0]);
          body.append(rendered);
          console.log(response.checkin[i]);
        }
      }
    });

</script>