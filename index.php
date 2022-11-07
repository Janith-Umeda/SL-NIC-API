<?php
    //! Sri Lankan NIC Validate Check API Using PHP
    //* Author : Janith Umeda Madushan.
    //* API Type : Public / Free to Use / No OAuth.
    //* Method : GET/POST/REQUEST
    //* Content Type : Aplication/JSON 
    //! CORS Policy : All

    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    
    $months_days = [
        ["month"=>"January","days"=>31],
        ["month"=>"February","days"=>29],
        ["month"=>"March","days"=>31],
        ["month"=>"April","days"=>30],
        ["month"=>"May","days"=>31],
        ["month"=>"June","days"=>30],
        ["month"=>"July","days"=>31],
        ["month"=>"August","days"=>31],
        ["month"=>"September","days"=>30],
        ["month"=>"October","days"=>31],
        ["month"=>"November","days"=>30],
        ["month"=>"December","days"=>31],
    ];
    $charc = ["x","v"];

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $NIC = $_GET['nic'];
            break;
        case 'POST':
            $NIC = $_POST['nic'];
            break;
        case 'REQUEST':
            $NIC = $_REQUEST['nic'];
            break;
    }
    
    if (!isset($NIC)){
        header("Status Code",true,401);
        exit();

    }else{

        $response = [];

        $end_charc = strtolower(substr($NIC,9,1));

        if(strlen($NIC)===10 && is_numeric(substr($NIC,0,9)) && !is_numeric($end_charc) && in_array($end_charc,$charc)){
            $status = True;
            $st_des = "Valid";
            $type = "Old";
            $year = substr($NIC,0,2);
            $day = substr($NIC,2,3);
            $character = substr($NIC,9,10);
            $gender = get_gender($day);
            $birthday = get_bdate($day,$months_days,$year);
            switch ($end_charc){
                case "v":
                    $v_ele = "Yes";
                    break;
                case "x":
                    $v_ele = "No";
                    break;
            }
            $modi_nic = modify_nic($NIC);
            
        }elseif(strlen($NIC) === 12 && is_numeric($NIC)){
            $status = True;
            $st_des = "Valid";
            $type = "New";
            $year = substr($NIC,0,4);
            $day = substr($NIC,4,3);
            $character = "no";
            $gender = get_gender($day);
            $birthday = get_bdate($day,$months_days,$year);
            $v_ele = "Unknown";
            $modi_nic = "";

        }elseif(empty($NIC)){
            $status = False;
            $st_des = "Empty";
            $type = "";
            $gender = "";
            $birthday = ["",""];
            $v_ele = "";
            $modi_nic = "";
        }else{
            $status = False;
            $st_des = "Invalid";
            $type = "";
            $gender = "";
            $birthday = ["",""];
            $v_ele = "";
            $modi_nic = "";
        }
        
        $response = [
                    "status"=>$status,
                    "status_description"=>$st_des,
                    "type"=>$type,
                    "nic_details"=>[
                            "gender"=>$gender,
                            "birthday"=> [
                                'format1'=>$birthday[0],
                                'format2'=>$birthday[1]
                            ],
                        ],
                    "vote_eligible"=>$v_ele,
                    "modified_nic"=>$modi_nic
                ];
    }

    function get_gender($day){
        if($day < 500){
            return "Male";
        }else{
            return "Female";
        }
    }
    function get_bdate($day,$months_days,$year){
        $month = "";
        if($day > 500) {$day = $day-500;}

        for ($i=0; $i < sizeof($months_days); $i++) { 
            if($months_days[$i]['days'] < $day){
                $day = $day - $months_days[$i]['days'];
            }else{
                $month = $months_days[$i]['month'];
                break;
            }
        }

        $df = new DateTime($day.'-'.$month.'-'.$year);
        $y = date_format($df,'Y');
        $m = date_format($df,'m');
        $d = date_format($df,'d');

        return [$y.'-'.$m.'-'.$d,$year.'-'.$month.'-'.$day];
    }

    function modify_nic($old_nic){
        $y = substr($old_nic,0,2);
        $b = substr($old_nic,2,3);
        $s = substr($old_nic,5,3);
        $cd = substr($old_nic,8,1);

        return "19".$y.$b."0".$s.$cd;
    }


    die(json_encode($response));
?>