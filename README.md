# Check-in 37º CEOD SP
###### Sistema de Check-in para o 37º Congresso Estadual da Ordem DeMolay do Estado de São Paulo

Sistema desenvolvido por mim para o 37º CEOD SP realizado nos dias 19 e 20 de Novembro de 2016 na cidade de São Paulo.

Algumas das features presentes são:

1. Autenticação HTTP básica
2. Appcache para recursos estáticos e agilidade no carregamento em redes móveis.
4. Função "Adicionar à tela de início" para iOS e Android via JSON Manifest.
5. Utilização de api básica em php com [php-crud-api](https://github.com/mevdschee/php-crud-api).
6. [Mustache.js](https://mustache.github.io/) como engine de templates.
7. [Select2.js](https://select2.github.io/) como caixa de busca para os membros inscritos.
8. [Superlógica](http://superlogica.com/developers/api/) como API de Pagamento por restrição da entidade.
9. [API do SISDM-SCODB](http://demolay.org.br) como repositório de dados dos Inscritos.

A API sofreu algumas leves modificações para impedir alteração em tabelas não necessárias. A autenticação não era um requerimento e por isso não foquei muito em restrição de acesso específico, o sistema só será conhecido pelos membros da equipe, o HTTP Básico deve resolver. 

E sim, eu estou ciente de alguns dos bugs existentes, e de falhas de segurança, mas o pouco tempo e necessidade de algo rápido fizeram com que eu deixasse de lado alguns pontos digamos, menos pertinentes, mas não menos importantes. 

**Sinta-se à vontade para sugerir qualquer alteração ou mesmo enviar sua versão do código, prometo considerar com carinho.**


P.S.: O script foi originalmente pensado para rodar em Linux + PHP, porém a instituição tem um servidor Windows,  por isso os ```web.config``` em coexistência com os ```.htaccess```.
