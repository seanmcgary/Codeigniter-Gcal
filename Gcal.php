<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * CodeIgniter Google Calendar Library 
 * 
 * Author: Sean McGary (http://www.seanmcgary.com), sean@seanmcgary.com
 *
 * ========================================================
 * REQUIRES: php5, ZendGdata
 * ========================================================
 * 
 * VERSION: Beta 1.0 (2009-06-15)
 * LICENSE: GNU GENERAL PUBLIC LICENSE - Version 2, June 1991
 * 
 **/
// fire up the Zend Loader
// we are going to assume that you have a path in
// your php.ini set to look for the ZendGdata library
// if you dont, then do the following:
//
// require_once 'path/to/ZendGdata/library';
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');
 
class Gcal {

    
    
    
	
	function getAuthSubUrl($redirectUrl){	
		// indicates the app will only access Google Calendar feeds
		$scope = 'http://www.google.com/calendar/feeds';
		$secure = false; // if you are registered with SubAuth, then this can be true
		$session = true;
		return Zend_Gdata_AuthSub::getAuthSubTokenUri($redirectUrl, $scope, $secure, $session);	
	}
	
	
	function clientLogin($username, $password){
		$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME; 
		$client = Zend_Gdata_ClientLogin::getHttpClient($username,$password,$service);
		
		return $client;
	}
	
	function outputCalendarList($client){
		$gdataCal = new Zend_Gdata_Calendar($client);
		$calFeed = $gdataCal->getCalendarListFeed();
		
		return $calFeed;
	}
	
	function outputCalendarEvents($client){
		$gdataCal = new Zend_Gdata_Calendar($client);
		$eventFeed = $gdataCal->getCalendarEventFeed();
		
		return $eventFeed;
	}
	
	function calEventsByDateRange($client, $startDate, $endDate){
		$gdataCal = new Zend_Gdata_Calendar($client);
		$query = $gdataCal->newEventQuery();
		$query->setUser('default');
		$query->setVisibility('private');
		$query->setProjection('full');
		$query->setOrderby('starttime');
		$query->setStartMin($startDate);
		$query->setStartMax($endDate);
		
		$eventFeed = $gdataCal->getCalendarEventFeed($query);
		
		return $eventFeed;
	}
	
	function calFullTextQuery($client, $eventQuery){
		$gdataCal = new Zend_Gdata_Calendar($client);
		$query = $gdataCal->newEventQuery();
		$query->setUser('default');
		$query->setVisibility('private');
		$query->setProjection('full');
		$query->setQuery($eventQuery);
		$eventFeed = $gdataCal->getCalendarEventFeed($query);
		return $eventFeed;
	}
	
	function createEvent($client, $eventArray){
		$gdataCal = new Zend_Gdata_Calendar($client);
		$newEvent = $gdataCal->newEventEntry();
		
		$newEvent->title = $gdataCal->newTitle($eventArray['title']);
		$newEvent->where = array($gdataCal->newWhere($eventArray['where']));
		$newEvent->content = $gdataCal->newContent($eventArray['desc']);
		
		$when = $gdataCal->newWhen();
		$when->startTime = "{$eventArray['startDate']}T{$eventArray['startTime']}:00.000{$eventArray['tzOffset']}:00";
		$when->endTime = "{$eventArray['endDate']}T{$eventArray['endTime']}:00.000{$eventArray['tzOffset']}:00";
		$newEvent->when = array($when);
		
		//upload the even to the calendar server
		//a copy of the event as it is recorded on the server is returned
		$createdEvent = $gdataCal->insertEvent($newEvent);
		return $createdEvent->id->text;
		
	}
	
	function createQuickEvent($client, $quickAddText){
		$gdataCal = new Zend_Gdata_Calendar($client);
		$event = $gdataCal->newEventEntry();
		$event->content = $gdataCal->newContent($quickAddText);
		$event->quickAdd = $gdataCal->newQuickAdd('true');
		$newEvent = $gdataCal->insertEvent($event);
	}
	
	
	
	
	
}

?>