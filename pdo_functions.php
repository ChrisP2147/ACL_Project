<?php
require_once('conn.inc.php');
//$state = "notLoggedIn";
$superUser = false;
$id = $name = $firstname = $id_group = $id_user = $id_group = "";
$id_permission = $id_array = $name_array = $firstname_array = $username_array = $passwd_array = $id_group_array = $id_user_array = array();
$userCount = $submitNumber = 0;
// Connect to DB /////////////////////////////////////////////
function connect_DB_pdo()
{
    global $host, $dbname, $user, $password;
    $dsn = 'mysql:host='. $host . ';dbname=' . $dbname;
    $pdo = new PDO($dsn, $user, $password);
    return $pdo;
}

// get data from DB (USER LOGIN) /////////////////////////////
function get_data_pdo()
{   
    global $state, $id, $id_group, $name, $firstname, $id_permission, $id_user, $superUser;
    $pdo = connect_DB_pdo();

    if ($_POST["user"] != "" && $_POST["passwd"] != ""){
        $_SESSION["user"] = $_POST["user"];
        $_SESSION["passwd"] = $_POST["passwd"]; 
    }

    $sql = 'SELECT * FROM benutzer WHERE benutzername = ? && passwort = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION["user"], $_SESSION["passwd"]]);
    $row_count = $stmt->rowCount();

    if ($row_count > 0){
        $_SESSION["state"] = "loggedIn";
        //$state = "loggedIn";

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($results as $res)
        {
            $id = $res['id'];
            $name = $res['name'];
            $firstname = $res['vorname'];
        }
        $_SESSION["name"] = $firstname;
        $_SESSION["surname"] = $name;  
    }
    else{
        $_SESSION["state"] = "wrongUser";
    }

    $sql = 'SELECT * from rel_benutzer_gruppe where id_benutzer = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $row_count = $stmt->rowCount();

    if ($row_count > 0){
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($results as $res)
        {
            $id_group = $res['id_gruppen'];
            $id_user = $res['id_benutzer'];
        }
    }
    else{
        echo "no data found in rel_benutzer_gruppe";
    }

    $sql = 'SELECT * from rel_gruppe_recht where id_gruppen = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_group]);
    $row_count = $stmt->rowCount();

    if ($row_count > 0){
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($results as $res)
        {
            $id_permission[] = $res['id_rechte'];
        }

        if (count($id_permission) > 3){
            if ($id_permission[3] == 4){
                $superUser = true;
            }             
        } 
    }
    else{
        echo "no data found in rel_gruppe_recht";
    }
    return $id_permission;
    $pdo = null;             
}

// show all users /////////////////////////////////////////////
function show_users_pdo()
{
    global $id, $id_group, $id_group_array, $name, $firstname, $id_array, $id_permission, $id_user;
    global $id_user_array, $firstname_array, $name_array;
    load_user_pdo();

    $idCount = count($id_array);
    $idUserCount = count($id_user_array);
    $group = "";

    for ($i=0; $i<$idCount; $i++)
        {       
          for ($j=0; $j<$idUserCount; $j++)
              {
                if ($id_array[$i] == $id_user_array[$j]){
                    $groupID = $id_group_array[$j]; 
                }                                   
              }
              if ($groupID == 1){
                  $group = "Geschäftsleitung";
              }                
              if ($groupID == 2){
                  $group = "Mitarbeiter";
              }
              if ($groupID == 3){
                  $group = "Besucher";
              }
              if ($groupID == 4){
                  $group = "Praktikanten";
              }                
              if ($groupID == 5){
                  $group = "Admin";
              }
                                                                 
          echo "<div class=oneUser>";         
          echo "<div class=userDetails>".$firstname_array[$i]."&nbsp"."&nbsp".$name_array[$i]."</div>";
          echo "<div class=groupName>".$group."</div>";
          echo "<input type='submit' class='btnEdit' name='edit[$i]' value='bearbeiten' />";
          echo "</div>";                
        }
    $pdo = null; 
}

