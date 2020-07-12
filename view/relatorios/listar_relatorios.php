<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="../../jquery/jquery-1.11.3.min.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);


$.getJSON('?action=listarRelatorio',function(json){
              if(json.relatorio_meses.length>0){
                  alert('oi');
              }else{
                  alert('nada');
              }
          })
      function drawChart() {
          
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          ['Work',     11],
          ['Eat',      2],
          ['Commute',  2],
          ['Watch TV', 2],
          ['Sleep',    7]
        ]);

        var options = {
          title: 'My Daily Activities'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="piechart" style="width: 900px; height: 500px;"></div>
    <?php
    $array_obj = array();
    foreach($dados as $obj){
        //var_dump($obj);
        array_push($array_obj, array($obj['descricao']=>$obj['sum(b.valor_pagamento)']));
    }
    echo json_encode(array("relatorio_meses"=>$array_obj));
    ?>
  </body>
</html>