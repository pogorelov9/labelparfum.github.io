<?php

header("Content-Type: text/html; charset=utf-8"); //charset

//адрес почты для отправки уведомления
$to = "test@gmail.com,test@yandex.ru"; //несколько ящиков могут перечисляться через запятую
$from = "noreply@".($_SERVER["HTTP_HOST"]); //адрес, от которого придёт уведомление, можно не трогать

// данные для отправки смс

$id = "";
$key = "";
$sms_login = "";
$sms_pass = "";
$frm = "callme"; // добавьте новую подпись в смс-шлюзе и дождитесь апрува
$num = ""; // ваш номер в формате без + (f.e. 380501112233 или 79218886622)
$prv = ""; // на выбор: sms.ru, infosmska.ru, bytehand.com, sms-sending.ru, smsaero.ru

function uc($s){
	$s = urlencode($s);
	return $s;
}

function gf($s){ // no shit
	$s = substr((htmlspecialchars($_GET[$s])), 0 , 500);
	if (strlen($s)>1) return $s;
}

function sendSMS($to, $msg){
	global $id;
	global $key;
	global $from;
	global $frm;
	global $num;
	global $prv;
	global $sms_login;
	global $sms_pass;
	
	$u['sms.ru'] = "sms.ru/sms/send?api_id=".uc($key)."&to=".uc($num)."&text=".uc($msg);
	$u['bytehand.com'] = "bytehand.com:3800/send?id=".uc($id)."&key=".uc($key)."&to=".uc($num)."&partner=callme&from=".uc($frm)."&text=".uc($msg);
	$u['sms-sending.ru'] = "lcab.sms-sending.ru/lcabApi/sendSms.php?login=".uc($sms_login)."&password=".uc($sms_pass)."&txt=".uc($msg)."&to=".uc($num);
	$u['infosmska.ru'] = "api.infosmska.ru/interfaces/SendMessages.ashx?login=".uc($sms_login)."&pwd=".uc($sms_pass)."&sender=SMS&phones=".uc($num)."&message=".uc($msg);
	$u['smsaero.ru'] = "gate.smsaero.ru/send/?user=".uc($sms_login)."&password=".md5(uc($sms_pass))."&to=".uc($num)."&text=".uc($msg)."&from=".uc($frm);
	
	$r = file_get_contents("http://".$u[$prv]);	
}

function translit($str) {
	$tr = array("А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I","Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH","Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"","Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j","з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l","м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h","ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y","ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya");
	return strtr($str,$tr);
} 
// translit * ProgrammerZ.Ru
//далее можно не трогать

$time = time(); // время отправки
$interval = $time - $_GET['ctime'];
if ($interval < 1) { // интервал отправки (сек)
	$result = "error";
	$cls = "c_error";
	$time = "";
	$message = "Сообщение уже было отправлено.";	
} else {

if ((strlen($_GET['cname'])>2)&&((strlen($_GET['cphone'])>5))){
	$phone = gf("cphone");
	$ref = gf("ref");
	$name = gf("cname");
	$comment = gf("ccmnt");
	$url = gf("url");
	$mess = "";

	// get city
	$ip = $_SERVER['REMOTE_ADDR'];
	$geo = file_get_contents('http://freegeoip.net/json/'.$ip);
	$geo = json_decode($geo, true);

function addToMess($c, $o){
	global $mess;
	if(strlen($o)>2) {
		$mess = $mess."<b>".$c."</b>:<br>".$o."<br><br>";
	}
}

	$title = "CallMe: обратный звонок";
	addToMess("Телефон",$phone);
	addToMess("Имя",$name);
	addToMess("Комментарий",$comment);
	addToMess("Отправлено со страницы",$url);
	addToMess("Источник трафика",$ref);
	addToMess("IP",$ip);
	addToMess("Откуда запрос",(($geo['city'])." (".($geo['country_name']).")" ));

	$mess = $mess."<hr><a href=''>Следите</a> за обновлениями.<br>
	Спасибо за то, что пользуетесь CallMe.";
	
	$headers  = "Content-type: text/html; charset=utf-8 \r\n"; 
	$headers .= "From: CallMe 1.8.0 <".$from.">\r\n"; 

$msg = "Callme:". translit($name).",".translit($phone)." ";
$msg .= substr(translit($comment), 0, (160-strlen($msg)));

@mail($to, $title, $mess, $headers);
	$result = "success";
	$cls = "c_success";
	$message = "Спасибо, сообщение отправлено"; //сообщение об отправке
	if (($id!="")||($key!="")||($sms_login!="")) { 
		@sendSMS($num, $msg); 
	}
} else {
	$result = "error";
	$cls = "c_error";
	$time = "";
	$message = "Заполните все поля.";
}
}
?>{
"result": "<?php echo $result; ?>",
"cls": "<?php echo $cls; ?>",
"time": "<?php echo $time; ?>",
"message": "<?php echo $message; ?>"
}