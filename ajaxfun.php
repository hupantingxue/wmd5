<?php
function md5show ($string)
{
	global $empire, $dbtbpre;
	$data = array( 
		'status' => 'fail' 
	);
	$string = trim($string);
	if (empty($string))
	{
		$data['msg'] = 'error';
		ajaxJsonData($data);
	}
	$type = '';
	$string = RepPostVar(strtolower($string));
	$len = strlen($string);
	$type_arr = getDecryptType();
	if (!array_key_exists($len, $type_arr))
	{
		$data['msg'] = 'error';
		ajaxJsonData($data);
	}
	$decrypt_fun = trim($type_arr[$len]);
	if (function_exists($decrypt_fun))
	{
		$r = $decrypt_fun($string, '', $type);
	}
	if (!empty($r['text']))
	{
		$data['status'] = 'success';
		$strlen = strlen($r['text']);
		if ((is_numeric($r['text']) && $strlen < 7) || (is_string($r['text']) && $strlen < 6))
		{
			$data['md5pass'] = $string;
			$data['md5text'] = $r['text'];
		}
		else
		{
			$data['id'] = $string;
			$data['t'] = $type;
			$data['md5text'] = '';
			$data['ispay'] = 'true';
			$data['act'] = 'buymd5text';
		}
	}
	$time = time();
	if($data['status']=='success'){
		$sql="insert into {$dbtbpre}tj(zs,cg,sb,time,type) values(1,1,0,'$time','$type')";
	}else{
		$sql="insert into {$dbtbpre}tj(zs,cg,sb,time,type) values(1,0,1,'$time','$type')";
	}
	$empire->query($sql);
	ajaxJsonData($data);
}

function getDecryptType()
{
	$data = array(
		16 =>	'getMD5Text16',  //md5_16 mysql
		32 => 'getMD5Text',  //md5_32 ntlm
		40 => 'getSha1Text',  // mysql5 sha1
		64 => 'getSha256Text',
		96 => 'getSha384Text',
		128 => 'getSha512Text',
	);
	return $data;
}

function getDecryptFun()
{
     $data = array(
		'md532' => 'getMD5Text',
		'mmd532' => 'getMMD5Text',
		'mmmd532' => 'getMMMD5Text',
		'md516' => 'getMD5Text16',
		'mmd516' => 'getMMD5Text16',
		'msha1' => 'getMD5Sha1Text',
		'mysql' => 'getMysqlText',
		'mysql5' => 'getMysql5Text',
		'sha1' => 'getSha1Text',
		'ssha1' => 'getSSha1Text',
		'sha1md5' => 'getSha1MD5Text',
		'sha256' => 'getSha256Text',
		'sha384' => 'getSha384Text',
		'sha512' => 'getSha512Text',
		'ntlm' => 'getNtlmText'
	);
	return $data;
}

function getDecryptTable($str)
{
	if (empty($str)) return;
	if (is_numeric($str[0]))
	{
		$ss = "s" . substr($str, 0, 2);
	}
	else
	{
		$ss = substr($str, 0, 2);
		if ((0 == strcasecmp($ss, "as")) || (0 == strcasecmp($ss, "db")) || (0 == strcasecmp($ss, "in")) || (0 == strcasecmp($ss, "if")) || (0 == strcasecmp($ss, "or")))
		{
			$ss = "s" . $ss;
		}
	}
	return $ss;
}

//获取MD5 32位
function getMD5Text ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'md532';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('md5_32');
	$collection = $db->selectCollection($table);
	$result = $collection->findOne(array( 
		'md32' => $str 
	));
	if (empty($result))
	{
		$result = getMMD5Text ($str, $table, $type);
	}
	if (empty($result))
	{
		$result = getMMMD5Text ($str, $table, $type);
	}
	if (empty($result))
	{
		$result = getMD5Sha1Text ($str, $table, $type);
	}
	if (empty($result))
	{
		$result = getNtlmText ($str, $table, $type);
	}
	return $result;
}

//获取双重MD5 32位
function getMMD5Text ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'mmd532';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('mmd5');
	$collection = $db->selectCollection($table);
	return $collection->findOne(array( 
		'mmd5' => $str 
	));
}

