$(function() {
    var err = '';
    var serviceType, packageType, price, send1ContactName, send1Company, send1Phone1, send1Address1, send1City, send1State, send1Zip;
    var send1CountryCode, send2ContactName, send2Company, send2Phone1, send2Address1, send2City, send2Zip, send2State, send2CountryCode;
    var send2Resident, shipPackages, packWeight, dimensionL, dimensionW, dimensionH, item_id;

    var pickupCountryCode, pickupContactName, pickupAddress1, pickupZip, pickupCity, pickupState, pickupPhone1, pickupResident;//pickup

    /*
     * Validate fedex form
     * @returns {Boolean}
     */
    function check_request() {
        err = '';
        if (!serviceType) {
            err = 'Select "Service type"';
        } else if (!packageType) {
            err = 'Select "Package type"';
        } else if (!price) {
            err = 'Field "Declared Value" is empty';
        } else if (!send1CountryCode) {
            err = 'Select correct "Country/Location"';
        } else if (!send2CountryCode) {
            err = 'Select correct "Country/Location"';
        } else if (!shipPackages) {
            err = 'Select "No. of packages"';
        } else if (!packWeight) {
            err = 'Field "Weight" is empty';
        } else if (!dimensionL || !dimensionW || !dimensionH) {
            err = 'Incorrect "Dimensions"';
        } else if (!item_id) {
            err = 'Server error, current item not found';
        } else if (!send1Zip) {
            err = 'Field "ZIP" is empty';
        } else if (!send2Zip) {
            err = 'Field "ZIP" is empty';
        }
        if (err) {
            alert(err);
            return false;
        } else {
            return true;
        }
    }

    /*
     * Validate all fields
     * @returns {Boolean}
     */
    function check_request_all() {
        errors_aval = check_request();
        if (errors_aval) {
            if (!send1ContactName || !send1Phone1 || !send1Address1 || !send1City || !send1State || !send2ContactName || !send2Phone1 || !send2Address1 || !send2City || !send2State) {
                err = 'Fill all Required contact information';
            }

            //COD
            if ($('input[name=specServCOD]').is(':checked') && !$('input[name=CodAmount]').val()) {
                err = 'Field "Total COD amount" is empty';
            }
            //Billing
            if ($('select[name=billingData]').val() != 'SENDER' && !$('input[name=billingAccountNo]').val()) {
                err = 'Billing Details, Account no. is empty';
            }
            //Hold at FedEx location
            if ($('input[name=specServHold]').is(':checked') && $('input[name=holdLocation]:checked').length === 0) {
                err = 'Select Location for Hold at FedEx location';
            }

            if (err) {
                alert(err);
                return false;
            } else {
                return true;
            }
        } else {
            return errors_aval;
        }
    }

    /*
     * fill shipment variables
     */
    function get_shipment_variables() {
        serviceType = $('select[name=serviceType]').val();
        packageType = $('select[name=packageType]').val();
        price = $('input[name=price]').val();
        send1ContactName = $('input[name=send1ContactName]').val();
        send1Company = $('input[name=send1Company]').val();
        send1Phone1 = $('input[name=send1Phone1]').val();
        send1Address1 = $('input[name=send1Address1]').val();
        send1City = $('input[name=send1City]').val();
        send1State = $('input[name=send1State]').val();
        send1Zip = $('input[name=send1Zip]').val();
        send1CountryCode = $('select[name=send1CountryCode]').val();
        send2ContactName = $('input[name=send2ContactName]').val();
        send2Company = $('input[name=send2Company]').val();
        send2Phone1 = $('input[name=send2Phone1]').val();
        send2Address1 = $('input[name=send2Address1]').val();
        send2City = $('input[name=send2City]').val();
        send2Zip = $('input[name=send2Zip]').val();
        send2State = $('input[name=send2State]').val();
        send2CountryCode = $('select[name=send2CountryCode]').val();
        send2Resident = $('input[name=send2Resident]').val();
        shipPackages = $('select[name=shipPackages]').val();
        packWeight = $('input[name=packWeight]').val();
        dimensionL = $('input[name=dimensionL]').val();
        dimensionW = $('input[name=dimensionW]').val();
        dimensionH = $('input[name=dimensionH]').val();
        item_id = $('input[name=item_id]').val();
        CodAmount = $('input[name=CodAmount]').val();
    }

    /*
     * Calculate Fedex Rates & Transit Times
     */
    $('input[name=calculate]').click(function() {
        var $this = $(this);
        $('.calk_results').html('');
        $('.calk_results_err').html('');
        $this.next().show();

        get_shipment_variables();

        if (check_request()) {
            $.post('/admin/print/ajax', {
                'func': 'calcTransit',
                'data': $('form').serialize()
            }, function(data) {
                $this.next().hide();
                if (data.ok) {
                    $('.calk_results').html(data.ok);
                } else if (data.err) {
                    $('.calk_results_err').html(data.err);
                }
            }, 'json');
        } else {
            $this.next().hide();
        }
    });

    /* 
     * Change Sending Type 
     */
    $('select[name=type]').change(function() {
        var val = $(this).val();
        if (val == 'pickup') {
            document.location = '/admin/sales/fedex/?pickup=1';
        } else {
            document.location = '/admin/sales/fedex';
        }
    });

    /* 
     * New shipment/Pickup action 
     */
    $('input[name=send]').click(function() {
        var type = $(this).parents('form').find('select[name=type]').val();

        if (type == 'pickup') {
            get_pickup_variables();
            check_rez = check_pickup();
        } else {
            get_shipment_variables();
            check_rez = check_request_all();
        }

        if (!check_rez) {
            return false;
        } else {
            $('.modal_bg').show();
            $('.loading').show();

            $.post('/admin/print/ajax', {
                'func': 'fedexShipment',
                'data': $(this).parents('form').serialize()
            }, function(data) {
                $('.loading').hide();

                if (data.ok) {
                    $('.modal_bg .contents').html(data.ok);
                } else if (data.err) {
                    $('.modal_bg .err').html(data.err);
                }
            }, 'json');
        }
        return false;
    });



    /*
     * New Pickup 
     */
    function get_pickup_variables() {
        pickupCountryCode = $('select[name=pickupCountryCode]').val();
        pickupContactName = $('input[name=pickupContactName]').val();
        pickupAddress1 = $('input[name=pickupAddress1]').val();
        pickupZip = $('input[name=pickupZip]').val();
        pickupCity = $('input[name=pickupCity]').val();
        pickupState = $('input[name=pickupState]').val();
        pickupPhone1 = $('input[name=pickupPhone1]').val();
        pickupWeight = $('input[name=pickupWeight]').val();
        pickupDate = $('input[name=pickupDate]').val();
    }

    /*
     * Check Pickup form
     * @returns {Boolean}
     */
    function check_pickup() {
        err = false;
        if (!pickupCountryCode || !pickupContactName || !pickupAddress1 || !pickupZip || !pickupCity || !pickupState || !pickupPhone1 || !pickupWeight || !pickupDate) {
            err = 'Fill all Required fields';
        } else if ($('input[name=pickupSchedule]:checked').length === 0) {
            err = 'Select Schedule a FedEx Express Pickup/Ground Pickup';
        }

        if (err) {
            alert(err);
            return false;
        } else {
            return true;
        }
    }

    /*
     * Special Services show/hide options
     */
    $('input[name=specServCOD]').change(function() {
        if ($(this).is(':checked')) {
            $('.cod_amount').show();
        } else {
            $('.cod_amount').hide();
        }
    });

    /*
     * Change Bill transportation to... select
     */
    $('select[name=billingData]').change(function() {
        if ($(this).val() != 'SENDER') {
            $('#billaccount').show();
        } else {
            $('#billaccount').hide();
        }
    });

    /*
     * Hold at FedEx location get data from Fedex
     */
    $('input[name=specServHold]').click(function() {
        var $this = $(this),
                $load = $this.parents('.checker').next().next();
        $load.show();
        if ($(this).is(':checked')) {
            $.post('/admin/print/ajax', {
                'func': 'hold_location',
                'data': $('form').serialize()
            }, function(data) {
                $load.hide();
                if (data.ok) {
                    $('.hold_details').html(data.ok).show();
                } else if (data.err) {
                    $('.hold_details').html('<em class="error">' + data.err + '</em>').show();
                }
            }, 'json');
        } else {
            $('.hold_details').hide();
        }
    });

    /*
     * Hold data location
     */
    $('input[name=holdLocation]').live('change', function() {
        var holdCompany = $(this).parent().find('input[name=holdCompany]').val(),
                holdPhone = $(this).parent().find('input[name=holdPhone]').val(),
                holdStreet = $(this).parent().find('input[name=holdStreet]').val(),
                holdState = $(this).parent().find('input[name=holdState]').val(),
                holdZip = $(this).parent().find('input[name=holdZip]').val(),
                holdCountry = $(this).parent().find('input[name=holdCountry]').val();

        $('input[name=holdPhoneRez]').val(holdPhone);
        $('input[name=holdCompanyRez]').val(holdCompany);
        $('input[name=holdStreetRez]').val(holdStreet);
        $('input[name=holdStateRez]').val(holdState);
        $('input[name=holdZipRez]').val(holdZip);
        $('input[name=holdCountryRez]').val(holdCountry);
    });


    /* 
     * Close Shipment 
     */
    $('.close_ship').click(function() {
        var id = $(this).data('id'),
                more = $(this).data('more');
        if (confirm('Do you want to Close a Shipment?')) {
            $('.modal_bg').show();
            $('.loading').show();
            $.post('/admin/print/ajax', {
                'func': 'close_ship',
                'id': id,
                'more_id': more
            }, function(data) {
                $('.loading').hide();
                if (data.ok) {
                    $('.modal_bg .contents').html(data.ok + '<br><br><a href="" class="magentaBtn button_small">Continue</a>');
                } else if (data.err) {
                    $('.modal_bg .err').html(data.err);
                }
            }, 'json');
        }
    });


    /*
     * Fedex Autofill 
     */
    $('.save_fedex_autofill').click(function() {
        var type = $(this).data('type');
        $('.modal_bg').show();
        $('.modal_bg .loading').hide();
        $('.modal_bg .contents').html('<p><h3>Save Fedex Template</h3><br><label>Title:</label> <input type="text" name="fedex_title"> <button class="button_small whitishBtn confirm_save_template" data-type="' + type + '">Save</button></p><br>');
    });

    /*
     * Save Fedex autofill template
     */
    $('.confirm_save_template').live('click', function() {
        var $this = $(this),
                type = $this.data('type'),
                title = $('input[name=fedex_title]').val();
        $('.modal_bg .loading').show();
        $('.modal_bg .contents').html('');

        var data_block = $('form').find('a[data-type="' + type + '"]').parent();
        var data_arr = {};
        $.each(data_block.find('input,select'), function(i) {
            if ($(this).val() && $(this).is(':visible')) {
                if ($(this).attr('type') == 'text' || $(this).is('select')) {
                    data_arr[i] = {'name': $(this).attr('name'), 'val': $(this).val()};
                }else if($(this).attr('type') == 'radio' || $(this).attr('type') == 'checkbox'){
                    if($(this).is(':checked')){
                        data_arr[i] = {'name': $(this).attr('name'), 'val': $(this).val()};
                    }
                }
            }
        });

        $.post('/admin/print/ajax',{
            'func': 'save_fedex_template',
            'type': type,
            'title': title,
            'data': data_arr,
            'req_id': $('input[name=item_id]').val()
        }, function(data){
            if(data.id){
                $('select[name=fedex_autofill][data-type="'+type+'"]').append('<option value="'+data.id+'" selected="selected">'+title+'</option>')
            }else{
                alert('Server error!');
            }
            $('.modal_bg').hide();
        }, 'json');
    });

    /*
     * Get Fedex autofill template
     */
    $('select[name=fedex_autofill]').change(function() {
        var  $this = $(this),
            val = $this.val();
        if (val) {

            $.post('/admin/print/ajax', {
                'func': 'get_autofill_data',
                'id': val
            }, function(data) {
                if (data.data) {
                    
                    //clear
                    $this.parent().find('input[type=text]:visible').val('');
                    $this.parent().find('input[type=checkbox]:visible').removeAttr('checked');
                    $this.parent().find('input[type=radio]:visible').removeAttr('checked');

                    $.each(data.data, function(i, v) {
                        if ($('input[type=text][name="' + i + '"]').length > 0) {
                            $('input[type=text][name="' + i + '"]:visible').val(v);
                        } else if ($('input[type=checkbox][name="' + i + '"]').length > 0) {
                            $('input[type=checkbox][name="' + i + '"][value="' + v + '"]:visible').attr('checked', 'checked').change();
                        } else if ($('input[type=radio][name="' + i + '"]').length > 0) {
                            $('input[type=radio][name="' + i + '"][value="' + v + '"]:visible').attr('checked', 'checked').change();
                        } else if ($('select[name="' + i + '"]:visible').length > 0) {
                            $('select[name="' + i + '"]:visible option[value="' + v + '"]').attr('selected', 'selected').change();
                        } else if ($('textarea[name="' + i + '"]:visible').length > 0) {
                            $('textarea[name="' + i + '"]:visible').val(v);
                        }
                    });
                }
            }, 'json');
        }
    });

});