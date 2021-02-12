<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>mission5-1</title>
  </head>
  <body>

<?php
  $dsn = 'データベース名';
	$user = 'ユーザ名';
	$pwd = 'パスワード';
	$pdo = new PDO($dsn, $user, $pwd, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //投稿機能

    if (isset($_POST['submit']) && !empty($_POST['name']) && !empty($_POST['comment'])) {

      $id = null;
      $name = $_POST['name'];
      $comment = $_POST['comment'];
      $password = $_POST['password'];
      $posted = date("Y/m/d H:i:s");

    // editNoがないときは新規投稿、ある場合は編集 ***ここで判断
    if (empty($_POST['editNO'])) {
      // 以下、新規投稿機能

      //データベースへ書き込み
      $sql = $pdo -> prepare("INSERT INTO bulletinBoard (name, comment, password, posted) VALUES (:name, :comment, :password, :posted)");
      $sql -> bindParam(':name', $name, PDO::PARAM_STR);
      $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
      $sql -> bindParam(':password', $password, PDO::PARAM_STR);
      $sql -> bindParam(':posted', $posted, PDO::PARAM_STR);
      $sql -> execute();

    }
    else {
      // 以下編集機能
      //変更したい名前、変更したいコメントは自分で決めること
          $id = $_POST['editNO'];
  	      $sql = 'UPDATE bulletinBoard SET name=:name,comment=:comment WHERE id=:id';
  	      $stmt = $pdo->prepare($sql);
  	      $stmt->bindParam(':name', $name, PDO::PARAM_STR);
  	      $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
  	      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  	      $stmt->execute();
    }
  }


      //削除機能

      //削除フォームの送信の有無で処理を分岐
      if (!empty($_POST['dnum'])) {

          $id = $_POST['dnum'];
          $sql = 'SELECT * FROM bulletinBoard WHERE id=:id';
          $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
          $stmt->bindParam(':id', $id, PDO::PARAM_INT);
          $stmt->execute();                             // ←SQLを実行する。
          $results = $stmt->fetchAll();
	        foreach ($results as $row){
            $dpass = $row['password'];
          }
          if($_POST['dpass'] == $dpass){
	        $sql = 'delete from bulletinBoard where id=:id';
	        $stmt = $pdo->prepare($sql);
	        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	        $stmt->execute();
        }
      }

      //編集選択機能

      //編集フォームの送信の有無で処理を分岐
      if (!empty($_POST['edit'])) {

          //入力データの受け取りを変数に代入
          $id = $_POST['edit'];
          $sql = 'SELECT * FROM bulletinBoard WHERE id=:id';
          $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
          $stmt->bindParam(':id', $id, PDO::PARAM_INT);
          $stmt->execute();                             // ←SQLを実行する。
          $results = $stmt->fetchAll();
	        foreach ($results as $row){
            $enumber = $row['id'];
            $ename = $row['name'];
            $ecomment = $row['comment'];
            $epass = $row['password'];
          }
          if($_POST['epass'] == $epass){
            $editnumber = $row['id'];
            $editname = $row['name'];
            $editcomment = $row['comment'];
            $editpass = $row['password'];
          }

      }


?>

    <form action="mission5-1.php" method="post">
      <input type="text" name="name" placeholder="名前" value="<?php if(isset($editname)) {echo $editname;} ?>">
      <input type="text" name="comment" placeholder="コメント" value="<?php if(isset($editcomment)) {echo $editcomment;} ?>">
      <input type="text" name="password" placeholder="パスワード" value="<?php if(isset($editpass)){echo $editpass;}?>">
      <input type="hidden" name="editNO" value="<?php if(isset($editnumber)) {echo $editnumber;} ?>">
      <input type="submit" name="submit" value="送信"><br>

      <input type="text" name="dnum" placeholder="削除対象番号">
      <input type="text" name="dpass" placeholder="パスワード">
      <input type="submit" name="delete" value="削除"><br>

      <input type="text" name="edit" placeholder="編集対象番号">
      <input type="text" name="epass" placeholder="パスワード">
      <input type="submit" value="編集">
    </form>

    <h2>投稿内容</h2>

<?php

  $sql = 'SELECT * FROM bulletinBoard';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].'<>';
		echo $row['name'].'<>';
		echo $row['comment'].'<>';
        echo $row['posted'].'<br>';
	  echo "<hr>";
	}

?>
  </body>
</html>
