<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class eBayNotifications extends Controller
{
    function eBayNotifications()
    {
        parent::Controller();
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
    }

    function SubscribeForNotifications()
    {
        //parent::Controller();
        //$this->load->model('Auth_model');
        //$this->Auth_model->VerifyAdmin();

        require($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');


        //Tuk se abonirame za iventi. Za da vidim vsichki iventi, za koito mozem
        //da se zapishem viz - https://developer.ebay.com/devzone/XML/docs/Reference/eBay/types/NotificationEventTypeCodeType.html


        $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
                            <SetNotificationPreferencesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                              <RequesterCredentials>
                                <eBayAuthToken>'.$userToken.'</eBayAuthToken>
                              </RequesterCredentials>
                              <Version>697</Version>
                              <ApplicationDeliveryPreferences>
                                <AlertEmail>mailto://pilot@la-tronics.com</AlertEmail>
                                <AlertEnable>Enable</AlertEnable>
                                <ApplicationEnable>Enable</ApplicationEnable>
                                <ApplicationURL>https://www.la-tronics.com/ebaynotif.php</ApplicationURL>
                                 <DeliveryURLName>https://www.la-tronics.com/ebaynotif.php</DeliveryURLName>
                                <DeviceType>Platform</DeviceType>
                              </ApplicationDeliveryPreferences>
                              <UserDeliveryPreferenceArray>
                                <NotificationEnable>
                                  <EventType>FixedPriceTransaction</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                                <NotificationEnable>
                                  <EventType>EndOfAuction</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                                <NotificationEnable>
                                  <EventType>ItemClosed</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                                <NotificationEnable>
                                  <EventType>ItemRevised</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                                <NotificationEnable>
                                  <EventType>ItemSold</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                                <NotificationEnable>
                                  <EventType>ItemUnsold</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                                <NotificationEnable>
                                  <EventType>ReturnClosed</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                                <NotificationEnable>
                                  <EventType>ReturnCreated</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                                <NotificationEnable>
                                  <EventType>ReturnDelivered</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                                <NotificationEnable>
                                  <EventType>ReturnEscalated</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                                <NotificationEnable>
                                  <EventType>ReturnRefundOverdue</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                                <NotificationEnable>
                                  <EventType>ReturnSellerInfoOverdue</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                                <NotificationEnable>
                                  <EventType>ReturnShipped</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                                <NotificationEnable>
                                  <EventType>ReturnWaitingForSellerInfo</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                                 <NotificationEnable>
                                  <EventType>ItemMarkedShipped</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                                   <NotificationEnable>
                                  <EventType>ItemExtended</EventType>
                                  <EventEnable>Enable</EventEnable>
                                </NotificationEnable>
                               </UserDeliveryPreferenceArray>
                            </SetNotificationPreferencesRequest>';
        
        $verb        = 'SetNotificationPreferences';
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, '697', $this->config->config['ebaysiteid'], $verb);
        $responseXml = $session->sendHttpRequest($requestXmlBody);

        if (stristr($responseXml, 'HTTP 404') || $responseXml == '') 
        {
            echo '<Br>Error in notification subscription';
        }
        else
        {
            $xml = simplexml_load_string($responseXml);
            printcool($xml);
        }
    }

    function GetNotifications()
    {
            require($this->config->config['ebaypath'].'get-common/keys.php');
		    require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

             $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
                                <GetNotificationPreferencesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                                <RequesterCredentials>
                                <eBayAuthToken>'.$userToken.'</eBayAuthToken>
                                </RequesterCredentials>
                                <Version>697</Version>
                                <PreferenceLevel>Application</PreferenceLevel>
                                </GetNotificationPreferencesRequest>';

            $verb        = 'GetNotificationPreferences';
            $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, '697', $this->config->config['ebaysiteid'], $verb);
            $responseXml = $session->sendHttpRequest($requestXmlBody);

        if (stristr($responseXml, 'HTTP 404') || $responseXml == '') 
        {
            echo '<Br>Error in notification check';
        }
        else
        {
            $xml = simplexml_load_string($responseXml);
            printcool($xml);
        }
    }

    function GetNotificationsUsage()  //RETURNS ERROR
    {
        require($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
        
        // <eBayAuthToken>'.$userToken.'</eBayAuthToken>

        $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
                            <GetNotificationsUsageRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                              <EndTime></EndTime>
                              <ItemID></ItemID>
                              <StartTime></StartTime>
                            <ErrorLanguage> string </ErrorLanguage>
                              <MessageID> string </MessageID>
                              <Version>697</Version>
                              <WarningLevel> string </WarningLevel>
                            </GetNotificationsUsageRequest>';

        $verb        = 'GetNotificationsUsage';
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, '697', $this->config->config['ebaysiteid'], $verb);
        $responseXml = $session->sendHttpRequest($requestXmlBody);

        if (stristr($responseXml, 'HTTP 404') || $responseXml == '') 
        {
            echo '<Br>Error in notification Usage check';
        }
        else
        {
            $xml = simplexml_load_string($responseXml);
            printcool($xml);
        }

    }
}