//获取三重MD5 32位
function getMMMD5Text ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'mmmd532';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('mmmd5');
	$collection = $db->selectCollection($table);
	return $collection->findOne(array( 
		'mmmd5' => $str 
	));
}

//获取MD5 SHA1 32位
function getMD5Sha1Text ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'md5sha1';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('msha1');
	$collection = $db->selectCollection($table);
	return $collection->findOne(array( 
		'msha1' => $str 
	));
}

//获取MD5 16位
function getMD5Text16 ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'md516';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('md5_16');
	$collection = $db->selectCollection($table);
	$result = $collection->findOne(array( 
		'md16' => $str 
	));
	if (empty($result))
	{
		$result = getMMD5Text16 ($str, $table, $type);
	}
	/*if (empty($result))
	{
		$result = getMMMD5Text16 ($str, $table, $type);
	}*/
	if (empty($result))
	{
		$result = getMysqlText ($str, $table, $type);
	}
	return $result;
}

//获取双重MD5 16位
function getMMD5Text16 ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'mmd516';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('mmd5_16');
	$collection = $db->selectCollection($table);
	return $collection->findOne(array( 
		'mmd5_16' => $str 
	));
}

//获取三重MD5 16位
function getMMMD5Text16 ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'mmmd516';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('mmmd5_16');
	$collection = $db->selectCollection($ss);
	return $collection->findOne(array( 
		'mmmd5_16' => $str 
	));
}

function getMysqlText ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'mysql';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('mysql');
	$collection = $db->selectCollection($table);
	return $collection->findOne(array( 
		'mysql' => $str 
	));
}

function getMysql5Text ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'mysql5';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('mysql5');
	$collection = $db->selectCollection($table);
	return $collection->findOne(array( 
		'mysql5' => $str 
	));
}

function getNtlmText ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'ntlm';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('ntlm');
	$collection = $db->selectCollection($table);
	return $collection->findOne(array( 
		'ntlm' => $str 
	));
}

function getSha1Text ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'sha1';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('sha1');
	$collection = $db->selectCollection($table);
	$result = $collection->findOne(array( 
		'sha1' => $str 
	));
	if (empty($result))
	{
		$result = getSSha1Text ($str, $table, $type);
	}
	if (empty($result))
	{
		$result = getMysql5Text ($str, $table, $type);
	}
	if (empty($result))
	{
		$result = getSha1MD5Text ($str, $table, $type);
	}
	return $result;
}

function getSSha1Text ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'ssha1';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('ssha1');
	$collection = $db->selectCollection($table);
	return $collection->findOne(array( 
		'ssha1' => $str 
	));
}

function getSha1MD5Text ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'sha1md5';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('sha1md5');
	$collection = $db->selectCollection($table);
	return $collection->findOne(array( 
		'sha1md5' => $str 
	));
}

function getSha256Text ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'sha256';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('sha256');
	$collection = $db->selectCollection($table);
	return $collection->findOne(array( 
		'sha256' => $str 
	));
}

function getSha384Text ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'sha384';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('sha384');
	$collection = $db->selectCollection($table);
	return $collection->findOne(array( 
		'sha384' => $str 
	));
}

function getSha512Text ($str, $table = '', &$type = '')
{
	if (empty($table))
	{
		$table = getDecryptTable($str);
	}
	if (empty($str) || empty($table)) return false;
	$type = 'sha512';
	$conn = new MongoClient("mongodb://sj.wmd5.com:27010");
	$db = $conn->selectDB('sha512');
	$collection = $db->selectCollection($table);
	return $collection->findOne(array( 
		'sha512' => $str 
	));
}

