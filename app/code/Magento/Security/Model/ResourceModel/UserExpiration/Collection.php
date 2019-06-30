<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Security\Model\ResourceModel\UserExpiration;

/**
 * Admin user expiration collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'user_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Security\Model\UserExpiration::class,
            \Magento\Security\Model\ResourceModel\UserExpiration::class
        );
    }

    /**
     * Filter for expired, active users.
     *
     * @param string $now
     * @return $this
     */
    public function addActiveExpiredUsersFilter($now = null): Collection
    {
        if ($now === null) {
            $now = new \DateTime();
            $now->format('Y-m-d H:i:s');
        }
        $this->getSelect()->joinLeft(
            ['user' => $this->getTable('admin_user')],
            'main_table.user_id = user.user_id',
            ['is_active']
        );
        $this->addFieldToFilter('expires_at', ['lt' => $now])
            ->addFieldToFilter('user.is_active', 1);

        return $this;
    }

    /**
     * Filter collection by user id.
     * @param array $userIds
     * @return Collection
     */
    public function addUserIdsFilter($userIds = []): Collection
    {
        return $this->addFieldToFilter('main_table.user_id', ['in' => $userIds]);
    }

    /**
     * Get any expired records for the given user.
     *
     * @param $userId
     * @return Collection
     */
    public function addExpiredRecordsForUserFilter($userId): Collection
    {
        return $this->addActiveExpiredUsersFilter()
            ->addFieldToFilter('main_table.user_id', $userId);
    }
}
