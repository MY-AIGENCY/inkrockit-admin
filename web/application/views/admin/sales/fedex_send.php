<div id="activity_stats">
    <h3>Create a Shipment</h3>
</div> 

<form method="POST" name="item_edit" <?php if (!empty($_GET['pickup'])) echo 'style="display:none"' ?>>
    <?php
    if (!empty($err)) {
        ?><div class="msgbar msg_Error hide_onC">
            <p><?= $err ?></p>
        </div>
        <br class="clear"><?php
}
    ?>

    <div class="left">
        <div class="col300">
            <label><h6>Type:</h6></label>
            <select name="type" class="long">
                <option value="ship">Ship</option>
                <option value="pickup">Pick Up</option>
            </select>
            <br class="clear"><br>
        </div>

        <div class="col300">
            <a class="save_fedex_autofill right" data-type="ship_from">Save as Template</a><br class="clear">
            <h4 class="from_block left">1. From</h4>
            <select name="fedex_autofill" class="right" data-type="ship_from">
                <option value="">Autofill with...</option>
                <?php
                if(!empty($autofill['ship_from'])){
                    foreach ($autofill['ship_from'] as $val){
                        ?><option value="<?=$val['id']?>"><?=$val['title']?></option><?php
                    }
                }
                ?>
            </select>
            <br class="clear">
            <hr>
            
            <label>Country/Location*</label>
            <select name="send1CountryCode" class="long">
                <option value="">Select</option><option value="AF">Afghanistan</option><option value="AL">Albania</option><option value="DZ">Algeria</option><option value="AS">American Samoa</option><option value="AD">Andorra</option><option value="AO">Angola</option><option value="AI">Anguilla</option><option value="AG">Antigua</option><option value="AR">Argentina</option><option value="AM">Armenia</option><option value="AW">Aruba</option><option value="AU">Australia</option><option value="AT">Austria</option><option value="AZ">Azerbaijan</option><option value="BS">Bahamas</option><option value="BH">Bahrain</option><option value="BD">Bangladesh</option><option value="BB">Barbados</option><option value="BAR:AG">Barbuda(Antigua)</option><option value="BY">Belarus</option><option value="BE">Belgium</option><option value="BZ">Belize</option><option value="BJ">Benin</option><option value="BM">Bermuda</option><option value="BT">Bhutan</option><option value="BO">Bolivia</option><option value="BON:BQ">Bonaire(Caribbean Netherlands)</option><option value="BA">Bosnia-Herzegovina</option><option value="BW">Botswana</option><option value="BR">Brazil</option><option value="VG">British Virgin Islands</option><option value="BN">Brunei</option><option value="BG">Bulgaria</option><option value="BF">Burkina Faso</option><option value="BI">Burundi</option><option value="KH">Cambodia</option><option value="CM">Cameroon</option><option value="CA">Canada</option><option value="CAIS:ES">Canary Islands(Spain)</option><option value="CV">Cape Verde</option><option value="BQ">Caribbean Netherlands</option><option value="KY">Cayman Islands</option><option value="TD">Chad</option><option value="CHIS:GB">Channel Islands(United Kingdom)</option><option value="CL">Chile</option><option value="CN">China</option><option value="CO">Colombia</option><option value="CG">Congo</option><option value="CD">Congo, Democratic Republic of</option><option value="CK">Cook Islands</option><option value="CR">Costa Rica</option><option value="HR">Croatia</option><option value="CW">Curacao</option><option value="CY">Cyprus</option><option value="CZ">Czech Republic</option><option value="DK">Denmark</option><option value="DJ">Djibouti</option><option value="DM">Dominica</option><option value="DO">Dominican Republic</option><option value="TL">East Timor</option><option value="EC">Ecuador</option><option value="EG">Egypt</option><option value="SV">El Salvador</option><option value="ENG:GB">England(United Kingdom)</option><option value="GQ">Equatorial Guinea</option><option value="ER">Eritrea</option><option value="EE">Estonia</option><option value="ET">Ethiopia</option><option value="FO">Faroe Islands</option><option value="FJ">Fiji</option><option value="FI">Finland</option><option value="FR">France</option><option value="GF">French Guiana</option><option value="PF">French Polynesia</option><option value="GA">Gabon</option><option value="GM">Gambia</option><option value="GE">Georgia</option><option value="DE">Germany</option><option value="GH">Ghana</option><option value="GI">Gibraltar</option><option value="GRCA:KY">Grand Cayman(Cayman Islands)</option><option value="GRBR:GB">Great Britain(United Kingdom)</option><option value="GRTH:VG">Great Thatch Islands(British Virgin Islands)</option><option value="GRTO:VG">Great Tobago Islands(British Virgin Islands)</option><option value="GR">Greece</option><option value="GL">Greenland</option><option value="GD">Grenada</option><option value="GP">Guadeloupe</option><option value="GU">Guam</option><option value="GT">Guatemala</option><option value="GN">Guinea</option><option value="GY">Guyana</option><option value="HT">Haiti</option><option value="HN">Honduras</option><option value="HK">Hong Kong</option><option value="HU">Hungary</option><option value="IS">Iceland</option><option value="IN">India</option><option value="ID">Indonesia</option><option value="IQ">Iraq</option><option value="IE">Ireland</option><option value="IL">Israel</option><option value="IT">Italy</option><option value="CI">Ivory Coast</option><option value="JM">Jamaica</option><option value="JP">Japan</option><option value="JO">Jordan</option><option value="JVDI:VG">Jost Van Dyke Islands(British Virgin Islands)</option><option value="KZ">Kazakhstan</option><option value="KE">Kenya</option><option value="KI">Kiribati</option><option value="KW">Kuwait</option><option value="KG">Kyrgyzstan</option><option value="LA">Laos</option><option value="LV">Latvia</option><option value="LB">Lebanon</option><option value="LS">Lesotho</option><option value="LR">Liberia</option><option value="LY">Libya</option><option value="LI">Liechtenstein</option><option value="LT">Lithuania</option><option value="LU">Luxembourg</option><option value="MO">Macau</option><option value="MK">Macedonia</option><option value="MG">Madagascar</option><option value="MW">Malawi</option><option value="MY">Malaysia</option><option value="MV">Maldives</option><option value="ML">Mali</option><option value="MT">Malta</option><option value="MH">Marshall Islands</option><option value="MQ">Martinique</option><option value="MR">Mauritania</option><option value="MU">Mauritius</option><option value="MX">Mexico</option><option value="FM">Micronesia</option><option value="MD">Moldova</option><option value="MC">Monaco</option><option value="MN">Mongolia</option><option value="ME">Montenegro</option><option value="MS">Montserrat</option><option value="MA">Morocco</option><option value="MZ">Mozambique</option><option value="NA">Namibia</option><option value="NR">Nauru</option><option value="NP">Nepal</option><option value="NL">Netherlands</option><option value="NC">New Caledonia</option><option value="NZ">New Zealand</option><option value="NI">Nicaragua</option><option value="NE">Niger</option><option value="NG">Nigeria</option><option value="NU">Niue</option><option value="NOIS:VG">Norman Island(British Virgin Islands)</option><option value="NOIR:GB">Northern Ireland(United Kingdom)</option><option value="MP">Northern Mariana Islands</option><option value="NO">Norway</option><option value="OM">Oman</option><option value="PK">Pakistan</option><option value="PW">Palau</option><option value="PS">Palestine</option><option value="PA">Panama</option><option value="PG">Papua New Guinea</option><option value="PY">Paraguay</option><option value="PE">Peru</option><option value="PH">Philippines</option><option value="PL">Poland</option><option value="PT">Portugal</option><option value="PR">Puerto Rico</option><option value="QA">Qatar</option><option value="RE">Reunion</option><option value="RO">Romania</option><option value="ROT:MP">Rota(Northern Mariana Islands)</option><option value="RU">Russia</option><option value="RW">Rwanda</option><option value="SAB:BQ">Saba(Caribbean Netherlands)</option><option value="SAI:MP">Saipan(Northern Mariana Islands)</option><option value="WS">Samoa</option><option value="SAMA:IT">San Marino(Italy)</option><option value="SA">Saudi Arabia</option><option value="SCO:GB">Scotland(United Kingdom)</option><option value="SN">Senegal</option><option value="RS">Serbia</option><option value="SC">Seychelles</option><option value="SG">Singapore</option><option value="SK">Slovak Republic</option><option value="SI">Slovenia</option><option value="SB">Solomon Islands</option><option value="ZA">South Africa</option><option value="KR">South Korea</option><option value="ES">Spain</option><option value="LK">Sri Lanka</option><option value="STBA:GP">St. Barthelemy(Guadeloupe)</option><option value="STCH:KN">St. Christopher(Saint Kitts And Nevis)</option><option value="STCR:VI">St. Croix Island(U S Virgin Islands)</option><option value="STEU:BQ">St. Eustatius(Caribbean Netherlands)</option><option value="STJO:VI">St. John(U S Virgin Islands)</option><option value="KN">St. Kitts and Nevis</option><option value="LC">St. Lucia</option><option value="SX">St. Maarten</option><option value="MF">St. Martin</option><option value="STTH:VI">St. Thomas(U S Virgin Islands)</option><option value="VC">St. Vincent</option><option value="SR">Suriname</option><option value="SZ">Swaziland</option><option value="SE">Sweden</option><option value="CH">Switzerland</option><option value="SY">Syria</option><option value="TAH:PF">Tahiti(French Polynesia)</option><option value="TW">Taiwan</option><option value="TZ">Tanzania</option><option value="TH">Thailand</option><option value="TIN:MP">Tinian(Northern Mariana Islands)</option><option value="TG">Togo</option><option value="TO">Tonga</option><option value="TOIS:VG">Tortola Island(British Virgin Islands)</option><option value="TT">Trinidad and Tobago</option><option value="TN">Tunisia</option><option value="TR">Turkey</option><option value="TM">Turkmenistan</option><option value="TC">Turks and Caicos Islands</option><option value="TV">Tuvalu</option><option value="UG">Uganda</option><option value="UA">Ukraine</option><option value="UNIS:VC">Union Island(St. Vincent)</option><option value="AE">United Arab Emirates</option><option value="GB">United Kingdom</option><option value="US" selected="selected">United States</option><option value="VI">U.S. Virgin Islands</option><option value="UY">Uruguay</option><option value="UZ">Uzbekistan</option><option value="VU">Vanuatu</option><option value="VACI:IT">Vatican City(Italy)</option><option value="VE">Venezuela</option><option value="VN">Vietnam</option><option value="WAL:GB">Wales(United Kingdom)</option><option value="WF">Wallis and Futuna Islands</option><option value="YE">Yemen</option><option value="ZM">Zambia</option><option value="ZW">Zimbabwe</option>
            </select><br class="clear">
            <label>Company</label>
            <input type="text" name="send1Company" value="InkRockit"><br class="clear">
            <label>Contact name*</label>
            <input type="text" name="send1ContactName" value="Cyrus Tucker"><br class="clear">
            <label>Address 1*</label>
            <input type="text" name="send1Address1" value="205 Springview Dr"><br class="clear">
            <label>ZIP*</label>
            <input type="text" name="send1Zip" value="32773"><br class="clear">
            <label>City*</label>
            <input type="text" name="send1City" value="Sanford"><br class="clear">
            <label>State*</label>
            <input type="text" name="send1State" value="FL"><br class="clear">
            <label>Phone no.*</label>
            <input type="text" name="send1Phone1" value="386.255.9000"> <br class="clear">
            <br class="clear">
        </div>

        <div class="col300">
            <a class="save_fedex_autofill right" data-type="ship_to">Save as Template</a><br class="clear">
            <h4 class="to_block left">2. To</h4>
            <select name="fedex_autofill" class="right" data-type="ship_to">
                <option value="">Autofill with...</option>
                <?php
                if(!empty($autofill['ship_to'])){
                    foreach ($autofill['ship_to'] as $val){
                        ?><option value="<?=$val['id']?>"><?=$val['title']?></option><?php
                    }
                }
                ?>
            </select>
            <br class="clear">
            <hr>
            
            <label>Country/Location*</label>
            <select name="send2CountryCode" class="long">
                <option value="">Select</option><option value="AF">Afghanistan</option><option value="AL">Albania</option><option value="DZ">Algeria</option><option value="AS">American Samoa</option><option value="AD">Andorra</option><option value="AO">Angola</option><option value="AI">Anguilla</option><option value="AG">Antigua</option><option value="AR">Argentina</option><option value="AM">Armenia</option><option value="AW">Aruba</option><option value="AU">Australia</option><option value="AT">Austria</option><option value="AZ">Azerbaijan</option><option value="BS">Bahamas</option><option value="BH">Bahrain</option><option value="BD">Bangladesh</option><option value="BB">Barbados</option><option value="BAR:AG">Barbuda(Antigua)</option><option value="BY">Belarus</option><option value="BE">Belgium</option><option value="BZ">Belize</option><option value="BJ">Benin</option><option value="BM">Bermuda</option><option value="BT">Bhutan</option><option value="BO">Bolivia</option><option value="BON:BQ">Bonaire(Caribbean Netherlands)</option><option value="BA">Bosnia-Herzegovina</option><option value="BW">Botswana</option><option value="BR">Brazil</option><option value="VG">British Virgin Islands</option><option value="BN">Brunei</option><option value="BG">Bulgaria</option><option value="BF">Burkina Faso</option><option value="BI">Burundi</option><option value="KH">Cambodia</option><option value="CM">Cameroon</option><option value="CA">Canada</option><option value="CAIS:ES">Canary Islands(Spain)</option><option value="CV">Cape Verde</option><option value="BQ">Caribbean Netherlands</option><option value="KY">Cayman Islands</option><option value="TD">Chad</option><option value="CHIS:GB">Channel Islands(United Kingdom)</option><option value="CL">Chile</option><option value="CN">China</option><option value="CO">Colombia</option><option value="CG">Congo</option><option value="CD">Congo, Democratic Republic of</option><option value="CK">Cook Islands</option><option value="CR">Costa Rica</option><option value="HR">Croatia</option><option value="CW">Curacao</option><option value="CY">Cyprus</option><option value="CZ">Czech Republic</option><option value="DK">Denmark</option><option value="DJ">Djibouti</option><option value="DM">Dominica</option><option value="DO">Dominican Republic</option><option value="TL">East Timor</option><option value="EC">Ecuador</option><option value="EG">Egypt</option><option value="SV">El Salvador</option><option value="ENG:GB">England(United Kingdom)</option><option value="GQ">Equatorial Guinea</option><option value="ER">Eritrea</option><option value="EE">Estonia</option><option value="ET">Ethiopia</option><option value="FO">Faroe Islands</option><option value="FJ">Fiji</option><option value="FI">Finland</option><option value="FR">France</option><option value="GF">French Guiana</option><option value="PF">French Polynesia</option><option value="GA">Gabon</option><option value="GM">Gambia</option><option value="GE">Georgia</option><option value="DE">Germany</option><option value="GH">Ghana</option><option value="GI">Gibraltar</option><option value="GRCA:KY">Grand Cayman(Cayman Islands)</option><option value="GRBR:GB">Great Britain(United Kingdom)</option><option value="GRTH:VG">Great Thatch Islands(British Virgin Islands)</option><option value="GRTO:VG">Great Tobago Islands(British Virgin Islands)</option><option value="GR">Greece</option><option value="GL">Greenland</option><option value="GD">Grenada</option><option value="GP">Guadeloupe</option><option value="GU">Guam</option><option value="GT">Guatemala</option><option value="GN">Guinea</option><option value="GY">Guyana</option><option value="HT">Haiti</option><option value="HN">Honduras</option><option value="HK">Hong Kong</option><option value="HU">Hungary</option><option value="IS">Iceland</option><option value="IN">India</option><option value="ID">Indonesia</option><option value="IQ">Iraq</option><option value="IE">Ireland</option><option value="IL">Israel</option><option value="IT">Italy</option><option value="CI">Ivory Coast</option><option value="JM">Jamaica</option><option value="JP">Japan</option><option value="JO">Jordan</option><option value="JVDI:VG">Jost Van Dyke Islands(British Virgin Islands)</option><option value="KZ">Kazakhstan</option><option value="KE">Kenya</option><option value="KI">Kiribati</option><option value="KW">Kuwait</option><option value="KG">Kyrgyzstan</option><option value="LA">Laos</option><option value="LV">Latvia</option><option value="LB">Lebanon</option><option value="LS">Lesotho</option><option value="LR">Liberia</option><option value="LY">Libya</option><option value="LI">Liechtenstein</option><option value="LT">Lithuania</option><option value="LU">Luxembourg</option><option value="MO">Macau</option><option value="MK">Macedonia</option><option value="MG">Madagascar</option><option value="MW">Malawi</option><option value="MY">Malaysia</option><option value="MV">Maldives</option><option value="ML">Mali</option><option value="MT">Malta</option><option value="MH">Marshall Islands</option><option value="MQ">Martinique</option><option value="MR">Mauritania</option><option value="MU">Mauritius</option><option value="MX">Mexico</option><option value="FM">Micronesia</option><option value="MD">Moldova</option><option value="MC">Monaco</option><option value="MN">Mongolia</option><option value="ME">Montenegro</option><option value="MS">Montserrat</option><option value="MA">Morocco</option><option value="MZ">Mozambique</option><option value="NA">Namibia</option><option value="NR">Nauru</option><option value="NP">Nepal</option><option value="NL">Netherlands</option><option value="NC">New Caledonia</option><option value="NZ">New Zealand</option><option value="NI">Nicaragua</option><option value="NE">Niger</option><option value="NG">Nigeria</option><option value="NU">Niue</option><option value="NOIS:VG">Norman Island(British Virgin Islands)</option><option value="NOIR:GB">Northern Ireland(United Kingdom)</option><option value="MP">Northern Mariana Islands</option><option value="NO">Norway</option><option value="OM">Oman</option><option value="PK">Pakistan</option><option value="PW">Palau</option><option value="PS">Palestine</option><option value="PA">Panama</option><option value="PG">Papua New Guinea</option><option value="PY">Paraguay</option><option value="PE">Peru</option><option value="PH">Philippines</option><option value="PL">Poland</option><option value="PT">Portugal</option><option value="PR">Puerto Rico</option><option value="QA">Qatar</option><option value="RE">Reunion</option><option value="RO">Romania</option><option value="ROT:MP">Rota(Northern Mariana Islands)</option><option value="RU">Russia</option><option value="RW">Rwanda</option><option value="SAB:BQ">Saba(Caribbean Netherlands)</option><option value="SAI:MP">Saipan(Northern Mariana Islands)</option><option value="WS">Samoa</option><option value="SAMA:IT">San Marino(Italy)</option><option value="SA">Saudi Arabia</option><option value="SCO:GB">Scotland(United Kingdom)</option><option value="SN">Senegal</option><option value="RS">Serbia</option><option value="SC">Seychelles</option><option value="SG">Singapore</option><option value="SK">Slovak Republic</option><option value="SI">Slovenia</option><option value="SB">Solomon Islands</option><option value="ZA">South Africa</option><option value="KR">South Korea</option><option value="ES">Spain</option><option value="LK">Sri Lanka</option><option value="STBA:GP">St. Barthelemy(Guadeloupe)</option><option value="STCH:KN">St. Christopher(Saint Kitts And Nevis)</option><option value="STCR:VI">St. Croix Island(U S Virgin Islands)</option><option value="STEU:BQ">St. Eustatius(Caribbean Netherlands)</option><option value="STJO:VI">St. John(U S Virgin Islands)</option><option value="KN">St. Kitts and Nevis</option><option value="LC">St. Lucia</option><option value="SX">St. Maarten</option><option value="MF">St. Martin</option><option value="STTH:VI">St. Thomas(U S Virgin Islands)</option><option value="VC">St. Vincent</option><option value="SR">Suriname</option><option value="SZ">Swaziland</option><option value="SE">Sweden</option><option value="CH">Switzerland</option><option value="SY">Syria</option><option value="TAH:PF">Tahiti(French Polynesia)</option><option value="TW">Taiwan</option><option value="TZ">Tanzania</option><option value="TH">Thailand</option><option value="TIN:MP">Tinian(Northern Mariana Islands)</option><option value="TG">Togo</option><option value="TO">Tonga</option><option value="TOIS:VG">Tortola Island(British Virgin Islands)</option><option value="TT">Trinidad and Tobago</option><option value="TN">Tunisia</option><option value="TR">Turkey</option><option value="TM">Turkmenistan</option><option value="TC">Turks and Caicos Islands</option><option value="TV">Tuvalu</option><option value="UG">Uganda</option><option value="UA">Ukraine</option><option value="UNIS:VC">Union Island(St. Vincent)</option><option value="AE">United Arab Emirates</option><option value="GB">United Kingdom</option><option value="US" selected="selected">United States</option><option value="VI">U.S. Virgin Islands</option><option value="UY">Uruguay</option><option value="UZ">Uzbekistan</option><option value="VU">Vanuatu</option><option value="VACI:IT">Vatican City(Italy)</option><option value="VE">Venezuela</option><option value="VN">Vietnam</option><option value="WAL:GB">Wales(United Kingdom)</option><option value="WF">Wallis and Futuna Islands</option><option value="YE">Yemen</option><option value="ZM">Zambia</option><option value="ZW">Zimbabwe</option>
            </select><br class="clear">
            <label>Company</label>
            <input type="text" name="send2Company" value="<?= $item['company'] ?>"><br class="clear">
            <label>Contact name*</label>
            <input type="text" name="send2ContactName" value="<?= $item['first_name'] ?> <?= $item['last_name'] ?>"><br class="clear">
            <label>Address 1*</label>
            <input type="text" name="send2Address1" value="<?= $item['street'] ?>"><br class="clear">
            <label>ZIP*</label>
            <input type="text" name="send2Zip" value="<?= $item['zipcode'] ?>"><br class="clear">
            <label>City*</label>
            <input type="text" name="send2City" value="<?= $item['city'] ?>"><br class="clear">
            <label>State*</label>
            <input type="text" name="send2State" value="<?= $item['state'] ?>"><br class="clear">
            <label>Phone no.*</label>
            <input type="text" name="send2Phone1" value="<?= (empty($item['phone']))? '386.255.9000' : $item['phone'] ; ?>"> <br class="clear">
            <br class="clear">
            <input type="checkbox" name="send2Resident" value="1"> <span class="line_inp">This is a residential address</span><br class="clear">
        </div>

        <div class="col300">
            <a class="save_fedex_autofill right" data-type="ship_pack">Save as Template</a><br class="clear">
            <select name="fedex_autofill" class="right" data-type="ship_pack">
                <option value="">Autofill with...</option>
                <?php
                if(!empty($autofill['ship_pack'])){
                    foreach ($autofill['ship_pack'] as $val){
                        ?><option value="<?=$val['id']?>"><?=$val['title']?></option><?php
                    }
                }
                ?>
            </select>
            <h4>3. Package & Shipment Details</h4>
            <br class="clear">
            <hr>           
            
            <label>Ship date*</label>
            <input id="datepicker" type="text" name="shipDate" value="<?= date('m-d-Y') ?>" class="short"><br class="clear"><br class="clear">
            <label>No. of packages*</label>
            <select name="shipPackages" size="1">
                <?php for ($x = 1; $x <= 25; $x++) { ?>
                    <option value="<?= $x ?>"><?= $x ?></option>
                <?php } ?>
            </select><br class="clear">
            <label>Weight*</label>
            <input type="text" name="packWeight" value="<?=empty($packWeight)? 2 : $packWeight ;?>" class="short"> lbs<br class="clear">
            <label>Declared Value*</label>
            <input type="text" name="price" value="0" class="short"> U.S. Dollars<br class="clear">
            <label>Service type*</label>
            <select name="serviceType" class="contentsmall">
                <option value="FEDEX_GROUND">FedEx Ground</option>
                <option value="SMART_POST">FedEx SmartPost</option>
                <option value="FIRST_OVERNIGHT">First Overnight</option>
                <option value="PRIORITY_OVERNIGHT">Priority Overnight</option>
                <option value="STANDARD_OVERNIGHT">Standard Overnight</option>
                <option value="FEDEX_2_DAY_AM">FedEx 2Day AM</option>
                <option value="FEDEX_2_DAY">FedEx 2Day</option>
                <option value="FEDEX_EXPRESS_SAVER">FedEx Express Saver</option>
            </select>
            <br class="clear">
            
            <div class="smart_detail hide">
                <b>Smart Post details</b><br>
                <label>Indicia Type:</label>
                <select name="Indicia">
                    <option value="MEDIA_MAIL">MEDIA MAIL</option>
                    <option value="PARCEL_SELECT">PARCEL SELECT</option>
                    <option value="PRESORTED_BOUND_PRINTED_MATTER">PRESORTED BOUND PRINTED MATTER</option>
                    <option value="PRESORTED_STANDART">PRESORTED STANDART</option>
                    <option value="PARCEL_RETURN">PARCEL RETURN</option>
                </select><br class="clear">
                <label>Undeliverable Pack:</label>
                <select name="AncillaryEndorsement">
                    <option value="ADDRESS_CORRECTION">Address Service Requested</option>
                    <option value="CARRIER_LEAVE_IF_NO_RESPONSE">Carrier leave if No response</option>
                    <option value="CHANGE_SERVICE">Change Service Requested</option>
                    <option value="FORWARDING_SERVICE">Forwarding Service Requested</option>
                    <option value="RETURN_SERVICE">Return Service Requested</option>
                </select><br class="clear">
            </div>
            
            <label>Package type*</label>
            <select name="packageType">
                <option value="YOUR_PACKAGING">Your Packaging</option>
                <option value="FEDEX_ENVELOPE">FedEx Envelope</option>
                <option value="FEDEX_PAK">FedEx Pak</option>
                <option value="FEDEX_BOX">FedEx Box</option>
                <option value="FEDEX_TUBE">FedEx Tube</option>
            </select>
            <br class="clear">
            <div class="dimensions">
                <label>Dimensions</label>
                L:<input type="text" name="dimensionL" value="<?=(empty($maxWidth))? 14 : ceil($maxWidth) ;?>" style="width: 30px">
                W:<input type="text" name="dimensionW" value="<?=(empty($maxHeight))? 10 : ceil($maxHeight) ;?>" style="width: 30px">
                H:<input type="text" name="dimensionH" value="1" style="width: 30px"> IN
            </div>
        </div>

    </div>

    <div class="left">
        <div class="col300">
            <a class="save_fedex_autofill right" data-type="ship_billing">Save as Template</a><br class="clear">
            <h4 class="left">4. Billing Details</h4>
            <select name="fedex_autofill" class="right" data-type="ship_billing">
                <option value="">Autofill with...</option>
                <?php
                if(!empty($autofill['ship_billing'])){
                    foreach ($autofill['ship_billing'] as $val){
                        ?><option value="<?=$val['id']?>"><?=$val['title']?></option><?php
                    }
                }
                ?>
            </select>
            <br class="clear">
            <hr>
            
            <label>Bill transportation to</label>
            <select name="billingData" size="1">
                <option value="SENDER">InkRockit-868</option>
                <option value="RECIPIENT">Recipient</option>
                <option value="THIRD_PARTY">Third party</option>
            </select><br class="clear">
            <div class="hide" id="billaccount">
                <label>Account no.*</label><input type="text" name="billingAccountNo" value="">
            </div>
            
            <label>User ABBR:</label>
            <input type="text" disabled="disabled" readonly="readonly" value="<?=$item['abbr']?>" style="width: 110px; background: #DDD">
        </div>

        <div class="col300">
            <h4>Special Services (optional)</h4><br class="clear">

            <input type="checkbox" name="specServCOD" value="COD"> <span class="line_inp">COD (Collect on Delivery)</span><br class="clear">
            <div class="cod_amount">
                <label>Total COD amount:*</label> <input type="text" class="short" name="CodAmount" value=""> U.S. Dollars<br class="clear">
                <br class="clear">
                <strong style="font-weight: bold; color: #333">Collection Information</strong><br class="clear"><br class="clear">
                <label>Collection type:</label>
                <select name="CollectionType" size="1">
                    <option value="ANY">Any</option>
                    <option value="CASH">Cash</option>
                    <option value="COMPANY_CHECK">Company check</option>
                    <option value="GUARANTEED_FUNDS">Guaranteed funds</option>
                    <option value="PERSONAL_CHECK">Personal check</option>
                </select>
                <br class="clear">
            </div>
            <input type="checkbox" name="specServHold" value="HOLD_AT_LOCATION"> 
            <span class="line_inp">Hold at FedEx location</span>
            <img src="/images/admin/info-loader.gif" class="hide" style="display: none; ">

            <br class="clear">
            <div class="hold_details"></div>
            <hr>
            <br class="clear">
            FedEx® Delivery Signature Options:<br class="clear">
            <select name="signatureType" size="1">
                <option value="NO_SIGNATURE_REQUIRED">No signature required</option>
                <option value="INDIRECT">Indirect signature required</option>
                <option value="DIRECT">Direct signature required</option>
                <option value="ADULT">Adult signature required</option>
            </select>
            <br class="clear">
        </div>


        <div class="col300">
            <h4>Pickup/Drop-off (optional)</h4><br class="clear">
            <em>Alert:</em>
            FedEx Express®, FedEx Express® Freight and FedEx Ground® pickups must be scheduled separately.<br class="clear">

            <input type="checkbox" name="pick_dropp_1" value="on"> <span class="line_inp">Schedule a pickup</span><br class="clear">
            <input type="checkbox" name="pick_dropp_2" value="on"> <span class="line_inp">Drop off package at a FedEx location</span><br class="clear">
            <input type="checkbox" name="pick_dropp_3" value="on" checked="checked"> <span class="line_inp">Use an already scheduled pickup at my location</span><br class="clear">
        </div>


        <div class="col300">
            <h4>E-mail Notifications (optional)</h4><br class="clear">
            <div class="left" style="margin-right: 40px">
                Sender E-mail<br class="clear">
                <input type="text" name="SenderEmail" value="ctucker@inkrockit.com"><br class="clear">
                <select name="SenderEmailLang" size="1">
                    <option value="en">English</option>
                    <option value="zhCN">Chinese(Simplified)</option><option value="zhTW">Chinese(Traditional)</option><option value="zhHK">Chinese(Trad. HKG)</option><option value="da">Danish</option><option value="nl">Dutch</option><option value="fr">French</option><option value="frCA">French(Canada)</option><option value="de">German</option><option value="it">Italian</option><option value="ja">Japanese</option><option value="ko">Korean</option><option value="pt">Portuguese(Latin America)</option><option value="es">Spanish(Latin America)</option><option value="esES">Spanish(Spain)</option><option value="esUS">Spanish(United States)</option><option value="sv">Swedish</option>
                </select>
            </div>
            <div class="left">
                <b>Notification type:</b><br class="clear">
                <input type="checkbox" name="SenderTypeShip" value="on"> <span class="line_inp">Ship</span><br class="clear">
                <input type="checkbox" name="SenderTypeTendered" checked="checked" value="on"> <span class="line_inp">Tendered</span><br class="clear">
                <input type="checkbox" name="SenderTypeException" checked="checked" value="on"> <span class="line_inp">Exception</span><br class="clear">
                <input type="checkbox" name="SenderTypeDelivery" checked="checked" value="on"> <span class="line_inp">Delivery</span><br class="clear">
            </div>
            <br clear="all">
            <hr>

            <div class="left" style="margin-right: 40px">
                Recipient E-mail<br class="clear">
                <input type="text" name="RecipientEmail" value="<?=$item['email']?>"><br class="clear">
                <select name="RecipientEmailLang" size="1">
                    <option value="en">English</option><option value="zhCN">Chinese(Simplified)</option><option value="zhTW">Chinese(Traditional)</option><option value="zhHK">Chinese(Trad. HKG)</option><option value="da">Danish</option><option value="nl">Dutch</option><option value="fr">French</option><option value="frCA">French(Canada)</option><option value="de">German</option><option value="it">Italian</option><option value="ja">Japanese</option><option value="ko">Korean</option><option value="pt">Portuguese(Latin America)</option><option value="es">Spanish(Latin America)</option><option value="esES">Spanish(Spain)</option><option value="esUS">Spanish(United States)</option><option value="sv">Swedish</option>
                </select>
            </div>
            <div class="left">
                <b>Notification type:</b><br class="clear">
                <input type="checkbox" name="RecipientTypeShip" value="on"> <span class="line_inp">Ship</span><br class="clear">
                <input type="checkbox" name="RecipientTypeTendered" checked="checked" value="on"> <span class="line_inp">Tendered</span><br class="clear">
                <input type="checkbox" name="RecipientTypeException" checked="checked" value="on"> <span class="line_inp">Exception</span><br class="clear">
                <input type="checkbox" name="RecipientTypeDelivery" checked="checked" value="on"> <span class="line_inp">Delivery</span><br class="clear">
            </div>
            <br clear="all">
            <hr>

            <div class="left" style="margin-right: 40px">
                Other E-mail<br class="clear">
                <input type="text" name="OtherEmail" value="dtraub@inkrockit.com"><br class="clear">
                <select name="OtherEmailLang" size="1">
                    <option value="en">English</option><option value="zhCN">Chinese(Simplified)</option><option value="zhTW">Chinese(Traditional)</option><option value="zhHK">Chinese(Trad. HKG)</option><option value="da">Danish</option><option value="nl">Dutch</option><option value="fr">French</option><option value="frCA">French(Canada)</option><option value="de">German</option><option value="it">Italian</option><option value="ja">Japanese</option><option value="ko">Korean</option><option value="pt">Portuguese(Latin America)</option><option value="es">Spanish(Latin America)</option><option value="esES">Spanish(Spain)</option><option value="esUS">Spanish(United States)</option><option value="sv">Swedish</option>
                </select>
            </div>
            <div class="left">
                <b>Notification type:</b><br class="clear">
                <input type="checkbox" name="OtherTypeShip" value="on"> <span class="line_inp">Ship</span><br class="clear">
                <input type="checkbox" name="OtherTypeTendered" checked="checked" value="on"> <span class="line_inp">Tendered</span><br class="clear">
                <input type="checkbox" name="OtherTypeException" checked="checked" value="on"> <span class="line_inp">Exception</span><br class="clear">
                <input type="checkbox" name="OtherTypeDelivery" checked="checked" value="on"> <span class="line_inp">Delivery</span><br class="clear">
            </div>
            <br clear="all">
            <hr>
            Select format:<br class="clear">
            <input type="radio" name="messType" checked="checked" value="HTML"> <span class="line_inp">HTML</span><br class="clear">
            <input type="radio" name="messType" value="TEXT"> <span class="line_inp">Text</span><br class="clear">  
            <input type="radio" name="messType" value="WIRELESS"> <span class="line_inp">Wireless</span><br class="clear">
        </div>

        <div class="col300">
            <h4>Rates & Transit Times (optional)</h4><br class="clear">
            <div class="calk_results_err" style="color: red"></div>
            <div class="calk_results"></div>

            <input type="button" value="Calculate" name="calculate">
            <img src="/images/admin/info-loader.gif" class="hide">

        </div>

        <div class="col300">
            <h4>5. Complete your Shipment</h4><br class="clear"><br>
            <?php
            if(!empty($item['processed_date'])){
                ?>
                <b style="background: #ff849b; padding: 10px 20px; font-size: 14px;">Attention, the package was sent earlier!</b>
                <?php
            }
            ?>
            <br clear="all"><br class="clear">
            
            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
            <input type="submit" name="send" value="Ship" class="right dblueBtn button_small">

            <br clear="all">
        </div>

    </div>
    <br class="clear">
