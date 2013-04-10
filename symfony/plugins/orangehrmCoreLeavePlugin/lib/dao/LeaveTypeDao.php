<?php

/*
 *
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */

class LeaveTypeDao extends BaseDao {

    /**
     * Get Logger instance. Creates if not already created.
     *
     * @return Logger
     */
    protected function getLogger() {
        if (is_null($this->logger)) {
            $this->logger = Logger::getLogger('leave.LeaveTypeDao');
        }

        return($this->logger);
    }

    /**
     *
     * @param LeaveType $leaveType
     * @return boolean
     */
    public function saveLeaveType(LeaveType $leaveType) {
        try {
            if ($leaveType->getLeaveTypeId() == '') {

                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($leaveType);
                $leaveType->setLeaveTypeId($idGenService->getNextID());
            }

            $leaveType->save();

            return true;
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in saveLeaveType:" . $e);
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Leave Type
     * @param array leaveTypeList
     * @returns boolean
     * @throws DaoException
     */
    public function deleteLeaveType($leaveTypeList) {

        try {

            $q = Doctrine_Query::create()
                            ->update('LeaveType lt')
                            ->set('lt.availableFlag', '?', '0')
                            ->whereIn('lt.leaveTypeId', $leaveTypeList);
            $numDeleted = $q->execute();
            if ($numDeleted > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in deleteLeaveType:" . $e);
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get Leave Type list
     * @param mixed $operationalCountryId 
     * @return LeaveType Collection
     */
    public function getLeaveTypeList($operationalCountryId = null) {
        try {
            $q = Doctrine_Query::create()
                            ->from('LeaveType lt')
                            ->where('lt.availableFlag = 1')
                            ->orderBy('lt.leaveTypeName');
            
            if (!is_null($operationalCountryId)) {
                if (is_array($operationalCountryId)) {
                    $q->andWhereIn('lt.operationalCountryId', $operationalCountryId);
                } else {
                    $q->andWhere('lt.operationalCountryId = ? ', $operationalCountryId);
                }
            }
            
            $leaveTypeList = $q->execute();

            return $leaveTypeList;
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in getLeaveTypeList:" . $e);
            throw new DaoException($e->getMessage());
        }
    }

    public function getDeletedLeaveTypeList($operationalCountryId = null) {
        try {
            $q = Doctrine_Query::create()
                            ->from('LeaveType lt')
                            ->where('lt.availableFlag = 0')
                            ->orderBy('lt.leaveTypeId');

            if (!is_null($operationalCountryId)) {
                $q->andWhere('lt.operationalCountryId = ? ', $operationalCountryId);
            }
            
            $leaveTypeList = $q->execute();

            return $leaveTypeList;
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in getDeletedLeaveTypeList:" . $e);
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Read Leave Type
     * @return LeaveType
     */
    public function readLeaveType($leaveTypeId) {
        try {
            return Doctrine::getTable('LeaveType')->find($leaveTypeId);
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in readLeaveType:" . $e);
            throw new DaoException($e->getMessage());
        }
    }

    public function readLeaveTypeByName($leaveTypeName) {
        try {
            $q = Doctrine_Query::create()
                            ->from('LeaveType lt')
                            ->where("lt.leaveTypeName = ?", $leaveTypeName)
                            ->andWhere('lt.availableFlag = 1');

            $leaveTypeCollection = $q->execute();

            return $leaveTypeCollection[0];
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in readLeaveTypeByName:" . $e);
            throw new DaoException($e->getMessage());
        }
    }

    public function undeleteLeaveType($leaveTypeId) {

        try {

            $q = Doctrine_Query::create()
                            ->update('LeaveType lt')
                            ->set('lt.availableFlag', '1')
                            ->where("lt.leaveTypeId = '" . $leaveTypeId . "'");

            $numUpdated = $q->execute();

            if ($numUpdated > 0) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in undeleteLeaveType:" . $e);
            throw new DaoException($e->getMessage());
        }
    }

}