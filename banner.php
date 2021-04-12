<?php

    //database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $db = 'paruyrtest';
    $conn = new mysqli($servername, $username, $password, $db);

    if ($conn->connect_error)
    {
        die("Connection failed: " . $conn->connect_error);
    }

    //Getting Ip address
    $ip = $_SERVER['REMOTE_ADDR'];

    //Getting user agent
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    //Getting page url
    $page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    //Preparing sql query for getting filtered data from db
    /**
     * No need to real escape the string as we are only using php built in values,
     * so no hacking chance is available there
     */
    $strQuery = "SELECT * FROM info WHERE ip_address = '" . $ip . "' AND user_agent='" . $user_agent . "' AND page_url='" . $page_url . "'";

    //Execute query
    $result = mysqli_query($conn, $strQuery) or die(mysqli_error());


    //Collecting data from db
    $infos = array();
    while ($row = mysqli_fetch_assoc($result))
    {
        $infos[] = $row;
    }
    $created_status = false;

    //Getting current date time and prepare for mysql insertion
    $time = date('Y-m-d H:i:s', time());

    //check if data exists in db
    if (empty($infos))
    {
        //keep user info in DB
        $view_count = 1;

        //no need to escape as its server lues
        $sql = "INSERT INTO info (ip_address, user_agent, view_date, page_url, view_count)
        VALUES ('" . $ip . "', '" . $user_agent . "', '" . $time . "', '" . $page_url . "', '" . $view_count . "')";

        //if inserted to db
        if (mysqli_query($conn, $sql))
        {
            $created_status = true;
        }
        else
        {
            //catch error and exit code
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            die;
        }
    }
    else
    {
        //in case it needs to be update the existsing row in db

        //getting first element of the array (no need to check there as its checked before with empty function)
        $current_info = $infos[0];
        
        //incrementing view count number
        $count = $current_info['view_count'] + 1;

        //preparing sql query for updating the existing row in db
        $sql = "UPDATE info SET view_count=" . $count . ", view_date='" . $time . "' WHERE id='" . $current_info['id'] . "'";

        //executing the query
        if ($conn->query($sql) === true)
        {
            $created_status = true;
        }
        else
        {
            //catching the error and exit
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            die;
        }

    }
    
    //closing mysql connection
    mysqli_close($conn);

    //check if db work has been done
    if ($created_status)
    {
        // open the file in a binary mode
        $name = './test.png';
        $fp = fopen($name, 'rb');

        // send the right headers
        header("Content-Type: image/png");
        header("Content-Length: " . filesize($name));

        // dump the picture and stop the script
        fpassthru($fp);
        exit;
    }

?>
