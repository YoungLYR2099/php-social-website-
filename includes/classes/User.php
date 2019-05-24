<?php
class User {
    private $con;
    private $user;//associate array map all key(like username,id) to all info


    public function __construct($con, $user)
    {
        $this->con = $con;
        $user_details_query = mysqli_query($con,"SELECT * FROM users WHERE username='$user'");
        $this->user = mysqli_fetch_array($user_details_query);
    }
    public function getUsername(){
        return $this->user['username'];
    }

    public function getNumberOfFriendRequests() {
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$username'");
        return mysqli_num_rows($query);
    }

    public function getNumPosts() {
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "SELECT num_posts FROM users WHERE username='$username'");
        $row = mysqli_fetch_array($query);
        return $row['num_posts'];
    }

    public function getFirstAndLastName(){
        $username = $this->user['username'];
        $query = mysqli_query($this->con,"SELECT first_name,last_name from users WHERE  username='$username'" );
        $row = mysqli_fetch_array($query);
        return $row['first_name']." ".$row['last_name'];
    }

    public function getProfilePic(){
        $username = $this->user['username'];
        $query = mysqli_query($this->con,"SELECT profile_pic from users WHERE  username='$username'" );
        $row = mysqli_fetch_array($query);
        return $row['profile_pic'];
    }

    public function getFriendArray(){
        return $this->user['friend_array'];

    }

    public function isClosed(){
        $usrname = $this->user['username'];
        $query = mysqli_query($this->con, "SELECT user_closed FROM users WHERE username='$usrname'");
        $row = mysqli_fetch_array($query);

        if ($row['user_closed']=='yes')
            return true;
        else
            return false;

    }

    public function isFriend($username_to_check){
        $usernameComma = "," . $username_to_check . ",";
        //strstr(haystack,needle) self or in friend_array
        if (strstr($this->user['friend_array'] ,$usernameComma) || $username_to_check == $this->user['username']){
            return true;
        }
        else{
            return false;
        }


    }


    public function didReceiveRequest($user_from){
        $user_to = $this->user['username'];
        $check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
        if(mysqli_num_rows($check_request_query) > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function didSendRequest($user_to){
        $user_from = $this->user['username'];
        $check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
        if(mysqli_num_rows($check_request_query) > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function removeFriend($user_to_remove){
        $logged_in_user = $this->user['username'];
        $query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_remove'");
        $row = mysqli_fetch_array($query);
        $friend_array_username = $row['friend_array'];
        //delete friends in current logged user
        $new_friend_array = str_replace($user_to_remove . ',','',$this->user['friend_array']);
        $remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$logged_in_user' ");
        //delete friends in user_to_remove
        $new_friend_array = str_replace($logged_in_user . ',','',$friend_array_username);
        $remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$user_to_remove' ");


    }

    public function sendRequest($user_to){
        $user_from = $this->user['username'];
        $query = mysqli_query($this->con, "INSERT INTO friend_requests VALUES ('','$user_to','$user_from')");
    }

    public function getMutualFriends($user_to_check){
        //find common friend in two users' friendArray.
        $mutualFriends = 0;
        $user_array = $this->user['friend_array'];
        $user_array_explode = explode(",", $user_array);

        $query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_check'");
        $row = mysqli_fetch_array($query);
        $user_to_check_array = $row['friend_array'];
        $user_to_check_array_explode = explode(",", $user_to_check_array);

        $user_to_check_map = array();//use it as hashset
        foreach ($user_to_check_array_explode as $i) {
            $user_to_check_map[$i] = 1;
        }
        foreach($user_array_explode as $i){
            if($i != "" && isset($user_to_check_map[$i])){
                $mutualFriends++;
            }
        }
        return $mutualFriends;
    }




}


?>
