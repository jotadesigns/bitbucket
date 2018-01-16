<?php
function getHTMLIssuesGlobal($issues_json){
  $full_html = "";

  //si tenemos resultados
  if(isset($issues_json["issues"])){
    $full_html = '<section id="section_issues-global">';
    foreach($issues_json["issues"] as $issue){

        //INCIDENCIA
        $full_html .= '<div class="issue-global">';
            //cabecera issue
            $full_html .= '<div class="issue_cabecera">';
                if(isset($issue["priority"])){
                  $full_html .= '<img class="icon-priority" src="'.getPriorityImageUrl($issue["priority"]).'" width=40 height=40 />';
                }
                $full_html .= '<h3>'.$issue["title"].'</h3>';
                if(isset($issue["responsible"]["display_name"])){
                  $full_html .= "<p>".$issue["responsible"]["display_name"]."</p>";
                }
            $full_html .= '</div>';

            //cabecera issue
            $full_html .= '<div class="issue_desc">';

            $full_html .= '</div>';

        $full_html .= '</div>';
    }
    $full_html .= '</section>';

  //no hay resultados
  }else{
    $full_html = "No se han encontrado incidencias";
  }

  return $full_html;
}

//FUNCION QUE DEVUELVE LA URL DE LA IMAGEN USADA PARA LA PRIORIDAD
function getPriorityImageUrl($priority){
  $url = "";
  switch ($priority) {
    case 'major':
      $url = "https://d301sr5gafysq2.cloudfront.net/4cb129c5a220/img/issues/priorities/major.svg";
      break;
    case 'critical':
      $url = "https://d301sr5gafysq2.cloudfront.net/4cb129c5a220/img/issues/priorities/critical.svg";
      break;
    case 'minor':
      $url = "https://d301sr5gafysq2.cloudfront.net/4cb129c5a220/img/issues/priorities/minor.svg";
      break;

    default:
      break;
  }
  return $url;
}