</form>








<form action="" method="post" <?php if (empty($_GET['pickup'])) echo 'style="display:none"' ?>>
    <div class="left">
        <div class="col300">
            <label><h6>Type:</h6></label>
            <select name="type" class="long">
                <option value="ship">Ship</option>
                <option value="pickup" selected="selected">Pick Up</option>
            </select>
            <br class="clear">
            <br>
        </div>

        <div class="col300">
            <a class="save_fedex_autofill right" data-type="pickup_address">Save as Template</a><br class="clear">
            <h6 class="left">1. Pickup Address</h6>
            <select name="fedex_autofill" class="right" data-type="pickup_address">
                <option value="">Autofill with...</option>
                <?php
                if(!empty($autofill['pickup_address'])){
                    foreach ($autofill['pickup_address'] as $val){
                        ?><option value="<?=$val['id']?>"><?=$val['title']?></option><?php
                    }
                }
                ?>
            </select>
            <br class="clear">
            <hr>
            
            <label>Country/Location*</label>
            <select name="pickupCountryCode" class="long">
                <option value="">Select</option><option value="AF">Afghanistan</option><option value="AL">Albania</option><option value="DZ">Algeria</option><option value="AS">American Samoa</option><option value="AD">Andorra</option><option value="AO">Angola</option><option value="AI">Anguilla</option><option value="AG">Antigua</option><option value="AR">Argentina</option><option value="AM">Armenia</option><option value="AW">Aruba</option><option value="AU">Australia</option><option value="AT">Austria</option><option value="AZ">Azerbaijan</option><option value="BS">Bahamas</option><option value="BH">Bahrain</option><option value="BD">Bangladesh</option><option value="BB">Barbados</option><option value="BAR:AG">Barbuda(Antigua)</option><option value="BY">Belarus</option><option value="BE">Belgium</option><option value="BZ">Belize</option><option value="BJ">Benin</option><option value="BM">Bermuda</option><option value="BT">Bhutan</option><option value="BO">Bolivia</option><option value="BON:BQ">Bonaire(Caribbean Netherlands)</option><option value="BA">Bosnia-Herzegovina</option><option value="BW">Botswana</option><option value="BR">Brazil</option><option value="VG">British Virgin Islands</option><option value="BN">Brunei</option><option value="BG">Bulgaria</option><option value="BF">Burkina Faso</option><option value="BI">Burundi</option><option value="KH">Cambodia</option><option value="CM">Cameroon</option><option value="CA">Canada</option><option value="CAIS:ES">Canary Islands(Spain)</option><option value="CV">Cape Verde</option><option value="BQ">Caribbean Netherlands</option><option value="KY">Cayman Islands</option><option value="TD">Chad</option><option value="CHIS:GB">Channel Islands(United Kingdom)</option><option value="CL">Chile</option><option value="CN">China</option><option value="CO">Colombia</option><option value="CG">Congo</option><option value="CD">Congo, Democratic Republic of</option><option value="CK">Cook Islands</option><option value="CR">Costa Rica</option><option value="HR">Croatia</option><option value="CW">Curacao</option><option value="CY">Cyprus</option><option value="CZ">Czech Republic</option><option value="DK">Denmark</option><option value="DJ">Djibouti</option><option value="DM">Dominica</option><option value="DO">Dominican Republic</option><option value="TL">East Timor</option><option value="EC">Ecuador</option><option value="EG">Egypt</option><option value="SV">El Salvador</option><option value="ENG:GB">England(United Kingdom)</option><option value="GQ">Equatorial Guinea</option><option value="ER">Eritrea</option><option value="EE">Estonia</option><option value="ET">Ethiopia</option><option value="FO">Faroe Islands</option><option value="FJ">Fiji</option><option value="FI">Finland</option><option value="FR">France</option><option value="GF">French Guiana</option><option value="PF">French Polynesia</option><option value="GA">Gabon</option><option value="GM">Gambia</option><option value="GE">Georgia</option><option value="DE">Germany</option><option value="GH">Ghana</option><option value="GI">Gibraltar</option><option value="GRCA:KY">Grand Cayman(Cayman Islands)</option><option value="GRBR:GB">Great Britain(United Kingdom)</option><option value="GRTH:VG">Great Thatch Islands(British Virgin Islands)</option><option value="GRTO:VG">Great Tobago Islands(British Virgin Islands)</option><option value="GR">Greece</option><option value="GL">Greenland</option><option value="GD">Grenada</option><option value="GP">Guadeloupe</option><option value="GU">Guam</option><option value="GT">Guatemala</option><option value="GN">Guinea</option><option value="GY">Guyana</option><option value="HT">Haiti</option><option value="HN">Honduras</option><option value="HK">Hong Kong</option><option value="HU">Hungary</option><option value="IS">Iceland</option><option value="IN">India</option><option value="ID">Indonesia</option><option value="IQ">Iraq</option><option value="IE">Ireland</option><option value="IL">Israel</option><option value="IT">Italy</option><option value="CI">Ivory Coast</option><option value="JM">Jamaica</option><option value="JP">Japan</option><option value="JO">Jordan</option><option value="JVDI:VG">Jost Van Dyke Islands(British Virgin Islands)</option><option value="KZ">Kazakhstan</option><option value="KE">Kenya</option><option value="KI">Kiribati</option><option value="KW">Kuwait</option><option value="KG">Kyrgyzstan</option><option value="LA">Laos</option><option value="LV">Latvia</option><option value="LB">Lebanon</option><option value="LS">Lesotho</option><option value="LR">Liberia</option><option value="LY">Libya</option><option value="LI">Liechtenstein</option><option value="LT">Lithuania</option><option value="LU">Luxembourg</option><option value="MO">Macau</option><option value="MK">Macedonia</option><option value="MG">Madagascar</option><option value="MW">Malawi</option><option value="MY">Malaysia</option><option value="MV">Maldives</option><option value="ML">Mali</option><option value="MT">Malta</option><option value="MH">Marshall Islands</option><option value="MQ">Martinique</option><option value="MR">Mauritania</option><option value="MU">Mauritius</option><option value="MX">Mexico</option><option value="FM">Micronesia</option><option value="MD">Moldova</option><option value="MC">Monaco</option><option value="MN">Mongolia</option><option value="ME">Montenegro</option><option value="MS">Montserrat</option><option value="MA">Morocco</option><option value="MZ">Mozambique</option><option value="NA">Namibia</option><option value="NR">Nauru</option><option value="NP">Nepal</option><option value="NL">Netherlands</option><option value="NC">New Caledonia</option><option value="NZ">New Zealand</option><option value="NI">Nicaragua</option><option value="NE">Niger</option><option value="NG">Nigeria</option><option value="NU">Niue</option><option value="NOIS:VG">Norman Island(British Virgin Islands)</option><option value="NOIR:GB">Northern Ireland(United Kingdom)</option><option value="MP">Northern Mariana Islands</option><option value="NO">Norway</option><option value="OM">Oman</option><option value="PK">Pakistan</option><option value="PW">Palau</option><option value="PS">Palestine</option><option value="PA">Panama</option><option value="PG">Papua New Guinea</option><option value="PY">Paraguay</option><option value="PE">Peru</option><option value="PH">Philippines</option><option value="PL">Poland</option><option value="PT">Portugal</option><option value="PR">Puerto Rico</option><option value="QA">Qatar</option><option value="RE">Reunion</option><option value="RO">Romania</option><option value="ROT:MP">Rota(Northern Mariana Islands)</option><option value="RU">Russia</option><option value="RW">Rwanda</option><option value="SAB:BQ">Saba(Caribbean Netherlands)</option><option value="SAI:MP">Saipan(Northern Mariana Islands)</option><option value="WS">Samoa</option><option value="SAMA:IT">San Marino(Italy)</option><option value="SA">Saudi Arabia</option><option value="SCO:GB">Scotland(United Kingdom)</option><option value="SN">Senegal</option><option value="RS">Serbia</option><option value="SC">Seychelles</option><option value="SG">Singapore</option><option value="SK">Slovak Republic</option><option value="SI">Slovenia</option><option value="SB">Solomon Islands</option><option value="ZA">South Africa</option><option value="KR">South Korea</option><option value="ES">Spain</option><option value="LK">Sri Lanka</option><option value="STBA:GP">St. Barthelemy(Guadeloupe)</option><option value="STCH:KN">St. Christopher(Saint Kitts And Nevis)</option><option value="STCR:VI">St. Croix Island(U S Virgin Islands)</option><option value="STEU:BQ">St. Eustatius(Caribbean Netherlands)</option><option value="STJO:VI">St. John(U S Virgin Islands)</option><option value="KN">St. Kitts and Nevis</option><option value="LC">St. Lucia</option><option value="SX">St. Maarten</option><option value="MF">St. Martin</option><option value="STTH:VI">St. Thomas(U S Virgin Islands)</option><option value="VC">St. Vincent</option><option value="SR">Suriname</option><option value="SZ">Swaziland</option><option value="SE">Sweden</option><option value="CH">Switzerland</option><option value="SY">Syria</option><option value="TAH:PF">Tahiti(French Polynesia)</option><option value="TW">Taiwan</option><option value="TZ">Tanzania</option><option value="TH">Thailand</option><option value="TIN:MP">Tinian(Northern Mariana Islands)</option><option value="TG">Togo</option><option value="TO">Tonga</option><option value="TOIS:VG">Tortola Island(British Virgin Islands)</option><option value="TT">Trinidad and Tobago</option><option value="TN">Tunisia</option><option value="TR">Turkey</option><option value="TM">Turkmenistan</option><option value="TC">Turks and Caicos Islands</option><option value="TV">Tuvalu</option><option value="UG">Uganda</option><option value="UA">Ukraine</option><option value="UNIS:VC">Union Island(St. Vincent)</option><option value="AE">United Arab Emirates</option><option value="GB">United Kingdom</option><option value="US" selected="selected">United States</option><option value="VI">U.S. Virgin Islands</option><option value="UY">Uruguay</option><option value="UZ">Uzbekistan</option><option value="VU">Vanuatu</option><option value="VACI:IT">Vatican City(Italy)</option><option value="VE">Venezuela</option><option value="VN">Vietnam</option><option value="WAL:GB">Wales(United Kingdom)</option><option value="WF">Wallis and Futuna Islands</option><option value="YE">Yemen</option><option value="ZM">Zambia</option><option value="ZW">Zimbabwe</option>
            </select><br class="clear">
            <label>Company</label>
            <input type="text" name="pickupCompany" value="<?= $item['company'] ?>"><br class="clear">
            <label>Contact name*</label>
            <input type="text" name="pickupContactName" value="<?= $item['first_name'] ?> <?= $item['last_name'] ?>"><br class="clear">
            <label>Address 1*</label>
            <input type="text" name="pickupAddress1" value="<?= $item['street'] ?>"><br class="clear">
            <label>ZIP*</label>
            <input type="text" name="pickupZip" value="<?= $item['zipcode'] ?>"><br class="clear">
            <label>City*</label>
            <input type="text" name="pickupCity" value="<?= $item['city'] ?>"><br class="clear">
            <label>State*</label>
            <input type="text" name="pickupState" value="<?= $item['state'] ?>"><br class="clear">
            <label>Phone no.*</label>
            <input type="text" name="pickupPhone1" value="<?= $item['phone'] ?>"> 
            <br class="clear">
            <input type="checkbox" name="pickupResident" value="1"> <span class="line_inp">This is a residential address</span><br class="clear">
        </div>

    </div>
    <div class="left">
        <div class="col300">
            <a class="save_fedex_autofill right" data-type="pickup_pack">Save as Template</a><br class="clear">
            <h6 class="left">2. Package Information</h6>
            <select name="fedex_autofill" class="right" data-type="pickup_pack">
                <option value="">Autofill with...</option>
                <?php
                if(!empty($autofill['pickup_pack'])){
                    foreach ($autofill['pickup_pack'] as $val){
                        ?><option value="<?=$val['id']?>"><?=$val['title']?></option><?php
                    }
                }
                ?>
            </select>
            <br class="clear">
            <hr>
            <label>Building Part:</label>
            <select name="packLocation">
                <option value="APARTMENT">APARTMENT</option>
                <option value="BUILDING">BUILDING</option>
                <option value="DEPARTMENT">DEPARTMENT</option>
                <option value="SUITE">SUITE</option>
                <option value="FLOOR">FLOOR</option>
                <option value="ROOM">ROOM</option>
            </select><br class="clear">
            <label>Building Part<br> Description:</label><br>
            <input type="text" name="partDescr" value="" >
            <br class="clear"><br>
            <hr>
            <br class="clear">
            <label>Total no. of packages</label>
            <select name="countPackages" size="1">
                <?php for ($x = 1; $x <= 25; $x++) { ?>
                    <option value="<?= $x ?>"><?= $x ?></option>
                <?php } ?>
            </select><br class="clear">
            <label>Total weight:*</label>
            <input type="text" name="pickupWeight" value="2" class="short"> lbs<br class="clear">
            <label>Pickup date:*</label>
            <input id="datepicker2" type="text" name="pickupDate" class="short" value="<?= date('Y-m-d') ?>"><br class="clear">
            <label>Ready time:*</label>
            <select name="pickupTime" size="1">
               	<option value="08:00">8:00 am</option><option value="08:30">8:30 am</option><option value="09:00">9:00 am</option><option value="09:30">9:30 am</option><option value="10:00">10:00 am</option><option value="10:30">10:30 am</option><option value="11:00">11:00 am</option><option value="11:30">11:30 am</option><option value="12:00">12:00 pm</option><option value="12:30">12:30 pm</option><option value="13:00">1:00 pm</option><option value="13:30">1:30 pm</option><option value="14:00">2:00 pm</option><option value="14:30">2:30 pm</option><option value="15:00">3:00 pm</option><option value="15:30">3:30 pm</option><option value="16:00">4:00 pm</option><option value="16:30">4:30 pm</option><option value="17:00">5:00 pm</option><option value="17:30">5:30 pm</option><option value="18:00">6:00 pm</option>
            </select>
            <br class="clear">
            <label>Latest time available:*</label>
            <select name="pickupLatest" size="1">
                <option value="12:00">12:00 pm</option><option value="12:30">12:30 pm</option><option value="13:00">1:00 pm</option><option value="13:30">1:30 pm</option><option value="14:00">2:00 pm</option><option value="14:30">2:30 pm</option><option value="15:00">3:00 pm</option><option value="15:30">3:30 pm</option><option value="16:00">4:00 pm</option><option value="16:30">4:30 pm</option><option value="17:00">5:00 pm</option><option value="17:30">5:30 pm</option><option value="18:00">6:00 pm</option><option value="18:30">6:30 pm</option><option value="19:00">7:00 pm</option><option value="19:30">7:30 pm</option><option value="20:00">8:00 pm</option><option value="20:30">8:30 pm</option><option value="21:00">9:00 pm</option><option value="21:30">9:30 pm</option><option value="22:00">10:00 pm</option><option value="22:30">10:30 pm</option><option value="23:00">11:00 pm</option><option value="23:30">11:30 pm</option><option value="24:00">12:00 am</option>
            </select>
            <br class="clear">
            <label>Location of packages or special instructions</label>
            <input type="text" name="pickupSpecial"><br class="clear">
            <input type="radio" name="pickupSchedule" value="FDXE"> <span class="line_inp">Schedule a FedEx Express Pickup</span><br class="clear">
            <input type="radio" name="pickupSchedule" value="FDXG"> <span class="line_inp">Schedule a FedEx Ground Pickup</span>
        </div>

        <div class="col300">
            <h6>E-mail Notifications (optional)</h6><br class="clear">
            <label>Sender:</label>
            <input type="text" name="SenderEmail" value=""><br class="clear">
            <label>&nbsp;</label><select name="senderEmailLang">
                <option value="">Select</option><option value="zhHK">Chinese (Trad. HKG)</option><option value="zhTW">Chinese (Traditional)</option><option value="cs">Czech</option><option value="da">Danish</option><option value="nl">Dutch</option><option value="en" selected="selected">English</option><option value="fr">French</option><option value="frCA">French (Canada)</option><option value="de">German</option><option value="it">Italian</option><option value="ja">Japanese</option><option value="ko">Korean</option><option value="pl">Polish</option><option value="pt">Portuguese (Latin America)</option><option value="esES">Spanish (Latin America)</option><option value="es">Spanish (Spain)</option><option value="esUS">Spanish (United States)</option><option value="sv">Swedish</option>
            </select><br class="clear">
            <label>Recipient:</label>
            <input type="text" name="RecipientEmail" value=""><br class="clear">
            <label>&nbsp;</label><select name="RecipientEmailLang" >
                <option value="">Select</option><option value="zhHK">Chinese (Trad. HKG)</option><option value="zhTW">Chinese (Traditional)</option><option value="cs">Czech</option><option value="da">Danish</option><option value="nl">Dutch</option><option value="en" selected="selected">English</option><option value="fr">French</option><option value="frCA">French (Canada)</option><option value="de">German</option><option value="it">Italian</option><option value="ja">Japanese</option><option value="ko">Korean</option><option value="pl">Polish</option><option value="pt">Portuguese (Latin America)</option><option value="esES">Spanish (Latin America)</option><option value="es">Spanish (Spain)</option><option value="esUS">Spanish (United States)</option><option value="sv">Swedish</option>
            </select>
            <br class="clear">
            <br class="clear">
            <hr>
            Select format:<br class="clear">
            <input type="radio" name="messType" checked="checked" value="HTML"> <span class="line_inp">HTML</span><br class="clear">
            <input type="radio" name="messType" value="TEXT"> <span class="line_inp">Text</span><br class="clear">  
            <input type="radio" name="messType" value="WIRELESS"> <span class="line_inp">Wireless</span><br class="clear">
        </div>
        <div class="col300">
            <h6>3. Complete Pickup</h6><br class="clear">
            
            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
            <input type="submit" name="send" value="Pick Up" class="right dblueBtn button_small"><br class="clear">
        </div>
    </div>
    <br class="clear">
</form>