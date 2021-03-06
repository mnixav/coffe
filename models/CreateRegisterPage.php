<?php
/**
 * Created by PhpStorm.
 * User: MNixA
 * Date: 03.03.2018
 * Time: 12:16
 */

class CreateRegisterPage {

    public static $err, $db;

    function __construct() {
        /*echo mail("", "Дратути", "Вы возможно жоп\nНо наша адиминистрация не уверена
        \nУбедите нас что вы не жоп");*/

        FrontController::$navigation = "/logout/nav.php";
        FrontController::$content = "/regist/content.php";
        FrontController::$stylesheet = "main.css";
        FrontController::$script = "/regist/script.php";

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            self::$db = DbConnect::getCoffee();
            $this->register();
        }

        require_once "/views/index.php";
    }

    function register() {
        $login = mb_strtolower(trim($_POST['login']));
        $email = mb_strtolower(trim($_POST['email']));
        $password = $_POST['password'];
        $password2 = $_POST['password2'];

        if ($this->checkPasswords($password, $password2)) {
            if ($this->checkLogin($login)) {
                if ($this->checkEmail($email)) {
                    $this->addUserToDb($login, $email, $password);
                }
            }
        }
    }

    function checkPasswords($pass1, $pass2) {
        if ($pass1 != $pass2) {
            self::$err = "Пароли не совпадают";
        } else {
            return true;
        }
    }

    function checkLogin($login) {
        $sql = "SELECT login FROM users WHERE login = '$login'";
        $result = self::$db->query($sql) or die("ошибка запроса");
        if(is_array($result->fetch(PDO::FETCH_ASSOC))){
            self::$err = "Такой логин уже занят";
        } else {
            return true;
        }
    }

    function checkEmail($email) {
        $sql = "SELECT email FROM users WHERE email = '$email'";
        $result = self::$db->query($sql) or die("ошибка запроса");
        if(is_array($result->fetch(PDO::FETCH_ASSOC))){
            self::$err = "Этот email адрес уже зарегистрирован";
        } else {
            return true;
        }
    }

    function addUserToDb($login, $email, $password) {
        $sql = "INSERT INTO users (login, email, password) 
                          VALUES ('$login', '$email', '$password')";
        self::$db->exec($sql);
        $_SESSION['login'] = $login;
        FrontController::$navigation = "/mainSite/nav.php";
        FrontController::$content = "/regist/done.php";
    }
}