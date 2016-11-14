<?php
$valid_passwords = array ('CEOD' => '37ceodSP',
                        'Rapsys' => 'rapsys01',
                        'Bariani' => 'bari01',
                        'Evair' => 'eva01',
                        'Pansani' => 'pansani01',
                        'Lucas' => 'lucas01',
                        'cred1' => '9124',
                        'cred2' => '2661',
                        'cred3' => '7511',
                        'cred4' => '9576',
                        'cred5' => '9012',
                        'cred6' => '2812',
                        'cred7' => '1285',
                        'cred8' => '8739',
                        'cred9' => '1305',
                        'cred10' => '2263');
$valid_users = array_keys($valid_passwords);

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];

$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

if (!$validated) {
  header('WWW-Authenticate: Basic realm="CEODSP"');
  header('HTTP/1.0 401 Unauthorized');
  die ("Not authorized");
}

session_start();

require('./template/header.php');

// If arrives here, is a valid user.
echo "<p class='text-right' id='userId' data-id='$user'><small>Usuário: $user.</small></p>";

?>

<img src="./images/logo_checkin.png" class="img-responsive center-block" alt="Logo do Congresso Estadual - Sistema de Check-In">
<div class="text-center"><a class="text-center btn btn-link" href="./presentes.php">Lista de Presentes</a></div>

<p class="text-center"><small>Insira a CID, Nome ou Endereço de E-mail para pesquisar entre os inscritos.</small></p>

<div class="text-center">
  <select class="inscritos text-left" style="width: 75%;" data-placeholder="Comece a digitar para buscar uma inscrição">
					</select>
</div>
<input type="button" onClick="exibeDetalhes();" data-loading value="Iniciar Check-in" class="inputButton">

<div class="col-xs-12" id="alert-checkin" style="display: none;">
  <div class="alert alert-success" role="alert"><strong>Bem-vindo! </strong>Check-in efetuado com sucesso!</div>
</div>

<div id="painel">
  

  <div class="col-xs-12">
    <div class="col-sm-12 col-md-7">
      <div id="dadosInscrito">
      </div>
    </div>
    <div class="col-sm-12 col-md-5">
      <div id="opcoesCheckin">
      </div>
    </div>
  </div>
</div>

<?php 
require('./template/footer.php');

?>

<script>

var checkinStart = "";

function formatRepo(repo) {
  if (repo.loading) return repo.text;

  var markup = "<strong>" + repo.cid + '</strong> - ' + repo.nome;

  markup += "<br>" + repo.email;

  return markup;
}

function formatRepoSelection(repo) {
  return repo.nome || repo.cid;
}

var URL = "http://ceod2016.demolaysp.com.br/checkin/inc/api.php/";

$(".inscritos").select2({
  placeholder: {
    id: '-1', // the value of the option
    text: 'Select an option'
  },
  ajax: {
    url: URL + 'inscritos?columns=id,nome,cid,email&transform=1&satisfy=any',
    dataType: 'json',
    delay: 250,
    data: function(params) {
      return 'filter[]=cid,sw,' + params.term + '&filter[]=nome,sw,' + params.term + '&filter[]=email,sw,' + params.term; // search term
    },
    processResults: function(data) {
      return {
        results: data.inscritos
      };
    },
    cache: true
  },

  language: 'pt',
  escapeMarkup: function(markup) {
    return markup;
  }, // let our custom formatter work
  minimumInputLength: 1,
  templateResult: formatRepo, // omitted for brevity, see the source of this page
  templateSelection: formatRepoSelection // omitted for brevity, see the source of this pag
});

