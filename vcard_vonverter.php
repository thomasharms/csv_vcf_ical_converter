<?php

    require_once("constants.php");
    
    class vCard_Parse {
        
        function __construct(){}
        
        function __destruct(){}
        
        //extract content of a vCardfile into string
        public static function get_vCard_file_content($vCard_file_path = NULL){
            
            if(!empty($vCard_file_path) && is_file($vCard_file_path)){
                return file_get_contents($vCard_file_path);
            }
        }
        
        //split a string with vCards into set of multiple single vCards
        public static function split_vCard_set_into_single_vCards($vCard_string = NULL){
            
            if(!empty($vCard_string) && is_string($vCard_string)){
                
                //returns 2 identical arrays [0] and [1]
                preg_match_all('|(BEGIN:VCARD.*?END:VCARD)|sm', $vCard_string, $vCard_set);
            }
            return is_array($vCard_set) ? $vCard_set[0] : FALSE;
        }
        
        //splits a string of a single vCard into array of sinlge rows
        public static function split_vCard_into_Rows($vCard = NULL){
            if(!empty($vCard)){
                $att_lines = preg_split('/$\R?^/m', $vCard);
            }
            return is_array($att_lines) ? $att_lines : FALSE;
        }
        
        public static function split_attribute_line_2_values($attribute_line = NULL){
            if(!is_array($attribute_line)){
                $att_vals = preg_split('|[:;]|s', $attribute_line);
            }
            return is_array($att_vals) ? $att_vals : FALSE;
        }
        
        public static function parse_name_values($vCard_string = NULL){
            if(!empty($vCard_string) && is_string($vCard_string)){
                
                //preg_match('%%'                
            }
        }
        
        //vCard is a string of a single vCard
        //retruns a set of attributes in this fashion:
        //array(rownr. => array($attribute_name, $attribute_value1, $attribute_value2,...)
        public static function parse_vCard($vCard = NULL){
            
            $vCard_values = array();
            
            //array of single line of vCard 
            $lines_of_attributes = vCard_Parse::split_vCard_into_Rows($vCard);
            
            //vCard_values is array :: array(rownr. => array($attribute_name, $attribute_value1, $attribute_value2,...)
            //empty values will be there for not set values in adressbook
            foreach($lines_of_attributes as $attribute_line){
                $vCard_values[] = vCard_Parse::split_attribute_line_2_values($attribute_line);
            }
            return is_array($vCard_values) ? $vCard_values : FALSE;
            
        }
        
        //vcard is array (line_nr. => array($attribute_name, $attribte_value1, $attribute_value2))
        public static function set_attributes($vcard = NULL){
            
            $contact = new Contact;
            $email_set = array();
            $telephon_set = array();
            $adress_set=array();
            
            foreach ($vcard as $attribute_set){
                
                if(in_array('N', $attribute_set)){
                    $contact->Nachname = trim($attribute_set[1]);
                    $contact->Vorname = trim($attribute_set[2]);
                    //ought to be middle name.. it will be concatenated on top of first name
                    $contact->Vorname .= trim($attribute_set[3]);
                    $contact->Titel = trim($attribute_set[4]);
                }
                
                elseif(in_array('FN', $attribute_set)){
                    $contact->Anzeigename = $attribute_set[1];
                }
                
                elseif(in_array('ORG', $attribute_set)){
                    $contact->Firma = $attribute_set[1];
                }
                
                elseif(in_array('NOTE', $attribute_set)){
                    $contact->Notizen = $attribute_set[1];
                }
                
                elseif(in_array('URL', $attribute_set) && in_array('type=WORK', $attribute_set)){
                    $contact->Webseite = $attribute_set[2];
                }
                
                /* VERSION IS IRRELEVANT
                elseif(in_array('VERSION', $attribute_set)){
                    $contact->version = $attribute_set[1];
                }
                */
                
                
                elseif(in_array('URL', $attribute_set) && in_array('type=HOME', $attribute_set)){
                    $contact->Webseite_alt = $attribute_set[2];
                }
                
                //EMail values will only be gathered at this point
                //value assingments will be done in seperat function when all EMail-Adresses are set up in array
                elseif(strpos($attribute_set[0], 'EMAIL') !== FALSE){
                    $email_set[] = $attribute_set;
                }
                
                //Adress values will only be gathered at this point
                //value assingments will be done in seperat function when all Adresses are set up in array
                elseif(strpos($attribute_set[0], 'ADR') !== FALSE){
                    $adress_set[] = $attribute_set;
                }
                
                //Telephon number values will only be gathered at this point
                //value assingments will be done in seperat function when all Telephone Numbers are set up in array
                elseif(strpos($attribute_set[0], 'TEL') !== FALSE){
                    $telephon_set[] = $attribute_set;
                }
                
                //values will be split into day, month, year by seperate function
                elseif(in_array('BDAY', $attribute_set)){
                    $self::parse_birthday_values($attribute_set[1], $contact);
                }
                
                /* UID IS NOT RELEVANT RIGHT NOW
                elseif(in_array('X-ABUID0', $attribute_set)){
                    $contact->additinal_attributes['UID'] = $attribute_set[1];
                }
                */
                
                //gather all phone numbers
                //will be computed by different function
                elseif(in_array('TEL', $attribute_set) || strpos($attribute_set[0], 'TEL') !== FALSE){
                    $telephon_set[] = $attribute_set;
                }
                
                //gather all adress sets
                //will be computed by different function
                elseif(in_array('ADR', $attribute_set) || strpos($attribute_set[0], 'ADR') !== FALSE){
                    $adress_set[] = $attribute_set;
                }
                
                elseif(in_array('EMAIL', $attribute_set) || strpos($attribute_set[0], 'EMAIL') !== FALSE){
                    $email_set[] = $attribute_set;
                }
                

                
                
                elseif(in_array('ADR', $attribute_set) && in_array('type=HOME', $attribute_set)){
                    $contact->Privat_Adresse_Straße = $attribute_set[4];
                    $contact->Privat_Ort = $attribute_set[5];
                    $contact->Privat_Bundesland = $attribute_set[6];
                    $contact->Privat_PLZ = $attribute_set[6];
                    $contact->Privat_Land = $attribute_set[7];
                }
                
                elseif(in_array('ADR', $attribute_set) && in_array('type=WORK', $attribute_set)){
                    $contact->Dienstlich_Adresse_Straße = $attribute_set[4];
                    $contact->Dienstlich_Ort = $attribute_set[5];
                    $contact->Dienstlich_Bundesland = $attribute_set[6];
                    $contact->Dienstlich_PLZ = $attribute_set[6];
                    $contact->Dienstlich_Land = $attribute_set[7];
                }   
            }
        }
        
        //$birthday will look like this: YYYY-MM-DD
        private static function parse_birthday_values_from_vcard($birthday = NULL, &$contact_object = NULL){
            
            if(empty($birthday) && is_object($contact_object)){
                $contact_object->Geburtsjahr = substr($birthday, 0 ,4);
                $contact_object->Geburtsmonat = substr($birthday, 5, 2);
                $contact_object->Geburtstag = substr($birthday, 8, 2);
            }
        }
        
        //$email_set contains arrays of all the different email_sets gathered during parsing process
        //$email_set = array(0=>array(0=>$attribute_name, 1=>$attribute_value1, 2=>$attribute_value2,..), 1=>array...)
        //there can be one line, many more or none
        //function needs to assign private mail and business mail account by preference
        //rest of the email adresses found can be stored in "additional values"
        //adresses will be distinguished in between:
        //type=pref (this will be the prefered used)
        //type=WORK or type=HOME
        private static function parse_email_values_from_vcard($email_set = NULL, &$contact_object = NULL){
            
            if(!empty($email_set) && is_array($email_set) && is_object($contact_object)){
                
                //first step is to find prefered private and business email-adress
                foreach($email_set as $email_values){
                    
                    //gather prefered business_mail_adress(es), should be max one though
                    if(in_array('type=pref', $email_values) && in_array('type=WORK', $email_values)){
                        $business_pref_mail = $email_values;
                    }
                    
                    //gather prefered private_mail_adress(es), should be max one though
                    elseif(in_array('type=pref', $email_values) && in_array('type=HOME', $email_values)){
                        $private_pref_mail = $mail_values;
                    }
                    
                    //gather all not prefered private mails
                    elseif(!in_array('type=pref', $email_values) && in_array('type=HOME', $email_values)){
                        $additional_private_mail = $mail_values;
                    }
                    
                    //gather all not prefered business mails
                    elseif(!in_array('type=pref', $email_values) && in_array('type=WORK', $email_values)){
                        $additionnal_business_mail = $mail_values;
                    }
                }
                
                //business email first
                
                //if there is only one account prefered.. awesome, assign it to contact object
                if(!empty($business_pref_mail) && count($business_pref_mail) == 1){
                    foreach($business_pref_mail[0] as $mail_attribute){
                        
                        //if neither is in the string, then it is the actual mail adress
                        if((strpos($mail_attribute, 'EMAIL') === FALSE) && (strpos($mail_attribute, 'type=') === FALSE)){
                            
                            //in case value is not a valid mail adress, do not assign into $contact_object
                             self::check_mail_adress_valid($mail_attribute, $contact_object->EMail_Adresse_dienstlich);
                        }
                    }
                }
                
                //should not be the case that there are several adresses stored as prefered.. for convenience
                //if there is more then one prefered mail, store the first to contact_object
                //the 2nd should be stored in contact_object->additional_attributes
                //$contact_object->additional_attributes['EMail-Adresse_dienstlich'=>self::check_mail_adress_valid($mail_attribute)]
                elseif(!empty($business_pref_mail) && count($business_pref_mail) > 1){
                    
                    foreach($business_pref_mail as $mail_attribute_set){
                        
                        foreach($mail_attribute_set as $mail_attribute){
                            
                            //in case value is not a valid mail adress, do not assign into $contact_object
                            
                            //if everything checks out and value not been set yet.. go ahead
                            if((strpos($mail_attribute, 'EMAIL') === FALSE) && (strpos($mail_attribute, 'type=') === FALSE) && empty($contact_object->EMail_Adresse_dienstlich)){
                                
                                 self::check_mail_adress_valid($mail_attribute, $contact_object->EMail_Adresse_dienstlich);
                            }
                            
                            //if everything is fine and value had been set already, but EMail_Adresse_alt is not set yet: assign there
                            elseif((strpos($mail_attribute, 'EMAIL') === FALSE) && (strpos($mail_attribute, 'type=') === FALSE) && !empty($contact_object->EMail_Adresse_dienstlich) && empty($contact_object->EMail_Adresse_alt)){
                                self::check_mail_adress_valid($mail_attribute, $contact_object->EMail_Adresse_alt);
                            }
                            
                            //if everything is fine and value had been set already, assign to additional_attributes
                            elseif((strpos($mail_attribute, 'EMAIL') === FALSE) && (strpos($mail_attribute, 'type=') === FALSE) && !empty($contact_object->EMail_Adresse_dienstlich)){
                                self::check_mail_adress_valid($mail_attribute, $contact_object->additional_attributes['EMail-Adresse_dienstlich']);
                            }
                        }
                        
                    }
                    
                }
                
                //private email next
                
                //as it should be: there is only one prefered mail adress
                if(!empty($private_pref_mail) && count($private_pref_mail) == 1){
                    
                    foreach($private_pref_mail[0] as $mail_attribute){
                        
                        //if neither is in the string, then it is the actual mail adress
                        if((strpos($mail_attribute, 'EMAIL') === FALSE) && (strpos($mail_attribute, 'type=') === FALSE)){
                             
                             self::check_mail_adress_valid($mail_attribute, $contact_object->EMail_Adresse_dienstlich);
                        }
                    }
                    
                }
                
                //should not be the case that there are several adresses stored as prefered.. for convenience
                //if there is more then one prefered mail, store the first to contact_object
                //the 2nd should be stored in contact_object->additional_attributes
                //$contact_object->additional_attributes['EMail-Adresse']
                elseif(!empty($private_pref_mail) && count($private_pref_mail) > 1){
                    
                    foreach($private_pref_mail as $mail_attribute_set){
                        
                        foreach($mail_attribute_set as $mail_attribute){
                            
                            //in case value is not a valid mail adress, do not assign into $contact_object
                            
                            //if everything checks out and value not been set yet.. go ahead
                            if((strpos($mail_attribute, 'EMAIL') === FALSE) && (strpos($mail_attribute, 'type=') === FALSE) && empty($contact_object->EMail_Adresse)){
                                
                                 self::check_mail_adress_valid($mail_attribute, $contact_object->EMail_Adresse);
                            }
                            
                            //if everything is fine and $contact_object->EMail_Adresse had been set already, but EMail_Adresse_alt is not set yet: assign there
                            elseif((strpos($mail_attribute, 'EMAIL') === FALSE) && (strpos($mail_attribute, 'type=') === FALSE) && !empty($contact_object->EMail_Adresse) && empty($contact_object->EMail_Adresse_alt)){
                                self::check_mail_adress_valid($mail_attribute, $contact_object->EMail_Adresse_alt);
                            }
                            
                            //if everything is fine and value had been set already, assign to additional_attributes
                            elseif((strpos($mail_attribute, 'EMAIL') === FALSE) && (strpos($mail_attribute, 'type=') === FALSE) && !empty($contact_object->EMail_Adresse)){
                                self::check_mail_adress_valid($mail_attribute, $contact_object->additional_attributes['EMail-Adresse_Privat']);
                            }
                        }
                        
                    }
                    
                }
                
                
                //handle all additional business mail accounts
                //in case there had not been any prefered one stored yet, the first one gathered will be assigned
                
                //if there is more then one mail:
                // 1st if empty $contact_object->EMail_Adresse_dientslich
                // 2nd if empty $contact_object->EMail_Adresse_alt
                // 3rd store in $contact_object->additional_attributes['EMail-Adresse_dienstlich']
                if(!empty($additionnal_business_mail)){
                    
                    foreach($additionnal_business_mail as $mail_attribute_set){
                        
                        foreach($mail_attribute_set as $mail_attribute){
                            
                            //in case value is not a valid mail adress, do not assign into $contact_object
                            
                            //if everything checks out and value not been set yet.. go ahead
                            if((strpos($mail_attribute, 'EMAIL') === FALSE) && (strpos($mail_attribute, 'type=') === FALSE) && empty($contact_object->EMail_Adresse_dienstlich)){
                                
                                 self::check_mail_adress_valid($mail_attribute, $contact_object->EMail_Adresse_dienstlich);
                            }
                            
                            //if everything is fine and value had been set already, but EMail_Adresse_alt is not set yet: assign there
                            elseif((strpos($mail_attribute, 'EMAIL') === FALSE) && (strpos($mail_attribute, 'type=') === FALSE) && !empty($contact_object->EMail_Adresse_dienstlich) && empty($contact_object->EMail_Adresse_alt)){
                                self::check_mail_adress_valid($mail_attribute, $contact_object->EMail_Adresse_alt);
                            }
                            
                            //if everything is fine and value had been set already, assign to additional_attributes
                            elseif((strpos($mail_attribute, 'EMAIL') === FALSE) && (strpos($mail_attribute, 'type=') === FALSE) && !empty($contact_object->EMail_Adresse_dienstlich)){
                                self::check_mail_adress_valid($mail_attribute, $contact_object->additional_attributes['EMail-Adresse_dienstlich']);
                            }
                        }
                        
                    }
                }
                
                
                //handle any other private mail adress
                
                //if there is more then one mail:
                // 1st if empty $contact_object->EMail_Adresse_dientslich
                // 2nd if empty $contact_object->EMail_Adresse_alt
                // 3rd store in $contact_object->additional_attributes['EMail-Adresse_dienstlich']
                
                if(!empty($additional_private_mail)){
                    
                    foreach($additional_private_mail as $mail_attribute_set){
                        
                        foreach($mail_attribute_set as $mail_attribute){
                            
                            //in case value is not a valid mail adress, do not assign into $contact_object
                            
                            //if everything checks out and value not been set yet.. go ahead
                            if((strpos($mail_attribute, 'EMAIL') === FALSE) && (strpos($mail_attribute, 'type=') === FALSE) && empty($contact_object->EMail_Adresse)){
                                
                                 self::check_mail_adress_valid($mail_attribute, $contact_object->EMail_Adresse);
                            }
                            
                            //if everything is fine and $contact_object->EMail_Adresse had been set already, but EMail_Adresse_alt is not set yet: assign there
                            elseif((strpos($mail_attribute, 'EMAIL') === FALSE) && (strpos($mail_attribute, 'type=') === FALSE) && !empty($contact_object->EMail_Adresse) && empty($contact_object->EMail_Adresse_alt)){
                                self::check_mail_adress_valid($mail_attribute, $contact_object->EMail_Adresse_alt);
                            }
                            
                            //if everything is fine and value had been set already, assign to additional_attributes
                            elseif((strpos($mail_attribute, 'EMAIL') === FALSE) && (strpos($mail_attribute, 'type=') === FALSE) && !empty($contact_object->EMail_Adresse)){
                                self::check_mail_adress_valid($mail_attribute, $contact_object->additional_attributes['EMail-Adresse_Privat']);
                            }
                        }
                    }
                }
            }
        }
        
        //function responsible to check if a mail adress is valid
        //email_adress will only be put into $result, if it is a valid $mail_adress
        private static function check_mail_adress_valid($email_adress, &$result){
            
            if(empty($email_adress)){
                unset($result);
                exit;
            }
            
            //empty can be used, beause either 0 or FALSE is not a good sign for a valid mail adress
            if (empty(strpos($email_adress, '@', 3))){
                unset($result);
                exit;
            }
            
            if (empty(strpos($email_adress, '.', 4))){
                unset($result);
                exit;
            }
            
            if (strlen($email_adress) < 9){
                unset($result);
                exit;        
            }
            
            //if everything so far checks out... set the value
            //nothing to be returned, $result is given by reference
            $result = $email_adress;
        }
        
        
        //$telephone_set contains arrays of all the different email_sets gathered during parsing process
        //$telephone_set = array(0=>array(0=>$attribute_name, 1=>$attribute_value1, 2=>$attribute_value2,..), 1=>array...)
        //there could be one, more or none
        //function needs to assign private, mobile and business telephone numbers by preference
        //rest of the numbers found can be stored in "additional values"
        //phone numbers will be distinguished in between:
        //type=pref (this will be the prefered used)
        //type=WORK or type=HOME
        //type=FAX, type=PAGER will be stored in additional_attributes
        private static function parse_phone_number_values_from_vcard($phone_number_set = NULL, &$contact_object = NULL){
            
            if(!empty($phone_number_set) && is_array($phone_number_set) && is_object($contact_object)){
                
                //will be build: array(#=>array('attribute_name'=>'phone_number'))
                //problem may be multiple identical array_keys, which may overwrite each other
                $additional_numbers = array();
                
                //first find all prefered numbers
                
                foreach($phone_number_set as $attribute_set){
                    if(in_array('type=pref', $attribute_set)){
                        
                        //mobile numbers first
                        //should be only one or none, but data might be corrupted or inserting process in adress book is flawed
                        if(in_array('type=CELL', $attribute_set)){
                            
                            if(empty($contact_object->Telefon_dienstlich)){
                                
                                self::check_phone_number(array_pop($attribute_set), $contact_object->Telefon_dienstlich);
                            }
                            else{
                                $phone_mobile = array('Telefon_mobil'=>array_pop($attribute_set));
                                $additional_numbers[] = $phone_mobile;
                            }
                        }
                        
                        //business numbers
                        
                        elseif (in_array('type=WORK', $attribute_set) && in_array('type=VOICE', $attribute_set)){
                            
                            if(empty($contact_object->Telefon_dienstlich)){
                                
                                self::check_phone_number(array_pop($attribute_set), $contact_object->Telefon_dienstlich);
                            }
                            else{
                                $phone_business = array('Telefon_dienstlich'=>array_pop($attribute_set));
                                $additional_numbers[] = $phone_business;
                            }
                               
                        }
                        
                        //private numbers
                        
                        elseif (in_array('type=HOME', $attribute_set) && in_array('type=VOICE', $attribute_set)){
                            
                            if(empty($contact_object->Telefon_privat)){
                                
                                self::check_phone_number(array_pop($attribute_set), $contact_object->Telefon_privat);
                            }
                            else{
                                $phone_private = array('Telefon_privat'=>array_pop($attribute_set));
                                $additional_numbers[] = $phone_private;
                            }
                        }
                        
                        //phone number central
                        
                        elseif (in_array('type=MAIN', $attribute_set)){
                            
                            if(empty($contact_object->Telefon_dienstlich)){
                                
                                self::check_phone_number(array_pop($attribute_set), $contact_object->Telefon_dienstlich);
                            }
                            elseif(empty($contact_object->Telefon_privat)){
                                
                                self::check_phone_number(array_pop($attribute_set), $contact_object->Telefon_dienstlich);
                            } else {
                                
                                $phone_central = array('Telefon_zentral'=>array_pop($attribute_set));
                                $additional_numbers[] = $phone_central;
                            }
                        }
                        
                        //fax numbers private
                        
                        elseif (in_array('type=HOME', $attribute_set) && in_array('type=FAX', $attribute_set)){
                            
                            $fax_home = array('Fax_privat'=>array_pop($attribute_set));
                            $additional_numbers[] = $fax_home;
                        }
                        
                        //fax numbers business
                        
                        elseif (in_array('type=WORK', $attribute_set) && in_array('type=FAX', $attribute_set)){
                            
                            $fax_business = array('Fax_dienstlich'=>array_pop($attribute_set));
                            $additional_numbers[] = $fax_business;
                        }
                        
                        //pager
                        
                        elseif (in_array('type=PAGER', $attribute_set)){
                            
                            $pager = array('Pager'=>array_pop($attribute_set));
                            $additional_numbers[] = $pager;
                        }
                    }
                    
                    
                    // any other non prefered attribute
                    
                    else {
                        
                        //mobile numbers first
                        //should be only one or none, but data might be corrupted or inserting process in adress book is flawed
                        if(in_array('type=CELL', $attribute_set)){
                            
                            if(empty($contact_object->Telefon_dienstlich)){
                                
                                self::check_phone_number(array_pop($attribute_set), $contact_object->Telefon_dienstlich);
                            }
                            else{
                                $phone_mobile = array('Telefon_mobil'=>array_pop($attribute_set));
                                $additional_numbers[] = $phone_mobile;
                            }
                        }
                        
                        //business numbers
                        
                        elseif (in_array('type=WORK', $attribute_set) && in_array('type=VOICE', $attribute_set)){
                            
                            if(empty($contact_object->Telefon_dienstlich)){
                                
                                self::check_phone_number(array_pop($attribute_set), $contact_object->Telefon_dienstlich);
                            }
                            else{
                                $phone_business = array('Telefon_dienstlich'=>array_pop($attribute_set));
                                $additional_numbers[] = $phone_business;
                            }
                               
                        }
                        
                        //private numbers
                        
                        elseif (in_array('type=HOME', $attribute_set) && in_array('type=VOICE', $attribute_set)){
                            
                            if(empty($contact_object->Telefon_privat)){
                                
                                self::check_phone_number(array_pop($attribute_set), $contact_object->Telefon_privat);
                            }
                            else{
                                $phone_private = array('Telefon_privat'=>array_pop($attribute_set));
                                $additional_numbers[] = $phone_private;
                            }
                        }
                        
                        //phone number central
                        
                        elseif (in_array('type=MAIN', $attribute_set)){
                            
                            if(empty($contact_object->Telefon_dienstlich)){
                                
                                self::check_phone_number(array_pop($attribute_set), $contact_object->Telefon_dienstlich);
                            }
                            elseif(empty($contact_object->Telefon_privat)){
                                
                                self::check_phone_number(array_pop($attribute_set), $contact_object->Telefon_dienstlich);
                            } else {
                                
                                $phone_central = array('Telefon_zentral'=>array_pop($attribute_set));
                                $additional_numbers[] = $phone_central;
                            }
                        }
                        
                        //fax numbers private
                        
                        elseif (in_array('type=HOME', $attribute_set) && in_array('type=FAX', $attribute_set)){
                            
                            $fax_home = array('Fax_privat'=>array_pop($attribute_set));
                            $additional_numbers[] = $fax_home;
                        }
                        
                        //fax numbers business
                        
                        elseif (in_array('type=WORK', $attribute_set) && in_array('type=FAX', $attribute_set)){
                            
                            $fax_business = array('Fax_dienstlich'=>array_pop($attribute_set));
                            $additional_numbers[] = $fax_business;
                        }
                        
                        //pager
                        
                        elseif (in_array('type=PAGER', $attribute_set)){
                            
                            $pager = array('Pager'=>array_pop($attribute_set));
                            $additional_numbers[] = $pager;
                        }
                    }
                }
                
                
                //set additional_numbers into contact_object->additional_attributes
                //additional number: array(#=>array($attribute_name=>$number))
                foreach ($additional_numbers as $attribute_pairs){
                    foreach($attribute_pairs as $attribute_name => $contact_number){
                        
                        //in case multiple keys have the same name, they need to have different ones or they will overwrite each other
                        if(array_key_exists($attribute_name, $contact_object->additinal_attributes)){
                            
                            $element_number = 1;
                            while(array_key_exists($attribute_name.$element_number, $contact_object->additinal_attributes)){
                                $element_number++;
                            }
                             self::check_phone_number($contact_number, $contact_object->additinal_attributes[$attribute_name.$element_number]);
                        }
                        else {
                            
                             self::check_phone_number($contact_number, $contact_object->additinal_attributes[$attribute_name]);
                        }
                    }
                }
            }
        }
        
        private static function parse_house_number_off_adress_street($adress_street = NULL){
            
            if(!empty($adress_street) && is_string($adress_street)){
                
                preg_match('%\A[[a-zA-Z\s]+(\d+)\z%s', $adress_street, $matches);
                
                return !empty($matches[1]) ? $matches[1] : FALSE;
            }
        }
        
        private static function check_phone_number($phone_number, &$result){
            
            preg_match("%^([+]?[\d|\s]+)$%s", $phone_number, $matches);
            
            //check if the full pattern match has full length of Phone number.. so all character are digit, white space or a single + in the beginning
            if(count($matches[0]) == count($phone_number)){
                $result= $phone_number;
            }
            else {
                unset($result);
            }
        }
        
        
        //$adress_value_set contains arrays of all the different email_sets gathered during parsing process
        //$adress_value_set = array(0=>array(0=>$attribute_name, 1=>$attribute_value1, 2=>$attribute_value2,..), 1=>array...)
        //there could be one, more or none
        //function needs to assign private and business telephone numbers by preference
        //rest of the numbers found can be stored in "additional values"
        //adresses will be distinguished in between:
        //type=pref (this will be the prefered used)
        //type=WORK or type=HOME or any other adress set
        private static function parse_adresses_values_from_vcard($adress_value_set = NULL, &$contact_object = NULL){
            
            if(!empty($adress_value_set) && is_array($adress_value_set) && is_object($contact_object)){
                
                $additional_adress_sets = array();
                //begin with any prefered adress set and set it as private or business main adress
                
                foreach($adress_value_set as $adress_values){
                    
                    if(in_array('type=pref', $adress_values)){
                        
                        //first check for business adress
                        if(in_array('type=WORK', $adress_values) && empty($contact_object_>Dienstlich_Ort) && empty($contact_object->Dienstlich_Adresse_Straße) && empty($contact_object->Dienstlich_PLZ) && empty($contact_object->Dienstlich_Land) && empty($contact_object->Dienstlich_Bundesland)){
                            
                            self::normalize_adress_values($adress_values);
                            $contact_object->Dienstlich_Adresse_Straße = !empty($adress_values[0]) ? $adress_values[0] : NULL;
                            $contact_object->Dienstlich_Adresse_Hsnr = !empty(self::parse_house_number_off_adress_street($adress_values[0])) ? self::parse_house_number_off_adress_street($adress_values[0]) : NULL;
                            $contact_object->Dienstlich_Ort = !empty($adress_values[1]) ? $adress_values[1] : NULL;
                            $contact_object->Dienstlich_Bundesland = !empty($adress_values[2]) ? $adress_values[2] : NULL;
                            $contact_object->Dienstlich_PLZ = !empty($adress_values[3]) ? $adress_values[3] : NULL;
                            $contact_object->Dienstlich_Land = !empty($adress_values[4]) ? $adress_values[4] : NULL;
                        }
                        elseif(in_array('type=WORK', $adress_values)){
                            $additional_adress_sets[] = $adress_values;
                        }
                        
                        //private adress
                        elseif(in_array('type=HOME', $adress_values) && empty($contact_object_>Privat_Ort) && empty($contact_object->Privat_Adresse_Straße) && empty($contact_object->Privat_PLZ) && empty($contact_object->Privat_Land) && empty($contact_object->Privat_Bundesland)){
                            self::normalize_adress_values($adress_values);
                            $contact_object->Privat_Adresse_Straße = !empty($adress_values[0]) ? $adress_values[0] : NULL;
                            $contact_object->Privat_Adresse_Hsnr = !empty(self::parse_house_number_off_adress_street($adress_values[0])) ? self::parse_house_number_off_adress_street($adress_values[0]) : NULL;
                            $contact_object->Privat_Ort = !empty($adress_values[1]) ? $adress_values[1] : NULL;
                            $contact_object->Privat_Bundesland = !empty($adress_values[2]) ? $adress_values[2] : NULL;
                            $contact_object->Privat_PLZ = !empty($adress_values[3]) ? $adress_values[3] : NULL;
                            $contact_object->Privat_Land = !empty($adress_values[4]) ? $adress_values[4] : NULL;
                        }
                        elseif(in_array('type=HOME', $adress_values)){
                            $additional_adress_sets[] = $adress_values;
                        }
                        
                        // OTHER adresses
                        elseif(in_array('type=OTHER', $adress_values) && empty($contact_object_>Dienstlich_Ort) && empty($contact_object->Dienstlich_Adresse_Straße) && empty($contact_object->Dienstlich_PLZ) && empty($contact_object->Dienstlich_Land) && empty($contact_object->Dienstlich_Bundesland)){
                            self::normalize_adress_values($adress_values);
                            $contact_object->Dienstlich_Adresse_Straße = !empty($adress_values[0]) ? $adress_values[0] : NULL;
                            $contact_object->Dienstlich_Adresse_Hsnr = !empty(self::parse_house_number_off_adress_street($adress_values[0])) ? self::parse_house_number_off_adress_street($adress_values[0]) : NULL;
                            $contact_object->Dienstlich_Ort = !empty($adress_values[1]) ? $adress_values[1] : NULL;
                            $contact_object->Dienstlich_Bundesland = !empty($adress_values[2]) ? $adress_values[2] : NULL;
                            $contact_object->Dienstlich_PLZ = !empty($adress_values[3]) ? $adress_values[3] : NULL;
                            $contact_object->Dienstlich_Land = !empty($adress_values[4]) ? $adress_values[4] : NULL;
                        }
                        elseif(in_array('type=OTHER', $adress_values) && empty($contact_object_>Privat_Ort) && empty($contact_object->Privat_Adresse_Straße) && empty($contact_object->Privat_PLZ) && empty($contact_object->Privat_Land) && empty($contact_object->Privat_Bundesland)){
                            self::normalize_adress_values($adress_values);
                            $contact_object->Privat_Adresse_Straße = !empty($adress_values[0]) ? $adress_values[0] : NULL;
                            $contact_object->Privat_Adresse_Hsnr = !empty(self::parse_house_number_off_adress_street($adress_values[0])) ? self::parse_house_number_off_adress_street($adress_values[0]) : NULL;
                            $contact_object->Privat_Ort = !empty($adress_values[1]) ? $adress_values[1] : NULL;
                            $contact_object->Privat_Bundesland = !empty($adress_values[2]) ? $adress_values[2] : NULL;
                            $contact_object->Privat_PLZ = !empty($adress_values[3]) ? $adress_values[3] : NULL;
                            $contact_object->Privat_Land = !empty($adress_values[4]) ? $adress_values[4] : NULL;
                        }
                        elseif(in_array('type=OTHER', $adress_values)){
                            $additional_adress_sets[] = $adress_values;
                        }
                    } else {
                        
                        // fill up anything not set with not prefered adress set
                        
                        //first check for business adress
                        if(in_array('type=WORK', $adress_values) && empty($contact_object_>Dienstlich_Ort) && empty($contact_object->Dienstlich_Adresse_Straße) && empty($contact_object->Dienstlich_PLZ) && empty($contact_object->Dienstlich_Land) && empty($contact_object->Dienstlich_Bundesland)){
                            self::normalize_adress_values($adress_values);
                            $contact_object->Dienstlich_Adresse_Straße = !empty($adress_values[0]) ? $adress_values[0] : NULL;
                            $contact_object->Dienstlich_Adresse_Hsnr = !empty(self::parse_house_number_off_adress_street($adress_values[0])) ? self::parse_house_number_off_adress_street($adress_values[0]) : NULL;
                            $contact_object->Dienstlich_Ort = !empty($adress_values[1]) ? $adress_values[1] : NULL;
                            $contact_object->Dienstlich_Bundesland = !empty($adress_values[2]) ? $adress_values[2] : NULL;
                            $contact_object->Dienstlich_PLZ = !empty($adress_values[3]) ? $adress_values[3] : NULL;
                            $contact_object->Dienstlich_Land = !empty($adress_values[4]) ? $adress_values[4] : NULL;
                        }
                        elseif(in_array('type=WORK', $adress_values)){
                            $additional_adress_sets[] = $adress_values;
                        }
                        
                        //private adress
                        elseif(in_array('type=HOME', $adress_values) && empty($contact_object_>Privat_Ort) && empty($contact_object->Privat_Adresse_Straße) && empty($contact_object->Privat_PLZ) && empty($contact_object->Privat_Land) && empty($contact_object->Privat_Bundesland)){
                            self::normalize_adress_values($adress_values);
                            $contact_object->Privat_Adresse_Straße = !empty($adress_values[0]) ? $adress_values[0] : NULL;
                            $contact_object->Privat_Adresse_Hsnr = !empty(self::parse_house_number_off_adress_street($adress_values[0])) ? self::parse_house_number_off_adress_street($adress_values[0]) : NULL;
                            $contact_object->Privat_Ort = !empty($adress_values[1]) ? $adress_values[1] : NULL;
                            $contact_object->Privat_Bundesland = !empty($adress_values[2]) ? $adress_values[2] : NULL;
                            $contact_object->Privat_PLZ = !empty($adress_values[3]) ? $adress_values[3] : NULL;
                            $contact_object->Privat_Land = !empty($adress_values[4]) ? $adress_values[4] : NULL;
                        }
                        elseif(in_array('type=HOME', $adress_values)){
                            $additional_adress_sets[] = $adress_values;
                        }
                        
                        // OTHER adresses
                        elseif(in_array('type=OTHER', $adress_values) && empty($contact_object_>Dienstlich_Ort) && empty($contact_object->Dienstlich_Adresse_Straße) && empty($contact_object->Dienstlich_PLZ) && empty($contact_object->Dienstlich_Land) && empty($contact_object->Dienstlich_Bundesland)){
                            self::normalize_adress_values($adress_values);
                            $contact_object->Dienstlich_Adresse_Straße = !empty($adress_values[0]) ? $adress_values[0] : NULL;
                            $contact_object->Dienstlich_Adresse_Hsnr = !empty(self::parse_house_number_off_adress_street($adress_values[0])) ? self::parse_house_number_off_adress_street($adress_values[0]) : NULL;
                            $contact_object->Dienstlich_Ort = !empty($adress_values[1]) ? $adress_values[1] : NULL;
                            $contact_object->Dienstlich_Bundesland = !empty($adress_values[2]) ? $adress_values[2] : NULL;
                            $contact_object->Dienstlich_PLZ = !empty($adress_values[3]) ? $adress_values[3] : NULL;
                            $contact_object->Dienstlich_Land = !empty($adress_values[4]) ? $adress_values[4] : NULL;
                        }
                        elseif(in_array('type=OTHER', $adress_values) && empty($contact_object_>Privat_Ort) && empty($contact_object->Privat_Adresse_Straße) && empty($contact_object->Privat_PLZ) && empty($contact_object->Privat_Land) && empty($contact_object->Privat_Bundesland)){
                            self::normalize_adress_values($adress_values);
                            $contact_object->Privat_Adresse_Straße = !empty($adress_values[0]) ? $adress_values[0] : NULL;
                            $contact_object->Privat_Adresse_Hsnr = !empty(self::parse_house_number_off_adress_street($adress_values[0])) ? self::parse_house_number_off_adress_street($adress_values[0]) : NULL;
                            $contact_object->Privat_Ort = !empty($adress_values[1]) ? $adress_values[1] : NULL;
                            $contact_object->Privat_Bundesland = !empty($adress_values[2]) ? $adress_values[2] : NULL;
                            $contact_object->Privat_PLZ = !empty($adress_values[3]) ? $adress_values[3] : NULL;
                            $contact_object->Privat_Land = !empty($adress_values[4]) ? $adress_values[4] : NULL;
                        }
                        elseif(in_array('type=OTHER', $adress_values)){
                            $additional_adress_sets[] = $adress_values;
                        }
                    }
                }
                
                //fill up $contact_object->additinal_values with $name->value
                // it is important to be able to identify the values, to what adress set one belongs
                if(!empty($additional_adress_sets)){
                    
                    foreach ($additional_adress_sets as $adress_value_set){
                        
                        $adress_counter = 1;
                        //first check for business adress
                        if(in_array('type=WORK', $adress_value_set)){
                            
                            if( !array_key_exists('Dienstlich_Ort', $contact_object->additional_attributes) &&
                                !array_key_exists('Dienstlich_Adresse_Hsnr', $contact_object->additional_attributes) &&
                                !array_key_exists('Dienstlich_Adresse_Straße', $contact_object->additional_attributes) &&
                                !array_key_exists('Dienstlich_Bundesland', $contact_object->additional_attributes) &&
                                !array_key_exists('Dienstlich_PLZ', $contact_object->additional_attributes) &&
                                !array_key_exists('Dienstlich_Land', $contact_object->additional_attributes)){
                                
                                self::normalize_adress_values($adress_values);
                                $contact_object->additional_attributes['Dienstlich_Adresse_Straße'] = !empty($adress_values[0]) ? $adress_values[0] : NULL;
                                $contact_object->additional_attributes['Dienstlich_Adresse_Hsnr'] = !empty(self::parse_house_number_off_adress_street($adress_values[0])) ? self::parse_house_number_off_adress_street($adress_values[0]) : NULL;
                                $contact_object->additional_attributes['Dienstlich_Ort'] = !empty($adress_values[1]) ? $adress_values[1] : NULL;
                                $contact_object->additional_attributes['Dienstlich_Bundesland'] = !empty($adress_values[2]) ? $adress_values[2] : NULL;
                                $contact_object->additional_attributes['Dienstlich_PLZ'] = !empty($adress_values[3]) ? $adress_values[3] : NULL;
                                $contact_object->additional_attributes['Dienstlich_Land'] = !empty($adress_values[4]) ? $adress_values[4] : NULL;
                                
                            }
                                
                            while(  array_key_exists('Dienstlich_Ort'.$adress_counter, $contact_object->additional_attributes) ||
                                    array_key_exists('Dienstlich_Adresse_Hsnr', $contact_object->additional_attributes) ||
                                    array_key_exists('Dienstlich_Adresse_Straße'.$adress_counter, $contact_object->additional_attributes) ||
                                    array_key_exists('Dienstlich_Bundesland'.$adress_counter, $contact_object->additional_attributes) ||
                                    array_key_exists('Dienstlich_PLZ'.$adress_counter, $contact_object->additional_attributes) ||
                                    array_key_exists('Dienstlich_Land'.$adress_counter, $contact_object->additional_attributes)){
                                
                                $adress_counter++;
                            }
                            self::normalize_adress_values($adress_values);
                            $contact_object->additional_attributes['Dienstlich_Adresse_Straße'.$adress_counter] = !empty($adress_values[0]) ? $adress_values[0] : NULL;
                            $contact_object->additional_attributes['Dienstlich_Adresse_Hsnr'.$adress_counter] = !empty(self::parse_house_number_off_adress_street($adress_values[0])) ? self::parse_house_number_off_adress_street($adress_values[0]) : NULL;
                            $contact_object->additional_attributes['Dienstlich_Ort'.$adress_counter] = !empty($adress_values[1]) ? $adress_values[1] : NULL;
                            $contact_object->additional_attributes['Dienstlich_Bundesland'.$adress_counter] = !empty($adress_values[2]) ? $adress_values[2] : NULL;
                            $contact_object->additional_attributes['Dienstlich_PLZ'.$adress_counter] = !empty($adress_values[3]) ? $adress_values[3] : NULL;
                            $contact_object->additional_attributes['Dienstlich_Land'.$adress_counter] = !empty($adress_values[4]) ? $adress_values[4] : NULL;
                           
                        }
                        
                        elseif(in_array('type=HOME', $adress_value_set) || in_array('type=OTHER', $adress_value_set)){
                            
                            if( !array_key_exists('Privat_Ort', $contact_object->additional_attributes) &&
                                !array_key_exists('Privat_Adresse_Hsnr', $contact_object->additional_attributes) &&
                                !array_key_exists('Privat_Adresse_Straße', $contact_object->additional_attributes) &&
                                !array_key_exists('Privat_Bundesland', $contact_object->additional_attributes) &&
                                !array_key_exists('Privat_PLZ', $contact_object->additional_attributes) &&
                                !array_key_exists('Privat_Land', $contact_object->additional_attributes)){
                                
                                self::normalize_adress_values($adress_values);
                                $contact_object->additional_attributes['Privat_Adresse_Straße'] = !empty($adress_values[0]) ? $adress_values[0] : NULL;
                                $contact_object->additional_attributes['Privat_Adresse_Hsnr'] = !empty(self::parse_house_number_off_adress_street($adress_values[0])) ? self::parse_house_number_off_adress_street($adress_values[0]) : NULL;
                                $contact_object->additional_attributes['Privat_Ort'] = !empty($adress_values[1]) ? $adress_values[1] : NULL;
                                $contact_object->additional_attributes['Privat_Bundesland'] = !empty($adress_values[2]) ? $adress_values[2] : NULL;
                                $contact_object->additional_attributes['Privat_PLZ'] = !empty($adress_values[3]) ? $adress_values[3] : NULL;
                                $contact_object->additional_attributes['Privat_Land'] = !empty($adress_values[4]) ? $adress_values[4] : NULL;
                                
                            }
                                
                            while(  array_key_exists('Privat_Ort'.$adress_counter, $contact_object->additional_attributes) ||
                                    array_key_exists('Privat_Adresse_Hsnr', $contact_object->additional_attributes) ||
                                    array_key_exists('Privat_Adresse_Straße'.$adress_counter, $contact_object->additional_attributes) ||
                                    array_key_exists('Privat_Bundesland'.$adress_counter, $contact_object->additional_attributes) ||
                                    array_key_exists('Privat_PLZ'.$adress_counter, $contact_object->additional_attributes) ||
                                    array_key_exists('Privat_Land'.$adress_counter, $contact_object->additional_attributes)){
                                
                                $adress_counter++;
                            }
                            self::normalize_adress_values($adress_values);
                            $contact_object->additional_attributes['Privat_Adresse_Straße'.$adress_counter] = !empty($adress_values[0]) ? $adress_values[0] : NULL;
                            $contact_object->additional_attributes['Privat_Adresse_Hsnr'.$adress_counter] = !empty(self::parse_house_number_off_adress_street($adress_values[0])) ? self::parse_house_number_off_adress_street($adress_values[0]) : NULL;
                            $contact_object->additional_attributes['Privat_Ort'.$adress_counter] = !empty($adress_values[1]) ? $adress_values[1] : NULL;
                            $contact_object->additional_attributes['Privat_Bundesland'.$adress_counter] = !empty($adress_values[2]) ? $adress_values[2] : NULL;
                            $contact_object->additional_attributes['Privat_PLZ'.$adress_counter] = !empty($adress_values[3]) ? $adress_values[3] : NULL;
                            $contact_object->additional_attributes['Privat_Land'.$adress_counter] = !empty($adress_values[4]) ? $adress_values[4] : NULL;
                           
                        }
                    }
                }
                
            }
        }
        
        //PROBLEM: the position of the values is quite ambivalent
        //adress_value_set is array of adress values
        //strip beginning of the array off values, which are ADR or type
        //after that there will be 2 empty values for sure, strip them off as well
        //following will be array(x=>street,x+1=>city,x+2=>state,x+3=>postalcode,x+4->country)
        private static function normalize_adress_values(&$adress_value_set = NULL){
            
            if(!empty($adress_value_set) && is_array($adress_value_set)){
                $triming_done = 0;
                while($triming_done < 2){
                    
                    $adress_value = array_shift($adress_value_set);
                    if(strpos($adress_value, 'ADR') !== FALSE || strpos($adress_value, 'type') !== FALSE){
                        continue;
                    }
                    elseif(empty($adress_value)){
                        $triming_done++;
                    }
                }
            }
            return !empty($adress_value_set) ? TRUE : FALSE;
        }
    }
    
    class vCard_Converter extends vCard_Parse {
        
        //only the name of the file that needs to be converted, the path has to be set
        private     $vCard_file_name = NULL;
        
        private     $vCard_file_path = VCARD_TEMP_PATH.DS;
        
        private     $attribute_array= array();
        
        //content of vCard file as string
        private     $string_of_vCards = '';
        
        //content of vCard file split into sets of single vCards
        //array of the form: vCard#x => vCard_string
        //each vCard delimiter : BEGIN:VCARD...END:VCARD
        private     $set_of_vCards = array();
        
        private     $charset = 'UTF-8';
        
        //is an array in the fashion:
        //array(vCard_nr. => array(att_line_nr => array($attrbute_name, attribute_vlaue1, attribute_value2,...)))
        private     $single_vCard_set_parsed = array();
        
        private     $export_counter = 0;
        
        //array (vcard_nr =>array(attribute_name => attribute_value))
        private     $export_vcard_set = array();
        
        
        function __construct($vCard_file_name = NULL){
            
            $this->set_vCard_filename($vCard_file_name);
            $this->set_vCard_file_path();
            
        }
        
        function __destruct(){}
        
        private function set_vCard_filename($vCard_file_name = NULL){
            $this->vCard_file_name = $vCard_file_name;
        }
        
        private function set_vCard_file_path(){
            $this->vCard_file_path .= $this->vCard_file_name;
        }
        
        private function check_file_is_valid(){
            if(!empty($this->set_vCard_file_path) && is_file($this->set_vCard_file_path)){
                return TRUE;
            }
            else{return FALSE;}
        }
        
        private function get_values_from_file(){
            
            //get_content of file
            $this->string_of_vCards = vCard_Parse::get_vCard_file_content($this->vCard_file_path);
            
            //split into set of strings of single vCards
            $this->set_of_vCards = vCard_Parse::split_vCard_set_into_single_vCards($this->string_of_vCards);
            
            //in case somthing went wrong, set_of_vCards is FALSE
            if(!empty($this->set_of_vCards)){
                
                //parse every single vCard
                foreach($this->set_of_vCards as $card){
                    
                    //is an array in the fashion:
                    //array(vCard_nr. => array(att_line_nr => array($attrbute_name, attribute_vlaue1, attribute_value2,...)))
                    $this->single_vCard_set_parsed[] = vCard::parse_vCard($card);
                }
            }
        }
        
        
        public function to_array(){
            
            $this->get_values_from_file();
            
            foreach($this->single_vCard_set_parsed as $vcard){
                
                $vcard_attribute_set = array();
                
                $names = vCard::set_names($vcard);
            }
            
            
        }
        
    }

?>