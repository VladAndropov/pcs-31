<?php

$userid=JFactory::getUser()->id;
$username=JFactory::getUser()->name;


/* Коннектимся к MySQL базе данных */ 
$db = mysql_connect("localhost", "sdo", "123123"); 

if ( $db == "" ) { echo " DB Connection error...rn"; exit(); } 

mysql_select_db("vu",$db); 
//mysql_query("SET NAMES 'cp1251'");
mysql_query("SET NAMES utf8");

//-----------блок махара-----------------
//$Requete = "SELECT zzzmah_usr.id, univer_users.id, univer_users.name  FROM  zzzmah_usr  INNER JOIN  univer_users    ON zzzmah_usr.email = univer_users.email ";
$Requete = "SELECT univer_users.id,  univer_users.name  FROM  zzzmah_usr  INNER JOIN  univer_users    ON zzzmah_usr.email = univer_users.email ";
//$Requete = "SELECT username  ,lastname  FROM`zzzmah_usr`  ";
//$Requete = "SELECT owner   FROM`zzzmah_artefact` WHERE  artefacttype='lastname' ";
$result = mysql_query($Requete); 

print "<center><font size='12' color='#6666CC'><B> Выберите фамилию пользователя </B> </font></center>";


//-------дедаем табличку с фамилиями-------
echo "<form method='post'  id='form1'>"; 
echo "<select name='color' onChange=document.getElementById('form1').submit() size=20>rn";  

while ($row = mysql_fetch_array($result)) 
{ 

 echo '<option value="'.$row['id'].'">'.$row['name'].'</option>'."rn";        
}
echo "</select>";
echo "</form>";

//----если выбрали ----
if (!empty($_POST['color'])) { 
$userid = $_POST['color'];
// --находим махара юзерид ---
$Requete = "SELECT zzzmah_usr.id, univer_users.id, univer_users.name   FROM  zzzmah_usr   INNER JOIN  univer_users    ON zzzmah_usr.email = univer_users.email  ";
$result = mysql_query($Requete); 
while ($row = mysql_fetch_array($result)) {
 if ($userid ==$row[1]) {
	 $useridmah = $row[0];
	 $username = $row[2]; 
	 };   
}
//-----конец блока махара ----------- 

// -- сопоставили , теперь продолжаем с пользователем какого выбрали ---

$Requete = "SELECT COUNT( * ) AS repetitions, kod FROM univer_community_fields_values WHERE user_id=$userid AND `kod` != ' ' GROUP BY kod HAVING repetitions >0";


$result = mysql_query($Requete,$db); 
$i=0;$mymaxvalue=1;
if($result) 
{
	while($row = mysql_fetch_array($result)) 
{ $i=$i+1;
$g[$i]=($row["repetitions"]); 

}


} else
{
print "n Пользователь не заполнил профиль компетенциями!!!  ";
} 

if (!empty($g) and !empty($username)){
    
$mymaxvalue=max($g);

$mymax = array_search($mymaxvalue,$g);
$arrays=implode(",", $g);
$mymaxvalue=10*$mymaxvalue;


$Requete = "SELECT ad_headline  ,ad_text,   ad_competention  FROM univer_adsmanager_ads WHERE  ad_rank=".$mymax."  ORDER BY ad_competention Desc";
$result = mysql_query($Requete); 
while ($row = mysql_fetch_array($result)) 
{ 
$arraycounty=$row['ad_competention'].",".$arraycounty;
$arraytitle=$row['ad_headline'].",".$arraytitle;

} 
echo "<iframe name='iframe' style='overflow:hidden;' width='450' height='450'> </iframe>"; 
echo "<iframe name='iframe2' style='overflow:hidden;' width='450' height='450'> </iframe>"; 
// вебграф компетенций ----------
$input1.="<input type='hidden' name='mymax' value='$mymax' />";
$input2.="<input type='hidden' name='username' value='$username' />";
$input3.="<input type='hidden' name='arrays' value='$arrays' />";
$input0.=$input1.$input2.$input3;
   $form1 = "<form  id='moodleform' target='iframe' method='post' action='/../../example.radar.values.php'>".$input0."</form>";
 $script = "<script type='text/javascript'>document.getElementById('moodleform').submit();</script>";
echo $form1.$script;
// вебграф махара ----------
$input1.="<input type='hidden' name='useridmah' value='$useridmah' />";
$input2.="<input type='hidden' name='username' value='$username' />";
$input0.=$input1.$input2;
   $form2 = "<form  id='moodleform2' target='iframe2' method='post' action='.../../webgraph.php'>".$input0."</form>";
 $script2 = "<script type='text/javascript'>document.getElementById('moodleform2').submit();</script>";
echo $form2.$script2;

print "<center><font size='12' color='#ffffff'><a href=http://vu.elsu.ru/index.php?option=com_jumi&view=application&fileid=11&Itemid=593&userid=".$userid."&username=".$username.">Перейти к online-расчету подходящей профессии по достижениям пользователя </a> </font></center>";

 }else{echo "<font size='5' color='#CC33FF'><B> Пояснение:  </B>";
 echo $username." не заполнил профиль компетенциями!!!  Семантический Граф будет пустым. Нет смысла его строить. Выберите, например: Андропов, Мамырин, Лыкова, Дронов, Кураев, Невзгодин, Собова, Разнова, Целыковская, Ноздрачёв, Пиджоян, Морозова,Пастухова, Самсонова...</font>";
}
 }
