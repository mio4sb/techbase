
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mission5</title>
    
    <?php 

// DB接続設定
    $dsn='データベース名';
    $user='ユーザー名';
    $password='パスワード';
    //指定したデータベースへ接続
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //テーブル(カラム)作成(m4-2)
    //id,name,comment,date,color,sign,pass
    $sql = "CREATE TABLE IF NOT EXISTS mission5"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date TEXT,"
    . "color TEXT,"
    . "sign TEXT,"//最後以外,を入れ忘れないように
    . "pass TEXT"
    .");";
    
    $sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, date, color, sign, pass) VALUES (:name, :comment, :date, :color, :sign, :pass)");
    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
    $sql -> bindParam(':color', $color, PDO::PARAM_STR);
    $sql -> bindParam(':sign', $sign, PDO::PARAM_STR);
    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
   
    
?>
</head>
<?php
//準備
 
 $error_message =  array ();
    $display= array();
    $h_sign="";
    $nameval="";
    $colorval="";
    $commentval="";
    $submitbutton="投稿";
    $title="新規投稿";
//新規投稿
//送信ボタンが押されサインがない時
if (isset($_POST ["submit"]) && !($_POST ["h_sign"])) {
   $name_i = htmlspecialchars($_POST["name"],ENT_QUOTES);
    $comment_i= htmlspecialchars($_POST["comment"],ENT_QUOTES);
    $color_i= htmlspecialchars($_POST["color"],ENT_QUOTES);
    $pass_i= htmlspecialchars($_POST["pass"],ENT_QUOTES);
    $colorval="$color_i";
    //コメントが空の時
        if ($_POST["comment"]=="" ) {
          $error_message[]= '<font color="Crimson">コメントが入力されていません。<br></font>';
        }
    //名前が空の時
        if ( $_POST ["name"]=="") {
             $error_message[]='<font color="Crimson">名前が入力されていません。<br></font>';
        }
    //パスワードが空の時
        if ( $_POST ["pass"]=="") {
             $error_message[]='<font color="Crimson">パスワードを設定してください。<br></font>';
        }
        
      //エラーがある時
        if (count($error_message)){
            $nameval="$name_i";
            $commentval="$comment_i";
         
      //エラーがない時    
        }else{
           $date=date("Y/m/d H:i:s");
            //入力されたcommentの改行を文字に置換
            $search = ["\r\n", "\r", "\n"];
            $comment= str_replace($search,"<<改行>>", $comment_i);
            $name=$name_i;
            $color=$color_i;
            $sign="posted";
            $pass=$pass_i;
            $sql -> execute();
        }   
    
    
    

//削除機能
//送信ボタンが押された時
}elseif(isset($_POST ["d_submit"])){
     //削除対象番号が空欄の時
    if($_POST ["d_number"]==""){
        $error_message[]= '<font color="Crimson">削除対象番号が入力されていません。<br></font>';
    }
    //パスワードが入力されていない時
    if($_POST ["d_pass"]==""){
        $error_message[]= '<font color="Crimson">パスワードが入力されていません。<br></font>';
    }
    
     //削除対象番号とパスワードが入力された時
    if($_POST ["d_pass"]!="" && $_POST ["d_number"]!=""){
    $d_num = htmlspecialchars($_POST["d_number"],ENT_QUOTES); 
    $d_pass = htmlspecialchars($_POST["d_pass"],ENT_QUOTES);
    //$idを先に指定してからsqlに代入
    $id=$d_num;
    $sql = 'SELECT * FROM mission5 WHERE id=:id';
    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
     $stmt->execute();  // ←SQLを実行する。
    $results = $stmt->fetchAll(); 
    foreach ($results as $row){
        //password,sign抽出
        $post_pass= $row['pass'];
        $post_sign= $row['sign'];
    }
   
        if($post_sign=="deleted"){
            $error_message[]= '<font color="Crimson">削除済みの投稿です。<br></font>';
        //投稿が存在する時、パスワードが一致しない時       
        }elseif($post_pass!=$d_pass){
             $error_message[]= '<font color="Crimson">削除対象番号またはパスワードが違います。<br></font>';
        
        //投稿が存在し、パスワードも一致するとき   
        }else{
        $id = $d_num; //削除する投稿番号
        $name = "";
        $comment = "----削除されたメッセージです----";
        $date = "";
        $color= "#999999";
        $sign = "deleted";
        $pass= "";
        //変更したい名前、変更したいコメントは自分で決めること
        $sql = 'UPDATE mission5 SET name=:name,comment=:comment,date=:date,color=:color,sign=:sign, pass=:pass WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':sign', $sign, PDO::PARAM_STR);
        $stmt->bindParam(':color', $color, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->execute();
            
        $error_message[]= '<font color="teal">削除しました。<br></font>';
        }
    
        
    }
    

//編集機能
//入力フォームに再表示
}elseif(isset($_POST ["e_submit"])){
    //編集対象番号が空で送信された時
    if($_POST ["e_number"]==""){
        $error_message[]= '<font color="Crimson">編集対象番号が入力されていません。<br></font>';
    }
    //パスワードが入力されていない時
    if($_POST ["e_pass"]==""){
        $error_message[]= '<font color="Crimson">パスワードが入力されていません。<br></font>';
    }
    
    //パスワードと編集対象番号が入力された時
    if($_POST ["e_pass"]!="" && $_POST ["e_number"]!=""){
    $e_num = htmlspecialchars($_POST["e_number"],ENT_QUOTES); 
    $e_pass = htmlspecialchars($_POST["e_pass"],ENT_QUOTES);
    //$idを先に指定してからsqlに代入
    $id=$e_num;
    $sql = 'SELECT * FROM mission5 WHERE id=:id';
    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
    $stmt->execute();  // ←SQLを実行する。
    $results = $stmt->fetchAll(); 
        foreach ($results as $row){
            //password,sign抽出
            $post_pass= $row['pass'];
            $post_sign= $row['sign'];
            $edit_name= $row['name'];
            $edit_comment= $row['comment'];
            $edit_color= $row['color'];
            $edit_id= $row['id'];
        }
   
        if($post_sign=="deleted"){
            $error_message[]= '<font color="Crimson">削除済みの投稿です。<br></font>';
        //投稿が存在する時、パスワードが一致しない時       
        }elseif($post_pass!=$e_pass){
             $error_message[]= '<font color="Crimson">編集対象番号またはパスワードが違います。<br></font>';
        
        //投稿が存在し、パスワードも一致するとき   
        }else{
            
            $h_sign=$edit_id;
            $nameval=$edit_name;
            $colorval=$edit_color;
            //改行に直す
            $commentval= str_replace("<<改行>>","\n", $edit_comment);
            $submitbutton="再投稿";
            $title="編集";
        }
    }


}    

//再投稿
if(isset($_POST ["submit"]) && ($_POST ["h_sign"])){
    $name_i = htmlspecialchars($_POST["name"],ENT_QUOTES);
    $comment_e= htmlspecialchars($_POST["comment"],ENT_QUOTES);
    //改行変換
    $search = ["\r\n", "\r", "\n"];
    $comment_i= str_replace($search,"<<改行>>", $comment_e);
    $color_i= htmlspecialchars($_POST["color"],ENT_QUOTES);
    $pass_i= htmlspecialchars($_POST["pass"],ENT_QUOTES);
    $colorval="$color_i";
    $h_num=$_POST ["h_sign"];
    //コメントが空の時
        if ($_POST["comment"]=="" ) {
          $error_message[]= '<font color="Crimson">コメントが入力されていません。<br></font>';
        }
    //名前が空の時
        if ( $_POST ["name"]=="") {
             $error_message[]='<font color="Crimson">名前が入力されていません。<br></font>';
        }
    //パスワードが空の時
        if ( $_POST ["pass"]=="") {
             $error_message[]='<font color="Crimson">パスワードを設定してください。<br></font>';
        }
        
      //エラーがある時
        if (count($error_message)){
            $h_sign=$h_num;
            $nameval="$name_i";
            $commentval="$comment_i";
            $submitbutton="再投稿";
            $title="編集";
         
      //エラーがない時    
        }else{
            //データベースを編集
            $id =$_POST["h_sign"]; //変更する投稿番号
            $name = $name_i;
            $comment = $comment_i;
            $color = $color_i;
            $pass = $pass_i;
            $sign="posted";
            $date=date("Y/m/d H:i:s");
            $sql = 'UPDATE mission5 SET name=:name,comment=:comment,date=:date,color=:color,pass=:pass,sign=:sign WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':color', $color, PDO::PARAM_STR);
            $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':sign', $sign, PDO::PARAM_STR);
            $stmt->execute();  
             //'sign'を空に戻す
            $h_sign="";
            $nameval="";
            $commentval="";
            $submitbutton="投稿";
            $error_message[]= '<font color="teal">再投稿しました。<br></font>';
                
        }
        
  
        
}




