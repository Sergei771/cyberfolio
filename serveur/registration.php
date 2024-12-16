<?php
    include("index.php");

?>
<?php
    include("index.html");
?>

<?php
// modifier quand la table sql sera créé

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
    
        if(empty($username)){
            echo"Please enter a username";
        }
        elseif(empty($password)){
            echo"Please enter a password";
        }
        else{
            $hash = password_hash($password, PASSWORD_DEFAULT); 
            $sql = "INSERT INTO users (user, password)
                    VALUES ('$username', '$hash')";
            
            try{
                mysqli_query($conn, $sql);
                echo"You are now registered!";
            }
            catch(mysqli_sql_exception){
                echo"That username is taken";
            }
        }
    }

    mysqli_close($conn);
?>
