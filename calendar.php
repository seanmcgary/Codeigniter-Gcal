<?php
session_start();

	Class Calendar extends Controller {
	
		function Calendar(){
			parent::Controller();
			require_once 'Zend/Loader.php';
			Zend_Loader::loadClass('Zend_Gdata');
			Zend_Loader::loadClass('Zend_Gdata_AuthSub');
			Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
			Zend_Loader::loadClass('Zend_Gdata_Calendar');
		} 
		
		function authSubLogin(){
			
			$redirect = "http://noodles.local/index.php/calendar/gCalDemo";
			$this->load->library('Gcal');
			$authSubUrl = $this->gcal->getAuthSubUrl($redirect);
			$login = array('loggedIn' => true, 'token' => "");
			$_SESSION['loggedIn'] = $login;
			echo "<a href=\"".$authSubUrl."\">login to your google account</a>";
				
			
		}
		
		function clientLogin(){
			$username = 'username';
			$password = 'password';
			
			$this->load->library('Gcal');
			$this->gcal->clientLogin($username, $password);
			
			redirect('index.php/gCalDemo');		
		
		}
		
		
		function gCalDemo(){
			
			if(!isset($_SESSION['sessionToken']) && isset($_GET['token'])){
				$_SESSION['sessionToken'] = Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
			}	
			$client = Zend_Gdata_AuthSub::getHttpClient($_SESSION['sessionToken']);		
			// load the library
			$this->load->library('Gcal');		
			
			// examples of useage for each function
			
			//################################################
			//##############OutputCalendarList################
			//################################################
			
			/*
			$calFeed = $this->gcal->outputCalendarList($client);
			
			echo '<h1>' . $calFeed->title->text . '</h1>';
			echo '<ul>';
			foreach ($calFeed as $calendar) {
				echo '<li>'.$calendar->title->text.'</li>';
			}
			echo '</ul>';
			*/
			
			//################################################
			//##############End OutputCalendarList############
			//################################################
			echo "<br />- - - - - - - - - - - - - - - - - - - - 
			- - - - - - - - - - - - - - - - - - - - - - - - - - - -<br />";
			//################################################
			//##############OutputCalendarEvents##############
			//################################################
			
			/*
			$eventFeed = $this->gcal->outputCalendarEvents($client);
			
			echo "<ul>";
			foreach ($eventFeed as $event){
				echo "\t<li><b>Event Title: </b>".$event->title->text."</li>\n";
				
				echo "\t\t<ul>\n";
				echo "\t\t\t<li><b>Event ID: </b>". $event->id->text ."</li>\n";
				echo "\t\t\t<li><b>Content: </b>".$event->content->text."</li>\n";
				foreach($event->when as $when){
					//echo "<pre>";
					//print_r($when);
					//echo "</pre>";
					
					echo "\t\t\t<li><b>Starts:</b> " . $when->startTime . "</li>\n";
					echo "\t\t\t<li><b>Ends:</b> " . $when->endTime . "</li>\n";
				}
				echo "\t\t</ul>\n";
				echo "\t</li>\n";
			}
			echo "</ul>";
			*/
			
			//################################################
			//############End OutputCalendarEvents############
			//################################################
			echo "<br />- - - - - - - - - - - - - - - - - - - - 
			- - - - - - - - - - - - - - - - - - - - - - - - - - - -<br />";
			//################################################
			//##############calEventsByDateRange##############
			//################################################
			
			/*
			$startDate = '2009-06-15';
			$endDate = '2009-06-17';
			
			$eventQuery = $this->gcal->calEventsByDateRange($client, $startDate, $endDate);
			
			echo "<ul>";
			foreach ($eventQuery as $event){
				echo "\t<li><b>Event Title: </b>".$event->title->text."</li>\n";
				
				echo "\t\t<ul>\n";
				echo "\t\t\t<li><b>Event ID: </b>". $event->id->text ."</li>\n";
				echo "\t\t\t<li><b>Content: </b>".$event->content->text."</li>\n";
				foreach($event->when as $when){
					//echo "<pre>";
					//print_r($when);
					//echo "</pre>";
					
					echo "\t\t\t<li><b>Starts:</b> " . $when->startTime . "</li>\n";
					echo "\t\t\t<li><b>Ends:</b> " . $when->endTime . "</li>\n";
				}
				echo "\t\t</ul>\n";
				echo "\t</li>\n";
			}
			echo "</ul>";
			*/
			
			//################################################
			//############End calEventsByDateRange############
			//################################################
			echo "<br />- - - - - - - - - - - - - - - - - - - - 
			- - - - - - - - - - - - - - - - - - - - - - - - - - - -<br />";
			//################################################
			//##############calFullTextQuery##################
			//################################################
			/*
			$query = "enter your query string here"; // searches in both title and event content
			$eventFullQuery = $this->gcal->calFullTextQuery($client, $query);
			
			echo "<ul>";
			foreach ($eventFullQuery as $event){
				echo "\t<li><b>Event Title: </b>".$event->title->text."</li>\n";
				
				echo "\t\t<ul>\n";
				echo "\t\t\t<li><b>Event ID: </b>". $event->id->text ."</li>\n";
				echo "\t\t\t<li><b>Content: </b>".$event->content->text."</li>\n";
				foreach($event->when as $when){
					//echo "<pre>";
					//print_r($when);
					//echo "</pre>";
					
					echo "\t\t\t<li><b>Starts:</b> " . $when->startTime . "</li>\n";
					echo "\t\t\t<li><b>Ends:</b> " . $when->endTime . "</li>\n";
				}
				echo "\t\t</ul>\n";
				echo "\t</li>\n";
			}
			echo "</ul>";
			*/
					
			//################################################
			//##############End calFullTextQuery##############
			//################################################
			echo "<br />- - - - - - - - - - - - - - - - - - - - 
			- - - - - - - - - - - - - - - - - - - - - - - - - - - -<br />";
			//################################################
			//#################createEvent####################
			//################################################
			
			/*
			$eventArray = array('title'     =>  'My test event',
								'desc'      =>  'This is a description for my test event',
								'where'     =>  'My house',
								'startDate' =>  '2009-06-16',
								'startTime' =>  '22:00',
								'endDate'   =>  '2009-06-17',
								'endTime'   =>  '04:00',
								'tzOffset'  =>  '-04'
								);
			
			$this->gcal->createEvent($client, $eventArray);
			*/
			
			//################################################
			//###############End createEvent##################
			//################################################
			echo "<br />- - - - - - - - - - - - - - - - - - - - 
			- - - - - - - - - - - - - - - - - - - - - - - - - - - -<br />";
			//################################################
			//##############createQuickEvent##################
			//################################################
			
			/*
			// include a time + am/pm and day
			// day can be day of the week or tomorrow
			// can use a format like "next tuesday"
			$eventInfo = "Dinner at my house next tuesday at 9pm";
			$this->gcal->createQuickEvent($client, $eventInfo);
			*/
			
			//################################################
			//##############End createQuickEvent##############
			//################################################
			
			
		}
	
	}
?>