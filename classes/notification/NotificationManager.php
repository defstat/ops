<?php

/**
 * @file classes/notification/NotificationManager.php
 *
 * Copyright (c) 2014-2024 Simon Fraser University
 * Copyright (c) 2000-2024 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class NotificationManager
 *
 * @see NotificationDAO
 * @see Notification
 *
 * @brief Class for Notification Manager.
 */

namespace APP\notification;

use APP\core\Application;
use APP\facades\Repo;
use APP\notification\managerDelegate\ApproveSubmissionNotificationManager;
use APP\server\Server;
use PKP\core\PKPApplication;
use PKP\core\PKPRequest;
use PKP\notification\NotificationManagerDelegate;
use PKP\notification\PKPNotification;
use PKP\notification\PKPNotificationManager;

class NotificationManager extends PKPNotificationManager
{
    /** @var array Cache each user's most privileged role for each submission */
    public $privilegedRoles;

    /**
     * @copydoc PKPNotificationOperationManager::getNotificationUrl()
     */
    public function getNotificationUrl(PKPRequest $request, PKPNotification $notification): string
    {
        $router = $request->getRouter();
        $dispatcher = $router->getDispatcher();

        switch ($notification->getType()) {
            // OPS: links leading to a new submission have to be redirected to production stage
            case Notification::NOTIFICATION_TYPE_SUBMISSION_SUBMITTED:
                $contextDao = Application::getContextDAO();
                /** @var Server */
                $context = $contextDao->getById($notification->getContextId());
                return $dispatcher->url($request, PKPApplication::ROUTE_PAGE, $context->getPath(), 'workflow', 'production', $notification->getAssocId());
        }
        return parent::getNotificationUrl($request, $notification);
    }

    /**
     * Helper function to get an preprint title from a notification's associated object
     */
    public function _getPreprintTitle(PKPNotification $notification): string
    {
        if ($notification->getAssocType() != Application::ASSOC_TYPE_SUBMISSION) {
            throw new \Exception('Unexpected assoc type!');
        }
        $preprint = Repo::submission()->get($notification->getAssocId());
        return $preprint?->getCurrentPublication()?->getLocalizedFullTitle();
    }

    /**
     * Return a CSS class containing the icon of this notification type
     */
    public function getIconClass(PKPNotification $notification): string
    {
        return match($notification->getType()) {
            Notification::NOTIFICATION_TYPE_NEW_ANNOUNCEMENT => 'notifyIconNewAnnouncement',
            default => parent::getIconClass($notification)
        };
    }

    /**
     * @copydoc PKPNotificationManager::getMgrDelegate()
     */
    protected function getMgrDelegate(int $notificationType, int $assocType, int $assocId): ?NotificationManagerDelegate
    {
        switch ($notificationType) {
            case Notification::NOTIFICATION_TYPE_APPROVE_SUBMISSION:
                if ($assocType != Application::ASSOC_TYPE_SUBMISSION) {
                    throw new \Exception('Unexpected assoc type!');
                }
                return new ApproveSubmissionNotificationManager($notificationType);
        }
        // Otherwise, fall back on parent class
        return parent::getMgrDelegate($notificationType, $assocType, $assocId);
    }
}

if (!PKP_STRICT_MODE) {
    class_alias('\APP\notification\NotificationManager', '\NotificationManager');
}
