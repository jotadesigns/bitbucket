<?php
function getHTMLIssuesGlobal($issues_json,$coments_relevants){
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

            //content issue
            $full_html .= '<div class="issue_desc">';
                if(isset($coments_relevants[$issue["local_id"]])){
                    //demo
                    if(isset($coments_relevants[$issue["local_id"]]["wrapped"]["demo"])){
                        $coment_relevant_demo = $coments_relevants[$issue["local_id"]]["wrapped"]["demo"];
                        $full_html .= $coment_relevant_demo;
                    }
                    //archivos
                    if(isset($coments_relevants[$issue["local_id"]]["wrapped"]["archivos"])){
                        $coment_relevant_archivos = $coments_relevants[$issue["local_id"]]["wrapped"]["archivos"];
                        $full_html .= $coment_relevant_archivos;
                    }
                    $full_html .= '<span class="icon-expander" data-issue_id="'.$issue["local_id"].'">+</span>';
                }else{
                    $full_html .= 'No tenemos información detallada de esta incidencia :D';
                }

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

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}


function wrapComentsRelevants(&$coments_relevants){

    foreach($coments_relevants as $coment_relevant_id => $coment_relevant){

         $coment_relevant_secciones = explode("____", $coment_relevant["content"]);

         foreach($coment_relevant_secciones as $coment_relevant_seccion){
             $seccion = str_replace(' ', '', $coment_relevant_seccion);
             //Seccion DEMO
             if (preg_match("/\**demo/i", $seccion )) {
                $coments_relevants[$coment_relevant_id]["wrapped"]["demo"] = "tenemos demo";

             //Seccion ARCHIVOS
             }elseif(preg_match("/\**archivos/i", $seccion )){
                 $coments_relevants[$coment_relevant_id]["wrapped"]["archivos"] = "tenemos archivos";
             }
         }
    }
}