//投稿(掲示板)の表示準備
$sql = 'SELECT * FROM mission5';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    //逆順にする
    $results= array_reverse($results);
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        $post_num= $row['id'];
        $post_name=$row['name'];
        $post_date=$row['date'];
        $post_comment=$row['comment'];
        $post_color=$row['color'];
        $commentDisplay = str_replace("<<改行>>","<br>", $post_comment);
        $display[]="<FONT COLOR=\"$post_color \">$post_num: $post_name  $post_date <br>$commentDisplay <br><hr size='1'></FONT>";
    }   
?>
<body bgcolor="#FAF0E6">
       <!--入力フォーム-->
       <form method="post">
        <?php echo $title;?><br>
        <input type="text" name="name" placeholder="名前"  value="<?php echo $nameval;?>"　 >
        <input type="color" name="color" value="<?php echo $colorval;?>" ><br>
        <textarea placeholder="コメント" name="comment" rows="5" cols="40"><?php echo $commentval;?></textarea>
        <input type="hidden" name= "h_sign" value="<?php echo $h_sign;?>"  ><br>
        <input type="text" name="pass" placeholder="パスワードを設定してください" size="26"　>
        <input type="submit" name="submit" value="<?php echo $submitbutton;?>"><br><br>
        削除<br>
        <input type="number" name="d_number" placeholder="削除対象番号"><br>
        <input type="text" name="d_pass" placeholder="パスワード" >
        <input type="submit" name="d_submit" value="削除"><br><br>
        編集<br><input type="number" name="e_number" placeholder="編集対象番号"><br>
         <input type="text" name="e_pass" placeholder="パスワード" >
        <input type="submit" name="e_submit"value="編集">
        
        </form>
<?php
//エラーメッセージ等表示
    foreach ($error_message as $message){
        print ($message);
     }
            
    echo "<hr size='1'>";
        
    //投稿表示
    foreach ($display as $eachDisplay){
        print ($eachDisplay);
      
    }
   
?>
</body>
</html>