function buymd5text ($string, $type)
{
	global $empire, $dbtbpre;
	$data = array( 
		'status' => 'fail', 'msg' => '', 'md5text' => '' 
	);
	$string = trim($string);
	if (empty($string))
	{
		$data['msg'] = gbk_utf8('出错了，请稍后再试！');
		ajaxJsonData($data);
	}
	$type_arr = getDecryptFun();
	if (!array_key_exists($type, $type_arr))
	{
		$data['msg'] = gbk_utf8('不支持的解密类型');
		ajaxJsonData($data);
	}
	$user = ajaxIsLogin();
	if (empty($user))
	{
		$data['msg'] = gbk_utf8('请<a href=login.php  target="_blank"><font color="#0036D9">登陆</font></a>后查询， 没有帐号请<a href=reg.php  target="_blank"><font color="#0036D9">点击注册</font></a>');
		ajaxJsonData($data);
	}

	$string = RepPostVar(strtolower($string));
	$decrypt_fun = trim($type_arr[$type]);
	if (function_exists($decrypt_fun))
	{
		$r = $decrypt_fun($string);
	}
	if (!empty($r['text']))
	{
		$ur = $empire->fetch1("select id from {$dbtbpre}enewsmd5record where password='$string' and uid='" . $user['userid'] . "' limit 1");
		if (!empty($ur['id']))
		{
			$data['msg'] = gbk_utf8('该记录已经购买，请到付费记录中查看！');	
		}
		else if (intval($user['buynum']) < 1)
		{
			$data['msg'] = gbk_utf8('余额不足，请<a href=recharge.php><font color="#0036D9">充值</font></a>');
		}
		else
		{
			$data['status'] = 'success';
			$data['md5text'] = $r['text'];
			$buytime = time();
			$empire->query("UPDATE {$dbtbpre}enewsmember SET buynum=buynum-1 WHERE userid='" . $user['userid'] . "'");
			$empire->query("INSERT INTO {$dbtbpre}enewsmd5record SET `uid`='" . $user['userid'] . "',`text`='" . $r['text'] . "',`password`='$string',buytime='$buytime'");
			
			//只保留该用户20条购买记录
			$row = $empire->fetch1("select id from {$dbtbpre}enewsmd5record where uid='" . $user['userid'] . "' ORDER BY id DESC limit 20,1");
			if (!empty($row['id']))
			{
				$empire->query("DELETE from {$dbtbpre}enewsmd5record WHERE uid='" . $user['userid'] . "'  AND `id`<='".$row['id']."'");
			}
		}
	}
	else
	{
		$data['msg'] = gbk_utf8('记录不存在');
	}
	ajaxJsonData($data);
}

function all_md5show ($md5)
{
	global $empire, $dbtbpre;
	$data = array( 
		'status' => 'fail', 'msg' => gbk_utf8('没有找到') 
	);

	$md5 = RepPostVar($md5);
	if (empty($md5))
	{
		$data['msg'] = gbk_utf8('请输入MD5密文');
		ajaxJsonData($data);
	}

	$user = ajaxIsLogin();
	if (empty($user))
	{
		$data['msg'] = gbk_utf8('请<a href=login.php  target="_blank"><font color="#0036D9">登陆</font></a>后查询， 没有帐号请<a href=reg.php  target="_blank"><font color="#0036D9">点击注册</font></a>');
		ajaxJsonData($data);
	}

	$i = 1;
	$string = $query = $query1 = '';
	$array = explode("\n", $md5);
	$type_arr = getDecryptType();
	foreach ($array as $key => $val)
	{
		$type = '';
		$val = trim($val);
		if (empty($val)) continue;
		if ($key > 200) break;
		$len = strlen($val);
		if (!array_key_exists($len, $type_arr)) continue;
		$val = RepPostVar(strtolower($val));
		$decrypt_fun = trim($type_arr[$len]);
		if (function_exists($decrypt_fun))
		{
			$r = $decrypt_fun($val, '', $type);
		}
		$buytime = time();
		if ($r['text'])
		{
			$ur = $empire->fetch1("select buynum from {$dbtbpre}enewsmember where `userid`='" . $user['userid'] . "' limit 1");
			$data['status'] = 'success';
			$strlen = strlen($r['text']);
			if ((is_numeric($r['text']) && $strlen < 7) || (is_string($r['text']) && $strlen < 6))
			{
				$string .= $val . ' = ' . $r['text'] . "\r\n";
			}
			else if (intval($ur['buynum']) < 1)
			{
				$string .= $val . ' = ' . gbk_utf8('已找到，余额不足') . "\r\n";
			}
			else
			{
				$string .= $val . ' = ' . $r['text'] . gbk_utf8('付费记录') . "\r\n";
				if ($i == 1)
				{
					$query .= "INSERT INTO {$dbtbpre}enewsmd5record(uid,text,password,buytime)VALUES('" . $user['userid'] . "','" . $r['text'] . "','" . $val. "','$buytime')";
				}
				else
				{
					$query .= ",('" . $user['userid'] . "','" . $r['text'] . "','" . $val . "','$buytime')";
				}
				$i++;
				$empire->query("UPDATE {$dbtbpre}enewsmember SET `buynum`=buynum-1 WHERE userid='" . $user['userid'] . "'");
			}
		}
		if($data['status']=='success'){
			$query1.=$query1?",(1,1,0,'$buytime','$type')":"insert into {$dbtbpre}tj(zs,cg,sb,time,type) values(1,1,0,'$buytime','$type')";
		}else{
			$query1.=$query1?",(1,0,1,'$buytime','$type')":"insert into {$dbtbpre}tj(zs,cg,sb,time,type) values(1,0,1,'$buytime','$type')";
		}
	   
	}
	if ($query)
	{
		$empire->query($query);
	}
	if ($query1)
	{
		$empire->query($query1);
	}
	$data['md5text'] = $string;
	ajaxJsonData($data);
}

