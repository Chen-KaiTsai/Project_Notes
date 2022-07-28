<!DOCTYPE html>
<html>

<head>
    <title>Customer_sign_up</title>
</head>

<body>
    <h2>Customer sign up</h2><br>
    <form action="cps5740_customer_sign_up_check_phase1.php" method="post">
        Login ID <input type="text" name="login_id"><br>
        Password <input type="text" name="password"><br>
        Retype Password <input type="text" name="retype_password"><br>
        First Name <input type="text" name="first_name"><br>
        Last Name <input type="text" name="last_name"><br>
        TEL <input type="text" name="tel"><br>
        Address <input type="text" name="address"><br>
        City <input type="text" name="city"><br>
        Zipcode <input type="text" name="zipcode"><br>
        
        <?php
		//states array
		$states = array(
                    ''=>'-----',
		    'AL'=>'Alabama',
		    'AK'=>'Alaska',
		    'AZ'=>'Arizona',
		    'AR'=>'Arkansas',
		    'CA'=>'California',
		    'CO'=>'Colorado',
		    'CT'=>'Connecticut',
		    'DE'=>'Delaware',
		    'DC'=>'District of Columbia',
		    'FL'=>'Florida',
		    'GA'=>'Georgia',
		    'HI'=>'Hawaii',
		    'ID'=>'Idaho',
		    'IL'=>'Illinois',
		    'IN'=>'Indiana',
		    'IA'=>'Iowa',
		    'KS'=>'Kansas',
		    'KY'=>'Kentucky',
		    'LA'=>'Louisiana',
		    'ME'=>'Maine',
		    'MD'=>'Maryland',
		    'MA'=>'Massachusetts',
		    'MI'=>'Michigan',
		    'MN'=>'Minnesota',
		    'MS'=>'Mississippi',
		    'MO'=>'Missouri',
		    'MT'=>'Montana',
		    'NE'=>'Nebraska',
		    'NV'=>'Nevada',
		    'NH'=>'New Hampshire',
		    'NJ'=>'New Jersey',
		    'NM'=>'New Mexico',
		    'NY'=>'New York',
		    'NC'=>'North Carolina',
		    'ND'=>'North Dakota',
		    'OH'=>'Ohio',
		    'OK'=>'Oklahoma',
		    'OR'=>'Oregon',
		    'PA'=>'Pennsylvania',
		    'RI'=>'Rhode Island',
		    'SC'=>'South Carolina',
		    'SD'=>'South Dakota',
		    'TN'=>'Tennessee',
		    'TX'=>'Texas',
		    'UT'=>'Utah',
		    'VT'=>'Vermont',
		    'VA'=>'Virginia',
		    'WA'=>'Washington',
		    'WV'=>'West Virginia',
		    'WI'=>'Wisconsin',
		    'WY'=>'Wyoming',
		);
	 ?>
        State <select name="state">
	    <?php foreach ($states as $key => $value) { ?>
            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
            <?php } ?>
		</select>
        <input type="submit" value="Sign up"><br>
    </form>    
</body>
</html>

