<?php

class Fedex {

    public $client;
    private $key = 'C25Z7Q5SMoN4o5NH';
    private $password = '4NQWz9B55sqo3S9hLrbAFSMp4';
    private $shipaccount = '276429868';
    private $billaccount = '276429868';
    private $meter = '104513138';

//    private $key = 'Lod4jsmQgsWKB8sp';
//    private $password = 'GPOdtMcBbAcjcBxQkLtSiHRi5';
//    private $shipaccount = '510087461';
//    private $billaccount = '510087461';
//    private $meter = '118510839';

    public function __construct() {
        require_once('Fedex-common.php');
        ini_set("soap.wsdl_cache_enabled", "0");

        $this->request['WebAuthenticationDetail'] = array(
            'UserCredential' => array(
                'Key' => $this->key,
                'Password' => $this->password
            )
        );
        $this->request['ClientDetail'] = array(
            'AccountNumber' => $this->shipaccount,
            'MeterNumber' => $this->meter
        );
        $this->request['TransactionDetail'] = array(
            'CustomerTransactionId' => '1'
        );
    }

    /* ------------------------------------------------- Pick Up --------------------------------------------- */

    public function run_pickup($pack_info) {
        $this->request['TransactionDetail'] = array(
            'CustomerTransactionId' => $pack_info['item_id']
        );

        $this->request['Version'] = array(
            'ServiceId' => 'disp',
            'Major' => 3,
            'Intermediate' => 0,
            'Minor' => 0);

        $ready = explode('-', $pack_info['pickupDate']);
        $readyTime = explode(':', $pack_info['pickupTime']);

        $this->request['OriginDetail'] = array(
            'PickupLocation' => array(
                'Contact' => array(
                    'PersonName' => $pack_info['pickupContactName'],
                    'CompanyName' => $pack_info['pickupCompany'],
                    'PhoneNumber' => $pack_info['pickupPhone1']),
                'Address' => array('StreetLines' => array($pack_info['pickupAddress1']),
                    'City' => $pack_info['pickupCity'],
                    'StateOrProvinceCode' => $pack_info['pickupState'],
                    'PostalCode' => $pack_info['pickupZip'],
                    'CountryCode' => $pack_info['pickupCountryCode']),
            ),
            'PackageLocation' => 'FRONT', // valid values NONE, FRONT, REAR and SIDE
            'BuildingPartCode' => $pack_info['packLocation'], // valid values APARTMENT, BUILDING, DEPARTMENT, SUITE, FLOOR and ROOM
            'BuildingPartDescription' => $pack_info['partDescr'],
            'ReadyTimestamp' => mktime($readyTime[0], $readyTime[1], 0, $ready[1], $ready[2], $ready[0]), // Replace with your ready date time
            'CompanyCloseTime' => $pack_info['pickupLatest']);

        $this->request['PackageCount'] = $pack_info['countPackages'];
        $this->request['TotalWeight'] = array(
            'Value' => floatval($pack_info['pickupWeight']),
            'Units' => 'LB'
        );
        $this->request['CarrierCode'] = $pack_info['pickupSchedule']; // valid values FDXE-Express, FDXG-Ground, FDXC-Cargo, FXCC-Custom Critical and FXFR-Freight
        $this->request['CourierRemarks'] = $pack_info['pickupSpecial'];

        $path_to_wsdl = "application/files/fedex/wsdl/PickupService_v3/PickupService_v3.wsdl";
        $client = new SoapClient($path_to_wsdl, array('trace' => 1));

        ob_start();
        $send_rez = NULL;

        try {
            if (Fedex_Common::setEndpoint('changeEndpoint')) {
                $newLocation = $client->__setLocation(setEndpoint('endpoint'));
            }
            $response = $client->createPickup($this->request);
            if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
                echo 'Pickup confirmation number is: ' . $response->PickupConfirmationNumber . '<br>';
                echo 'Location: ' . $response->Location . '<br>';
                $send_rez = true;
//                Fedex_Common::printSuccess($client, $response);
            } else {
                Fedex_Common::printError($client, $response);
            }
//            writeToLog($client);    // Write to log file   
        } catch (SoapFault $exception) {
            Fedex_Common::printFault($exception, $client);
            Fedex_Common::printSuccess($client, $response);
        }

