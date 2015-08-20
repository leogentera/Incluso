<?php

namespace MoodleApi\Model;

use MoodleApi\Model\MoodleStudies;
use MoodleApi\Model\MoodleBadge;
use MoodleApi\Model\MoodleAddress;
use MoodleApi\Model\MoodleSocialNetworks;
use MoodleApi\Model\MoodleFamiliaCompartamos;
use MoodleApi\Utilities\Common;

class MoodleUserProfile extends Common {
	public $email = null;
	public $firstname;
	public $fullname;
	public $id;
	public $lastname;
	public $mothername;
	public $profileimageurl;
	public $profileimageurlsmall;
	public $username;
	
	// From this point the generic variables
	public $studies = array ();
	public $address;
	public $phones = array ();
	public $socialNetworks = array ();
	public $familiaCompartamos = array ();
	public $rank = 0;
	public $stars = 0;
	public $attributesAndQualities = array ();
	public $strengths = array ();
	public $recomendedBachelorDegrees = array ();
	public $likesAndPreferences = array ();
	public $dreamsToBe = array ();
	public $dreamsToHave = array ();
	public $dreamsToDo = array ();
	public $badgesEarned = array ();
	public $badgesToEarn = array ();
	public $showMyInformation = true;
	public $showAttributesAndQualities = true;
	public $showLikesAndPreferences = true;
	public $showBadgesEarned = true;
	public $showStrengths = true;
	public $showRecomendedBachelorDegrees = true;
	public $showMyDreams = true;
	public $showGeneralData = true;
	public $showEducation = true;
	public $showAddress = true;
	public $showPhones = true;
	public $showSocialNetworks = true;
	public $showFamiliaCompartamos = true;
	public $showInspirationalCharacters = true;
	public $showILiveWith = true;
	public $showMainActivity = true;
	public $showCurrentEducation = true;
	public $showFamiliar = true;
	public $showMoneyIncome = true;
	public $showHealth = true;
	public $showDevices = true;
	public $showVideogames = true;
	public $alias = "";
	public $termsAndConditions = false;
	public $informationUsage = false;
	public $status = "Enabled";
	public $allowToSendAdvertisement = false;
	public $course = "2";
	public $additionalEmails = array ();
	public $inspirationalCharacters = array ();
	public $favoriteGames = array ();
	public $favoriteSports = array ();
	public $artisticActivities = array ();
	public $hobbies = array ();
	public $talents = array ();
	public $values = array ();
	public $habilities = array ();
	public $iLiveWith = "";
	public $mainActivity = array ();
	public $currentStudies = null;
	public $children = "";
	public $gotMoneyIncome = "";
	public $moneyIncome = array ();
	public $medicalCoverage = "";
	public $medicalInsurance = "";
	public $knownDevices = array ();
	public $ownDevices = array ();
	public $phoneUsage = array ();
	public $playVideogames = "";
	public $videogamesFrecuency = "";
	public $videogamesHours = "";
	public $kindOfVideogames = array ();
	public $gender = "";
	public $birthday = "";
	public $maritalStatus = "";
	public $age = "";
	public $birthCountry;
	public function __construct($data, $requestinguserid) {
		$customFields = array ();
		$currentStudies = new MoodleCurrentStudies(array());
		// We turn the custom fields to an array because otherwise, we should do a for loop to search for each value
		for($i = 0; count ( $data ['customfields'] ) > $i; $i ++) {
			$customFields [$data ['customfields'] [$i] ['name']] = $data ['customfields'] [$i] ['value'];
		}
		
		if (array_key_exists ( 'showMyInformation', $customFields )) {
			$this->showMyInformation = $customFields ['showMyInformation'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showAttributesAndQualities', $customFields )) {
			$this->showAttributesAndQualities = $customFields ['showAttributesAndQualities'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showLikesAndPreferences', $customFields )) {
			$this->showLikesAndPreferences = $customFields ['showLikesAndPreferences'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showBadgesEarned', $customFields )) {
			$this->showBadgesEarned = $customFields ['showBadgesEarned'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showStrengths', $customFields )) {
			$this->showStrengths = $customFields ['showStrengths'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showRecomendedBachelorDegrees', $customFields )) {
			$this->showRecomendedBachelorDegrees = $customFields ['showRecomendedBachelorDegrees'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showMyDreams', $customFields )) {
			$this->showMyDreams = $customFields ['showMyDreams'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		//
		
		if (array_key_exists ( 'showGeneralData', $customFields )) {
			$this->showGeneralData = $customFields ['showGeneralData'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showEducation', $customFields )) {
			$this->showEducation = $customFields ['showEducation'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showAddress', $customFields )) {
			$this->showAddress = $customFields ['showAddress'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showSocialNetworks', $customFields )) {
			$this->showSocialNetworks = $customFields ['showSocialNetworks'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showFamiliaCompartamos', $customFields )) {
			$this->showFamiliaCompartamos = $customFields ['showFamiliaCompartamos'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showInspirationalCharacters', $customFields )) {
			$this->showInspirationalCharacters = $customFields ['showInspirationalCharacters'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showILiveWith', $customFields )) {
			$this->showILiveWith = $customFields ['showILiveWith'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showMainActivity', $customFields )) {
			$this->showMainActivity = $customFields ['showMainActivity'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showCurrentEducation', $customFields )) {
			$this->showCurrentEducation = $customFields ['showCurrentEducation'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showFamiliar', $customFields )) {
			$this->showFamiliar = $customFields ['showFamiliar'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showMoneyIncome', $customFields )) {
			$this->showMoneyIncome = $customFields ['showMoneyIncome'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showMoneyIncome', $customFields )) {
			$this->showMoneyIncome = $customFields ['showMoneyIncome'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showMoneyIncome', $customFields )) {
			$this->showMoneyIncome = $customFields ['showMoneyIncome'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showHealth', $customFields )) {
			$this->showHealth = $customFields ['showHealth'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showDevices', $customFields )) {
			$this->showDevices = $customFields ['showDevices'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showVideogames', $customFields )) {
			$this->showVideogames = $customFields ['showVideogames'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'showPhones', $customFields )) {
			$this->showPhones = $customFields ['showPhones'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		if (array_key_exists ( 'course', $customFields )) {
			$this->course = $customFields ['course'];
			// var_dump($this->phones);
		}
		
		$this->firstname = (! empty ( $data ['firstname'] )) ? $data ['firstname'] : null;
		$this->fullname = (! empty ( $data ['fullname'] )) ? $data ['fullname'] : null;
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->lastname = (! empty ( $data ['lastname'] )) ? $data ['lastname'] : null;
		//$this->profileimageurl = (! empty ( $data ['profileimageurl'] )) ? $data ['profileimageurl'] : null;
		$this->profileimageurl = "http://incluso.definityfirst.com/moodle/pluginfile.php/42/user/icon/f1";
		$this->profileimageurlsmall = (! empty ( $data ['profileimageurlsmall'] )) ? $data ['profileimageurlsmall'] : null;
		$this->username = (! empty ( $data ['username'] )) ? $data ['username'] : null;
		
		if (array_key_exists ( 'alias', $customFields )) {
			$this->alias = $customFields ['alias'];
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'stars', $customFields )) {
			$this->stars = $customFields ['stars'];
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'termsAndConditions', $customFields )) {
			$this->termsAndConditions = $customFields ['termsAndConditions'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'informationUsage', $customFields )) {
			$this->informationUsage = $customFields ['informationUsage'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'status', $customFields )) {
			$this->status = $customFields ['status'];
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'allowToSendAdvertisement', $customFields )) {
			$this->allowToSendAdvertisement = $customFields ['allowToSendAdvertisement'] == 0 ? true : false;
			// var_dump($this->phones);
		}
		
		if (array_key_exists ( 'mothername', $customFields )) {
			$this->mothername = $customFields ['mothername'];
		}
		
		if ($requestinguserid == $this->id) {
			// if( $this->showMyInformation){
			$this->address = new MoodleAddress ( $data, $customFields );
			$this->email = (! empty ( $data ['email'] )) ? $data ['email'] : "";
			$this->studies = new MoodleStudies ();
			$this->studies = $this->studies->get ( $customFields );
			if (array_key_exists ( 'phones', $customFields )) {
				$this->phones = $this->createTableFromCompundField ( $customFields ['phones'] );
				// var_dump($this->phones);
			}
			
			$this->currentStudies = new MoodleCurrentStudies ();
			
			$this->currentStudies = $this->currentStudies->get ( $customFields );
			
			if (array_key_exists ( 'additionalEmails', $customFields )) {
				$this->additionalEmails = $this->createTableFromCompundField ( $customFields ['additionalEmails'] );
				// var_dump($this->phones);
			}
			
			$this->socialNetworks = new MoodleSocialNetworks ();
			$this->socialNetworks = $this->socialNetworks->get ( $customFields );
			$this->familiaCompartamos = new MoodleFamiliaCompartamos ();
			$this->familiaCompartamos = $this->familiaCompartamos->get ( $customFields );
			if (array_key_exists ( 'stage', $customFields )) {
				$this->stage = $customFields ['stage'];
			}
			
			$this->inspirationalCharacters = new MoodleCharacters ();
			
			$this->inspirationalCharacters = $this->inspirationalCharacters->get ( $customFields );
			
			if (array_key_exists ( 'favoriteGames', $customFields )) {
				$this->favoriteGames = $this->createTableFromCompundField ( $customFields ['favoriteGames'] );
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'iLiveWith', $customFields )) {
				$this->iLiveWith = $customFields ['iLiveWith'];
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'children', $customFields )) {
				$this->children = $customFields ['children'];
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'gotMoneyIncome', $customFields )) {
				$this->gotMoneyIncome = $customFields ['gotMoneyIncome'];
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'medicalCoverage', $customFields )) {
				$this->medicalCoverage = $customFields ['medicalCoverage'];
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'medicalInsurance', $customFields )) {
				$this->medicalInsurance = $customFields ['medicalInsurance'];
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'mainActivity', $customFields )) {
				$this->mainActivity = $this->createTableFromCompundField ( $customFields ['mainActivity'] );
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'moneyIncome', $customFields )) {
				$this->moneyIncome = $this->createTableFromCompundField ( $customFields ['moneyIncome'] );
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'knownDevices', $customFields )) {
				$this->knownDevices = $this->createTableFromCompundField ( $customFields ['knownDevices'] );
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'ownDevices', $customFields )) {
				$this->ownDevices = $this->createTableFromCompundField ( $customFields ['ownDevices'] );
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'phoneUsage', $customFields )) {
				$this->phoneUsage = $this->createTableFromCompundField ( $customFields ['phoneUsage'] );
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'playVideogames', $customFields )) {
				$this->playVideogames =  $customFields ['playVideogames'] ;
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'videogamesFrecuency', $customFields )) {
				$this->videogamesFrecuency = $customFields ['videogamesFrecuency'] ;
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'videogamesHours', $customFields )) {
				$this->videogamesHours = $customFields ['videogamesHours'] ;
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'kindOfVideogames', $customFields )) {
				$this->kindOfVideogames = $this->createTableFromCompundField ( $customFields ['kindOfVideogames'] );
			}
			
			if (array_key_exists ( 'gender', $customFields )) {
				$this->gender = $customFields ['gender'];
			}
			
			if (array_key_exists ( 'birthday', $customFields )) {
				$this->birthday = $customFields ['birthday'];
			}
			
			if (array_key_exists ( 'maritalStatus', $customFields )) {
				$this->maritalStatus = $customFields ['maritalStatus'];
			}
			
			if (array_key_exists ( 'birthCountry', $customFields )) {
				$this->birthCountry = $customFields ['birthCountry'];
			}
			
			// }
			// else{
			// $this->address=new MoodleAddress();
			// }
			
			 if( $this->showAttributesAndQualities && array_key_exists ( 'attributesAndQualities' , $customFields )){
					$this->attributesAndQualities = $this->createTableFromCompundField ( $customFields ['attributesAndQualities'] );
			 }
			
			if (array_key_exists ( 'talents', $customFields )) {
				$this->talents = $this->createTableFromCompundField ( $customFields ['talents'] );
			}
			
			if (array_key_exists ( 'values', $customFields )) {
				$this->values = $this->createTableFromCompundField ( $customFields ['values'] );
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'habilities', $customFields )) {
				$this->habilities = $this->createTableFromCompundField ( $customFields ['habilities'] );
				// var_dump($this->phones);
			}
			
			// var_dump($this->phones);
			// }
			
			// if($this->showLikesAndPreferences ){
			
			if (array_key_exists ( 'favoriteSports', $customFields )) {
				$this->favoriteSports = $this->createTableFromCompundField ( $customFields ['favoriteSports'] );
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'hobbies', $customFields )) {
				$this->hobbies = $this->createTableFromCompundField ( $customFields ['hobbies'] );
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'artisticActivities', $customFields )) {
				$this->artisticActivities = $this->createTableFromCompundField ( $customFields ['artisticActivities'] );
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'likesAndPreferences', $customFields )) {
				$this->likesAndPreferences = $this->createTableFromCompundField ( $customFields ['likesAndPreferences'] );
				// var_dump($this->phones);
			}
			// var_dump($this->phones);
			// }
			
			if (array_key_exists ( 'recomendedBachelorDegrees', $customFields )) {
				$this->recomendedBachelorDegrees = $this->createTableFromCompundField ( $customFields ['recomendedBachelorDegrees'] );
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'strengths', $customFields )) {
				$this->strengths = $this->createTableFromCompundField ( $customFields ['strengths'] );
			}
			
			if (array_key_exists ( 'likesAndPreferences', $customFields )) {
				$this->likesAndPreferences = $this->createTableFromCompundField ( $customFields ['likesAndPreferences'] );
				// //var_dump($this->phones);
			}
			
			// if($this->showMyDreams){
			if (array_key_exists ( 'dreamsToBe', $customFields )) {
				$this->dreamsToBe = $this->createTableFromCompundField ( $customFields ['dreamsToBe'] );
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'dreamsToHave', $customFields )) {
				$this->dreamsToHave = $this->createTableFromCompundField ( $customFields ['dreamsToHave'] );
				// var_dump($this->phones);
			}
			
			if (array_key_exists ( 'dreamsToDo', $customFields )) {
				$this->dreamsToDo = $this->createTableFromCompundField ( $customFields ['dreamsToDo'] );
				// var_dump($this->phones);
			}
			// }
		} else {
			$this->address = new MoodleAddress ();
		}
	}
	public function setRank($rank) {
		$this->rank = $rank;
	}
	public function setBadges($badgesEarned, $badgesToEarn) {
		if ($this->showBadgesEarned) {
			$this->badgesEarned = $badgesEarned;
			$this->badgesToEarn = $badgesToEarn;
		}
	}
}