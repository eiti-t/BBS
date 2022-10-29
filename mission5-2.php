<!DOCTYPE html>
<html lang = "ja">
<head>
    <meta charset = "UTF-8">
    <title>Internet Board</title>
</head>
<body>
    <?php
        $name = filter_input(INPUT_POST,"name"); //フォームからのデータ受け取り
        $comment = filter_input(INPUT_POST,"comment");
        $delete = filter_input(INPUT_POST,"delete");
        $edit = filter_input(INPUT_POST,"edit");
        $edit2 = filter_input(INPUT_POST, "editnum");
        $textpass = filter_input(INPUT_POST, "textpass");
        $deletepass = filter_input(INPUT_POST, "deletepass");
        $editpass = filter_input(INPUT_POST, "editpass");
        
        $dsn = 'データベース名'; //テーブルの作成と日時の決定
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
        $sql = "CREATE TABLE IF NOT EXISTS tb_51"
         ."("
         ."id INT AUTO_INCREMENT PRIMARY KEY,"
         ."name char(32),"
         ."comment TEXT,"
         ."date TEXT,"
         ."TEXTpass TEXT"
         .");";
        $stmt = $pdo->query($sql);
        $date = date("Y/m/d H:i:s");
        //下準備完了
        
        
        if((empty($delete) == false) && (empty($deletepass) == false)){ //データレコードの削除
            $sql = 'DELETE FROM tb_51 Where id=:id and TEXTpass=:TEXTpass';
            $stmt= $pdo->prepare($sql);
            $stmt -> bindParam(':id', $delete, PDO::PARAM_INT);
            $stmt -> bindParam(':TEXTpass', $deletepass, PDO::PARAM_STR);
            $stmt -> execute();
        }
        
        
        if((empty($edit) == false) && (empty($editpass) == false)){ //編集モードの前段階（編集対象内容のフォーム表示）
            $sql = 'SELECT * FROM tb_51 Where id=:id and TEXTpass=:TEXTpass';
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':id', $edit, PDO::PARAM_INT);
            $stmt -> bindParam(':TEXTpass', $editpass, PDO::PARAM_STR);
            $stmt -> execute();
            $result = $stmt->fetch();
            $editname = $result['name'];
            $editcomment = $result['comment'];
            $editpostnum = $result['id'];
            $editpassword = $result['TEXTpass'];
        }
        
        
        if((empty($edit2) == false)){ //編集モード本編（編集内容を保存）
            $sql = 'UPDATE tb_51 SET name=:name, comment=:comment,TEXTpass=:TEXTpass Where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt ->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt ->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt ->bindParam(':TEXTpass', $textpass, PDO::PARAM_STR);
            $stmt ->bindParam(':id', $edit2, PDO::PARAM_INT);
            $stmt ->execute();
            
            
        }elseif((empty($name) == false) && (empty($comment) == false)){ //新規投稿
            $sql = $pdo->prepare("INSERT INTO tb_51 (name, comment,date,TEXTpass) VALUES(:name, :comment, :date, :TEXTpass)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':TEXTpass', $textpass, PDO::PARAM_STR);
            $sql -> execute();
        }
        
        ?>
        
        
        <!--フォームの作成-->
        <form aciton = "" method = "post">
        <input type = "text" name = "name" placeholder = "名前" value = "<?php if(empty($editname) == false){echo $editname;}?>">
        <input type = "text" name = "comment" placeholder = "コメント" value = "<?php if(empty($editcomment) == false){echo $editcomment;}?>">
        <input type = "hidden" name = "editnum" value = "<?php if(empty($editpostnum) == false){echo $editpostnum;}?>">
        <input type = "text" name ="textpass" placeholder = "パスワードの設定" value = "<?php if(empty($editpassword) == false){echo $editpassword;}?>">
        <input type = "submit" value = "送信">
        <input type = "number" name = "delete" placeholder = "削除対象番号">
        <input type = "text" name ="deletepass" placeholder = "パスワード入力">
        <input type = "submit" value = "削除">
        <input type = "number" name = "edit" placeholder = "編集対象番号">
        <input type = "text" name ="editpass" placeholder = "パスワード入力">
        <input type = "submit" value = "編集">
    </form>
    
    
    <?php //ブラウザ表示
        echo "パスワードを設定しないと、削除・編集できません<br><br>";
        $sql = 'SELECT * FROM tb_51';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id'].' ';
            echo $row['name'].' ';
            echo $row['comment'].' ';
            echo $row['date'].'<br>';
        echo "<hr>";
        }
    ?>