// load all user data /////////////////////////////////////////////
function load_user_pdo()
{
   global $id, $id_group, $id_group_array, $name, $firstname, $id_array, $id_permission, $id_user;
   global $id_user_array, $firstname_array, $name_array;
    $pdo = connect_DB_pdo();

    $sql = 'SELECT * from benutzer';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $row_count = $stmt->rowCount();

    if ($row_count > 0){
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($results as $res)
        {
            $id_array[] = $res['id'];
            $name_array[] = $res['name'];
            $firstname_array[] = $res['vorname'];
            $username_array[] = $res['benutzername'];
            $passwd_array [] = $res['passwort']; 
        }
        $_SESSION["name"] = $firstname;
        $_SESSION["surname"] = $name;    
    }
    else{
        echo "no data found in benutzer";
    }

    $sql = 'SELECT * from rel_benutzer_gruppe';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $row_count = $stmt->rowCount();

    if ($row_count > 0){
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($results as $res)
        {
            $id_group_array[] = $res['id_gruppen'];
            $id_user_array[] = $res['id_benutzer'];
        }
    }
    else{
        echo "no data found in rel_benutzer_gruppe";
    }

    $sql = 'SELECT * from rel_gruppe_recht';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $row_count = $stmt->rowCount();

    if ($row_count > 0){
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($results as $res)
        {
            $id_rechte_array[] = $res['id_rechte'];
        }
    }
    else{
        echo "no data found in rel_gruppe_recht";
    }
}

// translate selected user in new FORM template //////////////// 
function edit_user()
{
    global $id_group, $name, $firstname, $id_permission, $id_user, $submitNumber;
    global $id_array, $name_array, $firstname_array, $username_array, $passwd_array;
    $pdo = connect_DB_pdo();

    $sql = 'SELECT * from benutzer';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $row_count = $stmt->rowCount();

    if ($row_count > 0){
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($results as $res)
        {
            $id_array[] = $res['id'];
            $name_array[] = $res['name'];
            $firstname_array[] = $res['vorname'];
            $username_array[] = $res['benutzername'];
            $passwd_array[] = $res['passwort']; 
        }
        $_SESSION["name"] = $firstname;
        $_SESSION["surname"] = $name;    
    }
    else{
        echo "no data found in benutzer";
    }   
     
    $counter = $id_array[$_SESSION["submitNumber"]];
         
    $sql = 'SELECT * from rel_benutzer_gruppe where id_benutzer = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$counter]);
    
    if ($row_count > 0){
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach($results as $res)
      {
        $id_group = $res['id_gruppen'];
        $id_user = $res['id_benutzer']; 
      }  
  }
  else{
      echo "no data found in rel_benutzer_gruppe";
  }  
$pdo = null;   
}

// save new user ///////////////////////////////////////////////////////
function save_new_user_pdo()
{
    global $username, $passwd, $surname, $firstname, $id_group;

    $username = $_POST["user"];
    $passwd = $_POST["passwd"];
    $surname = $_POST["surname"];
    $firstname = $_POST["firstname"];
    $id_group = $_POST["group"];
    
    $pdo = connect_DB_pdo();

    $sql = 'INSERT INTO benutzer(benutzername, passwort, name, vorname) values(?, ?, ?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $passwd, $surname, $firstname]);

    $sql = 'SELECT id FROM benutzer WHERE name = ? && vorname = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$surname, $firstname]);
    $userID = $stmt->fetch(PDO::FETCH_OBJ);


    $sql = 'INSERT INTO rel_benutzer_gruppe(id_gruppen, id_benutzer) VALUES(?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_group, $userID->id]);

    $pdo = null;
}

// save changes on loaded user ///////////////////////////////////////////
function save_user_pdo()
{
    global $id_array, $username, $passwd, $surname, $firstname, $id_group;

    $username = $_POST["user"];
    $passwd = $_POST["passwd"];
    $surname = $_POST["surname"];
    $firstname = $_POST["firstname"];
    $id_group = $_POST["group"];
    
    $pdo = connect_DB_pdo();

    $sql = 'SELECT * from benutzer';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $row_count = $stmt->rowCount();

    if ($row_count > 0){
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($results as $res)
        {
            $id_array[] = $res['id']; 
        }  
    }
    else{
        echo "no data found in benutzer";
    }

    $id = $id_array[$_SESSION["submitNumber"]];

    $sql = 'UPDATE benutzer SET benutzername = ?, passwort = ?, name = ?, vorname = ? WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $passwd, $surname, $firstname, $id]);
      
    $sql = 'UPDATE rel_benutzer_gruppe SET id_gruppen = ? WHERE id_benutzer = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_group, $id]);

    $pdo = null;
}

// delete one user /////////////////////////////////////////////////////
function delete_user_pdo()
{
    global $surname, $firstname;

    $surname = $_POST["surname"];
    $firstname = $_POST["firstname"];

    $pdo = connect_DB_pdo();

    $sql = 'SELECT id FROM benutzer WHERE name = ? && vorname = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$surname, $firstname]);
    $userID = $stmt->fetch(PDO::FETCH_OBJ);

    $sql = 'DELETE from rel_benutzer_gruppe Where id_benutzer = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userID->id]);

    $sql = 'DELETE from benutzer Where id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userID->id]);

    $pdo = null;
}
