<html>
  <head>
    <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
	<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyDgp73e3DsdxUTovh1E93bHoOB35_eIHHo"></script>
	<style>
/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
    position: relative;
    /*background-color: #fefefe;*/
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 40%;
	  height: 40%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

/* Add Animation */
@-webkit-keyframes animatetop {
    from {top:-300px; opacity:0} 
    to {top:0; opacity:1}
}

@keyframes animatetop {
    from {top:-300px; opacity:0}
    to {top:0; opacity:1}
}

/* The Close Button */
.close {
    color: white;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.modal-header {
    padding: 2px 16px;
    background-color: #071A6F;
    color: white;
}
.modal-img {
    position: absolute;
    width:300px;
}

</style>
  </head>
<title>Hash Search</title>

<body background="World_Map.jpg">
<link rel="stylesheet" type="text/css" href="Search_Results.css">

<?php
	$query_text = $SearchErr = "";
  $tweet_text = "tweet_text";
  $hashtags   = "hashtags";
  if ($_SERVER["REQUEST_METHOD"] == "GET") {
        /*echo "Entering the PHP file";*/
  	if (empty($_GET["querybox"])) {
        display_search_empty();
        echo "<br>";
        echo "<p align=center class='Search-Empty'>";
        echo "Search Cannot be Empty!";
        echo "</p>";
    }
    else{
      $raw_text = $_GET['querybox'];
    	$query_text = test_input($_GET['querybox']);
      $query_text = preg_replace('/#([\w-]+)/i', '$1', $query_text);
      /*$query_text = preg_replace('/(^|\s)#(\w*[a-zA-Z_]+\w*)/', $query_text);*/
      $xml_data   = invoke_search($query_text);
      /*echo $xml_data;*/
      /*Formats the data and displays it if the search query contains proper text*/
      /*$check_docs2 = $check_docs1->attributes()->numFound;*/
      /*if($xml_data->result->children()->attributes()->numFound == 0)*/
      if(empty($xml_data))
      /*if($check_docs2 == 0)*/{
        echo "<p align=center class='Para-Style'> <b> <strong>OOPS! You may want to try a different keyword for search!</b> </strong> <p>";
        display_search($query_text);
        echo "<p align=center class='Para-Style'> <b> <strong> Sorry your search did not return any results: </b> </strong> <p>";
        
      }
      else{
	 echo "<p align=center class='Para-Style'> <b> <strong> The Topics and Tweets Relating to the search </b> </strong> <p>";
		display_search($raw_text);
    $sample = display_topics2($xml_data);
    echo "<div class ='sidenav'>";
    echo "<ul>";
    echo "<li class='Topic-list'>Trending Subtopics</li>";
    echo "<li><a href='http://hashsearch.sytes.net/Search_Php.php?querybox=$sample[0]'>$sample[0]</a></li>";
    echo "<li><a href='http://hashsearch.sytes.net/Search_Php.php?querybox=$sample[1]'>$sample[1]</a></li>";
    echo "<li><a href='http://hashsearch.sytes.net/Search_Php.php?querybox=$sample[2]'>$sample[2]</a></li>";
    echo "<li><a href='http://hashsearch.sytes.net/Search_Php.php?querybox=$sample[3]'>$sample[3]</a></li>";
    echo "<li><a href='http://hashsearch.sytes.net/Search_Php.php?querybox=$sample[4]'>$sample[4]</a></li>";
    echo "</ul>";
    display_topics1($xml_data);
    display_data($xml_data); 
		display_map($query_text);
	echo"
	<!-- The Modal -->
	<div id='myModal' class='modal'>

		<!-- Modal content -->
	<div class='modal-content'>
    <div class='modal-header'>
      <span class='close'>&times;</span>
      <h2 align='center'>Tweet Distribution Across the Globe</h2>
       </div>
	  <div id='chart_div' class='modal-img'></div>
  </div>
</div>";
		
      }   
    } 
  }

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}


/*http://35.162.191.108:8983/solr/gettingstarted/select?defType=dismax&indent=on&pf=text_en^1.5&ps=2&q=christmas&rows=1000&wt=json*/
function invoke_search($query_text){
  /*$xml = simplexml_load_file('http://54.191.184.205:8984/solr/twoogle/select?indent=on&q='.$query_text.'&wt=xml') or die('Error: Cannot create object');*/
  $xml = simplexml_load_file('http://35.164.242.31:8983/solr/ChristmasData/select?defType=dismax&indent=on&pf=text_en^1.5&ps=2&facet.field=cluster_name&facet=on&q='.$query_text.'&rows=1000&wt=xml') or die('Error: Cannot create object');
  return $xml;
}

function display_search($raw_text){
  echo "<div id='tfheader'>
    <form  method='GET' action='http://hashsearch.sytes.net/Search_Php.php?querybox='$raw_text'>
            <input type='text' value='$raw_text' class='tftextinput' name='querybox' size='21' maxlength='120' style='align:center; placeholder='Search using HashTag' required'>
            <input type='submit' value='Search' class='tfbutton'>
			<input type='button' id='myBtn' class='tfbutton' value='View Analytics'>
    </form>
  <div class='tfclear'></div></div>";
  
}

