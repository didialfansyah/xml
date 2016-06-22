<?php
    require_once('lib/nusoap.php');
     
    $server = new nusoap_server;
     
    $server->configureWSDL('server', 'urn:server');
     
    $server->wsdl->schemaTargetNamespace = 'urn:server';
     
    //SOAP complex type return type (an array/struct)
    $server->wsdl->addComplexType(
        'MyTableData', // the type's name
        'complexType', // yes.. indeed it is a complex type.
        'struct', // php it's a structure. (only other option is array)
        'all', // compositor.
        '',// no restriction
        array(
            'id' => array('name' => 'id_user', 'type' => 'xsd:int'),
            'fname' => array('name' => 'fullname', 'type' => 'xsd:string'),
            'pass' => array('name' => 'email', 'type' => 'xsd:string'),
            'email' => array('name' => 'level', 'type' => 'xsd:string')
        )
    );

    $server->wsdl->addComplexType(
        'MyTableArray',//glorious name
        'complexType',// not a simpletype for sure!
        'array',// oh we are an array now!
        '',// bah. blank
        'SOAP-ENC:Array',
        array(),// our element is an array.
        array(
            array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:MyTableData[]')
        ),//the attributes of our array.
        'tns:MyTableData'// what type of array is this?  Oh it's an array of mytable data
    );


     
    //first simple function
    $server->register('hello',
                array('username' => 'xsd:string'),  //parameter
                array('return' => 'xsd:string'),  //output
                'urn:server',   //namespace
                'urn:server#helloServer',  //soapaction
                'rpc', // style
                'encoded', // use
                'Just say hello');  //description
     
    //this is the second webservice entry point/function 
    $server->register('login',
                array('username' => 'xsd:string', 'password'=>'xsd:string'),  //parameters
                array('return' => 'tns:MyTableArray'),  //output
                'urn:server',   //namespace
                'urn:server#loginServer',  //soapaction
                'rpc', // style
                'encoded', // use
                'Check user login');  //description
     
    //first function implementation
    function hello($username) {
            return 'Hello, '.$username.'!';
    }
     
    //second function implementation 
    function login($username, $password) {

            if($username == "mzero" AND $password == "dalfa123"){
                $con = mysql_connect("localhost","root","");
                mysql_select_db("testing", $con);
                
                $queryz = mysql_query("SELECT * FROM data");
                $nums = mysql_num_rows($queryz);
                $datas = array();
                $i = 0;

                while($show = mysql_fetch_array($queryz)){
                    $datas[$i] = array('id' => $show['id'], 'fname' => $show['fullname'], 'pass' => $show['password'], 'email' => $show['email']);
                    $i++;
                }
                
                return $datas;
            }
    }
     
    $HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
    $server->service($HTTP_RAW_POST_DATA);

?>