<?php
	// Set some initial headers
	header('Cache-Control: no-cache, must-revalidate');
	header('Content-type: text/html; charset=utf-8');

	// Constants to determine if this is an AJAX/POST event
	define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	define('IS_POST', isset($_SERVER['REQUEST_METHOD']) AND strtolower($_SERVER['REQUEST_METHOD']) === 'post');

	// Requesting tags
	if(IS_AJAX AND IS_POST)
	{
		// Mode for operation. Exit on no value
		$mode = isset($_GET['mode']) ? $_GET['mode'] : NULL;

		// Get the keyword entered on page
		$input = isset($_POST['input']) ? $_POST['input'] : NULL;

		// List of countries data obtained from http://en.wikipedia.org/wiki/ISO_3166-1
		$tagsCountry = array('Afghanistan','Åland Islands','Albania','Algeria','American Samoa','Andorra','Angola','Anguilla','Antarctica','Antigua and Barbuda','Argentina','Armenia','Aruba','Australia','Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bermuda','Bhutan','Bolivia, Plurinational State of','Bonaire, Sint Eustatius and Saba','Bosnia and Herzegovina','Botswana','Bouvet Island','Brazil','British Indian Ocean Territory','Brunei Darussalam','Bulgaria','Burkina Faso','Burundi','Cambodia','Cameroon','Canada','Cape Verde','Cayman Islands','Central African Republic','Chad','Chile','China','Christmas Island','Cocos (Keeling) Islands','Colombia','Comoros','Congo','Congo, the Democratic Republic of the','Cook Islands','Costa Rica','Côte d\'Ivoire','Croatia','Cuba','Curaçao','Cyprus','Czech Republic','Denmark','Djibouti','Dominica','Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Ethiopia','Falkland Islands (Malvinas)','Faroe Islands','Fiji','Finland','France','French Guiana','French Polynesia','French Southern Territories','Gabon','Gambia','Georgia','Germany','Ghana','Gibraltar','Greece','Greenland','Grenada','Guadeloupe','Guam','Guatemala','Guernsey','Guinea','Guinea-Bissau','Guyana','Haiti','Heard Island and McDonald Islands','Holy See (Vatican City State)','Honduras','Hong Kong','Hungary','Iceland','India','Indonesia','Iran, Islamic Republic of','Iraq','Ireland','Isle of Man','Israel','Italy','Jamaica','Japan','Jersey','Jordan','Kazakhstan','Kenya','Kiribati','Korea, Democratic People\'s Republic of','Korea, Republic of','Kuwait','Kyrgyzstan','Lao People\'s Democratic Republic','Latvia','Lebanon','Lesotho','Liberia','Libyan Arab Jamahiriya','Liechtenstein','Lithuania','Luxembourg','Macao','Macedonia, the former Yugoslav Republic of','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Martinique','Mauritania','Mauritius','Mayotte','Mexico','Micronesia, Federated States of','Moldova, Republic of','Monaco','Mongolia','Montenegro','Montserrat','Morocco','Mozambique','Myanmar','Namibia','Nauru','Nepal','Netherlands','New Caledonia','New Zealand','Nicaragua','Niger','Nigeria','Niue','Norfolk Island','Northern Mariana Islands','Norway','Oman','Pakistan','Palau','Palestinian Territory, Occupied','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Pitcairn','Poland','Portugal','Puerto Rico','Qatar','Réunion','Romania','Russian Federation','Rwanda','Saint Barthélemy','Saint Helena, Ascension and Tristan da Cunha','Saint Kitts and Nevis','Saint Lucia','Saint Martin (French part)','Saint Pierre and Miquelon','Saint Vincent and the Grenadines','Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore','Sint Maarten (Dutch part)','Slovakia','Slovenia','Solomon Islands','Somalia','South Africa','South Georgia and the South Sandwich Islands','Spain','Sri Lanka','Sudan','Suriname','Svalbard and Jan Mayen','Swaziland','Sweden','Switzerland','Syrian Arab Republic','Taiwan, Province of China','Tajikistan','Tanzania, United Republic of','Thailand','Timor-Leste','Togo','Tokelau','Tonga','Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Turks and Caicos Islands','Tuvalu','Uganda','Ukraine','United Arab Emirates','United Kingdom','United States','United States Minor Outlying Islands','Uruguay','Uzbekistan','Vanuatu','Venezuela, Bolivarian Republic of','Viet Nam','Virgin Islands, British','Virgin Islands, U.S.','Wallis and Futuna','Western Sahara','Yemen','Zambia','Zimbabwe');

		// Data obtained from http://data.stackexchange.com/stackoverflow/q/2762/
		$tagsStackOverflow = array('.htaccess','.net','actionscript-3','activerecord','ajax','algorithm','android','animation','apache','api','architecture','arrays','asp.net','asp.net-mvc','asp.net-mvc-2','asp.net-mvc-3','authentication','bash','blackberry','browser','c','c#','c++','caching','cakephp','class','cocoa','cocoa-touch','codeigniter','core-data','css','database','database-design','databinding','date','datetime','debugging','delphi','deployment','design','design-patterns','django','dll','dom','drupal','eclipse','email','entity-framework','events','excel','exception','facebook','file','firefox','flash','flex','forms','function','game-development','gcc','generics','git','google','google-app-engine','google-chrome','google-maps','grails','gridview','gui','gwt','hibernate','homework','html','html5','http','iis','image','inheritance','internet-explorer','ios','ipad','iphone','iphone-sdk-4.0','java','java-ee','javascript','jpa','jquery','jquery-plugins','jquery-ui','jsf','json','jsp','language-agnostic','layout','library','linq','linq-to-sql','linux','list','listview','logging','mac','math','matlab','memory','mod-rewrite','ms-access','multithreading','mvc','mysql','networking','nhibernate','object','objective-c','oop','opengl','optimization','oracle','orm','osx','parsing','pdf','performance','perl','php','php5','plugins','pointers','postgresql','python','qt','query','r','reflection','regex','rest','ruby','ruby-on-rails','ruby-on-rails-3','scala','scripting','search','security','select','serialization','server','servlets','session','sharepoint','shell','silverlight','sockets','sorting','spring','sql','sqlite','sql-server','sql-server-2005','sql-server-2008','stored-procedures','string','svn','swing','table','templates','testing','tomcat','tsql','uitableview','unit-testing','unix','url','validation','variables','vb.net','vba','version-control','video','vim','visual-c++','visual-studio','visual-studio-2008','visual-studio-2010','wcf','web','web-applications','web-development','web-services','winapi','windows','windows-phone-7','winforms','wordpress','wpf','xaml','xcode','xml','xslt','zend-framework');

		// Tags to use based on mode
		$tags = ($mode === 'stackoverflow') ? $tagsStackOverflow : $tagsCountry;

		// Store our results
		$results = array();

		// Max tags to return
		$MAX_TAGS = 1000;

		// Lookup pass #1 - Find all tags that start with our input
		foreach($tags as $value)
		{
			if(strlen($input) <= strlen($value))
			{
				if(substr(strtolower($value), 0, strlen($input)) === strtolower($input))
				{
					array_push($results, $value);
				}
			}

			// Stop at 10 results
			if(count($results) >= $MAX_TAGS) { break; }
		}

		// Lookup pass #2 - Find all tags that end with our input
		foreach($tags as $value)
		{
			// Stop at 10 results
			if(count($results) >= $MAX_TAGS) { break; }

			if(strlen($input) <= strlen($value))
			{
				if(substr(strtolower($value), strlen($value) - strlen($input)) == strtolower($input))
				{
					// Discard duplicates
					if(in_array($value, $results) === FALSE)
					{
						array_push($results, $value);
					}
				}
			}
		}

		// Lookup pass #3 - Find all tags that contains our input anywhere
		foreach($tags as $value)
		{
			// Stop at 10 results
			if(count($results) >= $MAX_TAGS) { break; }

			if(strstr(strtolower($value), $input) !== FALSE)
			{
				// Discard duplicates
				if(in_array($value, $results) === FALSE)
				{
					array_push($results, $value);
				}
			}
		}

		// Time to format our results.
		// For example if we searched for "an" and one of our matches was "Canada", the
		// returned text will be "C<strong>an</strong>ada" (without quotes)
		foreach($results as $key => $value)
		{
			$end = strlen($value);
			$offset = 0;
			$display = '';

			// Loop through our characters and find out where to put the tags
			while($offset < $end)
			{
				// See if our input exisst here
				$p = stripos($value, $input, $offset);

				// It does!
				if($p !== FALSE)
				{
					// Extract the text and wrap it in <strong>
					$display .= substr($value, $offset, $p - $offset).'<strong>'.substr($value, $p, strlen($input)).'</strong>';
					$offset = $p + strlen($input);
				}
				else
				{
					// Looks like we're at the end
					$display .= substr($value, $offset, $end - $offset);
					break;
				}
			}

			// Our results will be an array pair containing two values: value and display
			// value is the value used for the actual tag
			// display is used to populate the result list where matches will be shown in bold
			$results[$key] = array('value' => $value, 'display' => $display);
		}

		// Dump it out!
		die(json_encode($results));
	}
