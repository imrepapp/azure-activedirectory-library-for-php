<?php

/**
 * Copyright (c) 2016 Micorosft Corporation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Aashay Zajriya <aashay@introp.net>
 * @license MIT
 * @copyright (C) 2016 onwards Microsoft Corporation (http://microsoft.com/)
 */
namespace microsoft\adalphp\samples;
require(__DIR__ . '/../../vendor/autoload.php');

class sqlite {

    private static $sqlite;
    /**
     * Constructor.
     *
     * @param string $sqlitedb The path to the sqlite database file.
     */
    public function __construct($sqlitedb) {
        if (!file_exists($sqlitedb) || !is_readable($sqlitedb)) {
            throw new \Exception('Cannot read database.');
        }
        $this->dbfile = $sqlitedb;
        $this->db = new \PDO('sqlite:' . $this->dbfile);
    }
    
    public static function get_db($sqlitedb) {
        if (self::$sqlite == null) {
            self::$sqlite = new sqlite($sqlitedb);
        }
        return self::$sqlite;;
    }

    public function create_tables() {
        
        $r = $this->db->query('CREATE TABLE IF NOT EXISTS users (
                   id INTEGER PRIMARY KEY AUTOINCREMENT,
                   firstname VARCHAR(30),
                   lastname VARCHAR(30),
                   password VARCHAR(30),
                   email VARCHAR(50));');
        
        $this->db->query('CREATE TABLE IF NOT EXISTS ad_users (
                   id INTEGER PRIMARY KEY AUTOINCREMENT,
                   user_id INTEGER,
                   token TEXT NULL,
                   token_type VARCHAR(20) NULL,
                   o365_email VARCHAR(255) NULL);');
    }
    
    public function insert_user($firstname, $lastname, $email, $password) {
        $sql = 'INSERT INTO users(firstname, lastname, password, email) values (:firstname, :lastname, :password, :email)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':firstname', $firstname, \PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $lastname, \PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, \PDO::PARAM_STR);
        $stmt->bindParam(':email', strtolower($email), \PDO::PARAM_STR);
        $stmt->execute();

        $errinfo = $stmt->errorInfo();
        if ($errinfo[0] !== '00000') {
            throw new \Exception($errinfo[2]);
        }

        return $this->db->lastInsertId();;
    }

    public function verify_user($email, $password) {
        $email = strtolower($email);
        $result = $this->db->query("SELECT * FROM users WHERE email = '$email' and password = '$password'")->fetchAll();
        if (empty($result)){
            return FALSE;
        }
        return $result[0];
    }
    
    public function is_user_exist($email) {
        $email = strtolower($email);
        $user= array();
        $result = $this->db->query("SELECT * FROM users WHERE email = '$email'");
        if($result){
            $user = $result->fetchAll();
        }
        if (empty($user)){
            return FALSE;
        }
        return $user[0];
    }
    
    public function get_ad_user($id) {
        $result = $this->db->query( "SELECT * FROM ad_users WHERE user_id = " . $id)->fetchAll();
        if (empty($result)){
            return FALSE;
        }
        return $result[0];
    }
    
    public function insert_ad_user($token, $user_id, $o365_email, $token_type = 'access_token') {
        
        $sql = 'INSERT INTO ad_users(user_id, token, token_type, o365_email) values (:user_id, :token, :token_type, :o365_email)';
        $stmt = $this->db->prepare($sql);
        
        if ($token_type != 'id_token') {
            $token = @json_encode($token, true);
        }
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->bindParam(':token', $token, \PDO::PARAM_STR);
        $stmt->bindParam(':token_type', $token_type, \PDO::PARAM_STR);
        $stmt->bindParam(':o365_email', strtolower($o365_email), \PDO::PARAM_STR);
        $stmt->execute();
        
        $errinfo = $stmt->errorInfo();
        if ($errinfo[0] !== '00000') {
            throw new \Exception($errinfo[2]);
        }
        return $this->db->lastInsertId();
    }
    
    public function unlink_ad_user($userid) {
        return $this->db->query("DELETE from ad_users where user_id = " . $userid);
    }
    
    public function get_user($userId){
        $result = $this->db->query("SELECT * FROM users WHERE id = " . $userId)->fetchAll();
        if (empty($result)){
            return FALSE;
        }
        return $result[0];
    }
}
