<?php
require_once('pdo_functions.php');
require_once('vendor/autoload.php');
session_start();
$_SESSION["state"] = "not_loggedIn";
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

// funtion - create MD5 hashcode
$md5Filter = new Twig_SimpleFilter('md5', function($string) {
    return md5($string);
});
$twig->addFilter($md5Filter);

// function - render permissions (snippet)
$id_perm_function = new Twig_SimpleFilter('id_perm_function', function($array) {
    foreach ($array as $permission)
    {
      if ($permission == 1)
          echo "<div class=permission>lesen</div>";
      if ($permission == 2)
          echo "<div class=permission>schreiben</div>";
      if ($permission == 3)
          echo "<div class=permission>drucken</div>";
      if ($permission == 4)
          echo "<div class=permission>Benutzer verwalten</div>";    
    }
});
$twig->addFilter($id_perm_function);

// set the different states = ... ->
// "not_loggegIn", "logged_in", "manage", "createUser", "editUser", "editUserWrong", "dataSaved", "userDeleted"
////////////////////////////////////////////////////////////////////////////////

if (empty($_POST)){
    echo $twig->render('index.html', array(
        'state' => 'not_loggedIn',
    ));
}

if (isset($_POST["login"])){
    get_data_pdo();
    if ($_SESSION["state"] == "loggedIn"){
        echo $twig->render('index.html', array(
            'state' => 'logged_in',
            'SESSION_name' => $_SESSION["name"],
            'SESSION_surname' => $_SESSION["surname"],
            'permission_data' => $id_permission,
            'superUser' => $superUser,
        ));
        echo "state = logged in";
    }
    else{
        echo $twig->render('index.html', array(
            'state' => 'wrongUser',
        ));
    }                 
}
        
if (isset($_POST["back"])){
    unset($_POST);   
    get_data_pdo();
    if ($_SESSION["state"] == "loggedIn"){
        echo $twig->render('index.html', array(
            'state' => 'logged_in',
            'SESSION_name' => $_SESSION["name"],
            'SESSION_surname' => $_SESSION["surname"],
            'permission_data' => $id_permission,
            'superUser' => $superUser,
        ));
        echo "state = logged in";
    }
}

if (isset($_POST["back2"])){
    echo "<div class='frame'>";  
    echo "<h1>Alle Benutzer und deren Gruppen</h1>";  
    echo "<form action=".$_SERVER["PHP_SELF"]." method='post'>";
    show_users_pdo();
    echo $twig->render('index.html', array(
        'state' => 'manage',
    ));
}
                
if (isset($_POST["manageUser"])){
    echo "<div class='frame'>";  
    echo "<h1>Alle Benutzer und deren Gruppen</h1>";  
    echo "<form action=".$_SERVER["PHP_SELF"]." method='post'>";
    show_users_pdo();
    echo $twig->render('index.html', array(
        'state' => 'manage',
    ));
}
   
if (isset($_POST["logout"])){
    echo $twig->render('index.html', array(
        'state' => 'not_loggedIn',
    ));
    session_destroy();  
    $_SESSION = array();
    unset($_POST);
}

if (isset($_POST["createUser"])){
    echo $twig->render('index.html', array(
        'state' => 'createUser',
    ));
}
    
if (isset($_REQUEST["edit"])){
    $submitNumber = array_pop(array_keys($_REQUEST['edit']));
    $_SESSION["submitNumber"] = $submitNumber;
    edit_user();
    $sel = $_SESSION["submitNumber"];
    echo $twig->render('index.html', array(
        'state' => 'editUser',
        'surname' => $name_array[$sel],
        'firstname' => $firstname_array[$sel],
        'username' => $username_array[$sel],
        'passwd' => $passwd_array [$sel],
        'id_group' => $id_group,
    ));
}
    
if (isset($_POST["saveNewUser"])){
    if ($_POST["passwdRepeat"] != $_POST["passwd"]){
        echo $twig->render('index.html', array(
            'state' => 'editUserWrong',
            'surname' => $_POST["surname"],
            'firstname' => $_POST["firstname"],
            'username' => $_POST["user"],
            'passwd' => $_POST["passwd"],
            'id_group' => $_POST["group"],
            'button_check' => true,
        )); 
    }       
    else{
            save_new_user_pdo();   
            echo $twig->render('index.html', array(
                'state' => 'dataSaved',
                'firstname' => $firstname,
                'surname' => $surname,         
            ));
        }
}
    
if (isset($_POST["saveUser"])){
    if ($_POST["passwdRepeat"] != $_POST["passwd"]){
        edit_user();
        $sel = $_SESSION["submitNumber"];
        echo $twig->render('index.html', array(
            'state' => 'editUserWrong',
            'surname' => $name_array[$sel],
            'firstname' => $firstname_array[$sel],
            'username' => $username_array[$sel],
            'passwd' => $passwd_array [$sel],
            'id_group' => $id_group,
        ));
    }        
    else{
            save_user_pdo();
            echo $twig->render('index.html', array(
                'state' => 'dataSaved',
                'firstname' => $firstname,
                'surname' => $surname,
                
            ));
    }
}

if (isset($_POST["deleteUser"])){
    delete_user_pdo();
    echo $twig->render('index.html', array(
        'state' => 'userDeleted',
        'firstname' => $firstname,
        'surname' => $surname,       
    ));
}

