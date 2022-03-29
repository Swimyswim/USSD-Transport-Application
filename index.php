<?php
//connect the database
include_once 'fypdb.php';
require 'vendor/autoload.php';


//Reads the variable sent via POST from the AT gateway
$sessionId = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text = $_POST["text"];
$date = date("Y-m-d H-i-s");


if (isset($text)){
    $level = explode("*", $text);
    $step = explode("*", $text);
    $stage = explode("*", $text);
    $level2 = explode("*", $text);
    $stage1 = explode("*", $text);
    $part2 = explode("*", $text);

    if($text == ""){
        //This is the first request, CON signifies that there are other steps after this one
        $response = "CON What would you like to check \n";
        $response .= "1. Request a ride \n";
        $response .= "2. My Account \n";
        $response .= "3. Register \n";
        $response .= "4. Cancel Ride \n";
        $response .= "5. End Trip \n";
        $response .= "6. I am a Driver \n";
        $response .= "7. Trip Details \n";
        $response .= "8. Request Specific Driver";


    
    }
    else if($text == "1"){
        $sql = "SELECT * FROM rider WHERE riderphonenumber = '$phoneNumber'";
        $result = mysqli_query($conn, $sql);
        //A check is run here to verify whether the user associated with the particular phone number has already created an account

    
            if($row = mysqli_fetch_assoc($result)){
                $sql1 = "SELECT * FROM riderlocation WHERE riderphonenumber = '$phoneNumber'";
                $result1 = mysqli_query($conn, $sql1);
        
                if($row = mysqli_fetch_assoc($result1)){
                    $response ="END you have already requested a ride, please cancel that one first";
                }
                else{
                    $response = "CON Select Your Location \n";
                    $response .= "1. Jean Nelson Hall \n";
                    $response .= "2. Limann \n";
                    $response .= "3. Night Market \n";
                    $response .= "4. Sarbah Hall \n";
                    $response .= "5. Commonwealth Hall \n";
                    $response .= "6. Computer Science Department \n";
                    $response .= "7. Business School";
                }

            }
            else{
                $response = "END You do not have an account";
            }
      
        
    }
    else if(isset($level[1]) && $level[1]!="" && !isset($level[2]) && $level[0]=="1"){
        $response= "CON Your Location is ".$level[1].", select your destination \n";
        $response .= "1. Jean Nelson Hall \n";
        $response .= "2. Limann \n";
        $response .= "3. Night Market \n";
        $response .= "4. Sarbah Hall \n";
        $response .= "5. Commonwealth Hall \n";
        $response .= "6. Computer Science Department \n";
        $response .= "7. Business School";
        //Your location is shown and a prompt is received for you to enter your destination
        
    }
    else if(isset($level[2]) && $level[2]!="" && !isset($level[3]) && $level[0]=="1"){
        $response = "CON Enter Your prefered drivers code. Please Enter 0";
        
    }

    else if(isset($level[3]) && $level[3] == "0" && !isset($level[4]) && $level[0]=="1"){
        $response = "CON Confirm your ride \n PLEASE NOTE Every stop carries a charge of GHC 5.00. \n";
        $response .= "99. Confirm";

    }     
    else if(isset($level[4]) && $level[4] =="99" && !isset($level[5]) && $level[0]=="1"){
        $data=array(
        "phonenumber"=>$phoneNumber,
        "location" =>$level[1],
        "destination" => $level[2],
        );

        $sql = "INSERT INTO riderlocation (riderlocation, riderdestination, riderphonenumber, prefereddriver, requestdate) VALUES ('$level[1]', '$level[2]',  '$phoneNumber', '$level[3]', CURRENT_TIMESTAMP); ";
        mysqli_query($conn, $sql);
        //The data entered by the customer is now saved to the database
                
        $response="END Thank you for ordering \n You choose your location as option ".$level[1]." and your destination as option ".$level[2].  "\n Your Driver will contact you shortly, You should also receive a text message containing your drivers contact information. Else select option 7"; 
        
    }

    else if($text == "3"){
        $sql = "SELECT * FROM rider WHERE riderphonenumber = '$phoneNumber'";
        $result = mysqli_query($conn, $sql);
        //A check is run here to verify whether the user associated with the particular phone number has already created an account

            if($row = mysqli_fetch_assoc($result)){
                $response = "END You already have an account";
            }
            else {
                $response = "CON Enter your first name";

            }
      
        
    }
    else if(isset($step[1]) && $step[1]!="" && !isset($step[2]) && $step[0]=="3"){
        $response= "CON Your first name is ".$step[1].", enter your lastname";
            
    }
    else if(isset($step[2]) && $step[2]!="" && !isset($step[3]) && $step[0]=="3"){
        $response = "CON Choose ID type \n";
        $response .= "1. NHIS \n";
        $response .= "2. Voter ID \n";
        $response .= "3. Passport \n";
        $response .= "4. Drivers License ";

    }       
    else if(isset($step[3]) && $step[3]!="1 OR 2 OR 3 OR 4" && !isset($step[4]) && $step[0]=="3"){
        $response="CON Enter ID number (NHIS, Drivers License, Voters ID, Passport)\n";
        
    }
    else if(isset($step[4]) && $step[4]!="" && !isset($step[5]) && $step[0]=="3"){
        $response = "CON Your first name is " .$step[1]."\n and your last name is " .$step[2]. "\n Your ID Number is " .$step[4]. "\n Press 50 to confirm \n";
        $response .= "50. Confirm";
    
    }
    else if(isset($step[5]) && $step[5]=="50" && !isset($step[6]) && $step[0]=="3"){
        //Save data to database
        $data=array(
        "phonenumber"=>$phoneNumber,
        "first name" =>$step[1],
        "lastname" => $step[2],
        "ID Type" => $step[3],
        "ID Number" => $step[4]
        );
        $sql = "INSERT INTO rider (firstname, lastname, riderphonenumber, cardtype, cardnumber) VALUES ('$step[1]', '$step[2]', '$phoneNumber', '$step[3]', '$step[4]'); ";
        mysqli_query($conn, $sql);

        $response="END Thank you for registering \n Your first name is ".$step[1].", your lastname is ".$step[2]. ", Your ID number is ".$step[4];

    }
    else if($text == "4"){
        $sql = "DELETE FROM riderlocation WHERE riderphonenumber = '$phoneNumber'";
        $result1 = mysqli_query($conn, $sql);
        
            if($result1 == TRUE){
                $response = "END Your Ride has been cancelled";
            }
            else if($result1 == FALSE){
                $response = "END You have not requested a ride yet";
            }
        
    }
    else if($text == "2"){
        //This is a third level request after selecting 2 and 1 which displays the user account information
        $sql = "SELECT * FROM rider WHERE riderphonenumber = '$phoneNumber'";
        $result = mysqli_query($conn, $sql);
        

    
            while($row = mysqli_fetch_assoc($result)){
                $response = "END Your Firstname is " .$row['firstname']. "\n Your Lastname is " .$row['lastname']. "\n Your card details ".$row['cardnumber'];
            }
      
    }
    //At the end of a trip, the rider has to select this option
    else if($text == "5"){
            $sql1 = "INSERT INTO ridertrips SELECT * FROM riderlocation WHERE riderphonenumber = '$phoneNumber'";
            $result1 = mysqli_query($conn, $sql1);
            if($result1 == TRUE){
                $sql2 = "DELETE FROM riderlocation WHERE riderphonenumber = '$phoneNumber'";
                $result2 = mysqli_query($conn, $sql2);
                $response = "END Pay Your driver";
            }
            else if($result1 == FALSE){
                $response = "END Failed";
            }
        
    }
    else if($text == "6"){
        //This is when the user is a driver, the system checks from the driver database to verify whether or not the user has any record in the driver database
        $sql = "SELECT * FROM driver WHERE driverphonenumber = '$phoneNumber'";
        $result = mysqli_query($conn, $sql);
        

            if($row = mysqli_fetch_assoc($result)){
                $response = "CON choose action \n";
                $response .= "1. Find Rider \n";
                $response .= "2. My Account \n";
                $response .= "3. Cancel Ride \n";
                $response .= "4. Start Ride \n";
                $response .= "5. End Ride \n";
                $response .= "6. Payment \n";
                $response .= "7. Special Requests";
        
            }
            else {
                $response = "END You are not a driver";
            }
        

    }

    else if($text == "6*1"){
        $sql = "SELECT * FROM driverlocation WHERE driverphonenumber = '$phoneNumber'";
        $result = mysqli_query($conn, $sql);

        if($row = mysqli_fetch_assoc($result)){
        $response = "END You have already requested a rider";
        }

        else{
            $response = "CON Select Your Location \n";
            $response .= "1. Jean Nelson Hall \n";
            $response .= "2. Limann \n";
            $response .= "3. Night Market \n";
            $response .= "4. Sarbah Hall \n";
            $response .= "5. Commonwealth Hall \n";
            $response .= "6. Computer Science Department \n";
            $response .= "7. Business School";
        }

    }
    else if(isset($stage[2]) && $stage[2]!=="" && !isset($stage[3]) && $stage[1]=="1"){
        $response= "CON Your Location is ".$stage[2].", select your destination \n";
        // $response = "CON Select Your Destination \n";
        $response .= "1. Jean Nelson Hall \n";
        $response .= "2. Limann \n";
        $response .= "3. Night Market \n";
        $response .= "4. Sarbah Hall \n";
        $response .= "5. Commonwealth Hall \n";
        $response .= "6. Computer Science Department \n";
        $response .= "7. Business School";

    }    
    else if(isset($stage[3]) && !isset($stage[4]) && $stage[1]=="1"){
        $response = "CON Enter Your drivers ID";
    }
    
    else if(isset($stage[4]) && $stage[4]!== "" && !isset($stage[5]) && $stage[1]=="1"){
            $response = "CON Confirm your ride \n";
            $response .= "50. Confirm";
        
    }
    else if(isset($stage[5]) && $stage[5] =="50" && !isset($stage[6]) && $stage[1]=="1"){
        $data=array(
        "phonenumber"=>$phoneNumber,
        "location" =>$stage[2],
        "destination" => $stage[3],
        "driver ID" => $stage[4],
        );
        $sql = "INSERT INTO driverlocation (driverlocation, driverdestination, driverid, driverphonenumber) VALUES ('$stage[2]', '$stage[3]', '$stage[4]', '$phoneNumber'); ";
        mysqli_query($conn, $sql);
        
        
        $response="END Thank you \n your location is option ".$stage[2].  "\n A rider would be found for you shortly";
        

    }
    //The driver is able to view his/her account information
    else if($text == "6*2"){
        $sql = "SELECT * FROM driver WHERE driverphonenumber = '$phoneNumber'";
        $result = mysqli_query($conn, $sql);
        
            while($row = mysqli_fetch_assoc($result)){
                $response = "END Your Firstname is " .$row['firstname']. "\n Your Lastname is " .$row['lastname']. "\n Your license Number is ".$row['licenseno']. "\n Your car model is " .$row['vehiclemodel'].
                "\n Your vehicle registration number is " .$row['vehicleno']. "\n And your license will expire on " .$row['licenseexpdate']. "\n Your Driver ID is " .$row['driverid'];
            }
        

    }
    //The driver is able to cancel a trip
    else if($text == "6*3"){
        $sql = "DELETE FROM driverlocation WHERE driverphonenumber = '$phoneNumber'";
        $result1 = mysqli_query($conn, $sql);
        
            if($result1 == TRUE){
                $response = "END Your Ride has been cancelled";
            }
            else if($result1 == FALSE){
                $response = "END You have not placed a request yet";
            }
        
    }
    //when a driver connects with a rider, this is where he is able to start the trip
    else if($text == "6*4"){
        $sql2 = "SELECT * FROM riderlocation WHERE prefereddriver = '0'";
        $result2 = mysqli_query($conn, $sql2);
        if($row = mysqli_fetch_assoc($result2)){
            $response = "You would receive a text message with riders details";

            $sql = "INSERT INTO alltrips SELECT * FROM riderlocation INNER JOIN driverlocation ON riderlocation.riderlocation = driverlocation.driverlocation";
            $result1 = mysqli_query($conn, $sql);

                if($result1 == TRUE){
                    $sql1 = "UPDATE driverlocation SET starttime = CURRENT_TIMESTAMP WHERE driverphonenumber = '$phoneNumber'";
                    $result3 = mysqli_query($conn, $sql1);
                    


                    $sql4 = "UPDATE alltrips SET starttime = CURRENT_TIMESTAMP WHERE driverphonenumber = '$phoneNumber'";
                    $result4 = mysqli_query($conn, $sql4);
                    
                    
                    if($result4 == TRUE){
                        



                        //SEND SMS

                        

                        // Your Till credentials
                        $till_username = "cb177ea4013d4a0ea047f86f7f0296";
                        $till_api_key = "1ad39f853c72d032c00f837163c8fb15ddcc52a5";

                        // The Till project body
                        $till_project = [
                            "phone" => ["+16133043512"], //riders phonenumber
                            "text" => "Hello, Thank you, you can contact your driver on " .$phoneNumber//. " Your riders destination is " .$row['riderdestination']
                        ];

                        // Execute HTTP request
                        $client = new GuzzleHttp\Client();
                        try {

                            $res = $client->request(
                                "POST", 
                                "https://platform.tillmobile.com/api/send?username=".$till_username."&api_key=".$till_api_key,
                                ["body" => json_encode($till_project)]
                            );

                            // Till HTTP response body
                            echo $res->getBody();

                        } catch(Exception $e) {

                            echo $e;
                            

                        }
                        

                        //Driver Texts
                        $till_username = "cb177ea4013d4a0ea047f86f7f0296";
                        $till_api_key = "1ad39f853c72d032c00f837163c8fb15ddcc52a5";

                        // The Till project body
                        $till_project = [
                            "phone" => ["+16133043512"], //drivers phonenumber
                            "text" => "Hello, Thank you, you can contact your rider on " .$row['riderphonenumber']. " Your riders destination is " .$row['riderdestination']. "The Destinations are 1. Jean Nelson Hall 
                            2. Limann 
                            3. Night Market 
                            4. Sarbah Hall 
                            5. Commonwealth Hall 
                            6. Computer Science Department 
                            7. Business School"
                        ];

                        // Execute HTTP request
                        $client = new GuzzleHttp\Client();
                        try {

                            $res = $client->request(
                                "POST", 
                                "https://platform.tillmobile.com/api/send?username=".$till_username."&api_key=".$till_api_key,
                                ["body" => json_encode($till_project)]
                            );

                            // Till HTTP response body
                            echo $res->getBody();

                        } catch(Exception $e1) {

                            echo $e1;
                            

                        }
                    }
                    
                
                }
                else {
                    $response = "END You have not placed a request yet";
                }
        }
        else {
            $response = "END There are currently no rides available";
        }
        $response = "You would receive a text message with riders details";

    }
    //When the driver reaches his destination, this option allows him to end the ride
    else if($text == "6*5"){
        $sql = "SELECT * FROM driverlocation WHERE driverphonenumber = '$phoneNumber' AND starttime != NULL";
        $result = mysqli_query($conn, $sql);

        if($result == TRUE){
            $sql1 = "UPDATE driverlocation SET endtime = CURRENT_TIMESTAMP WHERE driverphonenumber = '$phoneNumber'";
            $result1 = mysqli_query($conn, $sql1);

            $sql5 = "UPDATE alltrips SET endtime = CURRENT_TIMESTAMP WHERE driverphonenumber = '$phoneNumber'";
            $result5 = mysqli_query($conn, $sql5);

            $sql2 = "INSERT INTO comptrips SELECT * FROM alltrips WHERE driverphonenumber = '$phoneNumber'";
            $result2 = mysqli_query($conn, $sql2);
            
            $response = "END Your trip has ended";
            
        }
        else{
            $response = "END Failed, You have not began a trip yet";
        }

    }
    // This option informs the system that the rider/passenger has paid, allowing both driver and passenger to begin another trip
    else if($text == "6*6"){
            $sql = "INSERT INTO drivertrips SELECT * FROM driverlocation WHERE driverphonenumber = '$phoneNumber'";
            $result = mysqli_query($conn, $sql);
            if($result == TRUE){
                $sql1 = "DELETE FROM driverlocation WHERE driverphonenumber = '$phoneNumber'";
                $result1 = mysqli_query($conn, $sql1);

                $sql2 = "DELETE FROM alltrips WHERE driverphonenumber = '$phoneNumber'";
                $result1 = mysqli_query($conn, $sql2);
                $response = "END Done";
            }
            else{
                $response = "END Failed";
            }
        
    }

    //This option allows the driver to start rides of which he has been requested specifically
    else if($text == "6*7"){

        $response = "CON Enter Your location \n";
    }
        else if(isset($stage1[2]) && $stage1[2]!=="" && !isset($stage1[3]) && $stage1[1]=="7"){
            $response = "CON Enter Your driver ID";
        }

        else if(isset($stage1[3]) && $stage1[3]!=="" && !isset($stage1[4]) &&$stage1[1]== "7"){
            $response = "CON Enter 50 to confirm \n";
            $response .= "50. Confirm";
        }

        else if(isset($stage1[4]) && $stage1[4]=="50" && !isset($stage1[5]) &&$stage1[1]== "7"){
            $data=array(
                "phonenumber"=>$phoneNumber,
                "location" => $stage1[2],
                "driver ID" => $stage1[3],
                );

        $sql1 =  "INSERT INTO driverlocation (driverlocation, driverdestination, driverid, driverphonenumber, starttime) VALUES ('$stage1[2]', 'SR', '$stage1[3]', '$phoneNumber', CURRENT_TIMESTAMP)";
        $result1 = mysqli_query($conn, $sql1);
    
            
        if($result1 == TRUE){
            $sql = "INSERT INTO alltrips SELECT * FROM riderlocation INNER JOIN driverlocation ON riderlocation.prefereddriver = driverlocation.driverid AND driverlocation.driverphonenumber = '$phoneNumber'";
            $result = mysqli_query($conn, $sql);
            
            $response = "END You have began the specially requetsed trip";
         }
         else {
             $response = "END Failed";
            }

        }
        
        //Check information about the current trip
     else if($text == "7"){
         $sql = "SELECT * FROM alltrips WHERE riderphonenumber = '$phoneNumber'";
         $result = mysqli_query($conn, $sql);

            if($row = mysqli_fetch_assoc($result)){
            $response = "END Your Driver is" .$row['driverphonenumber']. "\n and destination is " .$row['riderdestination'];
            }

            
            else {
            $response = "END You have not requested a ride yet";
            }
     }
     //Passengers request rides with specific driver, using his driver ID
    else if($text == "8"){

        $sql = "SELECT * FROM rider WHERE riderphonenumber = '$phoneNumber'";
        $result = mysqli_query($conn, $sql);
        //A check is run here to verify whether the user associated with the particular phone number has already created an account

    
            if($row = mysqli_fetch_assoc($result)){
                $sql1 = "SELECT * FROM riderlocation WHERE riderphonenumber = '$phoneNumber'";
                $result1 = mysqli_query($conn, $sql1);
        
                if($row = mysqli_fetch_assoc($result1)){
                    $response ="END you have already requested a ride, please cancel that one first";
                }
                else{
                    $response = "CON Select Your Location \n";
                    $response .= "1. Jean Nelson Hall \n";
                    $response .= "2. Limann \n";
                    $response .= "3. Night Market \n";
                    $response .= "4. Sarbah Hall \n";
                    $response .= "5. Commonwealth Hall \n";
                    $response .= "6. Computer Science Department \n";
                    $response .= "7. Business School";
                }

            }
            else{
                $response = "END You do not have an account";
            }
      
        
    }
    else if(isset($part2[1]) && $part2[1]!="" && !isset($part2[2]) && $part2[0]=="8"){
        $response= "CON Your Location is ".$part2[1].", Select your destination \n";
                    $response .= "1. Jean Nelson Hall \n";
                    $response .= "2. Limann \n";
                    $response .= "3. Night Market \n";
                    $response .= "4. Sarbah Hall \n";
                    $response .= "5. Commonwealth Hall \n";
                    $response .= "6. Computer Science Department \n";
                    $response .= "7. Business School";
        //Your location is shown and a prompt is received for you to select your destination
        
    }
    else if(isset($part2[2]) && $part2[2]!="" && !isset($part2[3]) && $part2[0]=="8"){
        $response = "CON Enter Your prefered drivers code";
      
        //the rider enters the drivers ID number
    }

    else if(isset($part2[3]) && !isset($part2[4]) && $part2[0]=="8"){
        $response = "CON Confirm your ride \n PLEASE NOTE Every stop carries a charge of GHC 5.00. \n";
        $response .= "99. Confirm";

    }     
    else if(isset($part2[4]) && $part2[4] =="99" && !isset($part2[5]) && $part2[0]=="8"){
        $data=array(
        "phonenumber"=>$phoneNumber,
        "location" =>$part2[1],
        "destination" => $part2[2],
        "prefereddriver" => $part2[3],
        );

        $response = "END Thank you for ordering \n Your Location is ".$part2[1]." and your destination is ".$part2[2].  "\n Your Driver will contact you shortly, Note that specific driver requests may take a while since your driver could already be on a trip"; 

        $sql2 = "INSERT INTO riderlocation (riderlocation, riderdestination, riderphonenumber, prefereddriver, requestdate) VALUES ('$part2[1]', '$part2[2]',  '$phoneNumber', '$part2[3]', CURRENT_TIMESTAMP); ";
        mysqli_query($conn, $sql2);

        //The data entered by the customer is now saved to the database
        //The data is also entered in the riderlocation table to allow the driver accept of decline
        $response = "END Thank you for ordering \n Your Location is ".$part2[1]." and your destination is ".$part2[2].  "\n Your Driver will contact you shortly, Note that specific driver requests may take a while since your driver could already be on a trip"; 

        $sql3 = "SELECT * FROM driver WHERE driverid = '$part2[3]' ";
        $result3 = mysqli_query($conn, $sql3);
        if($row = mysqli_fetch_assoc($result3)){


                    //SEND SMS


                        // Your Till credentials
                        $till_username = "cb177ea4013d4a0ea047f86f7f0296";
                        $till_api_key = "1ad39f853c72d032c00f837163c8fb15ddcc52a5";

                        // The Till project body
                        $till_project = [
                            "phone" => ["+16133043512"],//drivers number 
                            "text" => "Hello, " .$phoneNumber. " Located at " .$part2[1]. " With destination " .$part2[2]. " has requested you specifically, please contact the rider for further enquiry"
                        ];

                        // Execute HTTP request
                        $client = new GuzzleHttp\Client();
                        try {

                            $res = $client->request(
                                "POST", 
                                "https://platform.tillmobile.com/api/send?username=".$till_username."&api_key=".$till_api_key,
                                ["body" => json_encode($till_project)]
                            );

                            // Till HTTP response body
                            echo $res->getBody();

                        } catch(Exception $e) {

                            echo $e;
                            

                        }
                        $response = "END Your Ride has been successfully processed and your driver has been contedcted";

                    }  
                    else if ($row != mysqli_fetch_assoc($result3)){
                         //SEND SMS


                        // Your Till credentials
                        $till_username = "cb177ea4013d4a0ea047f86f7f0296";
                        $till_api_key = "1ad39f853c72d032c00f837163c8fb15ddcc52a5";

                        // The Till project body
                        $till_project = [
                            "phone" => ["+16133043512"],//drivers number 
                            "text" => "Hello, Kindly re-enter the driver's ID again, since the one entered is incorrect"
                        ];

                        // Execute HTTP request
                        $client = new GuzzleHttp\Client();
                        try {

                            $res = $client->request(
                                "POST", 
                                "https://platform.tillmobile.com/api/send?username=".$till_username."&api_key=".$till_api_key,
                                ["body" => json_encode($till_project)]
                            );

                            // Till HTTP response body
                            echo $res->getBody();

                        } catch(Exception $e3) {

                            echo $e3;
                            

                        }
                        $response = "END Your ride was unsuccessful, Kindly crosscheck the driver's ID and re-order the ride, Thank You";
                    }      
            
    }
    
}
header('Content-type: text/plain');
echo $response;


?>