//用户注册
function register ($add)
{
	global $empire, $dbtbpre, $public_r, $ecms_config;
	//关闭注册
	if ($public_r['register_ok'])
	{
		ajaxMsg('已关闭注册功能');
	}
	$username = RepPostVar(trim($add['username']));
	$password = RepPostVar(trim($add['password']));
	$email = RepPostStr(trim($add['email']));
	if (!$username || !$password || !$email)
	{
		ajaxMsg('请将信息填写完整');
	}
	$groupid = 1;
	//IP
	$regip = egetip();
	//用户字数
	$pr = $empire->fetch1("select min_userlen,max_userlen,min_passlen,max_passlen,regretime,regclosewords,regemailonly from {$dbtbpre}enewspublic limit 1");
	$userlen = strlen($username);
	if ($userlen < $pr[min_userlen] || $userlen > $pr[max_userlen])
	{
		ajaxMsg('用户名过长或过短');
	}
	//密码字数
	$passlen = strlen($password);
	if ($passlen < $pr[min_passlen] || $passlen > $pr[max_passlen])
	{
		ajaxMsg('密码过长或过短');
	}
	if ($add['repassword'] !== $password)
	{
		ajaxMsg('两次输入密码不一致');
	}
	if (!chemail($email))
	{
		ajaxMsg('邮箱格式不正确');
	}
	if (strstr($username, '|') || strstr($username, '*'))
	{
		ajaxMsg('用户名包含非法字符');
	}
	$username = RepPostStr($username);
	//重复用户
	$num = $empire->gettotal("select count(*) as total from {$dbtbpre}enewsmember where username='$username' limit 1");
	if ($num)
	{
		ajaxMsg('该用户名已注册');
	}
	//重复邮箱
	if ($pr['regemailonly'])
	{
		$num = $empire->gettotal("select count(*) as total from {$dbtbpre}enewsmember where email='$email' limit 1");
		if ($num)
		{
			ajaxMsg('该邮箱已注册');
		}
	}
	//注册时间
	$lasttime = time();
	$registertime = eReturnAddMemberRegtime();
	$rnd = make_password(20); //产生随机密码
	$userkey = eReturnMemberUserKey();
	//密码
	$truepassword = $password;
	$salt = eReturnMemberSalt();
	$password = eDoMemberPw($password, $salt);
	//审核
	$checked = ReturnGroupChecked($groupid);
	if ($checked && $public_r['regacttype'] == 1)
	{
		$checked = 0;
	}
	//验证附加表必填项
	$mr['add_filepass'] = ReturnTranFilepass();
	$fid = GetMemberFormId($groupid);
	$member_r = ReturnDoMemberF($fid, $add, $mr, 0, $username);
	
	$sql = $empire->query("insert into " . eReturnMemberTable() . "(" . eReturnInsertMemberF('username,password,rnd,email,registertime,groupid,userfen,userdate,money,zgroupid,havemsg,checked,salt,userkey') . ") values('$username','$password','$rnd','$email','$registertime','$groupid','$public_r[reggetfen]','0','0','0','0','$checked','$salt','$userkey');");
	//取得userid
	$userid = $empire->lastid();
	//附加表
	$addr = $empire->fetch1("select * from {$dbtbpre}enewsmemberadd where userid='$userid'");
	if (!$addr[userid])
	{
		$spacestyleid = ReturnGroupSpaceStyleid($groupid);
		$sql1 = $empire->query("insert into {$dbtbpre}enewsmemberadd(userid,spacestyleid,regip,lasttime,lastip,loginnum" . $member_r[0] . ") values('$userid','$spacestyleid','$regip','$lasttime','$regip','1'" . $member_r[1] . ");");
	}
	//更新附件
	UpdateTheFileOther(6, $userid, $mr['add_filepass'], 'member');
	if ($sql)
	{
		$logincookie = 0;
		if ($ecms_config['member']['regcookietime'])
		{
			$logincookie = time() + $ecms_config['member']['regcookietime'];
		}
		$r = $empire->fetch1("select * from {$dbtbpre}enewsmember where userid='$userid' limit 1");
		$set1 = esetcookie("mlusername", $username, $logincookie);
		$set2 = esetcookie("mluserid", $userid, $logincookie);
		$set3 = esetcookie("mlgroupid", $groupid, $logincookie);
		$set4 = esetcookie("mlrnd", $rnd, $logincookie);
		//验证符
		qGetLoginAuthstr($userid, $username, $rnd, $groupid, $logincookie);
		//登录附加cookie
		AddLoginCookie($r);
		ajaxMsg('success');
	}
	else
	{
		ajaxMsg('系统繁忙2');
	}
}