function display_search_empty(){
  $search_empty = "";
  echo "<p align=center class='Para-Style'> <b> <strong> Enter the HashTag to be searched: </b> </strong> <p>";
  echo "<div id='tfheader'>
    <form>
      <input type='text' class='tftextinput' name='querybox' size='21' maxlength='120' style='align:center;' placeholder='Search using HashTag' required>
            <input type='submit' method='GET' value='Search' class='tfbutton'>
    </form>

    <form action='http://hashsearch.sytes.net/Search_Php.php?querybox=$search_empty'>
    </form>
   <div class='tfclear'>
   </div>
   </div>";
  
}

function display_topics1($xml_data){
  echo"<ul>";
  $counter = 0;
  echo"<li class='Topic-list'>Trending HashTags</li>";
  foreach($xml_data->result->children() as $doc){
       foreach($doc->arr as $entity){
        $entity_name = $entity->attributes()->name;
        if($entity_name == "hashtags"){
          $tag = $entity->str;
          $myArray = explode(',', $tag);
          $custom_search_tag = str_replace('#', '', $myArray[0]);
          echo "<li><a href='http://hashsearch.sytes.net/Search_Php.php?querybox=$custom_search_tag'>$myArray[0]</a></li>";
            $counter = $counter + 1;
        }
       }
       if($counter == 5){
        break;
       }
     }
  echo"</ul>";
  echo"</div>";
}


function display_topics2($xml_data){
   $Topics_Array = array();
   $counter = 0;
   $aaa=0;
  foreach($xml_data->lst[1]->lst[1]->lst[0]->children() as $cont){
    $a=(string)$cont->attributes()->name;
    $conts[$aaa]=(string)$a;
    $aaa=$aaa+1;
  }
  return $conts;
}

/*
function display_topics2($xml_data){
  $Topics_Array = array();
  $counter = 0;
  echo "<br>";
  echo "<br>";
  echo"<ul>";
  echo"<li class='Topic-list'>Explore Further</li>";
  foreach($xml_data->result->children() as $doc){
       foreach($doc->arr as $entity){
        $entity_name = $entity->attributes()->name;
        if($entity_name == "cluster_name"){
              $cluster_name = $entity->str;
              if (!in_array($cluster_name, $Topics_Array)){
                 $counter = $counter + 1;
                 array_push($Topics_Array, $cluster_name);
                 echo "<li><a href='http://hashsearch.sytes.net/Search_Php.php?querybox=$cluster_name'>$cluster_name</a></li>";
              }
             if($counter == 5){
              break 2;
             }    
        }
       }
     }
  echo"</ul>";
}*/

function display_map($query_text)
{
	
	$xml_coor = simplexml_load_file(urlencode('http://35.164.242.31:8983/solr/ChristmasData/select?fl=tweet_loc&fq=tweet_loc:[-180%20TO%20180]&indent=on&facet.field=cluster_name&facet=on&q='.$query_text.'&rows=1000&wt=xml'));

	$coorstr= "";
	foreach($xml_coor->result->children() as $doc){
		$coorstr .= "['" . (string)$doc->arr[0]->double[0] . "," . (string)$doc->arr[0]->double[1]. "'],";
	}
	echo"<script type='text/javascript'>
     google.charts.load('upcoming', {'packages': ['geochart']});
     google.charts.setOnLoadCallback(drawMarkersMap);

    function drawMarkersMap() {
		var data = new google.visualization.DataTable();
		data.addColumn('string','Country');
		data.addRows([$coorstr]);
		
		var options = {
		sizeAxis: { maxSize: 3 },
    displayMode: 'markers',
		width:575,
    backgroundColor: '#81d4fa',
    colorAxis: {minValue: 0, maxValue: 100, colors: ['yellow']}
      };

      var chart = new google.visualization.GeoChart(document.getElementById('chart_div'));
      chart.draw(data, options);
    };
    </script>";
	
}

function display_topics($xml_data)
{
       echo "<ol class ='labels-list'><strong><b>Related Topics<b></strong>";
       foreach($xml_data->result->children() as $doc){
       foreach($doc->arr as $entity){
        $entity_name = $entity->attributes()->name;
        if($entity_name == "hashtags"){
          $tag = $entity->str;
          echo "<li>";
          echo $tag;
          echo "</li>";
        }
       }
     }
       echo "</ol>";

}

function display_data($xml_data){
  $tweet_text = "tweet_text";
  $counter = 0;
  echo "<div id='tweet-table' class='clear'>";
  /*echo "<table>";*/ 
  foreach($xml_data->result->children() as $doc){
     
     /*echo "<tr>";*/
     $counter =$counter+1;
     if($counter == 1){
       echo "<div class='plan' id='most-popular'>";
     }
     else {
       echo "<div class='plan'>";
     }
    foreach($doc->arr as $entity){
         $entity_name = $entity->attributes()->name;
         if($entity_name == "cluster_name"){
            /*echo "HashTag: ";*/
            /*echo "<td>";*/
            $tag = $entity->str;
            echo "<h3>";
            echo "<b>";
            echo $tag;
            echo "</b>";
            echo "</h3>";
            /*echo $entity->str;*/
            /*echo "</td>";*/
         }

    }
    foreach($doc->str as $entity){
      $entity_name = $entity->attributes()->name;
      if($entity_name == "text_en")
         {
            /*echo "<td>";*/
            echo "<br>";
            echo $entity;
            /*echo "</td>";*/
         }    
    }
    /*echo "</tr>";*/
    echo "</div>";
    if($counter % 2 == 0){
      echo "<br>";
    }
    if($counter == 10){
    break;
    }
    /*echo "---------Tweet Break-------";*/
 }
 echo "</div>";
 /*echo "</table>";*/
}
?>

<script>
// Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

</body>
</html>
