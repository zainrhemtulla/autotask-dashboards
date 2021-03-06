<?php
	/**
	 * Copyright 2013, Campai Business Solutions B.V. (http://www.campai.nl)
	 *
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 *
	 * @copyright     Copyright 2013, Campai Business Solutions B.V. (http://www.campai.nl)
	 * @link          http://autotask.campai.nl
	 * @license       MIT License (http://opensource.org/licenses/mit-license.php)
	 * @author        Coen Coppens <coen@campai.nl>
	 */
	App::uses('AutotaskAppModel', 'Autotask.Model');

	class Opentickets extends AutotaskAppModel {

		public $name = 'Opentickets';

		public $hasMany = array(
				'Autotask.Ticket'
			,	'Autotask.Ticketstatuscount'
		);


		public function getTotals(Array $aQueueIds){

			$aOldStatuses = $this->Ticketstatuscount->find('all', array(
					'conditions' => array(
							'Ticketstatuscount.created' => date('Y-m-d', strtotime('-1 day'))
						,	'Ticketstatuscount.ticketstatus_id' => 2
						,	'Ticketstatuscount.queue_id' => $aQueueIds
					)
			));

			$aNewStatuses = $this->Ticketstatuscount->find('all', array(
					'conditions' => array(
							'Ticketstatuscount.created' => date('Y-m-d')
						,	'Ticketstatuscount.ticketstatus_id' => 2
						,	'Ticketstatuscount.queue_id' => $aQueueIds
					)
			));

			$iNew = 0;
			if (!empty($aNewStatuses)) {

				foreach ($aNewStatuses as $aStatus) {
					$iNew += $aStatus['Ticketstatuscount']['count'];
				}

			}

			$aTotals = array();
			$aTotals = array(
					'counts' => array(
							'new' => $iNew
						,	'old' => 0
						,	'difference' => 0
					)
			);

			if (!empty($aOldStatuses['Ticketstatuscount']['count'])) {
				$iOld = $aOldStatuses['Ticketstatuscount']['count'];
			} else {
				$iOld = 0;
			}

			$aTotals['counts']['old'] = $iOld;

			// You cant divide by zero
			if (0 == $iOld) {
				$iOld = 1;
			}

			$aTotals['counts']['difference'] = number_format(((100*$iNew)/$iOld)-100, 0, ',', '.');

			return $aTotals;

		}

	}