In [ ]:
<?php

$userid=JFactory::getUser()->id;
$username=JFactory::getUser()->name;


// echo "<font size='5' color='#9b821d'><B> Вычисляем компетентности (ФГОС3) из курсов дистанционного обучения:</B>";

/* Коннектимся к MySQL базе данных */ 
$db = mysql_connect("localhost", "sdo", ""); 

if ( $db == "" ) { echo " DB Connection error...rn"; exit(); } 

mysql_select_db("sdo",$db); 
//mysql_query("SET NAMES 'cp1251'");
mysql_query("SET NAMES utf8");

//-----------блок махара-----------------
//$Requete = "SELECT mdl_user.id,  mdl_user.firstname, mdl_user.lastname  FROM  mdl_user  WHERE deleted=0";

$Requete = "SELECT mdl_user.id,  mdl_user.firstname, mdl_user.lastname  FROM  mdl_user  INNER JOIN  mdl_role_assignments  ON  mdl_user.id = mdl_role_assignments.userid WHERE mdl_role_assignments.roleid =5 AND mdl_user.deleted=0 " ;

$result = mysql_query($Requete); 

print "<center><B> Выберите фамилию студента </B> </center>";


//-------дедаем табличку с фамилиями-------
echo "<form method='post'  id='form1'>"; 
echo "<select name='color' onChange=document.getElementById('form1').submit() size=20>rn";  

while ($row = mysql_fetch_array($result)) 
{ 

 echo '<option value="'.$row['id'].'">'.$row['firstname'].' '.$row['lastname'].'</option>'."rn";        
}
echo "</select>";
echo "</form>";

//----если выбрали ----
if (!empty($_POST['color'])) { 
$userid = $_POST['color'];
// --находим сертификаты юзерид ---
$Requete = "SELECT competences_activite   FROM   `mdl_referentiel_certificat`   WHERE  userid=$userid  ";
$result = mysql_query($Requete); 
 echo "<B> Для данного пользователя список освоенных компетентностей таков:</B></BR>";
while ($row = mysql_fetch_array($result)) 
	{ echo $row[0] ; }
}
else {
    echo "<font size='5' color='#CC33FF'><B> Пояснение:  </B>";
 echo "Выберите, например: Марина Родионова,  Андрей ОСИПОВ,  Лариса ПИДЖОЯН, Светлана САМСОНОВА...</font>";
}
	


?>
In [ ]:
<?php

$userid=JFactory::getUser()->id;
$username=JFactory::getUser()->name;



/* Коннектимся к MySQL базе данных */ 
$db = mysql_connect("localhost", "sdo", ""); 

if ( $db == "" ) { echo "Ошибка, видимо, админ поменял пароль и не сказал мне, гад"; exit(); } 

mysql_select_db("sdo",$db); 
//mysql_query("SET NAMES 'cp1251'");
mysql_query("SET NAMES utf8");

//-----------блок махара-----------------
//$Requete = "SELECT zzzmah_usr.id, univer_users.id, univer_users.name  FROM  zzzmah_usr  INNER JOIN  univer_users    ON zzzmah_usr.email = univer_users.email ";
//$Requete = "SELECT mdl_user.id,  mdl_user.firstname, mdl_user.lastname  FROM  mdl_user  WHERE deleted=0 AND mdl_role_assignments.roleid=2";
$Requete = "SELECT mdl_user.id,  mdl_user.firstname, mdl_user.lastname  FROM  mdl_user  INNER JOIN  mdl_role_assignments  ON  mdl_user.id = mdl_role_assignments.userid WHERE mdl_role_assignments.roleid = 2 " ;
//$Requete = "SELECT username  ,lastname  FROM`zzzmah_usr`  ";
//$Requete = "SELECT owner   FROM`zzzmah_artefact` WHERE  artefacttype='lastname' ";
$result = mysql_query($Requete); 