//登录
function login ($add)
{
	global $empire, $dbtbpre, $public_r, $ecms_config;
	$username = trim($add['username']);
	$password = trim($add['password']);
	if (!$username || !$password)
	{
		ajaxMsg('请输入用户名和密码');
	}
	
	$username = RepPostVar($username);
	$password = RepPostVar($password);
	$num = 0;
	$r = $empire->fetch1("select * from {$dbtbpre}enewsmember where username='$username' limit 1");
	if (!$r['userid'])
	{
		ajaxMsg('密码不正确');
	}
	if (!eDoCkMemberPw($password, $r['password'], $r['salt']))
	{
		ajaxMsg('密码不正确');
	}
	if ($r['checked'] == 0)
	{
		ajaxMsg('账号未审核');
	}
	$rnd = make_password(20); //取得随机密码
	$lasttime = time();
	//IP
	$lastip = egetip();
	$usql = $empire->query("update {$dbtbpre}enewsmember set rnd='$rnd' where userid='$r[userid]'");
	$empire->query("update {$dbtbpre}enewsmemberadd set lasttime='$lasttime',lastip='$lastip',loginnum=loginnum+1 where userid='$r[userid]'");
	//设置cookie
	$lifetime = (int) $add['lifetime'];
	$logincookie = 0;
	if ($lifetime)
	{
		$logincookie = time() + $lifetime;
	}
	$set1 = esetcookie("mlusername", $username, $logincookie);
	$set2 = esetcookie("mluserid", $r['userid'], $logincookie);
	$set3 = esetcookie("mlgroupid", $r['groupid'], $logincookie);
	$set4 = esetcookie("mlrnd", $rnd, $logincookie);
	//验证符
	qGetLoginAuthstr($r['userid'], $username, $rnd, $r['groupid'], $logincookie);
	//登录附加cookie
	AddLoginCookie($r);
	if ($set1 && $set2)
	{
		ajaxMsg('success');
	}
	else
	{
		ajaxMsg('系统繁忙3');
	}
}

//退出登陆
function loginexit ()
{
	EmptyEcmsCookie();
	ajaxMsg('success');
}