function exibeDetalhes() {
  var painel = $('#painel');
  var dados = $('#dadosInscrito');
  
  var dataLimiteCamiseta = new Date(Date.UTC(2016,9,24));
  
  var inscrito = $('.inscritos').val()

  iniciaCheckin(inscrito);
  
  if (inscrito) {
    painel.slideUp();
    $.get("./template/detalhes.html", function carregaTemplate(template) {
      $.get(URL + "inscritos/" + inscrito, function carregaInscrito(inscricao) {
        
        if(inscricao.cb_liquidada && inscricao.cb_liquidada != "0000-00-00") {

          var t = inscricao.cb_liquidada.split('-');
          var d = new Date(Date.UTC(t[0], t[1]-1, t[2]));
          
          inscricao.camisetaConfirmada = dataLimiteCamiseta >= d ? 1 : null;
          inscricao.inscricaoConfirmada = 1;
          
        } else {
          inscricao.camisetaConfirmada = null;
          inscricao.inscricaoConfirmada = null;
        }
      
        var nascimento = inscricao.dataNascimento.split('-');
        var dateNascimento = new Date(Date.UTC(nascimento[0], nascimento[1]-1, nascimento[2]));
        inscricao.idade = calculaIdade(dateNascimento, new Date());

        var rendered = Mustache.render(template, inscricao);
        dados.html(rendered);
        painel.slideDown();
      });
    });
  }
  else {
    alert("Selecione um inscrito para iniciar o Check-in.");
  }
}

function exibeOpcoes(id_checkin) {
  var painel = $('#painel');
  var opcoes = $('#opcoesCheckin');
  opcoes.slideUp();

  var inscrito = $('.inscritos').val()

  if (inscrito) {
    $.get("./template/opcoes_checkin.html", function carregaTemplate(template) {
      $.get(URL + "checkin/" + id_checkin, function carregaCheckin(checkin) {
        checkin.camiseta = checkin.camiseta == 1 ? 1 : null;
        checkin.ca9 = checkin.ca9 == 1 ? 1 : null;
        checkin.epoc = checkin.epoc == 1 ? 1 : null;
        checkin.menor = checkin.menor == 1 ? 1 : null;
        checkin.pgto_balcao = checkin.pgto_balcao == 1 ? 1 : null;
        checkin.finalizado = checkin.finalizado == 1 ? 1 : null;
        
        var rendered = Mustache.render(template, checkin);
        opcoes.html(rendered);
        opcoes.slideDown();
      });
    });
  }
}

function iniciaCheckin(inscrito) {

  var opcoes = $('#opcoesCheckin');
  var checkin = $('#alert-checkin');
  var user = $('#userId').data('id');
  
  checkinStart = new Date().toISOString().slice(0, 19).replace('T', ' ');
  checkin.slideUp();
  
  $.get(URL + "checkin/?transform=1&filter=id_inscrito,eq," + inscrito, function validaCheckin(data) {
    
    if (!data.checkin.length) {

      var now = new Date().toISOString().slice(0, 19).replace('T', ' ');

      var checkin = {
        id_inscrito: inscrito,
        user: user
      }

      $.post(URL + "checkin", checkin)
        .done(function(data) {
          $('#confirmarCheckin').data('id',data);
          exibeOpcoes(data);
          
        });
    }
    else {
      
      exibeOpcoes(data.checkin[0].id_checkin);
    }

  });

}

function finalizaCheckin(id_checkin) {

  var now = new Date().toISOString().slice(0, 19).replace('T', ' ');
  var camiseta = $('#camiseta').is(':checked') ? 1 : "NULL";
  var epoc = $('#epoc').is(':checked') ? 1 : "NULL";
  var ca9 = $('#ca9').is(':checked') ? 1 : "NULL";
  var menor = $('#menor').is(':checked') ? 1 : "NULL";
  var pgto_balcao = $('#pgto_balcao').is(':checked') ? 1 : "NULL";
  var observacao = $('#observacao').val();
  
  var user = $('#userId').data('id');

  var checkin = {
    checkin_end: now,
    checkin_start: checkinStart,
    epoc: epoc,
    ca9: ca9,
    menor: menor,
    pgto_balcao: pgto_balcao,
    camiseta: camiseta,
    observacao: observacao,
    finalizado: 1,
    user: user
  }

  $.ajax({
    url: URL + "checkin/" + id_checkin,
    type: 'PUT',
    data: checkin,
    success: function(response) {
      var painel = $('#painel');
      var checkin = $('#alert-checkin');
      
      painel.slideUp();
      checkin.slideDown();
      
      $('.inscritos').val(null).trigger("change"); 
      
            
    }
  });
}
    
function calculaIdade(nascimento, hoje){
    return Math.floor(Math.ceil(Math.abs(nascimento.getTime() - hoje.getTime()) / (1000 * 3600 * 24)) / 365.25);
}

</script>