        $content = ob_get_contents();
        ob_end_clean();

        if ($send_rez) {
            $return = array('ok' => $content);

            $cookies = new Cookie();
            $admin = $cookies->get('admin_user');
            if (!empty($admin)) {
                $admin = unserialize($admin);
            }
            $note_message = 'Pick Up request been sent: #'.$response->PickupConfirmationNumber;
            $job = DB::sql_row('SELECT * FROM requests WHERE id=:id', array(':id' => $pack_info['item_id']));
            //add note
            DB::sql('INSERT INTO request_notes (request_id, `text`, `date`, author_id, job_id, type_user, company_id) VALUES (:id, :text, NOW(), :author_id, :job_id, "sys", :company_id)', array(
                ':id' => $pack_info['item_id'], ':text' => $note_message, ':author_id' => $admin['id'], ':job_id' => $job['job_id'], ':company_id'=>$job['company_id']));
            //add event
            Model::factory('Admin_Event')->add_event('fedex_pickup', $pack_info['item_id'], 'New Pick Up. Confirmation number '.$response->PickupConfirmationNumber);
        } else {
            $return = array('err' => $content);
        }
        return $return;
    }

    /* ------------------------------------------------ Open Shipment ---------------------------------------- */

    /*
     * fedex Pickup
     */

    public function run_ship($pack_info) {

        $this->request['TransactionDetail'] = array(
            'CustomerTransactionId' => $pack_info['item_id']
        );
        $session = Session::instance();
        $this->request['Version'] = array(
            'ServiceId' => 'ship',
            'Major' => '12',
            'Intermediate' => '1',
            'Minor' => '0'
        );

        if ($pack_info['billingData'] == 'SENDER') {
            $billing_account = $this->billaccount;
        } else {
            $billing_account = $pack_info['billingAccountNo'];
        }

        $date = explode('-', $pack_info['shipDate']);
        $date = date_create($date[2] . '-' . $date[0] . '-' . $date[1]);
        $ShipTimestamp = date_format($date, 'c');

        $this->request['RequestedShipment'] = array(
            'ShipTimestamp' => $ShipTimestamp,
            'DropoffType' => 'REGULAR_PICKUP',
            'ServiceType' => $pack_info['serviceType'],
            'PackagingType' => $pack_info['packageType'],
            /* Shipper Details */
            'Shipper' => array(
                'Contact' => array(
                    'PersonName' => $pack_info['send1ContactName'],
                    'CompanyName' => $pack_info['send1Company'],
                    'PhoneNumber' => $pack_info['send1Phone1']),
                'Address' => array(
                    'StreetLines' => array($pack_info['send1Address1']),
                    'City' => $pack_info['send1City'],
                    'StateOrProvinceCode' => $pack_info['send1State'],
                    'PostalCode' => $pack_info['send1Zip'],
                    'CountryCode' => $pack_info['send1CountryCode'],
                )
            ),
            /* Client Details */
            'Recipient' => array(
                'Contact' => array(
                    'PersonName' => $pack_info['send2ContactName'],
                    'CompanyName' => $pack_info['send2Company'],
                    'PhoneNumber' => $pack_info['send2Phone1'],
                ),
                'Address' => array(
                    'StreetLines' => array($pack_info['send2Address1']),
                    'City' => $pack_info['send2City'],
                    'PostalCode' => $pack_info['send2Zip'],
                    'StateOrProvinceCode' => $pack_info['send2State'],
                    'CountryCode' => $pack_info['send1CountryCode'],
                    'Residential' => (!empty($pack_info['send2Resident'])) ? true : false
                )
            ),
            /* Package */
            'RateRequestTypes' => array('LIST'), // valid values ACCOUNT and LIST
            'PackageCount' => $pack_info['shipPackages'],
            'RequestedPackageLineItems' => array(
                'SequenceNumber' => 1, //$pack_info['item_id']
                'Weight' => array(
                    'Value' => floatval($pack_info['packWeight']),
                    'Units' => 'LB'),
                'Dimensions' => array(
                    'Length' => floatval($pack_info['dimensionL']),
                    'Width' => floatval($pack_info['dimensionW']),
                    'Height' => floatval($pack_info['dimensionH']),
                    'Units' => 'IN')
            ),
            /* Billing */
            'ShippingChargesPayment' => array(
                'PaymentType' => $pack_info['billingData'],
                'Payor' => array(
                    'ResponsibleParty' => array(
                        'AccountNumber' => $billing_account,
                        'Contact' => '', //$pack_info['billingReference']
                        'Address' => array('CountryCode' => 'US')
                    )
                )
            ),
            'CustomerSpecifiedDetail' => array(
                'MaskedData' => $this->shipaccount
            ),
            /* Label */
            'LabelSpecification' => array(
                'LabelFormatType' => 'COMMON2D',
                'ImageType' => 'PNG',
                'LabelStockType' => 'PAPER_7X4.75'
            ),
            /* Special Services */
            'SpecialServiceTypes' => array('EMAIL_NOTIFICATION'),
            'EMailNotificationDetail' => array(
                'PersonalMessage' => 'PersonalMessage',
                'Recipients' => $this->getEmailNotifications($pack_info)
            ),
            'ReturnShipmentDetail' => array(
                'ReturnType' => 'PENDING',
                'ReturnEMailDetail' => array(
                    'MerchantPhoneNumber' => $pack_info['send1Phone1'],
                    'AllowedSpecialServices' => 'SATURDAY_DELIVERY')
            ),
            'PendingShipmentDetail' => array(
                'Type' => 'EMAIL',
                'ExpirationDate' => date('Y-m-d'),
                'EmailLabelDetail' => array(
                    'NotificationEMailAddress' => $pack_info['SenderEmail'],
                    'NotificationMessage' => 'message')
            ),
        );

        if ($pack_info['serviceType'] == 'SMART_POST') {
            $this->request['RequestedShipment']['SmartPostDetail'] = array(
                'Indicia' => $pack_info['Indicia'],
                'AncillaryEndorsement' => $pack_info['AncillaryEndorsement'],
                'SpecialServices' => 'USPS_DELIVERY_CONFIRMATION',
                'HubId' => '5303'
            );
        }
        $this->request['RequestedShipment']['SmartPostDetail'] = array(
            'Indicia' => 'MEDIA_MAIL',
            'AncillaryEndorsement' => 'CARRIER_LEAVE_IF_NO_RESPONSE',
                //'HubId' => '0000'
        );

        /* Signature */
        $this->request['SignatureOptionDetail'] = array(
            'OptionType' => $pack_info['signatureType']
        );

        $this->add_specServ($pack_info);

        ob_start();
        $send_rez = NULL;
        try {
            $path_to_wsdl = "application/files/fedex/wsdl/ShipService_v12/ShipService_v12.wsdl";
            $this->client = new SoapClient($path_to_wsdl, array('trace' => 1));

            if (Fedex_Common::setEndpoint('changeEndpoint')) {
                $newLocation = $this->client->__setLocation(Fedex_Common::setEndpoint('endpoint'));
            }
            $response = $this->client->processShipment($this->request);
            if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
                $url_label = '/files/fedex/png/' . $pack_info['item_id'] . '.png';
                $send_rez = true;

                //update DB
                $track_num = $response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;
                $track_type = $response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingIdType;

                if (!empty($response->CompletedShipmentDetail->SmartPostDetail->SmartPostId)) {
                    $usps = $response->CompletedShipmentDetail->SmartPostDetail->SmartPostId;
                }
                $usps_field = (!empty($usps)) ? $usps : '';

//                //old DB
//                $db_fedex = Database::instance('ink_fedex');
//                DB::query(Database::UPDATE, 'UPDATE requests SET processeddate=NOW(), tracking_number="USPS:' . $usps_field . ',' . $track_type . ':' . $track_num . '" WHERE id="' . $pack_info['item_id'] . '"')->execute($db_fedex);
//                

                $additional_request = '';
                //new DB
                $sent = DB::sql_row('SELECT tracking_number FROM requests WHERE id=:id', array(':id' => $pack_info['item_id']));
                if (empty($sent['tracking_number'])) {
                    DB::sql('UPDATE requests SET processed_date=NOW(), tracking_number="USPS:' . $usps_field . ',' . $track_type . ':' . $track_num . '" WHERE id="' . $pack_info['item_id'] . '"');
                    $ship_label = 'application/files/fedex/png/' . $pack_info['item_id'] . '.png';
                } else {
                    $r = DB::sql('INSERT INTO requests_more_sent (req_id,processed_date,tracking_number) VALUES (:req_id,NOW(),:tracking_number)', array(':req_id' => $pack_info['item_id'], ':tracking_number' => 'USPS:' . $usps_field . ',' . $track_type . ':' . $track_num));
                    $last_id = $r[0];
                    $ship_label = 'application/files/fedex/png/' . $pack_info['item_id'] . '_' . $last_id . '.png';
                    $additional_request = 'Additional Request. Label: ' . $pack_info['item_id'] . '_' . $last_id;
                }
                //save label
                $fp = fopen($ship_label, 'wb');
                fwrite($fp, ($response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image));
                fclose($fp);
                echo 'Label ' . $pack_info['item_id'] . ' was generated.<br><br>
                    <a href="" class="magentaBtn button_small">Continue</a><br><hr>
                    <a href="/admin/sales/fedex_labelslist">Print All Lables</a>
                ';
                $fedex_send = $session->get('fedex_send');
                $fedex_labels_list = $session->get('fedex_labels_list');
                $fedex_labels_list[] = array_shift($fedex_send);
                $session->set('fedex_send', $fedex_send);
                $session->set('fedex_labels_list', $fedex_labels_list);
            } else {

                Fedex_Common::printError($this->client, $response);
            }
            Fedex_Common::writeToLog($this->client);
        } catch (SoapFault $exception) {
            Fedex_Common::printFault($exception, $this->client);
        }
        $content = ob_get_contents();
        ob_end_clean();

        if ($send_rez) {
            $return = array('ok' => $content);

            $cookies = new Cookie();
            $admin = $cookies->get('admin_user');
            if (!empty($admin)) {
                $admin = unserialize($admin);
            }

            $mess_track = '';
            if (!empty($usps_field) || !empty($track_num)) {
                if (!empty($usps_field)) {
                    $mess_track = 'Tracking Number: ' . $usps_field . ', ' . $track_type . ':' . $track_num;
                } else {
                    $mess_track = 'Tracking Number: ' . $track_type . ':' . $track_num;
                }
            } 
            $note_message = 'Sample pack request been sent. ' . $mess_track . ' ' . $additional_request;
            $job = DB::sql_row('SELECT * FROM requests WHERE id=:id', array(':id' => $pack_info['item_id']));
            if (!empty($job)) {
                //add note
                DB::sql('INSERT INTO request_notes (request_id, `text`, `date`, author_id, job_id, type_user, `type`, company_id) VALUES (:id, :text, NOW(), :author_id, :job_id, "sys", "shipping_out", :company_id)', array(
                    ':id' => $pack_info['item_id'], ':text' => $note_message, ':author_id' => $admin['id'], ':job_id' => $job['job_id'], ':company_id'=>$job['company_id']));

                //add event
                Model::factory('Admin_Event')->add_event('fedex_ship', $pack_info['item_id'], 'New Shipment. '.$mess_track);
            }
        } else {
            $return = array('err' => $content);
        }
        return $return;
    }

    public function add_specServ($pack_info) {
        if (!empty($pack_info['specServCOD'])) {
            $this->request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'COD';
            $this->request['RequestedShipment']['SpecialServicesRequested']['CodDetail'] = array(
                'CodCollectionAmount' => array(
                    'Currency' => 'USD',
                    'Amount' => $pack_info['CodAmount']
                ),
                'CollectionType' => $pack_info['CollectionType']);
        }

        if (!empty($pack_info['specServHold'])) {
            $this->request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'HOLD_AT_LOCATION';
            $this->request['RequestedShipment']['SpecialServicesRequested']['HoldAtLocationDetail'] = array(
                'PhoneNumber' => $pack_info['holdPhoneRez'],
                'LocationContactAndAddress' => array(
                    'Contact' => $pack_info['holdCompanyRez'],
                    'Address' => $pack_info['holdStreetRez'] . ', ' . $pack_info['holdStateRez'] . ', ' . $pack_info['holdZipRez'] . ', ' . $pack_info['holdCountryRez']
                )
            );
        }
    }

    public function getEmailNotifications($pack_info) {
        $arr = array();
        if (!empty($pack_info['SenderEmail'])) {
            $sendTypes = array();
            if (!empty($pack_info['SenderTypeShip'])) {
                $sendTypes[] = 'ON_SHIPMENT';
            }
            if (!empty($pack_info['SenderTypeTendered'])) {
                $sendTypes[] = 'ON_TENDER';
            }
            if (!empty($pack_info['SenderTypeException'])) {
                $sendTypes[] = 'ON_EXCEPTION';
            }
            if (!empty($pack_info['SenderTypeDelivery'])) {
                $sendTypes[] = 'ON_DELIVERY';
            }
            if (!empty($sendTypes)) {
                $arr[] = array('EMailNotificationRecipientType' => 'SHIPPER',
                    'EMailAddress' => $pack_info['SenderEmail'],
                    'Format' => $pack_info['messType'],
                    'Localization' => array('LanguageCode' => $pack_info['SenderEmailLang'], 'LocaleCode' => $pack_info['SenderEmailLang']),
                    'NotificationEventsRequested' => $sendTypes
                );
            }
        }
        if (!empty($pack_info['RecipientEmail'])) {
            $sendTypes = array();
            if (!empty($pack_info['RecipientTypeShip'])) {
                $sendTypes[] = 'ON_SHIPMENT';
            }
            if (!empty($pack_info['RecipientTypeTendered'])) {
                $sendTypes[] = 'ON_TENDER';
            }
            if (!empty($pack_info['RecipientTypeException'])) {
                $sendTypes[] = 'ON_EXCEPTION';
            }
            if (!empty($pack_info['RecipientTypeDelivery'])) {
                $sendTypes[] = 'ON_DELIVERY';
            }
            if (!empty($sendTypes)) {
                $arr[] = array('EMailNotificationRecipientType' => 'RECIPIENT',
                    'EMailAddress' => $pack_info['RecipientEmail'],
                    'Format' => $pack_info['messType'],
                    'Localization' => array('LanguageCode' => $pack_info['RecipientEmailLang'], 'LocaleCode' => $pack_info['RecipientEmailLang']),
                    'NotificationEventsRequested' => $sendTypes
                );
            }
        }
        if (!empty($pack_info['OtherEmail'])) {
            $sendTypes = array();
            if (!empty($pack_info['OtherTypeShip'])) {
                $sendTypes[] = 'ON_SHIPMENT';
            }
            if (!empty($pack_info['OtherTypeTendered'])) {
                $sendTypes[] = 'ON_TENDER';
            }
            if (!empty($pack_info['OtherTypeException'])) {
                $sendTypes[] = 'ON_EXCEPTION';
            }
            if (!empty($pack_info['OtherTypeDelivery'])) {
                $sendTypes[] = 'ON_DELIVERY';
            }
            if (!empty($sendTypes)) {
                $arr[] = array('EMailNotificationRecipientType' => 'OTHER',
                    'EMailAddress' => $pack_info['OtherEmail'],
                    'Format' => $pack_info['messType'],
                    'Localization' => array('LanguageCode' => $pack_info['OtherEmailLang'], 'LocaleCode' => $pack_info['OtherEmailLang']),
                    'NotificationEventsRequested' => $sendTypes
                );
            }
        }
        return $arr;
    }

    /* ----------------------------------------- Fedex Rate -------------------------------------------- */

    public function calculate_rate($pack_info) {
        $this->request['Version'] = array(
            'ServiceId' => 'crs',
            'Major' => '13',
            'Intermediate' => '0',
            'Minor' => '0'
        );

        $date = explode('-', $pack_info['shipDate']);
        $date = date_create($date[2] . '-' . $date[0] . '-' . $date[1]);
        $ShipTimestamp = date_format($date, 'c');

        $this->request['ReturnTransitAndCommit'] = true;
        $this->request['RequestedShipment'] = array(
            'DropoffType' => 'REGULAR_PICKUP',
            'ShipTimestamp' => $ShipTimestamp,
            'ServiceType' => $pack_info['serviceType'],
            'PackagingType' => $pack_info['packageType'],
            'TotalInsuredValue' => array(
                'Ammount' => $pack_info['price'],
                'Currency' => 'USD'
            ),
            'Shipper' => array(
                'Contact' => array(
                    'PersonName' => $pack_info['send1ContactName'],
                    'CompanyName' => $pack_info['send1Company'],
                    'PhoneNumber' => $pack_info['send1Phone1']),
                'Address' => array(
                    'StreetLines' => array($pack_info['send1Address1']),
                    'City' => $pack_info['send1City'],
                    'StateOrProvinceCode' => $pack_info['send1State'],
                    'PostalCode' => $pack_info['send1Zip'],
                    'CountryCode' => $pack_info['send1CountryCode'],
                )
            ),
            'Recipient' => array(
                'Contact' => array(
                    'PersonName' => $pack_info['send2ContactName'],
                    'CompanyName' => $pack_info['send2Company'],
                    'PhoneNumber' => $pack_info['send2Phone1'],
                ),
                'Address' => array(
                    'StreetLines' => array($pack_info['send2Address1']),
                    'City' => $pack_info['send2City'],
                    'PostalCode' => $pack_info['send2Zip'],
                    'StateOrProvinceCode' => $pack_info['send2State'],
                    'CountryCode' => $pack_info['send1CountryCode'],
                    'Residential' => (!empty($pack_info['send2Resident'])) ? true : false
                )
            ),
            'ShippingChargesPayment' => array(
                'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
                'Payor' => array(
                    'ResponsibleParty' => array(
                        'AccountNumber' => $this->billaccount,
                        'CountryCode' => 'US')
                )
            ),
            'RateRequestTypes' => 'LIST',
            'PackageCount' => $pack_info['shipPackages'],
            'RequestedPackageLineItems' => array(
                'SequenceNumber' => 1,
                'GroupPackageCount' => 1,
                'Weight' => array(
                    'Value' => floatval($pack_info['packWeight']),
                    'Units' => 'LB'),
                'Dimensions' => array(
                    'Length' => floatval($pack_info['dimensionL']),
                    'Width' => floatval($pack_info['dimensionW']),
                    'Height' => floatval($pack_info['dimensionH']),
                    'Units' => 'IN')
            )
        );

        ob_start();

        try {
            $path_to_wsdl = "application/files/fedex/wsdl/RateService_v13/RateService_v13.wsdl";
            $this->client = new SoapClient($path_to_wsdl, array('trace' => 1));

            if (Fedex_Common::setEndpoint('changeEndpoint')) {
                $newLocation = $this->client->__setLocation(setEndpoint('endpoint'));
            }
            $send_rez = false;
            $response = $this->client->getRates($this->request);
            if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
                $rateReply = $response->RateReplyDetails;
                echo '<table border="1" class="activity_datatable">';
                echo '<tr><td></td>
                      <td><strong>Service Type</strong></td>
                      <td><strong>Amount</strong></td>
                      <td><strong>Delivery Date</strong></td></tr><tr>';

                $serviceType = '<td><input type="radio" name="fedexRate" value="' . $rateReply->ServiceType . '"></td>
                                <td>' . $rateReply->ServiceType . '</td>';

                $amount = '<td>$' . number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount, 2, ".", ",") . '</td>';

                if (array_key_exists('DeliveryTimestamp', $rateReply)) {
                    $deliveryDate = '<td>' . $rateReply->DeliveryTimestamp . '</td>';
                } else if (array_key_exists('TransitTime', $rateReply)) {
                    $deliveryDate = '<td>' . $rateReply->TransitTime . '</td>';
                } else {
                    $deliveryDate = '<td>&nbsp;</td>';
                }
                echo $serviceType . $amount . $deliveryDate;
                echo '</tr>';
                echo '</table>';
                $send_rez = true;
            } else {
                Fedex_Common::printError($this->client, $response);
            }
        } catch (SoapFault $exception) {
            Fedex_Common::printFault($exception, $this->client);
        }
        $content = ob_get_contents();
        ob_end_clean();
        if ($send_rez) {
            $return = array('ok' => $content);
        } else {
            $return = array('err' => $content);
        }
        return $return;
    }

    /* ------------------------------------ Search Fedex Location ------------------------------------ */

    public function find_location($data) {
        $path_to_wsdl = "application/files/fedex/wsdl/GlobalShipAddress/GlobalShipAddressService_v1.wsdl";

        $this->client = new SoapClient($path_to_wsdl, array('trace' => 1));
        $this->request['Version'] = array(
            'ServiceId' => 'gsai',
            'Major' => '1',
            'Intermediate' => '0',
            'Minor' => '0'
        );

        $this->request['EffectiveDate'] = date('Y-m-d');

        if ($data['type'] == 'ship') {
            $address = array('StreetLines' => $data['send2Address1'],
                'City' => $data['send2City'],
                'StateOrProvinceCode' => $data['send2State'],
                'PostalCode' => $data['send2Zip'],
                'CountryCode' => $data['send2CountryCode']);
            ;
        } else {
            $address = array('StreetLines' => $data['send1Address1'],
                'City' => $data['send1City'],
                'StateOrProvinceCode' => $data['send1State'],
                'PostalCode' => $data['send1Zip'],
                'CountryCode' => $data['send1CountryCode']);
            ;
        }
        $this->request['LocationsSearchCriterion'] = 'ADDRESS';
        $this->request['Address'] = $address;

        $this->request['MultipleMatchesAction'] = 'RETURN_ALL';
        $this->request['SortDetail'] = array(
            'Criterion' => 'DISTANCE',
            'Order' => 'LOWEST_TO_HIGHEST'
        );
        $this->request['Constraints'] = array(
            'RadiusDistance' => array(
                'Value' => 15.0,
                'Units' => 'KM'
            ),
            'ExpressDropOfTimeNeeded' => '15:00:00.00',
            'ResultFilters' => 'EXCLUDE_LOCATIONS_OUTSIDE_STATE_OR_PROVINCE',
            'SupportedRedirectToHoldServices' => array(
                'FEDEX_EXPRESS', 'FEDEX_GROUND', 'FEDEX_GROUND_HOME_DELIVERY'
            ),
            'RequiredLocationAttributes' => array(
                'ACCEPTS_CASH', 'ALREADY_OPEN'
            ),
            'ResultsRequested' => 5,
            'LocationContentOptions' => array(
                'HOLIDAYS'
            ),
            'LocationTypesToInclude' => array(
                'FEDEX_OFFICE'
            )
        );

        $this->request['DropoffServicesDesired'] = array(
            'Express' => 1, // Location desired services
            'FedExStaffed' => 1,
            'FedExSelfService' => 1,
            'FedExAuthorizedShippingCenter' => 1,
            'HoldAtLocation' => 1);

        try {

            ob_start();
            if (Fedex_Common::setEndpoint('changeEndpoint')) {
                $newLocation = $this->client->__setLocation(Fedex_Common::setEndpoint('endpoint'));
            }
            $response = $this->client->searchLocations($this->request);

            $send_rez = false;
            if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
                echo 'Total Locations Found: ' . $response->TotalResultsAvailable . '<br>';
                echo 'Locations Returned: ' . $response->ResultsReturned . '<br>';

                $elements = $response->AddressToLocationRelationships->DistanceAndLocationDetails;
                echo '<hr>
                    <input type="hidden" name="holdCompanyRez" value="">
                    <input type="hidden" name="holdPhoneRez" value="">
                    <input type="hidden" name="holdStreetRez" value="">
                    <input type="hidden" name="holdStateRez" value="">
                    <input type="hidden" name="holdZipRez" value="">
                    <input type="hidden" name="holdCountryRez" value="">
                <table width="100%">';
                echo'<tr>
                    <td>Select</td>
                    <td>Address</td>
                    <td>Distance</td>
                </tr>';
                if (!empty($elements)) {
                    foreach ($elements as $val) {
                        echo '<tr>
                            <td><input type="radio" name="holdLocation">
                                <input type="hidden" name="holdPhone" value="' . $val->LocationDetail->LocationContactAndAddress->Contact->FaxNumber . '">
                                <input type="hidden" name="holdCompany" value="' . $val->LocationDetail->LocationContactAndAddress->Contact->CompanyName . '">
                                <input type="hidden" name="holdStreet" value="' . $val->LocationDetail->LocationContactAndAddress->Address->StreetLines . '">
                                <input type="hidden" name="holdState" value="' . $val->LocationDetail->LocationContactAndAddress->Address->StateOrProvinceCode . '">
                                <input type="hidden" name="holdZip" value="' . $val->LocationDetail->LocationContactAndAddress->Address->PostalCode . '">
                                <input type="hidden" name="holdCountry" value="' . $val->LocationDetail->LocationContactAndAddress->Address->CountryCode . '">
                            </td>
                            <td>' . $val->LocationDetail->LocationContactAndAddress->Address->StreetLines . ', ' . $val->LocationDetail->LocationContactAndAddress->Address->StateOrProvinceCode . '</td>
                            <td>' . round($val->Distance->Value, 2) . ' MI</td>
                        </tr>';
                    }
                }
//                Fedex_Common::locationDetails($response->AddressToLocationRelationships->MatchedAddress, '');
//                Fedex_Common::locationDetails($response->AddressToLocationRelationships->DistanceAndLocationDetails, '');
                echo '</table>';
                $send_rez = true;
//                Fedex_Common::printSuccess($this->client, $response);
            } else {
                Fedex_Common::printError($this->client, $response);
            }

//            Fedex_Common::writeToLog($this->client);    // Write to log file   
        } catch (SoapFault $exception) {
            Fedex_Common::printFault($exception, $this->client);
        }

        $content = ob_get_contents();
        ob_end_clean();
        if ($send_rez) {
            $return = array('ok' => $content);
        } else {
            $return = array('err' => $content);
        }
        return $return;
    }

    /*
     * Cancel shipment
     */
    
    public function close_ship($trackingnumber) {
        $send_rez = false;
        $path_to_wsdl = "application/files/fedex/wsdl/ShipService_v12/ShipService_v12.wsdl";
        $client = new SoapClient($path_to_wsdl, array('trace' => 1));

        $this->request['Version'] = array(
            'ServiceId' => 'ship',
            'Major' => '12',
            'Intermediate' => '1',
            'Minor' => '0');
        $this->request['ShipTimestamp'] = date('c');
        $this->request['TrackingId'] = array(
            'TrackingIdType' => 'GROUND', // valid values EXPRESS, GROUND, USPS, etc
            'TrackingNumber' => $trackingnumber);
        $this->request['DeletionControl'] = 'DELETE_ONE_PACKAGE'; // Package/Shipment

        try {

            ob_start();
            if (Fedex_Common::setEndpoint('changeEndpoint')) {
                $newLocation = $client->__setLocation(Fedex_Common::setEndpoint('endpoint'));
            }
            $response = $client->deleteShipment($this->request);

            if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
                Fedex_Common::printSuccess($client, $response);
                $send_rez = true;
            } else {
                Fedex_Common::printError($client, $response);
            }
//          Fedex_Common::writeToLog($client);    // Write to log file   
        } catch (SoapFault $exception) {
            Fedex_Common::printFault($exception, $client);
        }

        $content = ob_get_contents();
        ob_end_clean();
        if ($send_rez) {
            $return = array('ok' => $content);
            $cookies = new Cookie();
            $admin = $cookies->get('admin_user');
            if (!empty($admin)) {
                $admin = unserialize($admin);
            }
            
            $note_message = 'Shipment Closed';
            $req = DB::sql_row('SELECT * FROM requests WHERE tracking_number=:track', array(':track' => $trackingnumber));
            if (!empty($req)) {
                //add event
                Model::factory('Admin_Event')->add_event('close_ship', $req['id'], 'Close Shipment. Tracking Number: '.$trackingnumber);
                
                DB::sql('INSERT INTO request_notes (request_id, `text`, `date`, author_id, job_id, type_user, company_id) VALUES (:id, :text, NOW(), :author_id, :job_id, "sys", :company_id)', array(
                    ':id' => $req['id'], ':text' => $note_message, ':author_id' => $admin['id'], ':job_id' => $req['job_id'], ':company_id'=>$req['company_id']));
            }
        } else {
            $return = array('err' => $content);
        }
        return $return;
    }

}