print "<center><B> Выберите фамилию преподавателя  </B> </center>";


//-------дедаем табличку с фамилиями-------
echo "<form method='post'  id='form1'>"; 
echo "<select name='color' onChange=document.getElementById('form1').submit() size=20>rn";  

while ($row = mysql_fetch_array($result)) 
{ 

 echo '<option value="'.$row['id'].'">'.$row['firstname'].' '.$row['lastname'].'</option>'."rn";        
}
echo "</select>";
echo "</form>";

//----если выбрали ----
if (!empty($_POST['color'])) { 
$userid = $_POST['color'];
// --находим сертификаты юзерид ---
$Requete = "SELECT competences_activite   FROM   `mdl_referentiel_certificat`   WHERE  userid=$userid  ";
$result = mysql_query($Requete); 
 echo "<B>Данный преподаватель выдал сертификаты об окончании курса исходя из следующих компетенций:</B></BR>";
while ($row = mysql_fetch_array($result)) 
	{ echo $row[0] ; }
}
else {
    echo "<font size='5' color='#CC33FF'><B> Пояснение:  </B>";
 echo "Выберите, например: Андропов,  Светлана TOKAREVA, Галина АЛЕКСАНДРОВНА ВОЕВОДИНА, Ирина ЗАЙЦЕВА, Евгений КИСЕЛЕВ, Андрей ОСИПОВ,  Лариса ПИДЖОЯН, Света САМСОНОВА...</font>";
}

?>
In [ ]:
<?php

$userid=JFactory::getUser()->id;
$username=JFactory::getUser()->name;


 

/* Коннектимся к MySQL базе данных */ 
$db = mysql_connect("localhost", "sdo", ""); 

if ( $db == "" ) { echo "Ошибка, видимо, админ поменял пароль и никому не сказал об этом"; exit(); } 

mysql_select_db("vu",$db); 
//mysql_query("SET NAMES 'cp1251'");
mysql_query("SET NAMES utf8");


//-----------блок moodle-----------------
$Requete = "SELECT  COUNT( course ) AS repetitions, course, userid, mdl_user.id,  mdl_user.firstname, mdl_user.lastname
FROM `mdl_course_completions` INNER JOIN  mdl_user ON  mdl_user.id = mdl_course_completions.userid
GROUP BY course
HAVING repetitions >1";
$result = mysql_query($Requete); 
while ($row = mysql_fetch_array($result)) 
{ 
$arraycounty=$row['repetitions'].",".$arraycounty;
$arraytitle=$row['firstname']." ".$row['lastname'].",".$arraytitle;
} 
$arraycounty =explode(",",$arraycounty);
$arraytitle =explode(",",$arraytitle);
//- выводим график на джаве---
$script01 = "<script src='highcharts.js'></script>";
$script02 = "<script src='exporting.js'></script>";
$div01 = "<div id='container' style='min-width: 400px; height: 400px; margin: 0 auto'></div>";
$div02 = "<div id='stage' style='color:red; text-align:center'>          Подсказка: Нажмите на столбик!  </div>";
 $input01="  <input type='hidden' id='driver' value='Load Data' />";
  $form01 = "<body>".$script01.$script02.$div01.$div02.$input01."</body>";  
echo $form01; 
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highcharts Example</title>
		<script> var arraycounty=<?php echo  json_encode($arraycounty,JSON_NUMERIC_CHECK); ?>; </script>
	<script> var arraytitle=<?php print  json_encode($arraytitle); ?>; </script>	
	
		<script type="text/javascript" src="jquery.min.js"></script>
	
		<script type="text/javascript">
