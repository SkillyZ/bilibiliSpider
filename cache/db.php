<?php
namespace cache;

use \PDO;
class DB {
	public static $db = array(
	    'dsn' => 'mysql:host=localhost;dbname=spider;port=3306;charset=utf8',
	    'host' => 'localhost',
	    'port' => '3306',
	    // 'dbname' => 'bilibili_video',
	    'username' => 'root',
	    'password' => '',
	    'charset' => 'utf8',
	);

	private static $pdo = null;
	private static $stmt = null;

	public function __construct() {
		self::_init(self::$db);
	}

	private static function _init($db)
	{
		if (is_object(self::$pdo)) {
			return ;
		}
		
		$options = [
		    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //默认是PDO::ERRMODE_SILENT, 0, (忽略错误模式)
		    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // 默认是PDO::FETCH_BOTH, 4 默认关联索引遍历
		];
		try {
		    self::$pdo = new PDO($db['dsn'], $db['username'], $db['password'], $options);
		} catch (PDOException $e) {
		    die('数据库连接失败:' . $e->getMessage());
		}
		//self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    //设置异常处理方式
		//self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);   //设置默认关联索引遍历
	}

	/**
	 * sql查询
	 * @param  string $sql       
	 * @param  array|string $bindValue 参数绑定
	 * @return array            
	 */
	public static function query($sql, $bindValue = null)
	{
		self::_init(self::$db);
		//1)使用query
		self::$stmt = self::$pdo->query($sql); //返回一个PDOStatement对象
		//$row = $stmt->fetch(); //从结果集中获取下一行，用于while循环
		$rows = self::$stmt->fetchAll(); //获取所有
		return $rows;

		//2)使用prepare 推荐!
		// $stmt = $pdo->prepare("select * from user where name = ? and age = ? ");
		// $stmt->bindValue(1,'test');
		// $stmt->bindValue(2,22);
		// $stmt->execute();  //执行一条预处理语句 .成功时返回 TRUE, 失败时返回 FALSE 
		// $rows = $stmt->fetchAll();
		// print_r($rows);
	}

	public static function count()
	{
		return self::$stmt->rowCount();
	}

	public static function insert($table, $data)
	{
		self::_init(self::$db);
		$items = [];
		$values = [];
		foreach ($data as $k => $v)
		{
			$v = stripslashes($v);
			$v = addslashes($v);
			$items[] = "`$k`";
			$values[] = "\"$v\"";
		}
		$items = implode(',', $items);
		$values = implode(',', $values);
		$sql = "INSERT IGNORE INTO {$table}  ({$items}) VALUES ({$values})";
		$count  =  self::$pdo->exec($sql); //返回受影响的行数 
		return self::$pdo->lastInsertId();
	}

	public static function update($table, $data, $where = null)
	{
		self::_init(self::$db);

		$items = [];
		foreach ($data as $k => $v)
		{
			$v = stripslashes($v);
			$v = addslashes($v);
			$items[] = "`$k` = \"$v\" ";
		}
		$items = implode(',', $items);
		$sql = "update {$table} set {$items} {$where}";
		$count  =  self::$pdo->exec($sql);
		return $count;
	}

	public static function delete($sql)
	{
		self::_init(self::$db);
		$count  =  self::$pdo->exec($sql);
		return $count;
	}

	// public static function ping()
	// {
	// 	if (!mysqli_ping(self::$conn))
	// 	{
	// 		@mysqli_close(self::$conn);
	// 		self::_init_mysql();
	// 	}
	// }

	//2)使用prepare 推荐!
	/*
	$stmt = $pdo->prepare("insert into user(name,gender,age)values(?,?,?)");
	$stmt->bindValue(1, 'test');
	$stmt->bindValue(2, 2);
	$stmt->bindValue(3, 23);
	$stmt->execute();
	*/

	//3)使用prepare 批量新增
	// $stmt = $pdo->prepare("insert into user(name,gender,age)values(?,?,?)");
	// $stmt->bindParam(1, $name);
	// $stmt->bindParam(2, $gender);
	// $stmt->bindParam(3, $age);

	// $data = array(
	//     array('t1', 1, 22),
	//     array('t2', 2, 23),
	// );

	// foreach ($data as $vo){
	//     list($name, $gender, $age) = $vo;
	//     $stmt->execute();
	// }

}