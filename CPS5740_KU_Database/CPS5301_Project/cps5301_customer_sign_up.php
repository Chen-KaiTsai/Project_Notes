<!DOCTYPE html>
<html>

<head>
    <title>Customer_sign_up</title>
</head>

<body>
    <h2>Customer sign up</h2><br>
    <form action="cps5301_customer_sign_up_check.php" method="post">
        Email <input type="text" name="c_email"><br>
        Password <input type="text" name="c_password"><br>
        Retype Password <input type="text" name="c_retype_password"><br>
        First Name <input type="text" name="c_first_name"><br>
        Last Name <input type="text" name="c_last_name"><br>
        TEL <input type="text" name="c_tel"><br>
        Address <input type="text" name="c_address"><br>
        City <input type="text" name="c_city"><br>
        Zipcode <input type="text" name="c_zipcode"><br>

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
        State <select name="c_state">
            <?php foreach ($states as $key => $value) { ?>
            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
            <?php } ?>
        </select>
        <input type="submit" value="Sign up"><br>
    </form>
</body>
</html>