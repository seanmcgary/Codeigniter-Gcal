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
// if you dont, then do the following instead of the current:
//
// require_once 'path/to/ZendGdata/library';
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');
 
class Gcal {   

    /**
     * Logs in via AuthSub. AuthSub is simiar to OAuth, but was developed
     * before OAuth became the "standard". AuthSub acts in a similar way as
     * OAuth. You login through Google, granting access to the site. You are
     * then returned to the site with an access token to authorize with.
     *
     * @param String $redirectUrl - URL that you want to redirect to
     *                              once a token is assigned
     * @return String - URL to follow to log into Google Calendar
     */
	function getAuthSubUrl($redirectUrl){	
		// indicates the app will only access Google Calendar feeds
		$scope = 'http://www.google.com/calendar/feeds';
		$secure = false; // if you are registered with SubAuth, then this can be true
		$session = true;
		return Zend_Gdata_AuthSub::getAuthSubTokenUri($redirectUrl, $scope, $secure, $session);	
	}
	
	/**
     * ClientLogin is simplier in that you dont need to be redirected
     * to sign in. Instead, simply provide your Google account credentials
     * and it works its magic, returning an access token. Generally ClientLogin
     * is reserved for  installed applications, but I provided access to it
     * anyway incase someone wants to access a Google Calendar without redirecting.
     *
     * @param String $username - Google account username
     * @param String $password = Google account password
     * @return String - token used for verification.
     */
	function clientLogin($username, $password){
		$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME; 
		$client = Zend_Gdata_ClientLogin::getHttpClient($username,$password,$service);
		
		return $client;
	}

    /**
     * Grabs a list of a users calendars
     *
     * @param String $client - Access token
     * @return calendars in array form
     */
	function outputCalendarList($client){
		$gdataCal = new Zend_Gdata_Calendar($client);
		$calFeed = $gdataCal->getCalendarListFeed();
		
		return $calFeed;
	}

    /**
     * List all events in user's calendar
     * 
     * @param String $client - Access token
     * @return events in array form
     */
	function outputCalendarEvents($client){
		$gdataCal = new Zend_Gdata_Calendar($client);
		$eventFeed = $gdataCal->getCalendarEventFeed();
		
		return $eventFeed;
	}

    /**
     * List events witin a specified range
     *
     * @param String $client - Access token
     * @param String $startDate - date 'YYYY-MM-DD'
     *               RFC 3339 timestamp format compatible
     * @param String $endDate - date 'YYYY-MM-DD'
     *               RFC 3339 timestamp format compatible
     * @return events in array form
     */
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
	/**
     * List events matching a full text query. Full text query searches
     * both title and event description for matches
     *
     * @param String $client - Access token
     * @param String $eventQuery - Query in string form
     * @return list of matching events in array form
     */
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

    /**
     * Create an event
     * @param String $client Access token
     * @param Associative Array $eventArray
     *                          'desc'      =>  'This is a description for the event',
								'where'     =>  'Location',
								'startDate' =>  'YYYY-MM-DD',
								'startTime' =>  'HH:MM', // 24hr time
								'endDate'   =>  'YYYY-MM-DD',
								'endTime'   =>  'HH:MM', // 24hr time
								'tzOffset'  =>  '00' // timezone offset from GMT
     * @return String - ID of created event
     */
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

    /**
     * Create a quick event from string input.
     * 
     * Should include time (am/pm), day of the week.
     *
     * Day of the week can be specified as "tomorrow"
     *
     * Can specify week as well with "next tuesday"
     *
     * A day without a modifier will default to that current week,
     * even if the day has already past
     *
     * @param String $client - Access token
     * @param String $quickAddText - Event description
     */
	function createQuickEvent($client, $quickAddText){
		$gdataCal = new Zend_Gdata_Calendar($client);
		$event = $gdataCal->newEventEntry();
		$event->content = $gdataCal->newContent($quickAddText);
		$event->quickAdd = $gdataCal->newQuickAdd('true');
		$newEvent = $gdataCal->insertEvent($event);
	}	
}

?>