$(function () {
	var d=new Date();
var day=d.getDate();
var month=d.getMonth() + 1;
var year=d.getFullYear();
var mymax = 2;
var mymaxvalue = 2;
    var chart;
	var koorx=1;

		if (mymax==1) { zvet = 'rgba(250, 0, 0, .5)', popokazatelu = 'Артистичность'   ; };
			if (mymax==2) { zvet = 'rgba(0, 0, 250, .5)'  , popokazatelu = 'Социальность'    ;};
				if (mymax==3) { zvet = 'rgba(255, 215, 0, .5)' , popokazatelu = 'Предприимчивость'  ;   };
					if (mymax==4) { zvet = 'rgba(139, 69, 19, .5)'  , popokazatelu = 'Консерватизм'   ; };
	if (mymax==5) { zvet = 'rgba(148, 0, 211, .5)'  , popokazatelu = 'Реалистичность'   ; };
		if (mymax==6) { zvet = 'rgba(0, 250, 0, .5)'  , popokazatelu = 'Интеллект'   ; };

    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
					zoomType: 'x'
              
            },
            title: {
                text: 'Рейтинг преподавателей на дату:'
            },
            subtitle: {
                text: day + "." + month + "." + year
            },
            xAxis: {labels: {
                step: 4
            },
                categories: 
               arraytitle
                
            },
            yAxis: {plotBands: [{ // visualize the weekend
                    from: 0,
                    to: mymaxvalue,
                    color: 'rgba(68, 170, 213, .2)'
                }],
                min: 0,
                title: {
                    text: 'Количество слушателей'
                }
            },

            legend: {
                layout: 'vertical',
                backgroundColor: '#F0FFF0',
                align: 'left',
                verticalAlign: 'top',
                x: 200,
                y: 70,
                floating: true,
                shadow: true
            },
            tooltip: {
                formatter: function() {
                    return ''+
                        this.x  +': '+ this.y +' слушателей';
		
                }
            },
				plotOptions: {
				 column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                },
            series: {
                cursor: 'pointer',
                events: {
                  click: 
function (event) 
{
        
              var url = 'http://sdo.elsu.ru/user/index.php?id=1&search='+ arraytitle[event.point.x];
	   
		   window.location.href = url ;
 
}        
                }
            }
        }
           ,
                series: [{
			   id: 'series-1',
				  type: 'column',
                name: 'Рейтинг',
                data: arraycounty,
     color:zvet
            },
					  {
                type: 'line',
                name: 'минимальная граница фильтра',
                data: [[0, mymaxvalue], [arraytitle.length-2, mymaxvalue]],
                marker: {
                    enabled: false
                },
                states: {
                    hover: {
                        lineWidth: 2
                    }
                },
                enableMouseTracking: false
            }
				  ]
        });
    });
    
});
		</script>
	</head>
	
	
</html>
In [ ]:
<?php

$userid=JFactory::getUser()->id;
$username=JFactory::getUser()->name;


 

/* Коннектимся к MySQL базе данных */ 
$db = mysql_connect("localhost", "sdo", ""); 

if ( $db == "" ) { echo "Ошибка, видимо, админ поменял пароль и не сказал мне, гад"; exit(); } 

mysql_select_db("sdo",$db); 
mysql_query("SET NAMES utf8");

$Requete = "SELECT  COUNT( teacherid ) AS repetitions,   mdl_user.firstname, mdl_user.lastname
FROM mdl_referentiel_certificat  INNER JOIN  mdl_user ON  mdl_user.id = mdl_referentiel_certificat.teacherid
WHERE mdl_referentiel_certificat.date_decision >0
AND mdl_referentiel_certificat.teacherid >0
GROUP BY teacherid
HAVING repetitions >0";
//выбрали повторения для учителя
$result = mysql_query($Requete); 
while ($row = mysql_fetch_array($result)) 
{ 
$arraycounty=$row['repetitions'].",".$arraycounty;
$arraytitle=$row['firstname']." ".$row['lastname'].",".$arraytitle;
} 

$arraycounty =explode(",",$arraycounty);
$arraytitle =explode(",",$arraytitle);
//- выводим график на джаве---
$script01 = "<script src='highcharts.js'></script>";
$script02 = "<script src='exporting.js'></script>";
$div01 = "<div id='container' style='min-width: 400px; height: 400px; margin: 0 auto'></div>";
$div02 = "<div id='stage' style='color:red; text-align:center'>          Подсказка: Нажмите на столбик!  </div>";
 $input01="  <input type='hidden' id='driver' value='Load Data' />";
  $form01 = "<body>".$script01.$script02.$div01.$div02.$input01."</body>";  
echo $form01; 
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highcharts Example</title>
		<script> var arraycounty=<?php echo  json_encode($arraycounty,JSON_NUMERIC_CHECK); ?>; </script>
	<script> var arraytitle=<?php print  json_encode($arraytitle); ?>; </script>	
	
		<script type="text/javascript" src="jquery.min.js"></script>
	
		<script type="text/javascript">