//修改密码
function EditPass ($add)
{
	global $empire, $dbtbpre, $public_r;
	$user_r = islogin(); //是否登陆
	$userid = $user_r[userid];
	$username = $user_r[username];
	$rnd = $user_r[rnd];
	//验证原密码
	$oldpassword = RepPostVar($add[oldpassword]);
	if (!$oldpassword)
	{
		ajaxMsg('请输入原密码');
	}
	$add[password] = RepPostVar($add[password]);
	$num = 0;
	$ur = $empire->fetch1("select " . eReturnSelectMemberF('userid,password,salt') . " from " . eReturnMemberTable() . " where " . egetmf('userid') . "='$userid'");
	if (empty($ur['userid']))
	{
		ajaxMsg('原密码不正确');
	}
	if (!eDoCkMemberPw($oldpassword, $ur['password'], $ur['salt']))
	{
		ajaxMsg('原密码不正确');
	}
	//密码
	$salt = '';
	if (empty($add[password]))
	{
		ajaxMsg('请输入新密码');
	}
	if ($add[password] !== $add[repassword])
	{
		ajaxMsg('新密码两次输入不一致');
	}
	$salt = eReturnMemberSalt();
	$password = eDoMemberPw($add[password], $salt);
	$sql = $empire->query("update " . eReturnMemberTable() . " set " . egetmf('password') . "='$password'," . egetmf('salt') . "='$salt' where " . egetmf('userid') . "='$userid'");
	EmptyEcmsCookie();
	ajaxMsg('success');
}

//是否登录
function ajaxIsLogin ($uid = 0, $uname = '', $urnd = '')
{
	global $empire, $dbtbpre, $public_r, $ecmsreurl, $ecms_config;
	if ($uid)
	{
		$userid = (int) $uid;
	}
	else
	{
		$userid = (int) getcvar('mluserid');
	}
	if ($uname)
	{
		$username = $uname;
	}
	else
	{
		$username = getcvar('mlusername');
	}
	$username = RepPostVar($username);
	if ($urnd)
	{
		$rnd = $urnd;
	}
	else
	{
		$rnd = getcvar('mlrnd');
	}
	if (!$userid)
	{
		return false;
	}
	$rnd = RepPostVar($rnd);
	$cr = $empire->fetch1("select " . eReturnSelectMemberF('userid,username,email,groupid,userfen,money,userdate,zgroupid,havemsg,checked,registertime,buynum') . " from " . eReturnMemberTable() . " where " . egetmf('userid') . "='$userid' and " . egetmf('username') . "='$username' and " . egetmf('rnd') . "='$rnd' limit 1");
	if (!$cr['userid'])
	{
		EmptyEcmsCookie();
		return false;
	}
	if ($cr['checked'] == 0)
	{
		EmptyEcmsCookie();
		return false;
	}
	//默认会员组
	if (empty($cr['groupid']))
	{
		$user_groupid = eReturnMemberDefGroupid();
		$usql = $empire->query("update " . eReturnMemberTable() . " set " . egetmf('groupid') . "='$user_groupid' where " . egetmf('userid') . "='" . $cr[userid] . "'");
		$cr['groupid'] = $user_groupid;
	}
	//是否过期
	if ($cr['userdate'])
	{
		if ($cr['userdate'] - time() <= 0)
		{
			OutTimeZGroup($cr['userid'], $cr['zgroupid']);
			$cr['userdate'] = 0;
			if ($cr['zgroupid'])
			{
				$cr['groupid'] = $cr['zgroupid'];
				$cr['zgroupid'] = 0;
			}
		}
	}
	$re[userid] = $cr['userid'];
	$re[rnd] = $rnd;
	$re[username] = $cr['username'];
	$re[email] = $cr['email'];
	$re[userfen] = $cr['userfen'];
	$re[money] = $cr['money'];
	$re[groupid] = $cr['groupid'];
	$re[userdate] = $cr['userdate'];
	$re[zgroupid] = $cr['zgroupid'];
	$re[havemsg] = $cr['havemsg'];
	$re[registertime] = $cr['registertime'];
	$re[buynum] = $cr['buynum'];
	return $re;
}
function ajaxMsg ($msg)
{
	exit($msg);
}
function ajaxJsonData ($array)
{
	$data = '';
	if (is_array($array))
	{
		$data = json_encode($array);
	}
	exit($data);
}

//编码转换
function gbk_utf8 ($str, $type = 0)
{
	if (!function_exists("iconv")) //是否支持iconv
	{
		$fun = "DoIconvVal";
		$code = "GB2312";
		$targetcode = "UTF8";
	}
	else
	{
		$fun = "iconv";
		$code = "GBK";
		$targetcode = "UTF-8";
	}
	if (empty($type))
	{
		$str = $fun($code, $targetcode, $str);
	}
	else
	{
		$str = $fun($targetcode, $code, $str);
	}
	return addslashes($str);
}
?>