$(function () {
	var d=new Date();
var day=d.getDate();
var month=d.getMonth() + 1;
var year=d.getFullYear();
var mymax = 1;
var mymaxvalue = 1;
    var chart;
	var koorx=1;

		if (mymax==1) { zvet = 'rgba(250, 0, 0, .5)', popokazatelu = 'Артистичность'   ; };
			if (mymax==2) { zvet = 'rgba(0, 0, 250, .5)'  , popokazatelu = 'Социальность'    ;};
				if (mymax==3) { zvet = 'rgba(255, 215, 0, .5)' , popokazatelu = 'Предприимчивость'  ;   };
					if (mymax==4) { zvet = 'rgba(139, 69, 19, .5)'  , popokazatelu = 'Консерватизм'   ; };
	if (mymax==5) { zvet = 'rgba(148, 0, 211, .5)'  , popokazatelu = 'Реалистичность'   ; };
		if (mymax==6) { zvet = 'rgba(0, 250, 0, .5)'  , popokazatelu = 'Интеллект'   ; };

    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
					zoomType: 'x'
              
            },
            title: {
                text: 'Рейтинг преподавателей на дату:'
            },
            subtitle: {
                text: day + "." + month + "." + year
            },
            xAxis: {labels: {
                step: 1
            },
                categories: 
               arraytitle
                
            },
            yAxis: {plotBands: [{ // visualize the weekend
                    from: 0,
                    to: mymaxvalue,
                    color: 'rgba(68, 170, 213, .2)'
                }],
                min: 0,
                title: {
                    text: 'Количество слушателей'
                }
            },

            legend: {
                layout: 'vertical',
                backgroundColor: '#F0FFF0',
                align: 'left',
                verticalAlign: 'top',
                x: 200,
                y: 70,
                floating: true,
                shadow: true
            },
            tooltip: {
                formatter: function() {
                    return ''+
                        this.x  +': выдал '+ this.y +' сертификатов';
		
                }
            },
				plotOptions: {
				 column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                },
            series: {
                cursor: 'pointer',
                events: {
                  click: 
function (event) 
{
        
              var url = 'http://sdo.elsu.ru/user/index.php?id=1&search='+ arraytitle[event.point.x];
	   
		   window.location.href = url ;
 
}        
                }
            }
        }
           ,
                series: [{
			   id: 'series-1',
				  type: 'column',
                name: 'Рейтинг',
                data: arraycounty,
     color:zvet
            },
					  {
                type: 'line',
                name: 'минимальная граница фильтра',
                data: [[0, mymaxvalue], [arraytitle.length-2, mymaxvalue]],
                marker: {
                    enabled: false
                },
                states: {
                    hover: {
                        lineWidth: 2
                    }
                },
                enableMouseTracking: false
            }
				  ]
        });
    });
    
});
		</script>
	</head>
	
	
</html>
In [ ]:
<?php

$userid=JFactory::getUser()->id;
$username=JFactory::getUser()->name;


// echo "<font size='5' color='#9b821d'><B> Вычисляем компетентности (ФГОС3) из курсов дистанционного обучения:</B>";

/* Коннектимся к MySQL базе данных */ 
$db = mysql_connect("localhost", "sdo", ""); 

if ( $db == "" ) { echo " DB Connection error...rn"; exit(); } 

mysql_select_db("sdo",$db); 
//mysql_query("SET NAMES 'cp1251'");
mysql_query("SET NAMES utf8");

//-----------блок первый-----------------

$Requete = "SELECT  mdl_tag_instance.itemid, mdl_tag.name   FROM  mdl_tag_instance  INNER JOIN   mdl_tag  ON  mdl_tag.id = mdl_tag_instance.tagid WHERE mdl_tag_instance.itemtype ='course'  " ;

$result = mysql_query($Requete); 

print "<center><B> Выберите таксон </B> </center>";


//-------делаем табличку с таксонами-------
echo "<form method='post'  id='form1'>"; 
echo "<select name='color' onChange=document.getElementById('form1').submit() size=20>rn";  

while ($row = mysql_fetch_array($result)) 
{ 

 echo '<option value="'.$row['itemid'].'">'.$row['name'].'</option>'."rn";        
}
echo "</select>";
echo "</form>";

//----если выбрали ----
if (!empty($_POST['color'])) { 
$courseid = $_POST['color'];

echo 'id курса = '.$courseid;
// --находим $courseid ---
$Requete = "SELECT mdl_referentiel_item_competence.description_item   FROM   mdl_referentiel_repartition INNER JOIN   mdl_referentiel_item_competence  ON  mdl_referentiel_repartition.code_item = mdl_referentiel_item_competence.code_item  WHERE  courseid=$courseid  ";
$result = mysql_query($Requete); 
 echo "</BR><B> Для данного таксона список компетентностей таков:</B></BR>";
while ($row = mysql_fetch_array($result)) 
	{ echo $row['description_item']."</BR>" ; }
}
else {
    echo "<font size='5' color='#CC33FF'><B> Пояснение:  </B>";
 echo "Выберите, например: сертификация...</font>";
}
	


?>
